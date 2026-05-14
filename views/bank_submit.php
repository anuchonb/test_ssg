<?php
// views/bank_submit.php
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

// รับ case_id จาก URL
$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;
?>

<div class="main-content">
    <div class="container-fluid">
        
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">🏦 ส่งธนาคาร - Case #<?php echo $case_id; ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <?php if($case_id): ?>
                            <li class="breadcrumb-item"><a href="case_detail.php?case_id=<?php echo $case_id; ?>">Case #<?php echo $case_id; ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active">ส่งธนาคาร</li>
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
            <!-- ถ้ายังไม่มี case_id ให้เลือก -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-university fa-4x text-muted mb-3"></i>
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
                <!-- ฟอร์มส่งธนาคาร -->
                <div class="col-lg-7">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-paper-plane"></i> บันทึกการส่งธนาคาร</h5>
                        </div>
                        <div class="card-body">
                            <form id="bankForm">
                                <input type="hidden" id="bank_case_id" value="<?php echo $case_id; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ธนาคาร <span class="text-danger">*</span></label>
                                        <select class="form-select" id="bank_name" required>
                                            <option value="">เลือกธนาคาร</option>
                                            <option value="ธนาคารกรุงเทพ">ธนาคารกรุงเทพ</option>
                                            <option value="ธนาคารกสิกรไทย">ธนาคารกสิกรไทย</option>
                                            <option value="ธนาคารกรุงไทย">ธนาคารกรุงไทย</option>
                                            <option value="ธนาคารไทยพาณิชย์">ธนาคารไทยพาณิชย์</option>
                                            <option value="ธนาคารออมสิน">ธนาคารออมสิน</option>
                                            <option value="ธนาคารอาคารสงเคราะห์">ธนาคารอาคารสงเคราะห์</option>
                                            <option value="ธนาคารกรุงศรีอยุธยา">ธนาคารกรุงศรีอยุธยา</option>
                                            <option value="ธนาคารทหารไทยธนชาต">ธนาคารทหารไทยธนชาต</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">วันที่ส่ง <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="submit_date" required 
                                               value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">หมายเหตุ</label>
                                    <textarea class="form-control" id="bank_note" rows="3" 
                                              placeholder="หมายเหตุเพิ่มเติม เช่น เอกสารที่ส่ง, เงื่อนไขพิเศษ..."></textarea>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary" onclick="saveBank()">
                                        <i class="fas fa-save"></i> บันทึก
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                        <i class="fas fa-redo"></i> ล้างฟอร์ม
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- ข้อมูลเคส + ประวัติ -->
                <div class="col-lg-5">
                    <!-- ข้อมูลเคส -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle"></i> ข้อมูลเคส #<?php echo $case_id; ?></h6>
                        </div>
                        <div class="card-body" id="caseInfoSummary">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary"></div> กำลังโหลด...
                            </div>
                        </div>
                    </div>
                    
                    <!-- ประวัติการส่ง -->
                    <div class="card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-history"></i> ประวัติการส่งธนาคาร</h6>
                            <span class="badge bg-light text-dark" id="historyCount">0</span>
                        </div>
                        <div class="card-body p-0">
                            <div id="bankHistory" style="max-height: 500px; overflow-y: auto;">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary"></div> กำลังโหลด...
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
// ============ BANK SUBMIT JS ============
const CASE_ID = <?php echo $case_id ? $case_id : 0; ?>;

$(document).ready(function() {
    if(CASE_ID) {
        loadCaseSummary();
        loadBankHistory();
    }
});

// ไปยังเคสที่ระบุ
function goToCase() {
    let id = $('#gotoCaseId').val();
    if(id) {
        window.location.href = 'bank_submit.php?case_id=' + id;
    }
}

// โหลดข้อมูลเคสสรุป
function loadCaseSummary() {
    $.get('../api/cases/get.php', { id: CASE_ID }, function(res) {
        if(res.success && res.data) {
            let c = res.data;
            let html = 
                '<p><strong>ลูกค้า:</strong> ' + (c.customer_name || '-') + '</p>' +
                '<p><strong>เบอร์โทร:</strong> ' + (c.phone || '-') + '</p>' +
                '<p><strong>สถานะ:</strong> <span class="badge bg-primary">' + (c.status || '-') + '</span></p>' +
                '<p><strong>เจ้าของเคส:</strong> ' + (c.owner_name || '-') + '</p>' +
                '<a href="case_detail.php?case_id=' + CASE_ID + '" class="btn btn-sm btn-outline-info">ดูรายละเอียดเคส</a>';
            $('#caseInfoSummary').html(html);
        }
    }, 'json');
}

