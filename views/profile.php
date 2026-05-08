<?php
// views/profile.php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../includes/header.php';
include_once '../includes/sidebar.php';
include_once '../includes/auth_check.php';
include_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">👤 ข้อมูลส่วนตัว</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">ข้อมูลส่วนตัว</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Profile Info -->
            <div class="col-xl-4">
                <!-- Profile Card -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <!-- Avatar -->
                        <div class="mb-3">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px; font-size: 40px;">
                                <?php echo mb_strtoupper(mb_substr($user_name, 0, 1, 'UTF-8')); ?>
                            </div>
                        </div>
                        
                        <!-- User Info -->
                        <h4 class="mb-1"><?php echo htmlspecialchars($user_name); ?></h4>
                        <p class="text-muted mb-2"><?php echo htmlspecialchars($user_email); ?></p>
                        
                        <?php 
                        $role_labels = [
                            'admin' => '<span class="badge bg-danger">👑 Admin</span>',
                            'admin_page' => '<span class="badge bg-info">📄 Admin Page</span>',
                            'kpi' => '<span class="badge bg-warning text-dark">✅ KPI</span>',
                            'support' => '<span class="badge bg-success">🔧 Support</span>'
                        ];
                        echo isset($role_labels[$user_role]) ? $role_labels[$user_role] : $user_role;
                        ?>
                        
                        <hr>
                        
                        <!-- User Details -->
                        <div class="text-start">
                            <p><strong><i class="fas fa-envelope"></i> อีเมล:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                            <p><strong><i class="fas fa-user-tag"></i> Role:</strong> 
                                <?php 
                                $role_names = [
                                    'admin' => 'ผู้ดูแลระบบ (Admin)',
                                    'admin_page' => 'Admin Page',
                                    'kpi' => 'KPI Checker',
                                    'support' => 'Support'
                                ];
                                echo isset($role_names[$user_role]) ? $role_names[$user_role] : $user_role;
                                ?>
                            </p>
                            <p><strong><i class="fas fa-calendar-alt"></i> วันที่สร้าง:</strong> 
                                <span id="createdDate">กำลังโหลด...</span>
                            </p>
                            <p><strong><i class="fas fa-clock"></i> เข้าสู่ระบบล่าสุด:</strong> 
                                <?php echo date('d/m/Y H:i:s', $_SESSION['login_time'] ?? time()); ?>
                            </p>
                        </div>
                        <!-- ใน views/profile.php -->
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fab fa-line"></i> เชื่อมต่อ LINE</h6>
                            </div>
                            <div class="card-body">
                                <?php
                                    include_once __DIR__.'../../config/database.php';

                                    $database = new Database();
                                    $db = $database->getConnection();
                                    
                                    $stmt = $db->prepare("SELECT line_user_id, line_display_name, line_connected_at FROM users WHERE id = ?");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    $line_info = $stmt->fetch();
                                ?>

                                <?php if ($line_info && $line_info['line_user_id']): ?>
                                    <!-- ✅ เชื่อมต่อแล้ว -->
                                    <div class="alert alert-success">
                                        <i class="fab fa-line"></i> 
                                        <strong>เชื่อมต่อแล้ว!</strong><br>
                                        LINE: <?php echo htmlspecialchars($line_info['line_display_name']); ?><br>
                                        <small>เชื่อมต่อเมื่อ: <?php echo $line_info['line_connected_at']; ?></small>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm" onclick="disconnectLine()">
                                        ยกเลิกการเชื่อมต่อ
                                    </button>
                                <?php else: ?>
                                    <!-- ❌ ยังไม่เชื่อมต่อ -->
                                    <div class="alert alert-warning">
                                        🔴 ยังไม่ได้เชื่อมต่อ LINE<br>
                                        <small>เชื่อมต่อเพื่อรับการแจ้งเตือน</small>
                                    </div>
                                    <button class="btn btn-success" onclick="connectLine()">
                                        <i class="fab fa-line"></i> เชื่อมต่อ LINE
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- My Stats Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-chart-bar"></i> สถิติของฉัน</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <h3 id="myTotalCases" class="text-primary">0</h3>
                                <small class="text-muted">เคสทั้งหมด</small>
                            </div>
                            <div class="col-6 mb-3">
                                <h3 id="myApprovedCases" class="text-success">0</h3>
                                <small class="text-muted">อนุมัติแล้ว</small>
                            </div>
                            <div class="col-6">
                                <h3 id="myFollowingCases" class="text-warning">0</h3>
                                <small class="text-muted">กำลังติดตาม</small>
                            </div>
                            <div class="col-6">
                                <h3 id="myKpiChecks" class="text-info">0</h3>
                                <small class="text-muted">ตรวจ KPI</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Edit Forms -->
            <div class="col-xl-8">
                <!-- Edit Profile Form -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user-edit"></i> แก้ไขข้อมูลส่วนตัว</h5>
                    </div>
                    <div class="card-body">
                        <form id="profileForm">
                            <input type="hidden" id="profile_user_id" value="<?php echo $user_id; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="profile_name" 
                                           value="<?php echo htmlspecialchars($user_name); ?>" required>
                                </div>
                                <div class="invalid-feedback">กรุณากรอกชื่อ-นามสกุล</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="profile_email" 
                                           value="<?php echo htmlspecialchars($user_email); ?>" required>
                                </div>
                                <div class="invalid-feedback">กรุณากรอกอีเมลให้ถูกต้อง</div>
                                <div id="profileEmailFeedback" class="form-text"></div>
                            </div>
                            
                            <button type="button" class="btn btn-primary" onclick="updateProfile()">
                                <i class="fas fa-save"></i> บันทึกข้อมูล
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Change Password Form -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-lock"></i> เปลี่ยนรหัสผ่าน</h5>
                    </div>
                    <div class="card-body">
                        <form id="passwordForm">
                            <div class="mb-3">
                                <label class="form-label">รหัสผ่านปัจจุบัน <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="password" class="form-control" id="current_password" 
                                           placeholder="กรอกรหัสผ่านปัจจุบัน" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('current_password', 'toggleCurrentIcon')">
                                        <i class="fas fa-eye" id="toggleCurrentIcon"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="new_password" 
                                           placeholder="กรอกรหัสผ่านใหม่ (อย่างน้อย 6 ตัว)" required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('new_password', 'toggleNewIcon')">
                                        <i class="fas fa-eye" id="toggleNewIcon"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    <small>รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" 
                                           placeholder="ยืนยันรหัสผ่านใหม่" required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('confirm_password', 'toggleConfirmIcon')">
                                        <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                                    </button>
                                </div>
                                <div id="passwordStrength" class="form-text"></div>
                                <div id="passwordMatch" class="form-text"></div>
                            </div>
                            
                            <!-- Password Requirements -->
                            <div class="card bg-light mb-3">
                                <div class="card-body py-2">
                                    <small><strong>ข้อกำหนดรหัสผ่าน:</strong></small>
                                    <ul class="mb-0 small">
                                        <li id="reqLength" class="text-muted">อย่างน้อย 6 ตัวอักษร</li>
                                        <li id="reqUpper" class="text-muted">มีตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว</li>
                                        <li id="reqLower" class="text-muted">มีตัวพิมพ์เล็กอย่างน้อย 1 ตัว</li>
                                        <li id="reqNumber" class="text-muted">มีตัวเลขอย่างน้อย 1 ตัว</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-warning" onclick="changePassword()">
                                <i class="fas fa-key"></i> เปลี่ยนรหัสผ่าน
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-history"></i> กิจกรรมล่าสุด</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>วันที่/เวลา</th>
                                        <th>กิจกรรม</th>
                                    </tr>
                                </thead>
                                <tbody id="myActivityLog">
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">กำลังโหลด...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ============ PROFILE JAVASCRIPT ============

