<?php
// views/kpi_check.php
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
if(!checkRole(['kpi', 'admin'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">✅ ตรวจสอบ KPI</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">ตรวจสอบ KPI</li>
                    </ol>
                </nav>
            </div>
            <div>
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-user-check"></i> ผู้ตรวจ: <?php echo htmlspecialchars($user_name); ?>
                </span>
            </div>
        </div>

        <!-- KPI Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ตรวจทั้งหมด</div>
                                <div class="h4 mb-0 font-weight-bold" id="statTotal">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">ผ่าน</div>
                                <div class="h4 mb-0 font-weight-bold" id="statPass">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-danger shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">ไม่ผ่าน</div>
                                <div class="h4 mb-0 font-weight-bold" id="statFail">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">รอตรวจ</div>
                                <div class="h4 mb-0 font-weight-bold" id="statPending">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Pending KPI -->
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> รายการรอตรวจ KPI</h5>
                        <button class="btn btn-sm btn-light" onclick="loadPendingKpi()">
                            <i class="fas fa-sync"></i> รีเฟรช
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Case ID</th>
                                        <th>ลูกค้า</th>
                                        <th>เบอร์โทร</th>
                                        <th>เจ้าของเคส</th>
                                        <th>วันที่สร้างเคส</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="pendingKpiBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status"></div>
                                            <p class="mt-2">กำลังโหลด...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent KPI Checks -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-history"></i> ประวัติการตรวจล่าสุด</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>วันที่ตรวจ</th>
                                        <th>Case ID</th>
                                        <th>ลูกค้า</th>
                                        <th>ผู้ตรวจ</th>
                                        <th>ผล</th>
                                        <th>เหตุผล</th>
                                    </tr>
                                </thead>
                                <tbody id="recentKpiBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status"></div>
                                            <p class="mt-2">กำลังโหลด...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Check & Stats -->
            <div class="col-xl-4">
                <!-- Quick KPI Check Form -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-check-double"></i> ตรวจ KPI ด่วน</h5>
                    </div>
                    <div class="card-body">
                        <form id="quickKpiForm">
                            <div class="mb-3">
                                <label class="form-label">Case ID <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">#</span>
                                    <input type="number" class="form-control" id="quickCaseId" 
                                           placeholder="กรอก Case ID" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">ผลการตรวจ <span class="text-danger">*</span></label>
                                <select class="form-select" id="quickResult" required>
                                    <option value="">เลือกผลการตรวจ</option>
                                    <option value="pass">✅ ผ่าน</option>
                                    <option value="fail">❌ ไม่ผ่าน</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">เหตุผล</label>
                                <select class="form-select" id="quickReason">
                                    <option value="">เลือกเหตุผล</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">หมายเหตุ</label>
                                <textarea class="form-control" id="quickNote" rows="2" 
                                          placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)"></textarea>
                            </div>
                            
                            <button type="button" class="btn btn-primary w-100" onclick="submitQuickKpi()">
                                <i class="fas fa-save"></i> บันทึกผล
                            </button>
                        </form>
                    </div>
                </div>

                <!-- My KPI Stats -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> สถิติของฉัน</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <h3 id="myTotalChecks" class="text-primary">0</h3>
                                <small class="text-muted">ตรวจวันนี้</small>
                            </div>
                            <div class="col-6 mb-3">
                                <h3 id="myPassRate" class="text-success">0%</h3>
                                <small class="text-muted">อัตราผ่าน</small>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <small class="text-muted">ตรวจทั้งหมด: <strong id="myAllChecks">0</strong> ครั้ง</small>
                        </div>
                    </div>
                </div>

                <!-- KPI Reasons Guide -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> เกณฑ์การตรวจ KPI</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2">✅ <strong>ผ่าน</strong> - คุณภาพการสนทนาดี ให้ข้อมูลครบถ้วน</li>
                            <li class="mb-2">❌ <strong>ไม่ตาม</strong> - ไม่ได้ติดตามลูกค้าภายใน 24 ชม.</li>
                            <li class="mb-2">❌ <strong>ลูกค้าไม่ตอบ</strong> - ติดต่อลูกค้าไม่ได้</li>
                            <li class="mb-2">❌ <strong>ติดบูโร</strong> - ลูกค้าติดเครดิตบูโร</li>
                            <li class="mb-2">❌ <strong>อายุงานไม่ถึง</strong> - อายุงานไม่ถึงเกณฑ์</li>
                            <li class="mb-2">❌ <strong>รายได้ไม่ถึง</strong> - รายได้ไม่ถึงเกณฑ์ขั้นต่ำ</li>
                            <li class="mb-2">❌ <strong>เอกสารไม่ครบ</strong> - เอกสารไม่สมบูรณ์</li>
                            <li>❌ <strong>อื่นๆ</strong> - ระบุเหตุผลเพิ่มเติม</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Check Detail Modal -->
<div class="modal fade" id="kpiDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle"></i> ตรวจ KPI - Case #<span id="detailCaseId"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Customer Info -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">ข้อมูลลูกค้า</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ชื่อ:</strong> <span id="detailCustomerName">-</span></p>
                                <p><strong>เบอร์โทร:</strong> <span id="detailCustomerPhone">-</span></p>
                                <p><strong>เกรด:</strong> <span id="detailCustomerGrade">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>โครงการ:</strong> <span id="detailProject">-</span></p>
                                <p><strong>เจ้าของเคส:</strong> <span id="detailOwner">-</span></p>
                                <p><strong>วันที่สร้าง:</strong> <span id="detailDate">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Follow History -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">ประวัติการติดตาม</h6>
                    </div>
                    <div class="card-body">
                        <div id="detailFollowHistory">
                            <p class="text-muted">กำลังโหลด...</p>
                        </div>
                    </div>
                </div>

                <!-- KPI Check Form -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">บันทึกผลการตรวจ</h6>
                    </div>
                    <div class="card-body">
                        <form id="detailKpiForm">
                            <input type="hidden" id="detailCaseIdInput">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ผลการตรวจ <span class="text-danger">*</span></label>
                                    <select class="form-select" id="detailResult" required>
                                        <option value="">เลือก</option>
                                        <option value="pass">✅ ผ่าน</option>
                                        <option value="fail">❌ ไม่ผ่าน</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">เหตุผล</label>
                                    <select class="form-select" id="detailReason">
                                        <option value="">เลือกเหตุผล</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">หมายเหตุ</label>
                                <textarea class="form-control" id="detailNote" rows="3" 
                                          placeholder="รายละเอียดเพิ่มเติม"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="submitDetailKpi()">
                    <i class="fas fa-save"></i> บันทึกผลการตรวจ
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ============ KPI CHECK JAVASCRIPT ============

$(document).ready(function() {
    console.log('KPI Check page loaded');
    
    // โหลดข้อมูลเริ่มต้น
    loadKpiStats();
    loadPendingKpi();
    loadRecentKpi();
    loadKpiReasons();
    loadMyStats();
    
    // Auto refresh ทุก 60 วินาที
    setInterval(function() {
        loadPendingKpi();
        loadKpiStats();
    }, 60000);
});

// ============ LOAD DATA ============
function loadKpiReasons() {
    $.ajax({
        url: '../api/master/get.php',
        type: 'GET',
        data: { type: 'kpi_reason' },
        dataType: 'json',
        timeout: 10000,
        success: function(data) {
            console.log('KPI Reasons:', data);
            
            let options = '<option value="">เลือกเหตุผล</option>';
            if(Array.isArray(data)) {
                data.forEach(function(item) {
                    options += '<option value="' + item.value + '">' + item.value + '</option>';
                });
            }
            
            $('#quickReason').html(options);
            $('#detailReason').html(options);
        },
        error: function() {
            console.error('Failed to load KPI reasons');
            // Fallback options
            const fallback = '<option value="">เลือกเหตุผล</option>' +
                '<option value="ไม่ตาม">ไม่ตาม</option>' +
                '<option value="ลูกค้าไม่ตอบ">ลูกค้าไม่ตอบ</option>' +
                '<option value="ติดบูโร">ติดบูโร</option>' +
                '<option value="อายุงานไม่ถึง">อายุงานไม่ถึง</option>' +
                '<option value="รายได้ไม่ถึง">รายได้ไม่ถึง</option>' +
                '<option value="เอกสารไม่ครบ">เอกสารไม่ครบ</option>' +
                '<option value="เปลี่ยนใจ">เปลี่ยนใจ</option>' +
                '<option value="อื่นๆ">อื่นๆ</option>';
            
            $('#quickReason').html(fallback);
            $('#detailReason').html(fallback);
        }
    });
}

function loadKpiStats() {
    $.ajax({
        url: '../api/kpi/stats.php',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('KPI Stats:', response);
            
            if(response.success) {
                $('#statTotal').text(response.total || 0);
                $('#statPass').text(response.pass || 0);
                $('#statFail').text(response.fail || 0);
                $('#statPending').text(response.pending || 0);
            } else {
                console.error('Stats error:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Stats API error:', status, error);
        }
    });
}

function loadPendingKpi() {
    $('#pendingKpiBody').html(
        '<tr><td colspan="6" class="text-center py-4">' +
        '<div class="spinner-border spinner-border-sm"></div> กำลังโหลด...</td></tr>'
    );
    
    $.ajax({
        url: '../api/kpi/pending.php',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Pending KPI:', response);
            
            if(response.success && response.data && response.data.length > 0) {
                let html = '';
                response.data.forEach(function(item) {
                    html += '<tr>' +
                        '<td><a href="case_detail.php?id=' + item.case_id + '" class="fw-bold">#' + item.case_id + '</a></td>' +
                        '<td>' + (item.customer_name || '-') + '</td>' +
                        '<td>' + (item.phone || '-') + '</td>' +
                        '<td>' + (item.owner_name || '-') + '</td>' +
                        '<td><small>' + formatDate(item.created_at) + '</small></td>' +
                        '<td>' +
                            '<button class="btn btn-sm btn-success" onclick="showKpiDetail(' + item.case_id + ')">' +
                                '<i class="fas fa-check"></i> ตรวจ' +
                            '</button>' +
                        '</td>' +
                    '</tr>';
                });
                $('#pendingKpiBody').html(html);
            } else {
                $('#pendingKpiBody').html(
                    '<tr><td colspan="6" class="text-center py-4 text-success">' +
                    '<i class="fas fa-check-circle fa-2x mb-2"></i>' +
                    '<p>ไม่มีรายการรอตรวจ 🎉</p></td></tr>'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('Pending KPI error:', status, error);
            $('#pendingKpiBody').html(
                '<tr><td colspan="6" class="text-center py-4 text-danger">' +
                '<i class="fas fa-exclamation-triangle"></i> ไม่สามารถโหลดข้อมูลได้' +
                '<br><button class="btn btn-sm btn-outline-primary mt-2" onclick="loadPendingKpi()">ลองใหม่</button>' +
                '</td></tr>'
            );
        }
    });
}

function loadRecentKpi() {
    $('#recentKpiBody').html(
        '<tr><td colspan="6" class="text-center py-4">' +
        '<div class="spinner-border spinner-border-sm"></div> กำลังโหลด...</td></tr>'
    );
    
    $.ajax({
        url: '../api/kpi/history.php',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Recent KPI:', response);
            
            if(response.success && response.data && response.data.length > 0) {
                let html = '';
                response.data.forEach(function(item) {
                    const resultBadge = item.result === 'pass' 
                        ? '<span class="badge bg-success">ผ่าน</span>' 
                        : '<span class="badge bg-danger">ไม่ผ่าน</span>';
                    
                    html += '<tr>' +
                        '<td><small>' + formatDate(item.created_at) + '</small></td>' +
                        '<td>#' + item.case_id + '</td>' +
                        '<td>' + (item.customer_name || '-') + '</td>' +
                        '<td>' + (item.checker_name || '-') + '</td>' +
                        '<td>' + resultBadge + '</td>' +
                        '<td>' + (item.reason || '-') + '</td>' +
                    '</tr>';
                });
                $('#recentKpiBody').html(html);
            } else {
                $('#recentKpiBody').html(
                    '<tr><td colspan="6" class="text-center py-4 text-muted">' +
                    '<i class="fas fa-history"></i> ยังไม่มีประวัติการตรวจ</td></tr>'
                );
            }
        },
        error: function() {
            $('#recentKpiBody').html(
                '<tr><td colspan="6" class="text-center py-4 text-danger">' +
                'ไม่สามารถโหลดประวัติได้</td></tr>'
            );
        }
    });
}

function loadMyStats() {
    $.ajax({
        url: '../api/kpi/stats.php',
        type: 'GET',
        data: { user_id: <?php echo $user_id; ?> },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            if(response.success) {
                $('#myTotalChecks').text(response.today || 0);
                $('#myAllChecks').text(response.total || 0);
                
                const rate = response.total > 0 
                    ? Math.round((response.pass / response.total) * 100) 
                    : 0;
                $('#myPassRate').text(rate + '%');
            }
        }
    });
}

// ============ SHOW KPI DETAIL MODAL ============
function showKpiDetail(caseId) {
    console.log('showKpiDetail:', caseId);
    
    $('#detailCaseId').text(caseId);
    $('#detailCaseIdInput').val(caseId);
    
    // Reset form
    $('#detailResult').val('');
    $('#detailReason').val('');
    $('#detailNote').val('');
    
    // Load case info
    $.ajax({
        url: '../api/cases/get.php',
        type: 'GET',
        data: { id: caseId },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            if(response.success && response.data) {
                const c = response.data;
                $('#detailCustomerName').text(c.customer_name || '-');
                $('#detailCustomerPhone').text(c.phone || '-');
                $('#detailCustomerGrade').text(c.grade || '-');
                $('#detailProject').text(c.project_name || '-');
                $('#detailOwner').text(c.owner_name || '-');
                $('#detailDate').text(formatDate(c.created_at));
            }
        }
    });
    
    // Load follow history
    $.ajax({
        url: '../api/follow/list.php',
        type: 'GET',
        data: { case_id: caseId },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            if(response.success && response.data && response.data.length > 0) {
                let html = '<ul class="list-group list-group-flush">';
                response.data.forEach(function(f) {
                    html += '<li class="list-group-item py-2">' +
                        '<strong>ครั้งที่ ' + f.step + ':</strong> ' + f.status +
                        '<br><small class="text-muted">' + (f.note || 'ไม่มีบันทึก') + '</small>' +
                        '<br><small class="text-muted">' + formatDate(f.created_at) + '</small>' +
                    '</li>';
                });
                html += '</ul>';
                $('#detailFollowHistory').html(html);
            } else {
                $('#detailFollowHistory').html('<p class="text-muted">ยังไม่มีการติดตาม</p>');
            }
        },
        error: function() {
            $('#detailFollowHistory').html('<p class="text-muted">ไม่สามารถโหลดประวัติได้</p>');
        }
    });
    
    $('#kpiDetailModal').modal('show');
}

