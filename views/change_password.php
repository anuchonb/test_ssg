<?php
// views/change_password.php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../includes/header.php';
include_once '../includes/sidebar.php';
include_once '../includes/auth_check.php';
include_once '../includes/functions.php';

$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">🔒 เปลี่ยนรหัสผ่าน</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="profile.php">ข้อมูลส่วนตัว</a></li>
                        <li class="breadcrumb-item active">เปลี่ยนรหัสผ่าน</li>
                    </ol>
                </nav>
            </div>
            <a href="profile.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <!-- Security Tips Card -->
                <div class="card mb-4 border-left-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-shield-alt fa-2x text-warning"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">คำแนะนำเพื่อความปลอดภัย</h6>
                                <ul class="mb-0 small text-muted">
                                    <li>ใช้รหัสผ่านที่คาดเดายาก (ตัวพิมพ์ใหญ่, ตัวพิมพ์เล็ก, ตัวเลข, อักขระพิเศษ)</li>
                                    <li>ไม่ใช้รหัสผ่านซ้ำกับบัญชีอื่น</li>
                                    <li>เปลี่ยนรหัสผ่านอย่างน้อยทุก 90 วัน</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password Form -->
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-key"></i> 
                            เปลี่ยนรหัสผ่าน - <?php echo htmlspecialchars($user_name); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="changePasswordForm" novalidate>
                            
                            <!-- Current Password -->
                            <div class="mb-4">
                                <label class="form-label">
                                    รหัสผ่านปัจจุบัน <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-unlock-alt"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="current_password" 
                                           placeholder="กรอกรหัสผ่านปัจจุบัน" 
                                           required
                                           autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('current_password', 'toggleCurrentIcon')"
                                            tabindex="-1">
                                        <i class="fas fa-eye" id="toggleCurrentIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label class="form-label">
                                    รหัสผ่านใหม่ <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="new_password" 
                                           placeholder="กรอกรหัสผ่านใหม่" 
                                           required
                                           minlength="6"
                                           autocomplete="new-password"
                                           oninput="checkPasswordStrength(this.value)">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('new_password', 'toggleNewIcon')"
                                            tabindex="-1">
                                        <i class="fas fa-eye" id="toggleNewIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Password Strength Meter -->
                            <div class="mb-3" id="passwordStrengthContainer" style="display: none;">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>ความแข็งแรงของรหัสผ่าน</small>
                                    <small id="strengthLabel" class="fw-bold">-</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" 
                                         id="strengthBar" 
                                         role="progressbar" 
                                         style="width: 0%"></div>
                                </div>
                            </div>

                            <!-- Password Requirements -->
                            <div class="card bg-light mb-3">
                                <div class="card-body py-3">
                                    <small class="fw-bold">ข้อกำหนดรหัสผ่าน:</small>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <i id="reqLengthIcon" class="fas fa-times-circle text-muted me-2"></i>
                                                <small id="reqLength" class="text-muted">อย่างน้อย 6 ตัวอักษร</small>
                                            </div>
                                            <div class="d-flex align-items-center mb-1">
                                                <i id="reqUpperIcon" class="fas fa-times-circle text-muted me-2"></i>
                                                <small id="reqUpper" class="text-muted">มีตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <i id="reqLowerIcon" class="fas fa-times-circle text-muted me-2"></i>
                                                <small id="reqLower" class="text-muted">มีตัวพิมพ์เล็กอย่างน้อย 1 ตัว</small>
                                            </div>
                                            <div class="d-flex align-items-center mb-1">
                                                <i id="reqNumberIcon" class="fas fa-times-circle text-muted me-2"></i>
                                                <small id="reqNumber" class="text-muted">มีตัวเลขอย่างน้อย 1 ตัว</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Confirm New Password -->
                            <div class="mb-3">
                                <label class="form-label">
                                    ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           placeholder="ยืนยันรหัสผ่านใหม่" 
                                           required
                                           minlength="6"
                                           autocomplete="new-password"
                                           oninput="checkPasswordMatch()">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('confirm_password', 'toggleConfirmIcon')"
                                            tabindex="-1">
                                        <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                                    </button>
                                </div>
                                <div id="passwordMatchMessage" class="form-text"></div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="profile.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> ยกเลิก
                                </a>
                                <button type="button" class="btn btn-warning btn-lg" onclick="changePassword()">
                                    <i class="fas fa-key"></i> เปลี่ยนรหัสผ่าน
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password History (Optional) -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-history"></i> ข้อมูลการเข้าใช้งาน</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="200"><strong>อีเมล:</strong></td>
                                <td><?php echo htmlspecialchars($user_email); ?></td>
                            </tr>
                            <tr>
                                <td><strong>เปลี่ยนรหัสผ่านล่าสุด:</strong></td>
                                <td id="lastPasswordChange">กำลังโหลด...</td>
                            </tr>
                            <tr>
                                <td><strong>เข้าสู่ระบบล่าสุด:</strong></td>
                                <td><?php echo date('d/m/Y H:i:s', $_SESSION['login_time'] ?? time()); ?></td>
                            </tr>
                            <tr>
                                <td><strong>IP Address:</strong></td>
                                <td><?php echo $_SERVER['REMOTE_ADDR'] ?? 'ไม่ระบุ'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ============ CHANGE PASSWORD JAVASCRIPT ============

