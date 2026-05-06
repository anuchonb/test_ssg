<?php
// views/inspection.php
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
                <h2 class="mb-1">🔍 ตรวจห้อง   - Case #<?php echo $case_id; ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <?php if($case_id): ?>
                            <li class="breadcrumb-item"><a href="case_detail.php?case_id=<?php echo $case_id; ?>">Case #<?php echo $case_id; ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active">ตรวจห้อง</li>
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
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
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
            <!-- มี case_id แล้ว -->
            <div class="row">
                <!-- ฟอร์มเพิ่มการตรวจ -->
                <div class="col-lg-5">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-plus-circle"></i> เพิ่มการตรวจห้อง</h5>
                        </div>
                        <div class="card-body">
                            <form id="inspectionForm" enctype="multipart/form-data">
                                <input type="hidden" id="insp_case_id" value="<?php echo $case_id; ?>">
                                <input type="hidden" id="inspection_id">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ครั้งที่ <span class="text-danger">*</span></label>
                                        <select class="form-select" id="inspection_round">
                                            <option value="1">ครั้งที่ 1</option>
                                            <option value="2">ครั้งที่ 2</option>
                                            <option value="3">ครั้งที่ 3</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">วันที่ตรวจ <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="inspection_date" 
                                               value="<?php echo date('Y-m-d\TH:i'); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                                    <select class="form-select" id="inspection_status">
                                        <option value="pass">✅ ผ่าน</option>
                                        <option value="fail">❌ ไม่ผ่าน (พบ defect)</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">หมายเหตุ / จุดที่พบ</label>
                                    <textarea class="form-control" id="inspection_note" rows="3" 
                                              placeholder="บันทึกจุดที่ตรวจพบ เช่น รอยร้าวที่ผนัง, สีไม่เรียบร้อย..."></textarea>
                                </div>
                                
                                <!-- อัปโหลดรูปภาพ -->
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="mb-2"><i class="fas fa-camera"></i> รูปภาพประกอบ</h6>
                                        <div class="mb-2">
                                            <label class="form-label"><small>อัปโหลดรูปภาพ (สูงสุด 5 รูป)</small></label>
                                            <input type="file" class="form-control" id="inspection_photos" 
                                                   accept="image/*" multiple onchange="previewImages()">
                                            <small class="text-muted">รองรับ .jpg, .png (สูงสุด 5MB ต่อรูป)</small>
                                        </div>
                                        <!-- พรีวิว -->
                                        <div id="imagePreview" class="row mt-2"></div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-primary btn-lg w-100" onclick="saveInspection()">
                                    <i class="fas fa-save"></i> บันทึกผลตรวจ
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- ข้อมูลเคส -->
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle"></i> ข้อมูลเคส #<?php echo $case_id; ?></h6>
                        </div>
                        <div class="card-body" id="caseInfoSummary">
                            <div class="text-center py-2">
                                <div class="spinner-border spinner-border-sm"></div> กำลังโหลด...
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ประวัติการตรวจ -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-history"></i> ประวัติการตรวจห้อง</h5>
                            <span class="badge bg-light text-dark" id="historyCount">0</span>
                        </div>
                        <div class="card-body">
                            <div id="inspectionList">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary"></div>
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

<!-- View Image Modal -->
<div class="modal fade" id="imageViewerModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img src="" class="img-fluid" style="max-height: 85vh;" id="viewerImage">
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <a href="#" target="_blank" class="btn btn-light btn-sm" id="btnViewFull"><i class="fas fa-external-link-alt"></i> ดูรูปเต็ม</a>
                <a href="#" download class="btn btn-primary btn-sm" id="btnDownload"><i class="fas fa-download"></i> ดาวน์โหลด</a>
            </div>
        </div>
    </div>
</div>

<script>
// ============ INSPECTION JS ============
const CASE_ID = <?php echo $case_id ? $case_id : 0; ?>;

$(document).ready(function() {
    if(CASE_ID) {
        loadCaseSummary();
        loadInspectionList();
    }
});