$(document).ready(function() {
    // Load user stats
    loadMyStats();
    
    // Load user details
    loadUserDetails();
    
    // Load activity log
    loadMyActivity();
    
    // Password strength checker
    $('#new_password').on('input', function() {
        checkPasswordStrength($(this).val());
    });
    
    // Password match check
    $('#confirm_password').on('input', function() {
        checkPasswordMatch();
    });
    
    // Email check
    $('#profile_email').on('blur', function() {
        const email = $(this).val().trim();
        const originalEmail = '<?php echo addslashes($user_email); ?>';
        if(email && email !== originalEmail) {
            checkEmailAvailability(email);
        }
    });
    
    // Remove invalid class on input
    $(document).on('input', '.is-invalid', function() {
        $(this).removeClass('is-invalid');
    });
});

// ============ Load Data ============
function loadMyStats() {
    $.ajax({
        url: '../api/users/get.php',
        type: 'GET',
        data: { id: <?php echo $user_id; ?> },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                const u = response.data;
                $('#myTotalCases').text(u.total_cases || 0);
                $('#createdDate').text(formatDateThai(u.created_at));
            }
        }
    });
    
    // Get approved cases count
    $.ajax({
        url: '../api/dashboard/stats.php',
        type: 'GET',
        data: { user_id: <?php echo $user_id; ?> },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                $('#myApprovedCases').text(response.data.approved_cases || 0);
                $('#myFollowingCases').text(response.data.following_cases || 0);
            }
        }
    });
    
    // Get KPI checks count
    $.ajax({
        url: '../api/kpi/stats.php',
        type: 'GET',
        data: { user_id: <?php echo $user_id; ?> },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#myKpiChecks').text(response.total || 0);
            }
        }
    });
}

