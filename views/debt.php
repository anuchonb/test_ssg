<?php
// views/debt.php
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
                <h2 class="mb-1">💳 ปิดหนี้  - Case #<?php echo $case_id; ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <?php if($case_id): ?>
                            <li class="breadcrumb-item"><a href="case_detail.php?case_id=<?php echo $case_id; ?>">Case #<?php echo $case_id; ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active">ปิดหนี้</li>
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
                    <i class="fas fa-money-bill-wave fa-4x text-muted mb-3"></i>
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
                <!-- ฟอร์มปิดหนี้ -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> บันทึกการปิดหนี้</h5>
                            <button class="btn btn-sm btn-line" onclick="copyDebtToClipboard()" title="Copy ส่ง LINE">
                                <i class="fab fa-line"></i> Copy ส่ง LINE
                            </button>
                        </div>
                        <div class="card-body">
                            <form id="debtForm">
                                <input type="hidden" id="debt_case_id" value="<?php echo $case_id; ?>">
                                
                                <!-- ข้อมูลการปิดหนี้ -->
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">วันที่ปิดหนี้ <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="debt_clear_date" 
                                               value="<?php echo date('Y-m-d\TH:i'); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">สถานที่</label>
                                        <input type="text" class="form-control" id="debt_location" 
                                               placeholder="เช่น ธนาคารกรุงเทพ สาขาสีลม">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">เจ้าหน้าที่</label>
                                        <input type="text" class="form-control" id="debt_staff_name" 
                                               placeholder="ชื่อเจ้าหน้าที่">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">หมายเหตุ</label>
                                    <textarea class="form-control" id="debt_note" rows="2" 
                                              placeholder="หมายเหตุเพิ่มเติม..."></textarea>
                                </div>
                                
                                <!-- รายการหนี้ -->
                                <hr>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">📋 รายการหนี้</h6>
                                    <button type="button" class="btn btn-sm btn-success" onclick="addDebtItem()">
                                        <i class="fas fa-plus"></i> เพิ่มรายการ
                                    </button>
                                </div>
                                
                                <div id="debtItemsContainer"></div>
                                
                                <!-- ยอดรวม -->
                                <div class="alert alert-info mt-3 d-flex justify-content-between align-items-center">
                                    <strong>💰 ยอดรวมหนี้ทั้งหมด:</strong>
                                    <h4 class="mb-0" id="debtTotal">0.00 บาท</h4>
                                </div>
                                
                                <!-- ปุ่มบันทึก -->
                                <button type="button" class="btn btn-primary btn-lg w-100 mt-2" onclick="saveDebt()">
                                    <i class="fas fa-save"></i> บันทึกการปิดหนี้
                                </button>
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
                                <div class="spinner-border spinner-border-sm"></div> กำลังโหลด...
                            </div>
                        </div>
                    </div>
                    
                    <!-- ✅ ประวัติการปิดหนี้ -->
                    <div class="card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-history"></i> ประวัติการปิดหนี้</h6>
                            <span class="badge bg-light text-dark" id="historyCount">0</span>
                        </div>
                        <div class="card-body p-0">
                            <div id="debtHistory" style="max-height: 500px; overflow-y: auto;">
                                <div class="text-center py-5">
                                    <div class="spinner-border spinner-border-sm"></div>
                                    <p class="mt-2">กำลังโหลด...</p>
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
// ============ DEBT JS ============
const CASE_ID = <?php echo $case_id ? $case_id : 0; ?>;
let debtItemCount = 0;

$(document).ready(function() {
    if(CASE_ID) {
        loadCaseSummary();
        loadDebtData();
        loadDebtHistory();
    }
});

