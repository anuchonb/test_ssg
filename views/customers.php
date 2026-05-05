<?php
// views/customers.php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../includes/header.php';
include_once '../includes/sidebar.php';
include_once '../includes/auth_check.php';
include_once '../includes/functions.php';

// ตรวจสอบ role
if(!checkRole(['admin_page', 'admin'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">👥 ข้อมูลลูกค้า</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">ข้อมูลลูกค้า</li>
                    </ol>
                </nav>
            </div>
            <button class="btn btn-primary" onclick="showCustomerForm()">
                <i class="fas fa-plus"></i> เพิ่มลูกค้าใหม่
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4" id="customerStats">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ลูกค้าทั้งหมด</div>
                                <div class="h4 mb-0 font-weight-bold" id="statTotalCustomers">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">เกรด A+</div>
                                <div class="h4 mb-0 font-weight-bold" id="statGradeAPlus">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-star fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">มีเคสอยู่</div>
                                <div class="h4 mb-0 font-weight-bold" id="statWithCases">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-folder-open fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">เพิ่มวันนี้</div>
                                <div class="h4 mb-0 font-weight-bold" id="statToday">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">ค้นหา</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" 
                                   placeholder="ค้นหาชื่อ, เบอร์โทร, รหัสลูกค้า..."
                                   onkeyup="debounceSearch()">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">ช่องทาง</label>
                        <select class="form-select" id="filterChannel" onchange="loadCustomers()">
                            <option value="">ทั้งหมด</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">เกรด</label>
                        <select class="form-select" id="filterGrade" onchange="loadCustomers()">
                            <option value="">ทั้งหมด</option>
                            <option value="A+">A+</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">โครงการ</label>
                        <select class="form-select" id="filterProject" onchange="loadCustomers()">
                            <option value="">ทั้งหมด</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">ภาระหนี้</label>
                        <select class="form-select" id="filterDebt" onchange="loadCustomers()">
                            <option value="">ทั้งหมด</option>
                            <option value="have">มีหนี้</option>
                            <option value="none">ไม่มีหนี้</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">รายชื่อลูกค้า</h5>
                <div>
                    <button class="btn btn-sm btn-success" onclick="exportCustomers()">
                        <i class="fas fa-download"></i> Export Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="customersTable">
                        <thead>
                            <tr>
                                <th style="width: 100px;">รหัส</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>เบอร์โทร</th>
                                <th>ช่องทาง</th>
                                <th>เกรด</th>
                                <th>โครงการ</th>
                                <th>ราคา</th>
                                <th>ภาระหนี้</th>
                                <th>สถานะ</th>
                                <th>วันที่เพิ่ม</th>
                                <th style="width: 130px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="customersTableBody">
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <select class="form-select form-select-sm" style="width: auto;" id="perPage" onchange="loadCustomers()">
                            <option value="10">10 รายการ</option>
                            <option value="25">25 รายการ</option>
                            <option value="50">50 รายการ</option>
                            <option value="100">100 รายการ</option>
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

<!-- Customer Form Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="customerModalTitle">
                    <i class="fas fa-user-plus"></i> เพิ่มลูกค้าใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customerForm" novalidate>
                    <input type="hidden" id="customer_id">
                    <input type="hidden" id="customer_code">
                    
                    <!-- Contact Information -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-address-card"></i> ข้อมูลติดต่อ</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                           placeholder="ชื่อ นามสกุล">
                                    <div class="invalid-feedback">กรุณากรอกชื่อ-นามสกุล</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">เบอร์โทร <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="tel" class="form-control" id="phone" name="phone" required
                                               placeholder="0xx-xxx-xxxx" maxlength="10"
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                               onblur="checkCustomerByPhone(this.value)">
                                        <span class="input-group-text" id="phoneStatus">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                    <div id="phoneFeedback" class="form-text"></div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Facebook</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                        <input type="text" class="form-control" id="facebook" name="facebook"
                                               placeholder="URL หรือชื่อ Facebook">
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Line ID</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-line"></i></span>
                                        <input type="text" class="form-control" id="line_id" name="line_id"
                                               placeholder="Line ID">
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">เพจที่พบ</label>
                                    <input type="text" class="form-control" id="page_name" name="page_name"
                                           placeholder="ชื่อเพจ">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customer Classification -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-tags"></i> ข้อมูลการจำแนก</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">ช่องทาง</label>
                                    <select class="form-select" id="channel" name="channel">
                                        <option value="">เลือกช่องทาง</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">เกรดลูกค้า</label>
                                    <select class="form-select" id="grade" name="grade">
                                        <option value="">เลือกเกรด</option>
                                        <option value="A+">A+ (ดีมาก)</option>
                                        <option value="A">A (ดี)</option>
                                        <option value="B">B (ปานกลาง)</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">โครงการที่สนใจ</label>
                                    <select class="form-select" id="project_id" name="project_id">
                                        <option value="">เลือกโครงการ</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">ราคาที่สนใจ</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="price" name="price" 
                                               step="0.01" placeholder="0.00">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">เงินทอน</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="cashback" name="cashback" 
                                               step="0.01" placeholder="0.00">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">ลักษณะการอยู่อาศัย</label>
                                    <select class="form-select" id="living_type" name="living_type">
                                        <option value="">เลือก</option>
                                        <option value="rent">🏠 ปล่อยเช่า</option>
                                        <option value="live">🏡 อยู่เอง</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">โซนที่สนใจ</label>
                                    <select class="form-select" id="zone" name="zone">
                                        <option value="">เลือกโซน</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Employment Information -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-briefcase"></i> ข้อมูลการทำงาน</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ชื่อบริษัท</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name"
                                           placeholder="ชื่อบริษัทที่ทำงาน">
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">อายุงาน (เดือน)</label>
                                    <input type="number" class="form-control" id="work_age_month" name="work_age_month"
                                           placeholder="0" min="0">
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">สวัสดิการ</label>
                                    <select class="form-select" id="welfare" name="welfare">
                                        <option value="">เลือก</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ภาระหนี้</label>
                                    <select class="form-select" id="debt_status" name="debt_status">
                                        <option value="">เลือก</option>
                                        <option value="have">❌ มีภาระหนี้</option>
                                        <option value="none">✅ ไม่มีภาระหนี้</option>
                                    </select>
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
                <button type="button" class="btn btn-success" id="btnCreateCase" style="display: none;" 
                        onclick="createCaseFromCustomer()">
                    <i class="fas fa-paper-plane"></i> บันทึกและส่งเคส
                </button>
                <button type="button" class="btn btn-primary" onclick="saveCustomer()">
                    <i class="fas fa-save"></i> บันทึกข้อมูล
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">ยืนยันการลบ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการลบข้อมูลลูกค้านี้ใช่หรือไม่?</p>
                <p class="text-danger"><strong>คำเตือน:</strong> การลบจะไม่สามารถกู้คืนได้ และจะลบเคสที่เกี่ยวข้องทั้งหมด</p>
                <input type="hidden" id="deleteCustomerId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" onclick="deleteCustomer()">
                    <i class="fas fa-trash"></i> ลบ
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentCustomerId = null;
let currentPage = 1;
let searchTimeout = null;

$(document).ready(function() {
    // Load initial data
    loadDropdowns();
    loadCustomers();
    loadCustomerStats();
    
    // Form validation - remove invalid class on input
    $(document).on('input', '.is-invalid', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Phone number formatting
    $('#phone').on('input', function() {
        let phone = $(this).val().replace(/[^0-9]/g, '');
        if(phone.length > 10) phone = phone.substring(0, 10);
        $(this).val(phone);
    });
    
    // Auto check customer when phone loses focus
    $('#phone').on('blur', function() {
        const phone = $(this).val().trim();
        if(phone.length >= 9 && !currentCustomerId) {
            checkCustomerByPhone(phone);
        }
    });
    
    // Show/hide create case button
    updateCreateCaseButton();
    
    // Prevent form submit on enter
    $('#customerForm').on('keydown', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            return false;
        }
    });
});

// ============ DROPDOWN LOADING ============
function loadDropdowns() {
    // Load channels
    loadDropdown('channel', '#channel');
    loadDropdown('channel', '#filterChannel');
    
    // Load projects
    loadProjects();
    loadProjectsFilter();
    
    // Load zones
    loadDropdown('zone', '#zone');
    
    // Load welfare
    loadDropdown('welfare', '#welfare');
}

function loadDropdown(type, selector) {
    $.ajax({
        url: '../api/master/get.php',
        type: 'GET',
        data: { type: type, active_only: 'true' },
        dataType: 'json',
        success: function(data) {
            let options = '<option value="">เลือก</option>';
            if(Array.isArray(data)) {
                data.forEach(item => {
                    options += `<option value="${item.value}">${item.value}</option>`;
                });
            }
            $(selector).html(options);
        },
        error: function() {
            console.error('Failed to load dropdown:', type);
            $(selector).html('<option value="">โหลดไม่สำเร็จ</option>');
        }
    });
}

function loadProjects() {
    $.ajax({
        url: '../api/projects/list.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                let options = '<option value="">เลือกโครงการ</option>';
                response.data.forEach(project => {
                    options += `<option value="${project.id}" data-price="${project.price || ''}">
                        ${project.name} ${project.price ? ' - ' + numberFormat(project.price) + ' บาท' : ''}
                    </option>`;
                });
                $('#project_id').html(options);
            }
        },
        error: function() {
            console.error('Failed to load projects');
            $('#project_id').html('<option value="">โหลดไม่สำเร็จ</option>');
        }
    });
}

