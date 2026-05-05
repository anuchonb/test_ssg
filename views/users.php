<?php
// views/users.php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../includes/header.php';
include_once '../includes/sidebar.php';
include_once '../includes/auth_check.php';
include_once '../includes/functions.php';

// เฉพาะ admin เท่านั้น
if(!checkRole('admin')) {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">👥 จัดการผู้ใช้ระบบ</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="settings.php">ตั้งค่า</a></li>
                        <li class="breadcrumb-item active">จัดการผู้ใช้</li>
                    </ol>
                </nav>
            </div>
            <button class="btn btn-primary" onclick="showUserForm()">
                <i class="fas fa-user-plus"></i> เพิ่มผู้ใช้ใหม่
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100 cursor-pointer" onclick="filterByRole('')">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ผู้ใช้ทั้งหมด</div>
                                <div class="h4 mb-0 font-weight-bold" id="statTotalUsers">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-info shadow h-100 cursor-pointer" onclick="filterByRole('admin_page')">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Admin Page</div>
                                <div class="h4 mb-0 font-weight-bold" id="statAdminPage">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-warning shadow h-100 cursor-pointer" onclick="filterByRole('kpi')">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">KPI</div>
                                <div class="h4 mb-0 font-weight-bold" id="statKpi">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-success shadow h-100 cursor-pointer" onclick="filterByRole('support')">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Support</div>
                                <div class="h4 mb-0 font-weight-bold" id="statSupport">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-headset fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">ค้นหา</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchUser" 
                                   placeholder="ค้นหาชื่อ, อีเมล..." onkeyup="debounceSearch()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" id="filterRole" onchange="loadUsers()">
                            <option value="">ทั้งหมด</option>
                            <option value="admin">👑 Admin</option>
                            <option value="admin_page">📄 Admin Page</option>
                            <option value="kpi">✅ KPI</option>
                            <option value="support">🔧 Support</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-secondary w-100" onclick="resetFilters()">
                            <i class="fas fa-redo"></i> รีเซ็ต
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">รายชื่อผู้ใช้</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="loadUsers()">
                    <i class="fas fa-sync"></i> รีเฟรช
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>อีเมล</th>
                                <th>Role</th>
                                <th>จำนวนเคส</th>
                                <th>วันที่สร้าง</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">📜 ประวัติการใช้งานล่าสุด</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>เวลา</th>
                                <th>ผู้ใช้</th>
                                <th>การกระทำ</th>
                            </tr>
                        </thead>
                        <tbody id="activityLogBody">
                            <tr>
                                <td colspan="3" class="text-center">กำลังโหลด...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Form Modal -->
<div class="modal fade" id="userModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="userModalTitle">
                    <i class="fas fa-user-plus"></i> เพิ่มผู้ใช้ใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="user_id">
                    
                    <!-- ข้อมูลส่วนตัว -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user"></i> ข้อมูลส่วนตัว</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="user_name" required
                                           placeholder="กรอกชื่อ-นามสกุล">
                                    <div class="invalid-feedback">กรุณากรอกชื่อ-นามสกุล</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select" id="user_role" required>
                                        <option value="">เลือก Role</option>
                                        <option value="admin_page">📄 Admin Page - กรอกข้อมูลลูกค้า</option>
                                        <option value="kpi">✅ KPI - ตรวจสอบคุณภาพ</option>
                                        <option value="support">🔧 Support - เอกสาร/ธนาคาร</option>
                                        <option value="admin">👑 Admin - ดูแลระบบทั้งหมด</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ข้อมูลการเข้าใช้งาน -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-lock"></i> ข้อมูลการเข้าใช้งาน</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="user_email" required
                                               placeholder="email@company.com">
                                    </div>
                                    <div class="invalid-feedback">กรุณากรอกอีเมลให้ถูกต้อง</div>
                                    <div id="emailFeedback" class="form-text"></div>
                                </div>
                            </div>
                            
                            <div class="row" id="passwordFields">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        รหัสผ่าน <span class="text-danger" id="passwordRequired">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="password" class="form-control" id="user_password" 
                                               placeholder="กรอกรหัสผ่าน" minlength="4">
                                        <button class="btn btn-outline-secondary" type="button" 
                                                onclick="togglePassword()">
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">อย่างน้อย 4 ตัวอักษร</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ยืนยันรหัสผ่าน <span class="text-danger" id="confirmRequired">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="password" class="form-control" id="user_confirm_password" 
                                               placeholder="ยืนยันรหัสผ่าน" minlength="4">
                                    </div>
                                    <div id="passwordMatch" class="form-text"></div>
                                </div>
                            </div>
                            
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> 
                                <span id="passwordHelpText">
                                    กรณีเพิ่มผู้ใช้ใหม่ ต้องกรอกรหัสผ่าน<br>
                                    กรณีแก้ไข เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- สถิติ (แสดงเฉพาะตอนแก้ไข) -->
                    <div class="card mb-3" id="userStatsCard" style="display: none;">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-chart-bar"></i> สถิติการทำงาน</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <h3 id="statUserCases">0</h3>
                                    <small class="text-muted">จำนวนเคส</small>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h3 id="statUserApproved">0</h3>
                                    <small class="text-muted">อนุมัติ</small>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h3 id="statUserKpiPass">0</h3>
                                    <small class="text-muted">KPI ผ่าน</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> ยกเลิก
                </button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">
                    <i class="fas fa-save"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">ยืนยันการลบผู้ใช้</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                </div>
                <div id="deleteUserInfo" class="mb-3"></div>
                <p class="text-danger"><strong>คำเตือน:</strong> 
                    เคสทั้งหมดของผู้ใช้นี้จะถูกโอนไปให้คุณ และไม่สามารถกู้คืนได้</p>
                <input type="hidden" id="deleteUserId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" onclick="deleteUser()">
                    <i class="fas fa-trash"></i> ลบผู้ใช้
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Force Logout Modal -->
<div class="modal fade" id="forceLogoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">บังคับออกจากระบบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการบังคับให้ผู้ใช้นี้ออกจากระบบใช่หรือไม่?</p>
                <input type="hidden" id="forceLogoutUserId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning" onclick="forceLogout()">
                    <i class="fas fa-sign-out-alt"></i> บังคับออกจากระบบ
                </button>
            </div>
        </div>
    </div>
</div>

    
<script>
const CURRENT_USER_ID = <?php echo $_SESSION['user_id']; ?>;
// ============ USERS JAVASCRIPT (แก้ไขใหม่) ============

// รับค่าจาก PHP (ต้องมี script tag นี้ใน users.php ก่อนโหลด js)
let currentUserId = null;
let searchTimeout = null;

$(document).ready(function() {
    // console.log('Users page loaded');
    // console.log('Current User ID:', CURRENT_USER_ID);
    // console.log('jQuery version:', $.fn.jquery);
    
    // ตรวจสอบว่า jQuery ทำงาน
    if(typeof $ === 'undefined') {
        alert('jQuery not loaded!');
        return;
    }
    
    // โหลดข้อมูล
    loadUsers();
    loadUserStats();
    loadActivityLog();
    
    // Email check
    $('#user_email').on('blur', function() {
        const email = $(this).val().trim();
        if(email && !currentUserId) {
            checkEmailAvailability(email);
        }
    });
    
    // Password match check
    $('#user_confirm_password').on('input', function() {
        checkPasswordMatch();
    });
});

// ============ LOAD USERS ============
function loadUsers() {
    //console.log('loadUsers() called');
    
    const search = $('#searchUser').val() || '';
    const role = $('#filterRole').val() || '';
    
    // Show loading
    $('#usersTableBody').html(
        '<tr>' +
        '<td colspan="7" class="text-center py-5">' +
        '<div class="spinner-border text-primary" role="status"></div>' +
        '<p class="mt-2">กำลังโหลดข้อมูล...</p>' +
        '</td>' +
        '</tr>'
    );
    
    // Call API
    $.ajax({
        url: '../api/users/list.php',
        type: 'GET',
        data: {
            search: search,
            role: role
        },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            // console.log('Users API Response:', response);
            
            if(response.success && response.data) {
                renderUsersTable(response.data);
            } else {
                showTableEmpty('ไม่พบข้อมูลผู้ใช้');
            }
        },
        error: function(xhr, status, error) {
            //console.error('Load users error:', status, error);
            //console.error('Response text:', xhr.responseText);
            
            $('#usersTableBody').html(
                '<tr>' +
                '<td colspan="7" class="text-center py-5 text-danger">' +
                '<i class="fas fa-exclamation-triangle fa-2x mb-3"></i>' +
                '<p>ไม่สามารถโหลดข้อมูลได้</p>' +
                '<p class="text-muted small">Error: ' + status + '</p>' +
                '<button class="btn btn-outline-primary btn-sm" onclick="loadUsers()">' +
                '<i class="fas fa-redo"></i> ลองใหม่</button>' +
                '</td>' +
                '</tr>'
            );
        }
    });
}