// ============ SUBMIT KPI ============
function submitQuickKpi() {
    const caseId = $('#quickCaseId').val();
    const result = $('#quickResult').val();
    const reason = $('#quickReason').val();
    const note = $('#quickNote').val();
    
    // Validate
    if(!caseId) {
        alert('กรุณากรอก Case ID');
        $('#quickCaseId').focus();
        return;
    }
    
    if(!result) {
        alert('กรุณาเลือกผลการตรวจ');
        return;
    }
    
    // Confirm
    if(!confirm('ยืนยันการตรวจ KPI Case #' + caseId + '?\nผล: ' + (result === 'pass' ? 'ผ่าน' : 'ไม่ผ่าน'))) {
        return;
    }
    
    doKpiCheck(caseId, result, reason, note, function() {
        // Clear form on success
        $('#quickCaseId').val('');
        $('#quickResult').val('');
        $('#quickReason').val('');
        $('#quickNote').val('');
    });
}

function submitDetailKpi() {
    const caseId = $('#detailCaseIdInput').val();
    const result = $('#detailResult').val();
    const reason = $('#detailReason').val();
    const note = $('#detailNote').val();
    
    // Validate
    if(!result) {
        alert('กรุณาเลือกผลการตรวจ');
        return;
    }
    
    // Confirm
    if(!confirm('ยืนยันการตรวจ KPI Case #' + caseId + '?')) {
        return;
    }
    
    doKpiCheck(caseId, result, reason, note, function() {
        $('#kpiDetailModal').modal('hide');
    });
}