function loadProjectsFilter() {
    $.ajax({
        url: '../api/projects/list.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                let options = '<option value="">ทั้งหมด</option>';
                response.data.forEach(project => {
                    options += `<option value="${project.id}">${project.name}</option>`;
                });
                $('#filterProject').html(options);
            }
        }
    });
}

// ============ CUSTOMER STATS ============
function loadCustomerStats() {
    $.ajax({
        url: '../api/customers/stats.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#statTotalCustomers').text(response.total || 0);
                $('#statGradeAPlus').text(response.grade_a_plus || 0);
                $('#statWithCases').text(response.with_cases || 0);
                $('#statToday').text(response.today || 0);
            }
        }
    });
}

// ============ CUSTOMER LIST ============
function loadCustomers(page = 1) {
    currentPage = page;
    
    const search = $('#searchInput').val();
    const channel = $('#filterChannel').val();
    const grade = $('#filterGrade').val();
    const project = $('#filterProject').val();
    const debt = $('#filterDebt').val();
    const perPage = $('#perPage').val() || 10;
    
    // Show loading
    showTableLoading();
    
    $.ajax({
        url: '../api/customers/list.php',
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            channel: channel,
            grade: grade,
            project_id: project,
            debt_status: debt
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                renderCustomersTable(response.data);
                renderPagination(response.pagination);
                $('#showingCount').text(response.data ? response.data.length : 0);
                $('#totalCount').text(response.pagination ? response.pagination.total : 0);
            } else {
                showTableEmpty();
            }
        },
        error: function(xhr, status, error) {
            console.error('Load customers error:', error);
            showTableError();
        }
    });
}