function renderUsersTable(users) {
    console.log('Rendering users:', users.length);
    
    if(!users || users.length === 0) {
        showTableEmpty('ไม่พบข้อมูลผู้ใช้');
        return;
    }
    
    let html = '';
    
    users.forEach(function(u) {
        const roleLabel = getRoleLabel(u.role);
        const roleColor = getRoleColor(u.role);
        const isCurrentUser = (u.id == CURRENT_USER_ID);
        
        html += '<tr ' + (isCurrentUser ? 'class="table-active"' : '') + '>' +
            '<td>' + u.id + (isCurrentUser ? ' <br><small class="text-muted">(คุณ)</small>' : '') + '</td>' +
            '<td>' +
                '<div class="d-flex align-items-center">' +
                    '<div class="bg-' + roleColor + ' text-white rounded-circle me-2 d-flex align-items-center justify-content-center" ' +
                         'style="width: 35px; height: 35px; font-size: 14px;">' +
                        (u.name ? u.name.charAt(0).toUpperCase() : '?') +
                    '</div>' +
                    '<div>' +
                        '<strong>' + (u.name || 'ไม่ระบุ') + '</strong>' +
                        '<br><small class="text-muted">' + (u.email || '') + '</small>' +
                    '</div>' +
                '</div>' +
            '</td>' +
            '<td>' + (u.email || '-') + '</td>' +
            '<td><span class="badge bg-' + roleColor + '">' + roleLabel + '</span></td>' +
            '<td><span class="badge bg-primary">' + (u.total_cases || 0) + ' เคส</span></td>' +
            '<td><small>' + formatDateThai(u.created_at) + '</small></td>' +
            '<td>' +
                '<div class="btn-group btn-group-sm">' +
                    '<button class="btn btn-warning" onclick="editUser(' + u.id + ')" title="แก้ไข">' +
                        '<i class="fas fa-edit"></i>' +
                    '</button>';
        
        if(!isCurrentUser) {
            html += '<button class="btn btn-danger" onclick="confirmDeleteUser(' + u.id + ', \'' + escapeHtml(u.name) + '\', ' + (u.total_cases || 0) + ')" title="ลบ">' +
                        '<i class="fas fa-trash"></i>' +
                    '</button>';
        }
        
        html += '</div>' +
            '</td>' +
        '</tr>';
    });
    
    $('#usersTableBody').html(html);
}