function doKpiCheck(caseId, result, reason, note, callback) {
    // Prepare data
    const postData = {
        case_id: parseInt(caseId),
        result: result,
        reason: reason || '',
        note: note || ''
    };
    
    console.log('Sending KPI data:', JSON.stringify(postData));
    
    $.ajax({
        url: '../api/kpi/check.php',
        type: 'POST',
        data: JSON.stringify(postData),
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('KPI Check response:', response);
            
            if(response.success) {
                alert('บันทึกผล KPI เรียบร้อย!');
                loadKpiStats();
                loadPendingKpi();
                loadRecentKpi();
                if(typeof callback === 'function') {
                    callback();
                }
            } else {
                alert('ผิดพลาด: ' + (response.message || 'ไม่สามารถบันทึกได้'));
            }
        },
        error: function(xhr, status, error) {
            console.error('KPI Check error:', status, error);
            console.error('Response:', xhr.responseText);
            
            let msg = 'ไม่สามารถบันทึกได้';
            try {
                const resp = JSON.parse(xhr.responseText);
                msg = resp.message || msg;
            } catch(e) {
                // ถ้า parse ไม่ได้ แสดง response text
                msg = 'Server error: ' + xhr.status;
            }
            
            alert('ผิดพลาด: ' + msg);
        }
    });
}

// ============ HELPERS ============
function formatDate(dateString) {
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

// ============ KEYBOARD SHORTCUTS ============
$(document).keydown(function(e) {
    // Ctrl+Enter to submit quick KPI
    if(e.ctrlKey && e.key === 'Enter') {
        if($('#quickCaseId').is(':focus')) {
            e.preventDefault();
            submitQuickKpi();
        }
    }
});
</script>

<?php include_once '../includes/footer.php'; ?>