function showTableLoading() {
    $('#customersTableBody').html(`
        <tr>
            <td colspan="11" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">กำลังโหลดข้อมูล...</p>
            </td>
        </tr>
    `);
}

function showTableEmpty() {
    $('#customersTableBody').html(`
        <tr>
            <td colspan="11" class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">ไม่พบข้อมูลลูกค้า</p>
                <button class="btn btn-primary btn-sm" onclick="showCustomerForm()">
                    <i class="fas fa-plus"></i> เพิ่มลูกค้าใหม่
                </button>
            </td>
        </tr>
    `);
}

function showTableError() {
    $('#customersTableBody').html(`
        <tr>
            <td colspan="11" class="text-center py-5 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <p>ไม่สามารถโหลดข้อมูลได้</p>
                <button class="btn btn-outline-primary btn-sm" onclick="loadCustomers()">
                    <i class="fas fa-redo"></i> ลองใหม่
                </button>
            </td>
        </tr>
    `);
}

function renderCustomersTable(customers) {
    if(!customers || customers.length === 0) {
        showTableEmpty();
        return;
    }
    
    let html = '';
    customers.forEach(c => {
        const gradeBadge = c.grade 
            ? `<span class="badge bg-${getGradeColor(c.grade)}">${c.grade}</span>` 
            : '<span class="badge bg-secondary">-</span>';
        
        const debtBadge = c.debt_status === 'have' 
            ? '<span class="badge bg-warning text-dark">มีหนี้</span>' 
            : c.debt_status === 'none' 
                ? '<span class="badge bg-success">ไม่มีหนี้</span>' 
                : '<span class="badge bg-secondary">-</span>';
        
        const caseBadge = c.case_status 
            ? `<span class="badge bg-${getCaseStatusColor(c.case_status)}">${c.case_status}</span>` 
            : '<span class="badge bg-secondary">ไม่มีเคส</span>';
        
        html += `
            <tr>
                <td><small class="text-muted">${c.customer_code || '-'}</small></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-${c.grade ? getGradeColor(c.grade) : 'secondary'} text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                             style="width: 32px; height: 32px; font-size: 14px; flex-shrink: 0;">
                            ${c.name ? c.name.charAt(0).toUpperCase() : '?'}
                        </div>
                        <div>
                            <div class="fw-semibold">${c.name || 'ไม่ระบุ'}</div>
                            ${c.facebook ? `<small class="text-primary"><i class="fab fa-facebook"></i> ${escapeHtml(c.facebook)}</small>` : ''}
                            ${c.line_id ? `<br><small class="text-success"><i class="fab fa-line"></i> ${escapeHtml(c.line_id)}</small>` : ''}
                        </div>
                    </div>
                </td>
                <td>
                    <a href="tel:${c.phone}" class="text-decoration-none">${c.phone || '-'}</a>
                </td>
                <td>${c.channel || '-'}</td>
                <td>${gradeBadge}</td>
                <td>${c.project_name || '-'}</td>
                <td class="text-end">${c.price ? numberFormat(c.price) + ' บาท' : '-'}</td>
                <td>${debtBadge}</td>
                <td>${caseBadge}</td>
                <td><small>${formatDateThai(c.created_at)}</small></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="editCustomer(${c.id})" title="แก้ไข">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${!c.case_id ? `
                            <button class="btn btn-success" onclick="createCase(${c.id})" title="ส่งเคส">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        ` : `
                            <button class="btn btn-warning" onclick="viewCase(${c.case_id})" title="ดูเคส">
                                <i class="fas fa-eye"></i>
                            </button>
                        `}
                        <button class="btn btn-danger" onclick="confirmDeleteCustomer(${c.id}, '${escapeHtml(c.name)}')" title="ลบ">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#customersTableBody').html(html);
}

function renderPagination(pagination) {
    if(!pagination || pagination.total_pages <= 1) {
        $('#pagination').html('');
        return;
    }
    
    let html = '';
    
    // Previous button
    html += `<li class="page-item ${!pagination.has_prev ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadCustomers(${pagination.current_page - 1}); return false;">
            <i class="fas fa-chevron-left"></i>
        </a>
    </li>`;
    
    // Page numbers
    for(let i = 1; i <= pagination.total_pages; i++) {
        if(i === 1 || i === pagination.total_pages || 
           (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadCustomers(${i}); return false;">${i}</a>
            </li>`;
        } else if(i === pagination.current_page - 3 || i === pagination.current_page + 3) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Next button
    html += `<li class="page-item ${!pagination.has_next ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadCustomers(${pagination.current_page + 1}); return false;">
            <i class="fas fa-chevron-right"></i>
        </a>
    </li>`;
    
    $('#pagination').html(html);
}

// ============ CUSTOMER CRUD ============
function showCustomerForm() {
    resetCustomerForm();
    $('#customerModalTitle').html('<i class="fas fa-user-plus"></i> เพิ่มลูกค้าใหม่');
    $('#btnCreateCase').hide();
    $('#phoneStatus').html('<i class="fas fa-search"></i>');
    $('#phoneFeedback').html('');
    $('#customerModal').modal('show');
}

function editCustomer(id) {
    // Show loading
    Swal.fire({
        title: 'กำลังโหลดข้อมูล...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../api/customers/get.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if(response.success && response.data) {
                fillCustomerForm(response.data);
                $('#customerModalTitle').html('<i class="fas fa-edit"></i> แก้ไขข้อมูลลูกค้า');
                $('#btnCreateCase').show();
                $('#customerModal').modal('show');
            } else {
                Swal.fire('ผิดพลาด!', 'ไม่พบข้อมูลลูกค้า', 'error');
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('ผิดพลาด!', 'ไม่สามารถโหลดข้อมูลได้', 'error');
        }
    });
}