// ✅ บันทึกการส่งธนาคาร
function saveBank() {
    let bankName = $('#bank_name').val();
    let submitDate = $('#submit_date').val();
    let note = $('#bank_note').val() || '';
    
    // Validate
    if(!bankName) {
        alert('กรุณาเลือกธนาคาร');
        $('#bank_name').focus();
        return;
    }
    
    if(!submitDate) {
        alert('กรุณาระบุวันที่ส่ง');
        $('#submit_date').focus();
        return;
    }
    
    // Confirm
    if(!confirm('บันทึกการส่งธนาคาร ' + bankName + '\nวันที่: ' + submitDate + '?')) {
        return;
    }
    
    // Disable button
    let btn = $('.btn-primary');
    let origText = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...').prop('disabled', true);
    
    $.ajax({
        url: '../api/bank/submit.php',
        type: 'POST',
        data: JSON.stringify({
            case_id: CASE_ID,
            bank_name: bankName,
            submit_date: submitDate,
            note: note
        }),
        contentType: 'application/json',
        dataType: 'json',
        timeout: 10000,
        success: function(res) {
            btn.html(origText).prop('disabled', false);
            console.log('Bank save response:', res);
            
            if(res.success) {
                // แสดง success
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ!',
                    text: res.message || 'ส่งธนาคารเรียบร้อย',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // ล้างฟอร์ม
                $('#bank_note').val('');
                
                // ✅ โหลดประวัติใหม่
                loadBankHistory();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด',
                    text: res.message || 'ไม่สามารถบันทึกได้'
                });
            }
        },
        error: function(xhr, status, error) {
            btn.html(origText).prop('disabled', false);
            console.error('Bank save error:', status, error);
            
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ (Status: ' + xhr.status + ')'
            });
        }
    });
}

// ✅ โหลดประวัติการส่งธนาคาร
function loadBankHistory() {
    // console.log('Loading bank history for case:', CASE_ID);
    
    $.ajax({
        url: '../api/bank/list.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        timeout: 10000,
        success: function(res) {
            console.log('Bank history response:', res);
            
            if(res.success && res.data && res.data.length > 0) {
                $('#historyCount').text(res.data.length);
                
                let html = '<div class="list-group list-group-flush">';
                
                res.data.forEach(function(b, index) {
                    let itemNumber = res.data.length - index;
                    
                    html += 
                    '<div class="list-group-item border-0 border-bottom py-3">' +
                        '<div class="d-flex">' +
                            // เลขข้อ
                            '<div class="me-3 text-center">' +
                                '<div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">' +
                                    '<strong>' + itemNumber + '</strong>' +
                                '</div>' +
                            '</div>' +
                            // เนื้อหา
                            '<div class="flex-grow-1">' +
                                '<div class="d-flex justify-content-between align-items-start">' +
                                    '<strong class="text-primary">' + (b.bank_name || 'ไม่ระบุ') + '</strong>' +
                                    '<small class="text-muted">' + formatDate(b.created_at) + '</small>' +
                                '</div>' +
                                '<p class="mb-1"><small><i class="far fa-calendar-alt"></i> วันที่ส่ง: ' + (b.submit_date || '-') + '</small></p>' +
                                (b.note ? '<p class="mb-0 text-muted small">' + b.note + '</p>' : '') +
                            '</div>' +
                        '</div>' +
                    '</div>';
                });
                
                html += '</div>';
                $('#bankHistory').html(html);
                
            } else {
                $('#historyCount').text('0');
                $('#bankHistory').html(
                    '<div class="text-center py-5">' +
                        '<i class="fas fa-university fa-4x text-muted mb-3"></i>' +
                        '<h6 class="text-muted">ยังไม่มีประวัติการส่งธนาคาร</h6>' +
                        '<p class="text-muted small">กรอกข้อมูลด้านซ้ายเพื่อบันทึกการส่งครั้งแรก</p>' +
                    '</div>'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('Bank history error:', status, error);
            console.error('Response:', xhr.responseText);
            
            $('#bankHistory').html(
                '<div class="text-center py-4 text-danger">' +
                    '<p>❌ ไม่สามารถโหลดประวัติได้</p>' +
                    '<button class="btn btn-sm btn-outline-danger" onclick="loadBankHistory()">' +
                        '<i class="fas fa-redo"></i> ลองใหม่</button>' +
                '</div>'
            );
        }
    });
}

// ล้างฟอร์ม
function resetForm() {
    $('#bank_name').val('');
    $('#bank_note').val('');
    $('#submit_date').val('<?php echo date('Y-m-d'); ?>');
    $('#bank_name').focus();
}

// Format date
function formatDate(dateString) {
    if(!dateString) return '-';
    try {
        let date = new Date(dateString);
        if(isNaN(date.getTime())) return dateString;
        return date.toLocaleDateString('th-TH', {
            year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
        });
    } catch(e) { return dateString; }
}
</script>

<?php
// ✅ เก็บ case_id ใน session
if($case_id) {
    $_SESSION['last_case_id'] = $case_id;
}
?>

<?php include_once '../includes/footer.php'; ?>