function loadUserDetails() {
    $.ajax({
        url: '../api/users/get.php',
        type: 'GET',
        data: { id: <?php echo $user_id; ?> },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                const u = response.data;
                $('#createdDate').text(formatDateThai(u.created_at));
            }
        }
    });
}

function loadMyActivity() {
    $.ajax({
        url: '../api/dashboard/recent_activities.php',
        type: 'GET',
        data: { user_id: <?php echo $user_id; ?>, limit: 20 },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data && response.data.length > 0) {
                let html = '';
                response.data.forEach(a => {
                    html += `
                        <tr>
                            <td><small>${formatDateThai(a.created_at)}</small></td>
                            <td>${a.action || 'ไม่ระบุ'}</td>
                        </tr>`;
                });
                $('#myActivityLog').html(html);
            } else {
                $('#myActivityLog').html('<tr><td colspan="2" class="text-center text-muted">ไม่มีกิจกรรม</td></tr>');
            }
        }
    });
}

// ============ Update Profile ============
function updateProfile() {
    const name = $('#profile_name').val().trim();
    const email = $('#profile_email').val().trim();
    
    // Validate
    if(!name) {
        $('#profile_name').addClass('is-invalid');
        showToast('กรุณากรอกชื่อ-นามสกุล', 'warning');
        return;
    }
    
    if(!email) {
        $('#profile_email').addClass('is-invalid');
        showToast('กรุณากรอกอีเมล', 'warning');
        return;
    }
    
    if(!isValidEmail(email)) {
        $('#profile_email').addClass('is-invalid');
        showToast('รูปแบบอีเมลไม่ถูกต้อง', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'ยืนยันการแก้ไข?',
        html: `ชื่อ: <strong>${name}</strong><br>อีเมล: <strong>${email}</strong>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/users/update.php',
                type: 'POST',
                data: JSON.stringify({
                    id: <?php echo $user_id; ?>,
                    name: name,
                    email: email
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: 'อัพเดทข้อมูลเรียบร้อย กรุณารีเฟรชหน้า',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('ผิดพลาด!', response.message || 'ไม่สามารถอัพเดทได้', 'error');
                    }
                },
                error: function(xhr) {
                    let message = 'ไม่สามารถอัพเดทข้อมูลได้';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire('ผิดพลาด!', message, 'error');
                }
            });
        }
    });
}

// ============ Change Password ============
function changePassword() {
    const currentPassword = $('#current_password').val();
    const newPassword = $('#new_password').val();
    const confirmPassword = $('#confirm_password').val();
    
    // Validate
    if(!currentPassword) {
        $('#current_password').addClass('is-invalid');
        showToast('กรุณากรอกรหัสผ่านปัจจุบัน', 'warning');
        return;
    }
    
    if(!newPassword) {
        $('#new_password').addClass('is-invalid');
        showToast('กรุณากรอกรหัสผ่านใหม่', 'warning');
        return;
    }
    
    if(newPassword.length < 6) {
        $('#new_password').addClass('is-invalid');
        showToast('รหัสผ่านต้องอย่างน้อย 6 ตัวอักษร', 'warning');
        return;
    }
    
    if(newPassword !== confirmPassword) {
        $('#confirm_password').addClass('is-invalid');
        showToast('รหัสผ่านไม่ตรงกัน', 'warning');
        return;
    }
    
    // Check password strength
    if(!isStrongPassword(newPassword)) {
        Swal.fire({
            icon: 'warning',
            title: 'รหัสผ่านไม่ปลอดภัย',
            text: 'รหัสผ่านควรมีตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก และตัวเลข',
            showCancelButton: true,
            confirmButtonText: 'เปลี่ยนอยู่ดี',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if(result.isConfirmed) {
                doChangePassword(currentPassword, newPassword);
            }
        });
    } else {
        doChangePassword(currentPassword, newPassword);
    }
}

function doChangePassword(currentPassword, newPassword) {
    Swal.fire({
        title: 'กำลังเปลี่ยนรหัสผ่าน...',
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
                $('#current_password').val('');
                $('#new_password').val('');
                $('#confirm_password').val('');
                $('#passwordStrength').html('');
                $('#passwordMatch').html('');
                
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'เปลี่ยนรหัสผ่านเรียบร้อย',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด!',
                    text: response.message || 'รหัสผ่านปัจจุบันไม่ถูกต้อง'
                });
            }
        },
        error: function(xhr) {
            let message = 'ไม่สามารถเปลี่ยนรหัสผ่านได้';
            if(xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire('ผิดพลาด!', message, 'error');
        }
    });
}

// ============ Helper Functions ============
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

function checkPasswordStrength(password) {
    let strength = 0;
    let feedback = '';
    
    // Length check
    if(password.length >= 6) {
        strength++;
        $('#reqLength').removeClass('text-muted').addClass('text-success');
    } else {
        $('#reqLength').removeClass('text-success').addClass('text-muted');
    }
    
    // Uppercase check
    if(/[A-Z]/.test(password)) {
        strength++;
        $('#reqUpper').removeClass('text-muted').addClass('text-success');
    } else {
        $('#reqUpper').removeClass('text-success').addClass('text-muted');
    }
    
    // Lowercase check
    if(/[a-z]/.test(password)) {
        strength++;
        $('#reqLower').removeClass('text-muted').addClass('text-success');
    } else {
        $('#reqLower').removeClass('text-success').addClass('text-muted');
    }
    
    // Number check
    if(/[0-9]/.test(password)) {
        strength++;
        $('#reqNumber').removeClass('text-muted').addClass('text-success');
    } else {
        $('#reqNumber').removeClass('text-success').addClass('text-muted');
    }
    
    // Show strength bar
    if(password.length === 0) {
        $('#passwordStrength').html('');
    } else if(strength <= 1) {
        $('#passwordStrength').html(`
            <small class="text-danger">
                <i class="fas fa-times-circle"></i> รหัสผ่านอ่อนเกินไป
            </small>
            <div class="progress" style="height: 5px;">
                <div class="progress-bar bg-danger" style="width: 25%"></div>
            </div>
        `);
    } else if(strength <= 2) {
        $('#passwordStrength').html(`
            <small class="text-warning">
                <i class="fas fa-exclamation-triangle"></i> รหัสผ่านปานกลาง
            </small>
            <div class="progress" style="height: 5px;">
                <div class="progress-bar bg-warning" style="width: 50%"></div>
            </div>
        `);
    } else if(strength <= 3) {
        $('#passwordStrength').html(`
            <small class="text-info">
                <i class="fas fa-check"></i> รหัสผ่านดี
            </small>
            <div class="progress" style="height: 5px;">
                <div class="progress-bar bg-info" style="width: 75%"></div>
            </div>
        `);
    } else {
        $('#passwordStrength').html(`
            <small class="text-success">
                <i class="fas fa-check-circle"></i> รหัสผ่านแข็งแรง
            </small>
            <div class="progress" style="height: 5px;">
                <div class="progress-bar bg-success" style="width: 100%"></div>
            </div>
        `);
    }
}

function isStrongPassword(password) {
    return password.length >= 6 && 
           /[A-Z]/.test(password) && 
           /[a-z]/.test(password) && 
           /[0-9]/.test(password);
}

function checkPasswordMatch() {
    const pass = $('#new_password').val();
    const confirm = $('#confirm_password').val();
    
    if(confirm.length === 0) {
        $('#passwordMatch').html('');
    } else if(pass === confirm) {
        $('#passwordMatch').html('<small class="text-success"><i class="fas fa-check-circle"></i> รหัสผ่านตรงกัน</small>');
    } else {
        $('#passwordMatch').html('<small class="text-danger"><i class="fas fa-times-circle"></i> รหัสผ่านไม่ตรงกัน</small>');
    }
}

function checkEmailAvailability(email) {
    $.ajax({
        url: '../api/users/check_email.php',
        type: 'GET',
        data: { email: email, exclude_id: <?php echo $user_id; ?> },
        dataType: 'json',
        success: function(response) {
            if(response.exists) {
                $('#profileEmailFeedback').html('<small class="text-danger">อีเมลนี้มีในระบบแล้ว</small>');
            } else {
                $('#profileEmailFeedback').html('<small class="text-success">อีเมลนี้ใช้ได้</small>');
            }
        }
    });
}

function showToast(message, icon = 'info') {
    Swal.fire({
        icon: icon,
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function formatDateThai(dateString) {
    if(!dateString) return '-';
    const date = new Date(dateString);
    if(isNaN(date.getTime())) return '-';
    
    return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>

<?php include_once '../includes/footer.php'; ?>