function fillCustomerForm(customer) {
    resetCustomerForm();
    currentCustomerId = customer.id;
    
    $('#customer_id').val(customer.id);
    $('#customer_code').val(customer.customer_code);
    $('#name').val(customer.name);
    $('#phone').val(customer.phone);
    $('#facebook').val(customer.facebook);
    $('#line_id').val(customer.line_id);
    $('#page_name').val(customer.page_name);
    $('#channel').val(customer.channel);
    $('#grade').val(customer.grade);
    $('#project_id').val(customer.project_id);
    $('#price').val(customer.price);
    $('#cashback').val(customer.cashback);
    $('#living_type').val(customer.living_type);
    $('#zone').val(customer.zone);
    $('#company_name').val(customer.company_name);
    $('#work_age_month').val(customer.work_age_month);
    $('#welfare').val(customer.welfare);
    $('#debt_status').val(customer.debt_status);
    
    // Update phone status
    $('#phoneStatus').html('<i class="fas fa-check text-success"></i>');
    $('#phoneFeedback').html('<small class="text-success">✅ แก้ไขข้อมูลลูกค้า</small>');
    
    updateCreateCaseButton();
}

function resetCustomerForm() {
    currentCustomerId = null;
    $('#customerForm')[0].reset();
    $('#customer_id').val('');
    $('#customer_code').val('');
    $('#phoneStatus').html('<i class="fas fa-search"></i>');
    $('#phoneFeedback').html('');
    $('#btnCreateCase').hide();
    $('.is-invalid').removeClass('is-invalid');
}