// ไปยังเคส
function goToCase() {
    let id = $('#gotoCaseId').val();
    if(id) window.location.href = 'inspection.php?case_id=' + id;
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

// พรีวิวรูปภาพ
function previewImages() {
    let files = $('#inspection_photos')[0].files;
    let html = '';
    
    if(files.length > 5) {
        alert('อัปโหลดได้สูงสุด 5 รูป');
        $('#inspection_photos').val('');
        return;
    }
    
    for(let i = 0; i < files.length; i++) {
        let file = files[i];
        if(!file.type.match('image.*')) continue;
        if(file.size > 5*1024*1024) { alert('ไฟล์ ' + file.name + ' เกิน 5MB'); continue; }
        
        let reader = new FileReader();
        reader.onload = (function(f, index) {
            return function(e) {
                html += 
                '<div class="col-4 mb-2">' +
                    '<div class="position-relative">' +
                        '<img src="' + e.target.result + '" class="img-thumbnail" style="height:80px;width:100%;object-fit:cover;">' +
                        '<span class="position-absolute top-0 end-0 badge bg-primary m-1">' + (index+1) + '</span>' +
                    '</div>' +
                '</div>';
                $('#imagePreview').html(html);
            };
        })(file, i);
        reader.readAsDataURL(file);
    }
}

// ✅ บันทึกการตรวจ
function saveInspection() {
    let round = $('#inspection_round').val();
    let date = $('#inspection_date').val();
    let status = $('#inspection_status').val();
    let note = $('#inspection_note').val().trim();
    
    if(!date) { alert('กรุณาระบุวันที่ตรวจ'); return; }
    if(!confirm('บันทึกผลตรวจห้องครั้งที่ ' + round + '?\nสถานะ: ' + (status==='pass'?'ผ่าน':'ไม่ผ่าน'))) return;
    
    let formData = new FormData();
    formData.append('case_id', CASE_ID);
    formData.append('round', round);
    formData.append('inspect_date', date);
    formData.append('status', status);
    formData.append('note', note);
    
    // เพิ่มรูปภาพ
    let files = $('#inspection_photos')[0].files;
    for(let i = 0; i < files.length; i++) {
        formData.append('photos[]', files[i]);
    }
    
    let btn = $('.btn-primary').last();
    let origText = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...').prop('disabled', true);
    
    $.ajax({
        url: '../api/inspection/save.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        timeout: 30000,
        success: function(res) {
            btn.html(origText).prop('disabled', false);
            if(res.success) {
                Swal.fire({ icon:'success', title:'บันทึกสำเร็จ!', timer:2000, showConfirmButton:false });
                // ล้างฟอร์ม
                $('#inspection_note').val('');
                $('#inspection_photos').val('');
                $('#imagePreview').html('');
                // โหลดรายการใหม่
                loadInspectionList();
            } else {
                Swal.fire({ icon:'error', title:'ผิดพลาด', text:res.message||'' });
            }
        },
        error: function(xhr) {
            btn.html(origText).prop('disabled', false);
            Swal.fire({ icon:'error', title:'ผิดพลาด', text:'Status: ' + xhr.status });
        }
    });
}

// ✅ โหลดประวัติการตรวจ
function loadInspectionList() {
    $.get('../api/inspection/list.php', { case_id: CASE_ID }, function(res) {
        if(res.success && res.data && res.data.length > 0) {
            $('#historyCount').text(res.data.length);
            
            let html = '<div class="row">';
            
            res.data.forEach(function(item) {
                let statusBadge = item.status === 'pass' 
                    ? '<span class="badge bg-success">✅ ผ่าน</span>' 
                    : '<span class="badge bg-danger">❌ ไม่ผ่าน</span>';
                
                // รูปภาพ
                let photosHtml = '';
                if(item.photos && item.photos.length > 0) {
                    photosHtml = '<div class="row mt-2">';
                    item.photos.forEach(function(photo) {
                        photosHtml += 
                        '<div class="col-md-3 col-sm-4 col-6 mb-2">' +
                            '<a href="#" onclick="openImageViewer(\'../' + photo + '\');return false;">' +
                                '<img src="../' + photo + '" class="img-thumbnail" style="height:80px;width:100%;object-fit:cover;" onerror="this.src=\'../assets/img/no-image.png\'">' +
                            '</a>' +
                        '</div>';
                    });
                    photosHtml += '</div>';
                }
                
                html += 
                '<div class="col-md-6 mb-3">' +
                    '<div class="card h-100">' +
                        '<div class="card-header d-flex justify-content-between align-items-center py-2">' +
                            '<h6 class="mb-0">🔍 ครั้งที่ ' + item.round + '</h6>' +
                            statusBadge +
                        '</div>' +
                        '<div class="card-body py-2">' +
                            '<p class="mb-1"><small><i class="far fa-calendar-alt"></i> ' + formatDate(item.inspect_date) + '</small></p>' +
                            (item.note ? '<p class="mb-1"><small>' + item.note + '</small></p>' : '') +
                            photosHtml +
                        '</div>' +
                    '</div>' +
                '</div>';
            });
            
            html += '</div>';
            $('#inspectionList').html(html);
        } else {
            $('#historyCount').text('0');
            $('#inspectionList').html(
                '<div class="text-center py-5 text-muted">' +
                    '<i class="fas fa-search fa-4x mb-3"></i>' +
                    '<h5>ยังไม่มีการตรวจห้อง</h5>' +
                    '<p>กรอกข้อมูลด้านซ้ายเพื่อบันทึกการตรวจครั้งแรก</p>' +
                '</div>'
            );
        }
    }, 'json').fail(function() {
        $('#inspectionList').html('<div class="alert alert-danger">❌ โหลดข้อมูลไม่สำเร็จ</div>');
    });
}

// เปิดรูปภาพ
function openImageViewer(src) {
    $('#viewerImage').attr('src', src);
    $('#btnViewFull').attr('href', src);
    $('#btnDownload').attr('href', src);
    let modal = new bootstrap.Modal(document.getElementById('imageViewerModal'));
    modal.show();
}

function formatDate(d) {
    if(!d) return '-';
    try { return new Date(d).toLocaleDateString('th-TH',{year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'}); } catch(e) { return d; }
}
</script>

<style>
.cursor-pointer { cursor: pointer; }
.btn-line { background:#25D366; color:white; border:none; padding:8px 20px; border-radius:20px; }
.btn-line:hover { background:#128C7E; color:white; }
</style>

<?php
if($case_id) {
    $_SESSION['last_case_id'] = $case_id;
}
?>

<?php include_once '../includes/footer.php'; ?>