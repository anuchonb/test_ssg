<?php
// views/logs.php
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
                <h2 class="mb-1">📜 บันทึกระบบ (Activity Logs)</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="settings.php">ตั้งค่า</a></li>
                        <li class="breadcrumb-item active">บันทึกระบบ</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button class="btn btn-outline-danger" onclick="clearLogs()">
                    <i class="fas fa-trash"></i> ล้างบันทึกเก่า
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">บันทึกทั้งหมด</div>
                                <div class="h4 mb-0 font-weight-bold" id="statTotalLogs">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">วันนี้</div>
                                <div class="h4 mb-0 font-weight-bold" id="statTodayLogs">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">ผู้ใช้ที่ใช้งาน</div>
                                <div class="h4 mb-0 font-weight-bold" id="statActiveUsers">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">ข้อผิดพลาด</div>
                                <div class="h4 mb-0 font-weight-bold" id="statErrors">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                    <div class="col-md-3">
                        <label class="form-label">ค้นหา</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchLog" 
                                   placeholder="ค้นหากิจกรรม, ผู้ใช้..." onkeyup="debounceSearch()">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">ประเภท</label>
                        <select class="form-select" id="filterType" onchange="loadLogs()">
                            <option value="">ทั้งหมด</option>
                            <option value="login">เข้าสู่ระบบ/ออกจากระบบ</option>
                            <option value="case">เคส</option>
                            <option value="customer">ลูกค้า</option>
                            <option value="kpi">KPI</option>
                            <option value="support">Support</option>
                            <option value="error">ข้อผิดพลาด</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">ผู้ใช้</label>
                        <select class="form-select" id="filterUser" onchange="loadLogs()">
                            <option value="">ทั้งหมด</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">วันที่เริ่ม</label>
                        <input type="date" class="form-control" id="filterDateFrom" onchange="loadLogs()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">วันที่สิ้นสุด</label>
                        <input type="date" class="form-control" id="filterDateTo" onchange="loadLogs()">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-secondary w-100" onclick="resetFilters()">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">รายการบันทึก</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="loadLogs()">
                        <i class="fas fa-sync"></i> รีเฟรช
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="exportLogs()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="width: 160px;">วันที่/เวลา</th>
                                <th style="width: 120px;">ผู้ใช้</th>
                                <th>กิจกรรม</th>
                                <th style="width: 100px;">Case ID</th>
                                <th style="width: 80px;">IP Address</th>
                            </tr>
                        </thead>
                        <tbody id="logsTableBody">
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <select class="form-select form-select-sm" style="width: auto;" id="perPage" onchange="loadLogs()">
                            <option value="25">25 รายการ</option>
                            <option value="50">50 รายการ</option>
                            <option value="100">100 รายการ</option>
                            <option value="200">200 รายการ</option>
                        </select>
                    </div>
                    <div>
                        <small class="text-muted">
                            แสดง <span id="showingCount">0</span> จาก <span id="totalCount">0</span> รายการ
                        </small>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ============ LOGS JAVASCRIPT ============

let currentPage = 1;
let searchTimeout = null;

$(document).ready(function() {
    // Set default date to today
    $('#filterDateFrom').val(getTodayDate());
    $('#filterDateTo').val(getTodayDate());
    
    // Load initial data
    loadLogStats();
    loadUsers();
    loadLogs();
    
    // Auto refresh every 60 seconds
    setInterval(loadLogs, 60000);
});

// ============ LOAD DATA ============
function loadLogStats() {
    $.ajax({
        url: '../api/logs/stats.php',
        type: 'GET',
        data: {
            date_from: $('#filterDateFrom').val(),
            date_to: $('#filterDateTo').val()
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#statTotalLogs').text(response.total_logs || 0);
                $('#statTodayLogs').text(response.today_logs || 0);
                $('#statActiveUsers').text(response.active_users || 0);
                $('#statErrors').text(response.errors || 0);
            }
        }
    });
}

function loadUsers() {
    $.ajax({
        url: '../api/users/list.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                let options = '<option value="">ทั้งหมด</option>';
                response.data.forEach(user => {
                    options += `<option value="${user.id}">${user.name}</option>`;
                });
                $('#filterUser').html(options);
            }
        }
    });
}