function checkCustomerByPhone(phone) {
    if(!phone || phone.length < 9) {
        $('#phoneStatus').html('<i class="fas fa-search"></i>');
        $('#phoneFeedback').html('');
        return;
    }
    
    // Don't check if editing same customer
    if(currentCustomerId) return;
    
    $('#phoneStatus').html('<i class="fas fa-spinner fa-spin"></i>');
    
    $.ajax({
        url: '../api/customers/get.php',
        type: 'GET',
        data: { phone: phone },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                const customer = response.data;
                
                $('#phoneStatus').html('<i class="fas fa-check text-success"></i>');
                $('#phoneFeedback').html(`
                    <small class="text-success">
                        ✅ พบข้อมูล ${customer.name} | 
                        <a href="#" onclick="fillCustomerForm(response.data); return false;" class="text-primary">
                            คลิกเพื่อแก้ไข
                        </a>
                    </small>
                `);
                
                // Store for later use
                window.foundCustomer = customer;
            } else {
                $('#phoneStatus').html('<i class="fas fa-plus text-primary"></i>');
                $('#phoneFeedback').html('<small class="text-muted">🆕 ลูกค้าใหม่</small>');
                window.foundCustomer = null;
            }
        },
        error: function() {
            $('#phoneStatus').html('<i class="fas fa-search"></i>');
            $('#phoneFeedback').html('');
        }
    });
}