function goToCase() {
    let id = $('#gotoCaseId').val();
    if(id) window.location.href = 'debt.php?case_id=' + id;
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

// ✅ โหลดข้อมูลหนี้ (ถ้ามี)
function loadDebtData() {
    $.get('../api/debt/get.php', { case_id: CASE_ID }, function(res) {
        if(res.success && res.data) {
            let d = res.data;
            $('#debt_clear_date').val(d.clear_date ? d.clear_date.replace(' ', 'T') : '');
            $('#debt_location').val(d.location || '');
            $('#debt_staff_name').val(d.staff_name || '');
            $('#debt_note').val(d.note || '');
            
            // ✅ โหลดรายการหนี้
            if(d.items && d.items.length > 0) {
                $('#debtItemsContainer').html('');
                debtItemCount = 0;
                d.items.forEach(function(item) {
                    addDebtItem(item.detail, item.amount);
                });
            }
        }
    }, 'json');
}

// ✅ โหลดประวัติการปิดหนี้
function loadDebtHistory() {
    console.log('Loading debt history for case:', CASE_ID);
    
    $.get('../api/debt/history.php', { case_id: CASE_ID }, function(res) {
        if(res.success && res.data && res.data.length > 0) {
            $('#historyCount').text(res.data.length);
            
            let html = '<div class="list-group list-group-flush">';
            
            res.data.forEach(function(d, index) {
                let itemNumber = res.data.length - index;
                
                html += 
                '<div class="list-group-item border-0 border-bottom py-3">' +
                    '<div class="d-flex">' +
                        '<div class="me-3 text-center">' +
                            '<div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">' +
                                '<strong>' + itemNumber + '</strong>' +
                            '</div>' +
                        '</div>' +
                        '<div class="flex-grow-1">' +
                            '<div class="d-flex justify-content-between align-items-start">' +
                                '<strong>💳 ปิดหนี้</strong>' +
                                '<small class="text-muted">' + formatDate(d.clear_date) + '</small>' +
                            '</div>' +
                            (d.location ? '<p class="mb-1 small"><i class="fas fa-map-marker-alt"></i> ' + d.location + '</p>' : '') +
                            (d.staff_name ? '<p class="mb-1 small"><i class="fas fa-user"></i> จนท.: ' + d.staff_name + '</p>' : '') +
                            (d.note ? '<p class="mb-0 text-muted small">' + d.note + '</p>' : '') +
                        '</div>' +
                    '</div>' +
                '</div>';
            });
            
            html += '</div>';
            $('#debtHistory').html(html);
            
        } else {
            $('#historyCount').text('0');
            $('#debtHistory').html(
                '<div class="text-center py-5 text-muted">' +
                    '<i class="fas fa-money-bill-wave fa-4x mb-3"></i>' +
                    '<h6>ยังไม่มีประวัติการปิดหนี้</h6>' +
                    '<p class="small">กรอกข้อมูลด้านซ้ายเพื่อบันทึกการปิดหนี้ครั้งแรก</p>' +
                '</div>'
            );
        }
    }, 'json').fail(function(xhr) {
        console.error('Load debt history error:', xhr.responseText);
        $('#debtHistory').html(
            '<div class="text-center py-4 text-danger">' +
                '<p>❌ ไม่สามารถโหลดประวัติได้</p>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="loadDebtHistory()">ลองใหม่</button>' +
            '</div>'
        );
    });
}

// เพิ่มรายการหนี้
function addDebtItem(detail, amount) {
    detail = detail || '';
    amount = amount || '';
    debtItemCount++;
    
    let html = 
    '<div class="row mb-2 align-items-end debt-item" id="debtRow' + debtItemCount + '">' +
        '<div class="col-md-5">' +
            '<label class="form-label small">รายละเอียด</label>' +
            '<input type="text" class="form-control form-control-sm" placeholder="เช่น บัตรเครดิต, สินเชื่อรถ" value="' + escapeHtml(detail) + '" id="debt_detail_' + debtItemCount + '">' +
        '</div>' +
        '<div class="col-md-4">' +
            '<label class="form-label small">จำนวนเงิน</label>' +
            '<div class="input-group input-group-sm">' +
                '<input type="number" class="form-control debt-amount" placeholder="0.00" step="0.01" value="' + amount + '" id="debt_amount_' + debtItemCount + '" oninput="calcDebtTotal()">' +
                '<span class="input-group-text">บาท</span>' +
            '</div>' +
        '</div>' +
        '<div class="col-md-2">' +
            '<button type="button" class="btn btn-danger btn-sm" onclick="$(\'#debtRow' + debtItemCount + '\').remove();calcDebtTotal();">' +
                '<i class="fas fa-trash"></i>' +
            '</button>' +
        '</div>' +
    '</div>';
    
    $('#debtItemsContainer').append(html);
    calcDebtTotal();
}

// คำนวณยอดรวม
function calcDebtTotal() {
    let total = 0;
    $('.debt-amount').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    $('#debtTotal').text(numberFormat(total) + ' บาท');
}

// ✅ บันทึกการปิดหนี้ (SweetAlert2 เต็มรูปแบบ)
function saveDebt() {
    let items = [];
    let hasError = false;

    $('.debt-item').each(function() {
        let detail = $(this).find('input[id^="debt_detail_"]').val();
        let amount = $(this).find('input[id^="debt_amount_"]').val();

        if (!detail || !amount) {
            hasError = true;
            return false;
        }
        items.push({ detail: detail, amount: parseFloat(amount) });
    });

    // ❌ ข้อมูลไม่ครบ
    if (hasError || items.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'ข้อมูลไม่ครบถ้วน',
            text: 'กรุณากรอกรายละเอียดและจำนวนเงินให้ครบทุกรายการ',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
        return;
    }

    // ✅ ยืนยันการบันทึก
    Swal.fire({
        title: 'ยืนยันการบันทึกปิดหนี้?',
        html: `
            <div class="text-start">
                <p>📋 <strong>จำนวนรายการ:</strong> ${items.length} รายการ</p>
                <p>💰 <strong>ยอดรวม:</strong> ${$('#debtTotal').text()}</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-save"></i> บันทึก',
        cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            let data = {
                case_id: CASE_ID,
                clear_date: $('#debt_clear_date').val(),
                location: $('#debt_location').val(),
                staff_name: $('#debt_staff_name').val(),
                note: $('#debt_note').val(),
                items: items
            };

            // Loading
            Swal.fire({
                title: 'กำลังบันทึก...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Call API
            $.ajax({
                url: '../api/debt/save.php',
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                dataType: 'json',
                timeout: 10000,
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ!',
                            text: 'บันทึกการปิดหนี้เรียบร้อยแล้ว',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadDebtHistory();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด',
                            text: res.message || 'ไม่สามารถบันทึกได้'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: 'ไม่สามารถบันทึกได้ (Status: ' + xhr.status + ')'
                    });
                }
            });
        }
    });
}

// Copy ส่ง LINE
function copyDebtToClipboard() {
    let text = '💳 ปิดหนี้ Case #' + <?php echo $case_id; ?> + '\n';
    $('.debt-item').each(function() {
        let detail = $(this).find('input[id^="debt_detail_"]').val();
        let amount = $(this).find('input[id^="debt_amount_"]').val();
        if(detail && amount) text += '📌 ' + detail + ': ' + numberFormat(amount) + ' บาท\n';
    });
    text += '💰 รวม: ' + $('#debtTotal').text();
    
    navigator.clipboard.writeText(text).then(function() {
        Swal.fire({ icon: 'success', title: 'คัดลอกแล้ว!', timer: 1500, showConfirmButton: false });
    });
}

// Helpers
function formatDate(d) {
    if(!d) return '-';
    try { return new Date(d).toLocaleDateString('th-TH',{year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'}); } catch(e) { return d; }
}
function numberFormat(n) {
    if(!n && n!==0) return '0';
    return new Intl.NumberFormat('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2}).format(parseFloat(n));
}
function escapeHtml(text) {
    let div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<style>
.btn-line { background:#25D366; color:white; border:none; padding:8px 20px; border-radius:20px; }
.btn-line:hover { background:#128C7E; color:white; }
</style>

<?php
if($case_id) {
    $_SESSION['last_case_id'] = $case_id;
}
?>

<?php include_once '../includes/footer.php'; ?>