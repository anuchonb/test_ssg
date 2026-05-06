<?php
// views/mortgage.php
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
if(!checkRole(['support', 'admin'])) {
    header("Location: dashboard.php");
    exit();
}

// ✅ รับ case_id จาก URL
$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;
?>

<div class="main-content">
    <div class="container-fluid">
        
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">🏠 จำนอง  - Case #<?php echo $case_id; ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <?php if($case_id): ?>
                            <li class="breadcrumb-item"><a href="case_detail.php?case_id=<?php echo $case_id; ?>">Case #<?php echo $case_id; ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active">จำนอง</li>
                    </ol>
                </nav>
            </div>
            <?php if($case_id): ?>
                <a href="case_detail.php?case_id=<?php echo $case_id; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> กลับไปเคส
                </a>
            <?php else: ?>
                <a href="cases.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> กลับ
                </a>
            <?php endif; ?>
        </div>

        <?php if(!$case_id): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-home fa-4x text-muted mb-3"></i>
                    <h5>กรุณาเลือกเคส</h5>
                    <p class="text-muted">คุณต้องเปิดจากหน้ารายละเอียดเคส หรือระบุ Case ID</p>
                    
                    <div class="row justify-content-center mt-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">#</span>
                                <input type="number" class="form-control" id="gotoCaseId" placeholder="กรอก Case ID">
                                <button class="btn btn-primary" onclick="goToCase()">ไป</button>
                            </div>
                        </div>
                    </div>
                    
                    <a href="cases.php" class="btn btn-outline-primary mt-3">ดูรายการเคสทั้งหมด</a>
                </div>
            </div>
        <?php else: ?>
            
            <div class="row">
                <!-- ฟอร์มจำนอง -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-home"></i> บันทึกการจำนอง</h5>
                        </div>
                        <div class="card-body">
                            <form id="mortgageForm">
                                <input type="hidden" id="mort_case_id" value="<?php echo $case_id; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">วันที่จำนอง <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="mortgage_date" 
                                               value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ธนาคาร <span class="text-danger">*</span></label>
                                        <select class="form-select" id="mortgage_bank">
                                            <option value="">เลือกธนาคาร</option>
                                            <option value="ธนาคารกรุงเทพ">ธนาคารกรุงเทพ</option>
                                            <option value="ธนาคารกสิกรไทย">ธนาคารกสิกรไทย</option>
                                            <option value="ธนาคารกรุงไทย">ธนาคารกรุงไทย</option>
                                            <option value="ธนาคารไทยพาณิชย์">ธนาคารไทยพาณิชย์</option>
                                            <option value="ธนาคารออมสิน">ธนาคารออมสิน</option>
                                            <option value="ธนาคารอาคารสงเคราะห์">ธนาคารอาคารสงเคราะห์</option>
                                            <option value="ธนาคารกรุงศรีอยุธยา">ธนาคารกรุงศรีอยุธยา</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ชื่อบัญชี</label>
                                        <input type="text" class="form-control" id="mortgage_account_name" 
                                               placeholder="ชื่อ-นามสกุล">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">เลขที่บัญชี</label>
                                        <input type="text" class="form-control" id="mortgage_account_number" 
                                               placeholder="เลขบัญชี">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">วงเงินอนุมัติ</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="mortgage_amount" 
                                                   step="0.01" placeholder="0.00">
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-primary" onclick="saveMortgage()">
                                    <i class="fas fa-save"></i> บันทึกการจำนอง
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- ข้อมูลเคส + ประวัติ -->
                <div class="col-lg-5">
                    <!-- ✅ ข้อมูลเคส -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle"></i> ข้อมูลเคส #<?php echo $case_id; ?></h6>
                        </div>
                        <div class="card-body" id="caseInfoSummary">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm"></div> กำลังโหลด...
                            </div>
                        </div>
                    </div>
                    
                    <!-- ✅ ประวัติการจำนอง -->
                    <div class="card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-history"></i> ประวัติการจำนอง</h6>
                            <span class="badge bg-light text-dark" id="historyCount">0</span>
                        </div>
                        <div class="card-body p-0">
                            <div id="mortgageHistory" style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm"></div> กำลังโหลด...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php endif; ?>
    </div>
</div>

<script>
const CASE_ID = <?php echo $case_id ? $case_id : 0; ?>;

$(document).ready(function() {
    if(CASE_ID) {
        loadCaseSummary();
        loadMortgageData();
        loadMortgageHistory();
    }
});

function goToCase() {
    let id = $('#gotoCaseId').val();
    if(id) window.location.href = 'mortgage.php?case_id=' + id;
}