function loadLogs(page = 1) {
    currentPage = page;
    
    const search = $('#searchLog').val();
    const type = $('#filterType').val();
    const user = $('#filterUser').val();
    const dateFrom = $('#filterDateFrom').val();
    const dateTo = $('#filterDateTo').val();
    const perPage = $('#perPage').val() || 25;
    
    // Show loading
    $('#logsTableBody').html(`
        <tr>
            <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">กำลังโหลดข้อมูล...</p>
            </td>
        </tr>
    `);
    
    $.ajax({
        url: '../api/logs/list.php',
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            type: type,
            user_id: user,
            date_from: dateFrom,
            date_to: dateTo
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                renderLogsTable(response.data);
                renderPagination(response.pagination);
                $('#showingCount').text(response.data ? response.data.length : 0);
                $('#totalCount').text(response.pagination ? response.pagination.total : 0);
            } else {
                showTableEmpty();
            }
        },
        error: function() {
            showTableError();
        }
    });
}

function renderLogsTable(logs) {
    if(!logs || logs.length === 0) {
        showTableEmpty();
        return;
    }
    
    let html = '';
    logs.forEach(log => {
        const typeIcon = getLogTypeIcon(log.action);
        const typeColor = getLogTypeColor(log.action);
        
        html += `
            <tr>
                <td>
                    <span class="badge bg-${typeColor} rounded-circle p-1" style="width: 25px; height: 25px;">
                        <i class="fas fa-${typeIcon} fa-xs"></i>
                    </span>
                </td>
                <td>
                    <small>${formatDateTime(log.created_at)}</small>
                </td>
                <td>
                    ${log.user_name ? `
                        <span class="fw-semibold">${escapeHtml(log.user_name)}</span>
                    ` : '<span class="text-muted">System</span>'}
                    ${log.user_role ? `<br><small class="badge bg-secondary">${getRoleLabel(log.user_role)}</small>` : ''}
                </td>
                <td>${log.action || 'ไม่ระบุ'}</td>
                <td>
                    ${log.case_id ? `<a href="case_detail.php?id=${log.case_id}" class="badge bg-primary text-decoration-none">#${log.case_id}</a>` : '<span class="text-muted">-</span>'}
                </td>
                <td>
                    <small class="text-muted">${log.ip_address || '-'}</small>
                </td>
            </tr>
        `;
    });
    
    $('#logsTableBody').html(html);
}

function showTableEmpty() {
    $('#logsTableBody').html(`
        <tr>
            <td colspan="6" class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <p class="text-muted">ไม่พบบันทึกกิจกรรม</p>
            </td>
        </tr>
    `);
}

function showTableError() {
    $('#logsTableBody').html(`
        <tr>
            <td colspan="6" class="text-center py-5 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <p>ไม่สามารถโหลดข้อมูลได้</p>
                <button class="btn btn-sm btn-outline-primary" onclick="loadLogs()">
                    <i class="fas fa-redo"></i> ลองใหม่
                </button>
            </td>
        </tr>
    `);
}

function renderPagination(pagination) {
    if(!pagination || pagination.total_pages <= 1) {
        $('#pagination').html('');
        return;
    }
    
    let html = '';
    
    // Previous
    html += `<li class="page-item ${!pagination.has_prev ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadLogs(${pagination.current_page - 1}); return false;">
            <i class="fas fa-chevron-left"></i>
        </a>
    </li>`;
    
    // Page numbers
    for(let i = 1; i <= pagination.total_pages; i++) {
        if(i === 1 || i === pagination.total_pages || 
           (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadLogs(${i}); return false;">${i}</a>
            </li>`;
        } else if(i === pagination.current_page - 3 || i === pagination.current_page + 3) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Next
    html += `<li class="page-item ${!pagination.has_next ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadLogs(${pagination.current_page + 1}); return false;">
            <i class="fas fa-chevron-right"></i>
        </a>
    </li>`;
    
    $('#pagination').html(html);
}

// ============ FILTERS ============
function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadLogs(1);
        loadLogStats();
    }, 500);
}