function saveCustomer() {
    // Validate required fields
    const name = $('#name').val().trim();
    const phone = $('#phone').val().trim();
    
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    
    // Validate name
    if(!name) {
        $('#name').addClass('is-invalid');
        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกชื่อ-นามสกุล',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
        $('#name').focus();
        return;
    }
    
    // Validate phone
    if(!phone || phone.length < 9) {
        $('#phone').addClass('is-invalid');
        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกเบอร์โทรให้ถูกต้อง',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
        $('#phone').focus();
        return;
    }
    
    // Collect form data
    const formData = {
        name: name,
        phone: phone,
        facebook: $('#facebook').val().trim(),
        line_id: $('#line_id').val().trim(),
        page_name: $('#page_name').val().trim(),
        channel: $('#channel').val(),
        grade: $('#grade').val(),
        project_id: $('#project_id').val() || null,
        price: $('#price').val() || null,
        cashback: $('#cashback').val() || null,
        living_type: $('#living_type').val(),
        zone: $('#zone').val(),
        company_name: $('#company_name').val().trim(),
        work_age_month: $('#work_age_month').val() || null,
        welfare: $('#welfare').val(),
        debt_status: $('#debt_status').val()
    };
    
    const isEdit = currentCustomerId ? true : false;
    const url = isEdit ? '../api/customers/update.php' : '../api/customers/create.php';
    
    if(isEdit) {
        formData.id = currentCustomerId;
    }
    
    // Confirm
    const confirmTitle = isEdit ? 'ยืนยันการแก้ไขข้อมูล?' : 'ยืนยันการเพิ่มลูกค้าใหม่?';
    const confirmText = `ชื่อ: ${name}<br>เบอร์: ${phone}`;

    Swal.fire({
        title: confirmTitle,
        html: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fas fa-save"></i> บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'กำลังบันทึก...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: url,
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    
                    if(response.success) {
                        if(!currentCustomerId && response.customer_id) {
                            currentCustomerId = response.customer_id;
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: response.message || 'บันทึกข้อมูลเรียบร้อย',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        $('#btnCreateCase').show();
                        updateCreateCaseButton();
                        loadCustomers(currentPage);
                        loadCustomerStats();
                        
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด!',
                            text: response.message || 'ไม่สามารถบันทึกข้อมูลได้'
                        });
                    }
                },
                error: function(xhr) {
                    let message = 'ไม่สามารถบันทึกข้อมูลได้';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด!',
                        text: message
                    });
                }
            });
        }
    });
}

function createCaseFromCustomer() {
    if(!currentCustomerId) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาบันทึกข้อมูลลูกค้าก่อน',
            text: 'ต้องบันทึกข้อมูลลูกค้าก่อนจึงจะส่งเคสได้'
        });
        return;
    }
    
    createCase(currentCustomerId);
}

function createCase(customerId) {
    Swal.fire({
        title: 'ยืนยันการส่งเคส?',
        text: 'คุณต้องการสร้างเคสให้ลูกค้าคนนี้ใช่หรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fas fa-paper-plane"></i> ส่งเคส',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            Swal.fire({
                title: 'กำลังสร้างเคส...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '../api/cases/create.php',
                type: 'POST',
                data: JSON.stringify({
                    customer_id: customerId,
                    owner_id: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; ?>
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#customerModal').modal('hide');
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'ส่งเคสสำเร็จ!',
                            text: `Case #${response.case_id} ถูกสร้างเรียบร้อย`,
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: '<i class="fas fa-eye"></i> ดูรายละเอียดเคส',
                            cancelButtonText: '<i class="fas fa-times"></i> ปิด'
                        }).then((result) => {
                            if(result.isConfirmed) {
                                window.location.href = `case_detail.php?case_id=${response.case_id}`;
                            } else {
                                loadCustomers(currentPage);
                                loadCustomerStats();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด!',
                            text: response.message || 'ไม่สามารถสร้างเคสได้'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด!',
                        text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                    });
                }
            });
        }
    });
}