// ✅ โหลดข้อมูลเคส
function loadCaseSummary() {
    $.ajax({
        url: '../api/cases/get.php',
        type: 'GET',
        data: { id: CASE_ID },
        dataType: 'json',
        timeout: 10000,
        success: function(res) {
            console.log('Case info:', res);
            if(res.success && res.data) {
                let c = res.data;
                $('#caseInfoSummary').html(
                    '<p><strong>ลูกค้า:</strong> ' + (c.customer_name || '-') + '</p>' +
                    '<p><strong>เบอร์โทร:</strong> ' + (c.phone || '-') + '</p>' +
                    '<p><strong>สถานะ:</strong> <span class="badge bg-primary">' + (c.status || '-') + '</span></p>' +
                    '<p><strong>เจ้าของเคส:</strong> ' + (c.owner_name || '-') + '</p>' +
                    '<a href="case_detail.php?case_id=' + CASE_ID + '" class="btn btn-sm btn-outline-info">ดูรายละเอียดเคส</a>'
                );
            } else {
                $('#caseInfoSummary').html('<div class="alert alert-warning">ไม่พบข้อมูลเคส #' + CASE_ID + '</div>');
            }
        },
        error: function(xhr) {
            $('#caseInfoSummary').html('<div class="alert alert-danger">❌ โหลดไม่สำเร็จ (Status: ' + xhr.status + ')</div>');
        }
    });
}

// ✅ โหลดข้อมูลจำนอง (ถ้ามี)
function loadMortgageData() {
    $.get('../api/mortgage/get.php', { case_id: CASE_ID }, function(res) {
        if(res.success && res.data) {
            let m = res.data;
            $('#mortgage_date').val(m.mortgage_date || '');
            $('#mortgage_bank').val(m.bank_name || '');
            $('#mortgage_account_name').val(m.account_name || '');
            $('#mortgage_account_number').val(m.account_number || '');
            $('#mortgage_amount').val(m.approved_amount || '');
        }
    }, 'json');
}

// ✅ โหลดประวัติ
function loadMortgageHistory() {
    $.get('../api/mortgage/list.php', { case_id: CASE_ID }, function(res) {
        if(res.success && res.data && res.data.length > 0) {
            $('#historyCount').text(res.data.length);
            let html = '<div class="list-group list-group-flush">';
            res.data.forEach(function(m, index) {
                html += 
                '<div class="list-group-item py-3">' +
                    '<div class="d-flex">' +
                        '<div class="me-3 text-center">' +
                            '<div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">' +
                                '<strong>' + (res.data.length - index) + '</strong>' +
                            '</div>' +
                        '</div>' +
                        '<div class="flex-grow-1">' +
                            '<strong>' + (m.bank_name || '-') + '</strong>' +
                            '<br><small>📅 ' + (m.mortgage_date || '-') + '</small>' +
                            '<br><small>💰 วงเงิน: ' + (m.approved_amount ? numberFormat(m.approved_amount) + ' บาท' : '-') + '</small>' +
                            (m.account_number ? '<br><small>🏦 บัญชี: ' + m.account_number + '</small>' : '') +
                        '</div>' +
                    '</div>' +
                '</div>';
            });
            html += '</div>';
            $('#mortgageHistory').html(html);
        } else {
            $('#historyCount').text('0');
            $('#mortgageHistory').html(
                '<div class="text-center py-5 text-muted">' +
                    '<i class="fas fa-home fa-4x mb-3"></i>' +
                    '<h6>ยังไม่มีประวัติการจำนอง</h6>' +
                '</div>'
            );
        }
    }, 'json');
}

// ✅ บันทึก
function saveMortgage() {
    let data = {
        case_id: CASE_ID,
        mortgage_date: $('#mortgage_date').val(),
        bank_name: $('#mortgage_bank').val(),
        account_name: $('#mortgage_account_name').val(),
        account_number: $('#mortgage_account_number').val(),
        approved_amount: $('#mortgage_amount').val() || 0
    };
    
    if(!data.bank_name) { alert('กรุณาเลือกธนาคาร'); return; }
    if(!confirm('บันทึกการจำนอง?')) return;
    
    $.post('../api/mortgage/save.php', JSON.stringify(data), function(res) {
        if(res.success) {
            alert('บันทึกสำเร็จ!');
            loadMortgageHistory();
        } else {
            alert('ผิดพลาด: ' + (res.message || ''));
        }
    }, 'json');
}

function numberFormat(n) {
    return n ? new Intl.NumberFormat('th-TH', {minimumFractionDigits:2,maximumFractionDigits:2}).format(parseFloat(n)) : '0';
}
</script>

<?php
    if($case_id) $_SESSION['last_case_id'] = $case_id;
    include_once '../includes/footer.php';
?>