function showTableEmpty(message) {
    $('#usersTableBody').html(
        '<tr>' +
        '<td colspan="7" class="text-center py-5 text-muted">' +
        '<i class="fas fa-user-slash fa-3x mb-3"></i>' +
        '<p>' + message + '</p>' +
        '<button class="btn btn-primary btn-sm" onclick="showUserForm()">' +
        '<i class="fas fa-plus"></i> เพิ่มผู้ใช้ใหม่</button>' +
        '</td>' +
        '</tr>'
    );
}

// ============ LOAD USER STATS ============
function loadUserStats() {
    console.log('loadUserStats() called');
    
    $.ajax({
        url: '../api/users/list.php',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Stats response:', response);
            
            if(response.success && response.data) {
                const users = response.data;
                let total = users.length;
                let adminPage = 0, kpi = 0, support = 0;
                
                users.forEach(function(u) {
                    if(u.role === 'admin_page') adminPage++;
                    else if(u.role === 'kpi') kpi++;
                    else if(u.role === 'support') support++;
                });
                
                $('#statTotalUsers').text(total);
                $('#statAdminPage').text(adminPage);
                $('#statKpi').text(kpi);
                $('#statSupport').text(support);
            }
        },
        error: function(xhr, status, error) {
            console.error('Stats error:', status, error);
        }
    });
}

