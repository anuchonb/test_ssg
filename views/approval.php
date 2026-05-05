<?php
// views/approval.php
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
                <h2 class="mb-1">💰 ผลอนุมัติ   - Case #<?php echo $case_id; ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <?php if($case_id): ?>
                            <li class="breadcrumb-item"><a href="case_detail.php?case_id=<?php echo $case_id; ?>">Case #<?php echo $case_id; ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active">ผลอนุมัติ</li>
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
            <!-- กรณียังไม่มี case_id -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-double fa-4x text-muted mb-3"></i>
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
                    
                    <a href="cases.php" class="btn btn-outline-primary mt-3">
                        <i class="fas fa-list"></i> ดูรายการเคสทั้งหมด
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- มี case_id แล้ว -->
            <div class="row">
                <!-- ฟอร์มผลอนุมัติ -->
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-check-double"></i> แบบฟอร์มผลอนุมัติ</h5>
                            <button class="btn btn-sm btn-line" onclick="copyApprovalToClipboard()" title="Copy ส่ง LINE">
                                <i class="fab fa-line"></i> Copy ส่ง LINE
                            </button>
                        </div>
                        <div class="card-body">
                            <form id="approvalForm">
                                <input type="hidden" id="ap_case_id" value="<?php echo $case_id; ?>">
                                
                                <!-- วงเงิน -->
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="mb-3"><i class="fas fa-money-bill-wave"></i> วงเงินอนุมัติ</h6>
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">วงเงินห้อง</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control approval-amount" id="ap_room_amount" 
                                                           step="0.01" placeholder="0.00" oninput="calcApprovalTotal()">
                                                    <span class="input-group-text">฿</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">วงเงินประกัน</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control approval-amount" id="ap_insurance_amount" 
                                                           step="0.01" placeholder="0.00" oninput="calcApprovalTotal()">
                                                    <span class="input-group-text">฿</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">วงเงินเฟอร์นิเจอร์</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control approval-amount" id="ap_furniture_amount" 
                                                           step="0.01" placeholder="0.00" oninput="calcApprovalTotal()">
                                                    <span class="input-group-text">฿</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">วงเงินรวม</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="ap_total_amount" 
                                                           step="0.01" placeholder="0.00" readonly style="background:#e8f5e9;font-weight:bold;font-size:18px;">
                                                    <span class="input-group-text">฿</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- วันที่ -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">วันเซ็นสัญญา</label>
                                        <input type="date" class="form-control" id="ap_contract_date">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">วันโอน</label>
                                        <input type="date" class="form-control" id="ap_transfer_date">
                                    </div>
                                </div>
                                
                                <!-- หมายเหตุ -->
                                <div class="mb-3">
                                    <label class="form-label">หมายเหตุ</label>
                                    <textarea class="form-control" id="ap_note" rows="3" 
                                              placeholder="หมายเหตุเพิ่มเติม..."></textarea>
                                </div>
                                
                                <!-- ปุ่ม -->
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-lg" onclick="saveApproval()">
                                        <i class="fas fa-save"></i> บันทึกผลอนุมัติ
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                        <i class="fas fa-redo"></i> ล้างฟอร์ม
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- ข้อมูลเคส + สรุป -->
                <div class="col-lg-4">
                    <!-- ข้อมูลเคส -->
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
                    
                    <!-- สรุปผลอนุมัติ -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> สรุปผลอนุมัติ</h6>
                        </div>
                        <div class="card-body" id="approvalSummary">
                            <p class="text-muted text-center">กรอกข้อมูลด้านซ้ายเพื่อดูสรุป</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<script>
// ============ APPROVAL JS ============
const CASE_ID = <?php echo $case_id ? $case_id : 0; ?>;

$(document).ready(function() {
    if(CASE_ID) {
        loadCaseSummary();
        loadApprovalData();
    }
});

// ไปยังเคสที่ระบุ
function goToCase() {
    let id = $('#gotoCaseId').val();
    if(id) {
        window.location.href = 'approval.php?case_id=' + id;
    }
}

// โหลดข้อมูลเคส
function loadCaseSummary() {
    $.get('../api/cases/get.php', { id: CASE_ID }, function(res) {
        if(res.success && res.data) {
            let c = res.data;
            $('#caseInfoSummary').html(
                '<p><strong>ลูกค้า:</strong> ' + (c.customer_name || '-') + '</p>' +
                '<p><strong>เบอร์โทร:</strong> ' + (c.phone || '-') + '</p>' +
                '<p><strong>สถานะ:</strong> <span class="badge bg-primary">' + (c.status || '-') + '</span></p>' +
                '<a href="case_detail.php?case_id=' + CASE_ID + '" class="btn btn-sm btn-outline-info">ดูรายละเอียดเคส</a>'
            );
        }
    }, 'json');
}

// โหลดข้อมูลอนุมัติ (ถ้ามี)
function loadApprovalData() {
    $.get('../api/approval/get.php', { case_id: CASE_ID }, function(res) {
        if(res.success && res.data) {
            let d = res.data;
            $('#ap_room_amount').val(d.room_amount || '');
            $('#ap_insurance_amount').val(d.insurance_amount || '');
            $('#ap_furniture_amount').val(d.furniture_amount || '');
            $('#ap_total_amount').val(d.total_amount || '');
            $('#ap_contract_date').val(d.contract_date ? d.contract_date.split(' ')[0] : '');
            $('#ap_transfer_date').val(d.transfer_date || '');
            $('#ap_note').val(d.note || '');
            
            updateApprovalSummary();
        }
    }, 'json');
}