function viewCase(caseId) {
    window.location.href = `case_detail.php?case_id=${caseId}`;
}

function confirmDeleteCustomer(id, name) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        html: `คุณต้องการลบข้อมูลของ <strong>${name}</strong> ใช่หรือไม่?<br>
               <span class="text-danger">การลบจะไม่สามารถกู้คืนได้!</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash"></i> ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            deleteCustomer(id);
        }
    });
}

function deleteCustomer(id) {
    Swal.fire({
        title: 'กำลังลบ...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../api/customers/delete.php',
        type: 'POST',
        data: JSON.stringify({ id: id }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'ลบสำเร็จ!',
                    text: 'ลบข้อมูลลูกค้าเรียบร้อย',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // If deleted customer is current in form, reset
                if(currentCustomerId === id) {
                    resetCustomerForm();
                    $('#customerModal').modal('hide');
                }
                
                loadCustomers(currentPage);
                loadCustomerStats();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด!',
                    text: response.message || 'ไม่สามารถลบได้'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด!',
                text: 'ไม่สามารถลบข้อมูลได้'
            });
        }
    });
}

function updateCreateCaseButton() {
    if(currentCustomerId) {
        $('#btnCreateCase').show();
    } else {
        $('#btnCreateCase').hide();
    }
}

// ============ FILTER & SEARCH ============
function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadCustomers(1);
    }, 500);
}

function resetFilters() {
    $('#searchInput').val('');
    $('#filterChannel').val('');
    $('#filterGrade').val('');
    $('#filterProject').val('');
    $('#filterDebt').val('');
    currentPage = 1;
    loadCustomers();
    loadCustomerStats();
}

function exportCustomers() {
    const search = $('#searchInput').val();
    const channel = $('#filterChannel').val();
    const grade = $('#filterGrade').val();
    const project = $('#filterProject').val();
    const debt = $('#filterDebt').val();
    
    // Build URL
    let url = `../api/customers/export.php?`;
    if(search) url += `search=${encodeURIComponent(search)}&`;
    if(channel) url += `channel=${encodeURIComponent(channel)}&`;
    if(grade) url += `grade=${encodeURIComponent(grade)}&`;
    if(project) url += `project_id=${project}&`;
    if(debt) url += `debt_status=${debt}&`;
    
    window.open(url, '_blank');
}

// ============ HELPER FUNCTIONS ============
function getGradeColor(grade) {
    const colors = { 'A+': 'success', 'A': 'primary', 'B': 'warning' };
    return colors[grade] || 'secondary';
}

function getCaseStatusColor(status) {
    const colors = {
        'ส่งเคส': 'primary',
        'กำลังติดตาม': 'warning',
        'อนุมัติ': 'success',
        'ยกเลิก': 'danger',
        'ไม่สนใจ': 'secondary',
        'วงเงินไม่ถึง': 'info'
    };
    return colors[status] || 'secondary';
}

function formatDateThai(dateString) {
    if(!dateString) return '-';
    const date = new Date(dateString);
    if(isNaN(date.getTime())) return '-';
    
    return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function numberFormat(number) {
    if(!number && number !== 0) return '0';
    return new Intl.NumberFormat('th-TH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(parseFloat(number));
}

function escapeHtml(text) {
    if(!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============ KEYBOARD SHORTCUTS ============
$(document).keydown(function(e) {
    // Ctrl + N: New Customer
    if(e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        showCustomerForm();
    }
    
    // Ctrl + F: Focus Search
    if(e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        $('#searchInput').focus();
    }
    
    // Escape: Close Modal
    if(e.key === 'Escape') {
        $('#customerModal').modal('hide');
        $('#deleteModal').modal('hide');
    }
});
</script>

<?php include_once '../includes/footer.php'; ?>