// ============ LOAD ACTIVITY LOG ============
function loadActivityLog() {
    console.log('loadActivityLog() called');
    
    $.ajax({
        url: '../api/dashboard/recent_activities.php',
        type: 'GET',
        data: { limit: 20 },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            if(response.success && response.data && response.data.length > 0) {
                let html = '';
                response.data.forEach(function(a) {
                    if(a.action && (a.action.includes('Login') || a.action.includes('Logout') || a.action.includes('User'))) {
                        html += '<tr>' +
                            '<td><small>' + formatDateThai(a.created_at) + '</small></td>' +
                            '<td>' + (a.user_name || 'System') + '</td>' +
                            '<td>' + a.action + '</td>' +
                        '</tr>';
                    }
                });
                
                if(html === '') {
                    html = '<tr><td colspan="3" class="text-center text-muted">ไม่มีประวัติ</td></tr>';
                }
                
                $('#activityLogBody').html(html);
            } else {
                $('#activityLogBody').html('<tr><td colspan="3" class="text-center text-muted">ไม่มีประวัติ</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Activity log error:', status, error);
            $('#activityLogBody').html('<tr><td colspan="3" class="text-center text-muted">โหลดไม่สำเร็จ</td></tr>');
        }
    });
}

// ============ CRUD FUNCTIONS ============
function showUserForm() {
    console.log('showUserForm() called');
    
    currentUserId = null;
    $('#user_id').val('');
    $('#user_name').val('');
    $('#user_email').val('');
    $('#user_role').val('');
    $('#user_password').val('');
    $('#user_confirm_password').val('');
    $('#emailFeedback').html('');
    
    // Show password fields for new user
    $('#passwordFields').show();
    $('#user_password').prop('required', true);
    $('#user_confirm_password').prop('required', true);
    
    $('#userStatsCard').hide();
    $('#userModalTitle').html('<i class="fas fa-user-plus"></i> เพิ่มผู้ใช้ใหม่');
    $('#userModal').modal('show');
}

function editUser(id) {
    console.log('editUser() called with id:', id);
    
    if(id == CURRENT_USER_ID) {
        window.location.href = 'profile.php';
        return;
    }
    
    $.ajax({
        url: '../api/users/get.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            
            if(response.success && response.data) {
                const u = response.data;
                currentUserId = u.id;
                
                $('#user_id').val(u.id);
                $('#user_name').val(u.name);
                $('#user_email').val(u.email);
                $('#user_role').val(u.role);
                
                // Hide required for password
                $('#user_password').prop('required', false).val('');
                $('#user_confirm_password').prop('required', false).val('');
                
                $('#userStatsCard').show();
                $('#statUserCases').text(u.total_cases || 0);
                
                $('#userModalTitle').html('<i class="fas fa-edit"></i> แก้ไขผู้ใช้');
                $('#userModal').modal('show');
            } else {
                alert('ไม่พบข้อมูลผู้ใช้');
            }
        },
        error: function(xhr, status, error) {
            //console.error('Edit user error:', status, error);
            alert('ไม่สามารถโหลดข้อมูลผู้ใช้ได้');
        }
    });
}