function resetFilters() {
    $('#searchLog').val('');
    $('#filterType').val('');
    $('#filterUser').val('');
    $('#filterDateFrom').val(getTodayDate());
    $('#filterDateTo').val(getTodayDate());
    currentPage = 1;
    loadLogs();
    loadLogStats();
}

// ============ ACTIONS ============
function clearLogs() {
    Swal.fire({
        title: 'ล้างบันทึกเก่า?',
        text: 'คุณต้องการลบบันทึกที่เก่ากว่า 30 วันใช่หรือไม่? การลบไม่สามารถกู้คืนได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash"></i> ล้างบันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            Swal.fire({
                title: 'กำลังล้างบันทึก...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '../api/logs/clear.php',
                type: 'POST',
                data: JSON.stringify({ days: 30 }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: response.message || 'ล้างบันทึกเรียบร้อย',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadLogs();
                        loadLogStats();
                    } else {
                        Swal.fire('ผิดพลาด!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('ผิดพลาด!', 'ไม่สามารถล้างบันทึกได้', 'error');
                }
            });
        }
    });
}

function exportLogs() {
    const search = $('#searchLog').val();
    const type = $('#filterType').val();
    const user = $('#filterUser').val();
    const dateFrom = $('#filterDateFrom').val();
    const dateTo = $('#filterDateTo').val();
    
    let url = '../api/logs/export.php?';
    if(search) url += `search=${encodeURIComponent(search)}&`;
    if(type) url += `type=${encodeURIComponent(type)}&`;
    if(user) url += `user_id=${user}&`;
    if(dateFrom) url += `date_from=${dateFrom}&`;
    if(dateTo) url += `date_to=${dateTo}&`;
    
    window.open(url, '_blank');
}

// ============ HELPERS ============
function getLogTypeIcon(action) {
    if(!action) return 'circle';
    if(action.includes('Login') || action.includes('เข้าสู่ระบบ')) return 'sign-in-alt';
    if(action.includes('Logout') || action.includes('ออกจากระบบ')) return 'sign-out-alt';
    if(action.includes('Case') || action.includes('เคส')) return 'folder';
    if(action.includes('Follow') || action.includes('ติดตาม')) return 'phone';
    if(action.includes('KPI')) return 'check-circle';
    if(action.includes('Customer') || action.includes('ลูกค้า')) return 'user';
    if(action.includes('Bank') || action.includes('ธนาคาร')) return 'university';
    if(action.includes('Document') || action.includes('เอกสาร')) return 'file-alt';
    if(action.includes('Delete') || action.includes('ลบ')) return 'trash';
    if(action.includes('Update') || action.includes('แก้ไข')) return 'edit';
    if(action.includes('Create') || action.includes('เพิ่ม') || action.includes('สร้าง')) return 'plus-circle';
    return 'circle';
}

function getLogTypeColor(action) {
    if(!action) return 'secondary';
    if(action.includes('Login') || action.includes('เข้าสู่ระบบ')) return 'success';
    if(action.includes('Logout') || action.includes('ออกจากระบบ')) return 'secondary';
    if(action.includes('Delete') || action.includes('ลบ')) return 'danger';
    if(action.includes('Error') || action.includes('ผิดพลาด')) return 'danger';
    if(action.includes('KPI') && action.includes('pass')) return 'success';
    if(action.includes('KPI') && action.includes('fail')) return 'danger';
    if(action.includes('Create') || action.includes('เพิ่ม') || action.includes('สร้าง')) return 'primary';
    if(action.includes('Update') || action.includes('แก้ไข')) return 'warning';
    return 'info';
}

function getRoleLabel(role) {
    const labels = {
        'admin': 'Admin',
        'admin_page': 'Admin Page',
        'kpi': 'KPI',
        'support': 'Support'
    };
    return labels[role] || role;
}

function getTodayDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatDateTime(dateString) {
    if(!dateString) return '-';
    const date = new Date(dateString);
    if(isNaN(date.getTime())) return '-';
    
    return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

function escapeHtml(text) {
    if(!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============ KEYBOARD SHORTCUTS ============
$(document).keydown(function(e) {
    if(e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        loadLogs();
    }
});
</script>

<?php include_once '../includes/footer.php'; ?>