let isPasswordVisible = {
    current: false,
    new: false,
    confirm: false
};

$(document).ready(function() {
    // Focus on current password field
    $('#current_password').focus();
    
    // Remove invalid class on input
    $(document).on('input', '.is-invalid', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Load last password change info
    loadPasswordInfo();
});

// ============ TOGGLE PASSWORD ============
function togglePassword(inputId, iconId) {
    const input = $('#' + inputId);
    const icon = $('#' + iconId);
    
    if(input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

// ============ PASSWORD STRENGTH ============
function checkPasswordStrength(password) {
    // Show/hide strength container
    if(password.length === 0) {
        $('#passwordStrengthContainer').hide();
        resetRequirements();
        return;
    }
    
    $('#passwordStrengthContainer').show();
    
    let score = 0;
    
    // Length check (6+)
    if(password.length >= 6) {
        score += 25;
        updateRequirement('reqLength', true);
    } else {
        updateRequirement('reqLength', false);
    }
    
    // Uppercase check
    if(/[A-Z]/.test(password)) {
        score += 25;
        updateRequirement('reqUpper', true);
    } else {
        updateRequirement('reqUpper', false);
    }
    
    // Lowercase check
    if(/[a-z]/.test(password)) {
        score += 25;
        updateRequirement('reqLower', true);
    } else {
        updateRequirement('reqLower', false);
    }
    
    // Number check
    if(/[0-9]/.test(password)) {
        score += 25;
        updateRequirement('reqNumber', true);
    } else {
        updateRequirement('reqNumber', false);
    }
    
    // Update progress bar
    $('#strengthBar').css('width', score + '%');
    
    // Update color and label
    const bar = $('#strengthBar');
    const label = $('#strengthLabel');
    
    bar.removeClass('bg-danger bg-warning bg-info bg-success');
    
    if(score <= 25) {
        bar.addClass('bg-danger');
        label.text('อ่อนมาก').removeClass('text-success text-warning text-info').addClass('text-danger');
    } else if(score <= 50) {
        bar.addClass('bg-warning');
        label.text('อ่อน').removeClass('text-success text-danger text-info').addClass('text-warning');
    } else if(score <= 75) {
        bar.addClass('bg-info');
        label.text('ปานกลาง').removeClass('text-success text-danger text-warning').addClass('text-info');
    } else {
        bar.addClass('bg-success');
        label.text('แข็งแรง').removeClass('text-danger text-warning text-info').addClass('text-success');
    }
}

function updateRequirement(reqId, isValid) {
    const icon = $(`#${reqId}Icon`);
    const text = $(`#${reqId}`);
    
    if(isValid) {
        icon.removeClass('fa-times-circle text-muted').addClass('fa-check-circle text-success');
        text.removeClass('text-muted').addClass('text-success');
    } else {
        icon.removeClass('fa-check-circle text-success').addClass('fa-times-circle text-muted');
        text.removeClass('text-success').addClass('text-muted');
    }
}

function resetRequirements() {
    const reqs = ['reqLength', 'reqUpper', 'reqLower', 'reqNumber'];
    reqs.forEach(req => {
        updateRequirement(req, false);
    });
}

// ============ PASSWORD MATCH ============
function checkPasswordMatch() {
    const pass = $('#new_password').val();
    const confirm = $('#confirm_password').val();
    
    if(confirm.length === 0) {
        $('#passwordMatchMessage').html('');
        $('#confirm_password').removeClass('is-valid is-invalid');
    } else if(pass === confirm) {
        $('#passwordMatchMessage').html(
            '<small class="text-success"><i class="fas fa-check-circle"></i> รหัสผ่านตรงกัน</small>'
        );
        $('#confirm_password').removeClass('is-invalid').addClass('is-valid');
    } else {
        $('#passwordMatchMessage').html(
            '<small class="text-danger"><i class="fas fa-times-circle"></i> รหัสผ่านไม่ตรงกัน</small>'
        );
        $('#confirm_password').removeClass('is-valid').addClass('is-invalid');
    }
}

// ============ CHANGE PASSWORD ============
function changePassword() {
    const currentPassword = $('#current_password').val();
    const newPassword = $('#new_password').val();
    const confirmPassword = $('#confirm_password').val();
    
    // Reset validation
    $('.is-invalid').removeClass('is-invalid');
    
    // Validate current password
    if(!currentPassword) {
        $('#current_password').addClass('is-invalid');
        showError('กรุณากรอกรหัสผ่านปัจจุบัน');
        $('#current_password').focus();
        return;
    }
    
    // Validate new password
    if(!newPassword) {
        $('#new_password').addClass('is-invalid');
        showError('กรุณากรอกรหัสผ่านใหม่');
        $('#new_password').focus();
        return;
    }
    
    // Check minimum length
    if(newPassword.length < 6) {
        $('#new_password').addClass('is-invalid');
        showError('รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร');
        $('#new_password').focus();
        return;
    }
    
    // Check if same as current
    if(newPassword === currentPassword) {
        $('#new_password').addClass('is-invalid');
        showError('รหัสผ่านใหม่ต้องไม่เหมือนกับรหัสผ่านปัจจุบัน');
        $('#new_password').focus();
        return;
    }
    
    // Validate confirm password
    if(!confirmPassword) {
        $('#confirm_password').addClass('is-invalid');
        showError('กรุณายืนยันรหัสผ่านใหม่');
        $('#confirm_password').focus();
        return;
    }
    
    // Check if passwords match
    if(newPassword !== confirmPassword) {
        $('#confirm_password').addClass('is-invalid');
        showError('รหัสผ่านไม่ตรงกัน');
        $('#confirm_password').focus();
        return;
    }
    
    // Check password strength
    if(!isStrongPassword(newPassword)) {
        Swal.fire({
            title: 'รหัสผ่านไม่ปลอดภัย',
            html: `
                <p>รหัสผ่านควรประกอบด้วย:</p>
                <ul class="text-start">
                    <li>ตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว</li>
                    <li>ตัวพิมพ์เล็กอย่างน้อย 1 ตัว</li>
                    <li>ตัวเลขอย่างน้อย 1 ตัว</li>
                </ul>
                <p>คุณต้องการเปลี่ยนรหัสผ่านนี้อยู่ดีหรือไม่?</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f0ad4e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'เปลี่ยนอยู่ดี',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if(result.isConfirmed) {
                submitPasswordChange(currentPassword, newPassword);
            }
        });
        return;
    }
    
    // Confirm
    Swal.fire({
        title: 'ยืนยันการเปลี่ยนรหัสผ่าน?',
        text: 'คุณจะต้องใช้รหัสผ่านใหม่ในการเข้าสู่ระบบครั้งต่อไป',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f0ad4e',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-key"></i> เปลี่ยนรหัสผ่าน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            submitPasswordChange(currentPassword, newPassword);
        }
    });
}

function submitPasswordChange(currentPassword, newPassword) {
    // Show loading
    Swal.fire({
        title: 'กำลังเปลี่ยนรหัสผ่าน...',
        html: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../api/auth/change_password.php',
        type: 'POST',
        data: JSON.stringify({
            current_password: currentPassword,
            new_password: newPassword
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                // Clear form
                $('#changePasswordForm')[0].reset();
                $('#passwordStrengthContainer').hide();
                $('#passwordMatchMessage').html('');
                resetRequirements();
                
                Swal.fire({
                    icon: 'success',
                    title: 'เปลี่ยนรหัสผ่านสำเร็จ!',
                    text: 'รหัสผ่านของคุณถูกเปลี่ยนเรียบร้อยแล้ว',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: '<i class="fas fa-check"></i> ตกลง'
                }).then(() => {
                    // Redirect to profile or dashboard
                    // window.location.href = 'profile.php';
                });
                
                // Update last password change info
                loadPasswordInfo();
                
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เปลี่ยนรหัสผ่านไม่สำเร็จ',
                    text: response.message || 'รหัสผ่านปัจจุบันไม่ถูกต้อง',
                    confirmButtonText: 'ลองอีกครั้ง'
                }).then(() => {
                    $('#current_password').focus();
                });
            }
        },
        error: function(xhr) {
            let message = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้';
            if(xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด!',
                text: message,
                confirmButtonText: 'ตกลง'
            });
        }
    });
}

// ============ HELPERS ============
function isStrongPassword(password) {
    return password.length >= 6 && 
           /[A-Z]/.test(password) && 
           /[a-z]/.test(password) && 
           /[0-9]/.test(password);
}

function showError(message) {
    // Show as toast instead of blocking alert
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    
    Toast.fire({
        icon: 'error',
        title: message
    });
}

function loadPasswordInfo() {
    // This would typically come from an API
    // For now, show current time as placeholder
    $('#lastPasswordChange').text('ยังไม่มีข้อมูล');
}

// ============ KEYBOARD SHORTCUTS ============
$(document).keydown(function(e) {
    // Ctrl + Enter to submit
    if(e.ctrlKey && e.key === 'Enter') {
        e.preventDefault();
        changePassword();
    }
    
    // Escape to go back
    if(e.key === 'Escape') {
        window.location.href = 'profile.php';
    }
});
</script>

<?php include_once '../includes/footer.php'; ?>