function saveUser() {
    //console.log('saveUser() called');
    
    const name = $('#user_name').val().trim();
    const email = $('#user_email').val().trim();
    const role = $('#user_role').val();
    const password = $('#user_password').val();
    const confirmPassword = $('#user_confirm_password').val();
    const isEdit = currentUserId ? true : false;
    
    // Validate
    if(!name) {
        alert('กรุณากรอกชื่อ-นามสกุล');
        return;
    }
    if(!email) {
        alert('กรุณากรอกอีเมล');
        return;
    }
    if(!role) {
        alert('กรุณาเลือก Role');
        return;
    }
    if(!isEdit && !password) {
        alert('กรุณากรอกรหัสผ่าน');
        return;
    }
    if(password && password !== confirmPassword) {
        alert('รหัสผ่านไม่ตรงกัน');
        return;
    }
    
    // Confirm
    if(!confirm(isEdit ? 'ยืนยันการแก้ไข?' : 'ยืนยันการเพิ่มผู้ใช้ใหม่?')) {
        return;
    }
    
    const postData = {
        name: name,
        email: email,
        role: role
    };
    
    if(password) {
        postData.password = password;
    }
    
    if(isEdit) {
        postData.id = currentUserId;
    }
    
    const url = isEdit ? '../api/users/update.php' : '../api/users/create.php';
    
    //console.log('Saving to:', url, postData);
    
    $.ajax({
        url: url,
        type: 'POST',
        data: JSON.stringify(postData),
        contentType: 'application/json',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            //console.log('Save response:', response);
            
            if(response.success) {
                $('#userModal').modal('hide');
                alert('บันทึกสำเร็จ!');
                loadUsers();
                loadUserStats();
            } else {
                alert('ผิดพลาด: ' + (response.message || 'ไม่สามารถบันทึกได้'));
            }
        },
        error: function(xhr, status, error) {
            //console.error('Save error:', status, error);
            alert('ไม่สามารถบันทึกได้: ' + status);
        }
    });
}

function confirmDeleteUser(id, name, caseCount) {
    if(id == CURRENT_USER_ID) {
        alert('ไม่สามารถลบบัญชีของตัวเองได้');
        return;
    }
    
    const msg = 'คุณต้องการลบ ' + name + ' ใช่หรือไม่?\n\nจำนวนเคส: ' + caseCount + ' เคส (จะถูกโอนไปให้คุณ)\n\nการลบไม่สามารถกู้คืนได้!';
    
    if(confirm(msg)) {
        $.ajax({
            url: '../api/users/delete.php',
            type: 'POST',
            data: JSON.stringify({ id: id }),
            contentType: 'application/json',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if(response.success) {
                    alert('ลบผู้ใช้สำเร็จ!');
                    loadUsers();
                    loadUserStats();
                } else {
                    alert('ผิดพลาด: ' + (response.message || 'ไม่สามารถลบได้'));
                }
            },
            error: function() {
                alert('ไม่สามารถลบผู้ใช้ได้');
            }
        });
    }
}

// ============ HELPER FUNCTIONS ============
function getRoleLabel(role) {
    const labels = {
        'admin': '👑 Admin',
        'admin_page': '📄 Admin Page',
        'kpi': '✅ KPI',
        'support': '🔧 Support'
    };
    return labels[role] || role;
}

function getRoleColor(role) {
    const colors = {
        'admin': 'danger',
        'admin_page': 'info',
        'kpi': 'warning',
        'support': 'success'
    };
    return colors[role] || 'secondary';
}

function formatDateThai(dateString) {
    if(!dateString) return '-';
    try {
        const date = new Date(dateString);
        if(isNaN(date.getTime())) return dateString;
        return date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch(e) {
        return dateString;
    }
}

function escapeHtml(text) {
    if(!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function checkEmailAvailability(email) {
    // Implementation
}

function checkPasswordMatch() {
    const pass = $('#user_password').val();
    const confirm = $('#user_confirm_password').val();
    if(confirm && pass !== confirm) {
        $('#passwordMatch').html('<small class="text-danger">รหัสผ่านไม่ตรงกัน</small>');
    } else if(confirm && pass === confirm) {
        $('#passwordMatch').html('<small class="text-success">รหัสผ่านตรงกัน</small>');
    } else {
        $('#passwordMatch').html('');
    }
}

function filterByRole(role) {
    $('#filterRole').val(role);
    loadUsers();
}

function resetFilters() {
    $('#searchUser').val('');
    $('#filterRole').val('');
    loadUsers();
}

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        loadUsers();
    }, 500);
}
</script>

<?php include_once '../includes/footer.php'; ?>