<?php
// index.php
session_start();

// ถ้า login แล้ว redirect ไป dashboard
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: views/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Condo System - เข้าสู่ระบบ</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Login Header -->
            <div class="login-header">
                <i class="fas fa-building"></i>
                <h3>CRM Condo System</h3>
                <p class="mb-0">ระบบจัดการคอนโดและสินเชื่อ</p>
            </div>
            
            <!-- Login Body -->
            <div class="login-body">
                <form id="loginForm">
                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i> อีเมล
                        </label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               placeholder="กรอกอีเมลของคุณ"
                               required
                               autocomplete="email">
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> รหัสผ่าน
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   placeholder="กรอกรหัสผ่าน"
                                   required
                                   autocomplete="current-password">
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">จำฉันไว้</label>
                        </div>
                        <a href="#" class="text-decoration-none" onclick="forgotPassword()">
                            ลืมรหัสผ่าน?
                        </a>
                    </div>
                    
                    <!-- Login Button -->
                    <button type="submit" class="btn btn-primary btn-login w-100">
                        <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                    </button>
                </form>
                
            </div>
            
            <!-- Login Footer -->
            <div class="login-footer">
                <p class="mb-0">&copy; 2026 CRM Condo System v1.0</p>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="text-white text-center">
                <div class="spinner-border mb-3" role="status"></div>
                <h5>กำลังเข้าสู่ระบบ...</h5>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // ตรวจสอบว่ามีการจำอีเมลไว้หรือไม่
            if(localStorage.getItem('remembered_email')) {
                $('#email').val(localStorage.getItem('remembered_email'));
                $('#remember').prop('checked', true);
            }
            
            // Submit Form
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                const email = $('#email').val().trim();
                const password = $('#password').val().trim();
                
                // Validation
                if(!email) {
                    $('#email').addClass('is-invalid');
                    Swal.fire({
                        icon: 'warning',
                        title: 'กรุณากรอกอีเมล',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }
                
                if(!password) {
                    $('#password').addClass('is-invalid');
                    Swal.fire({
                        icon: 'warning',
                        title: 'กรุณากรอกรหัสผ่าน',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }
                
                // แสดง Loading
                $('#loadingOverlay').fadeIn(200);
                
                // ส่ง AJAX
                $.ajax({
                    url: 'api/auth/login.php',
                    type: 'POST',
                    data: JSON.stringify({
                        email: email,
                        password: password
                    }),
                    contentType: 'application/json',
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        $('#loadingOverlay').fadeOut(200);
                        
                        if(response.success) {
                            // จดจำอีเมล
                            if($('#remember').is(':checked')) {
                                localStorage.setItem('remembered_email', email);
                            } else {
                                localStorage.removeItem('remembered_email');
                            }
                            
                            // แสดงข้อความสำเร็จ
                            Swal.fire({
                                icon: 'success',
                                title: 'เข้าสู่ระบบสำเร็จ',
                                text: `ยินดีต้อนรับ ${response.user.name}`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Redirect ตาม Role
                            setTimeout(function() {
                                redirectByRole(response.user.role);
                            }, 1500);
                            
                        } else {
                            // แสดงข้อผิดพลาด
                            $('#loginForm').addClass('shake');
                            setTimeout(() => $('#loginForm').removeClass('shake'), 300);
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'เข้าสู่ระบบไม่สำเร็จ',
                                text: response.message,
                                confirmButtonText: 'ลองอีกครั้ง'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loadingOverlay').fadeOut(200);
                        
                        let errorMessage = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้';
                        if(xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: errorMessage,
                            confirmButtonText: 'ตกลง'
                        });
                    }
                });
            });
            
            // ล้าง validation เมื่อพิมพ์
            $('#email, #password').on('input', function() {
                $(this).removeClass('is-invalid');
            });
        });
        
        // Toggle Password
        function togglePassword() {
            const passwordInput = $('#password');
            const toggleIcon = $('#toggleIcon');
            
            if(passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                toggleIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        }
        
        // Forgot Password
        function forgotPassword() {
            Swal.fire({
                title: 'ลืมรหัสผ่าน?',
                text: 'กรุณาติดต่อผู้ดูแลระบบ',
                icon: 'info',
                confirmButtonText: 'ตกลง'
            });
        }
        
        // Redirect by Role
        function redirectByRole(role) {
            switch(role) {
                case 'admin':
                    window.location.href = 'views/dashboard.php';
                    break;
                case 'admin_page':
                    window.location.href = 'views/customers.php';
                    break;
                case 'kpi':
                    window.location.href = 'views/kpi_check.php';
                    break;
                case 'support':
                    window.location.href = 'views/dashboard.php';
                    break;
                default:
                    window.location.href = 'views/dashboard.php';
            }
        }
        
        // ป้องกันการกด Back หลัง logout
        window.history.pushState(null, '', window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, '', window.location.href);
        };
    </script>
</body>
</html>