// คำนวณวงเงินรวม
function calcApprovalTotal() {
    let room = parseFloat($('#ap_room_amount').val()) || 0;
    let insurance = parseFloat($('#ap_insurance_amount').val()) || 0;
    let furniture = parseFloat($('#ap_furniture_amount').val()) || 0;
    let total = room + insurance + furniture;
    $('#ap_total_amount').val(total);
    
    updateApprovalSummary();
}

// อัปเดตสรุป
function updateApprovalSummary() {
    let room = parseFloat($('#ap_room_amount').val()) || 0;
    let insurance = parseFloat($('#ap_insurance_amount').val()) || 0;
    let furniture = parseFloat($('#ap_furniture_amount').val()) || 0;
    let total = room + insurance + furniture;
    
    if(total == 0) {
        $('#approvalSummary').html('<p class="text-muted text-center">กรอกข้อมูลด้านซ้ายเพื่อดูสรุป</p>');
        return;
    }
    
    let html = 
    '<table class="table table-sm table-bordered">' +
        '<tr><td>🏠 วงเงินห้อง</td><td class="text-end">' + numberFormat(room) + ' บาท</td></tr>' +
        '<tr><td>🛡️ วงเงินประกัน</td><td class="text-end">' + numberFormat(insurance) + ' บาท</td></tr>' +
        '<tr><td>🪑 วงเงินเฟอร์นิเจอร์</td><td class="text-end">' + numberFormat(furniture) + ' บาท</td></tr>' +
        '<tr class="table-success"><th>💰 วงเงินรวม</th><th class="text-end">' + numberFormat(total) + ' บาท</th></tr>';
    
    let contractDate = $('#ap_contract_date').val();
    let transferDate = $('#ap_transfer_date').val();
    
    if(contractDate) html += '<tr><td>📅 วันเซ็นสัญญา</td><td>' + formatThaiDate(contractDate) + '</td></tr>';
    if(transferDate) html += '<tr><td>🏠 วันโอน</td><td>' + formatThaiDate(transferDate) + '</td></tr>';
    
    html += '</table>';
    
    $('#approvalSummary').html(html);
}

// ✅ บันทึกผลอนุมัติ
function saveApproval() {
    let data = {
        case_id: CASE_ID,
        total_amount: $('#ap_total_amount').val() || 0,
        room_amount: $('#ap_room_amount').val() || 0,
        insurance_amount: $('#ap_insurance_amount').val() || 0,
        furniture_amount: $('#ap_furniture_amount').val() || 0,
        contract_date: $('#ap_contract_date').val(),
        transfer_date: $('#ap_transfer_date').val(),
        note: $('#ap_note').val() || ''
    };
    
    if(!confirm('บันทึกผลอนุมัติ?')) return;
    
    let btn = $('.btn-primary').last();
    let origText = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...').prop('disabled', true);
    
    $.ajax({
        url: '../api/approval/save.php',
        type: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        dataType: 'json',
        timeout: 10000,
        success: function(res) {
            btn.html(origText).prop('disabled', false);
            if(res.success) {
                Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ!', timer: 2000, showConfirmButton: false });
                updateApprovalSummary();
            } else {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message || '' });
            }
        },
        error: function(xhr) {
            btn.html(origText).prop('disabled', false);
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'Status: ' + xhr.status });
        }
    });
}

// Copy ส่ง LINE
function copyApprovalToClipboard() {
    let room = parseFloat($('#ap_room_amount').val()) || 0;
    let insurance = parseFloat($('#ap_insurance_amount').val()) || 0;
    let furniture = parseFloat($('#ap_furniture_amount').val()) || 0;
    let total = room + insurance + furniture;
    let transferDate = $('#ap_transfer_date').val() || '-';
    
    let text = 
        '📋 ผลอนุมัติ Case #' + CASE_ID + '\n' +
        '💰 วงเงินห้อง: ' + numberFormat(room) + ' บาท\n' +
        '🛡️ วงเงินประกัน: ' + numberFormat(insurance) + ' บาท\n' +
        '🪑 วงเงินเฟอร์นิเจอร์: ' + numberFormat(furniture) + ' บาท\n' +
        '💵 วงเงินรวม: ' + numberFormat(total) + ' บาท\n' +
        '📅 วันโอน: ' + transferDate;
    
    navigator.clipboard.writeText(text).then(function() {
        Swal.fire({ icon: 'success', title: 'คัดลอกแล้ว!', text: 'คัดลอกข้อความไปยังคลิปบอร์ด', timer: 1500, showConfirmButton: false });
    }).catch(function() {
        // Fallback
        let textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        Swal.fire({ icon: 'success', title: 'คัดลอกแล้ว!', timer: 1500, showConfirmButton: false });
    });
}

// ล้างฟอร์ม
function resetForm() {
    $('#ap_room_amount, #ap_insurance_amount, #ap_furniture_amount, #ap_total_amount').val('');
    $('#ap_contract_date, #ap_transfer_date').val('');
    $('#ap_note').val('');
    $('#approvalSummary').html('<p class="text-muted text-center">กรอกข้อมูลด้านซ้ายเพื่อดูสรุป</p>');
}

// Helpers
function numberFormat(n) {
    if(!n && n!==0) return '0';
    return new Intl.NumberFormat('th-TH', {minimumFractionDigits:2,maximumFractionDigits:2}).format(parseFloat(n));
}

function formatThaiDate(d) {
    if(!d) return '-';
    try {
        let date = new Date(d);
        return date.toLocaleDateString('th-TH', {year:'numeric',month:'long',day:'numeric'});
    } catch(e) { return d; }
}
</script>

<?php
if($case_id) {
    $_SESSION['last_case_id'] = $case_id;
}
?>

<?php include_once '../includes/footer.php'; ?>