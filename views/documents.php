<?php
// views/documents.php
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
                <h2 class="mb-1">📄 เอกสาร  - Case #<?php echo $case_id; ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <?php if($case_id): ?>
                            <li class="breadcrumb-item"><a href="case_detail.php?case_id=<?php echo $case_id; ?>">Case #<?php echo $case_id; ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active">เอกสาร</li>
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
                    <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
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
                <!-- ข้อมูลเคส -->
                <div class="col-lg-4">
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
                    
                    <!-- สรุปเอกสาร -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> สรุปสถานะเอกสาร</h6>
                        </div>
                        <div class="card-body" id="docStatusSummary">
                            <p class="text-muted">จัดการสถานะเอกสารด้านขวา</p>
                        </div>
                    </div>
                </div>
                
                <!-- ฟอร์มจัดการเอกสาร + อัปโหลดไฟล์ -->
                <div class="col-lg-8">
                    <!-- สถานะเอกสาร -->
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> สถานะเอกสาร</h5>
                        </div>
                        <div class="card-body">
                            <form id="docStatusForm">
                                <input type="hidden" id="doc_case_id" value="<?php echo $case_id; ?>">
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">เอกสารขั้นที่ 1</label>
                                        <select class="form-select" id="doc_status_1" onchange="updateDocSummary()">
                                            <option value="">เลือก</option>
                                            <option value="ผ่าน">✅ ผ่าน</option>
                                            <option value="ไม่ผ่าน">❌ ไม่ผ่าน</option>
                                            <option value="รอดำเนินการ">⏳ รอดำเนินการ</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">เอกสารขั้นที่ 2</label>
                                        <select class="form-select" id="doc_status_2" onchange="updateDocSummary()">
                                            <option value="">เลือก</option>
                                            <option value="ผ่าน">✅ ผ่าน</option>
                                            <option value="ไม่ผ่าน">❌ ไม่ผ่าน</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">เอกสารขั้นที่ 3</label>
                                        <select class="form-select" id="doc_status_3" onchange="updateDocSummary()">
                                            <option value="">เลือก</option>
                                            <option value="เรียบร้อย">✅ เรียบร้อย</option>
                                            <option value="ยกเลิก">❌ ยกเลิก</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">ธนาคาร</label>
                                        <input type="text" class="form-control" id="doc_bank_name" placeholder="ชื่อธนาคาร">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">เลขที่บัญชี</label>
                                        <input type="text" class="form-control" id="doc_bank_account" placeholder="เลขบัญชี">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Pre-Check</label>
                                        <select class="form-select" id="doc_precheck">
                                            <option value="">เลือก</option>
                                            <option value="ผ่าน">✅ ผ่าน</option>
                                            <option value="ไม่ผ่าน">❌ ไม่ผ่าน</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">สถานะปิดหนี้</label>
                                        <select class="form-select" id="doc_debt_close">
                                            <option value="">เลือก</option>
                                            <option value="done">✅ ปิดแล้ว</option>
                                            <option value="not_done">❌ ยังไม่ปิด</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-primary" onclick="saveDocumentStatus()">
                                    <i class="fas fa-save"></i> บันทึกสถานะเอกสาร
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- อัปโหลดไฟล์ -->
                    <div class="card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-upload"></i> อัปโหลดเอกสาร</h5>
                            <span class="badge bg-light text-dark" id="fileCount">0</span>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <label class="form-label">ประเภทเอกสาร <span class="text-danger">*</span></label>
                                    <select class="form-select" id="doc_type">
                                        <option value="">เลือกประเภทเอกสาร</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">เลือกไฟล์ <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="uploadFile">
                                    <small class="text-muted">รองรับ .jpg, .png, .pdf (สูงสุด 5MB)</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button class="btn btn-success w-100" onclick="uploadFile()">
                                        <i class="fas fa-upload"></i> อัปโหลด
                                    </button>
                                </div>
                            </div>
                            
                            <!-- รายการไฟล์ -->
                            <h6 class="mb-3">📁 เอกสารที่อัปโหลด</h6>
                            <div id="fileList" style="max-height: 400px; overflow-y: auto;">
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
// ============ DOCUMENTS JS ============
const CASE_ID = <?php echo $case_id ? $case_id : 0; ?>;

$(document).ready(function() {
    if(CASE_ID) {
        loadCaseSummary();
        loadDocumentTypes();
        loadDocumentStatus();
        loadFileList();
    }
});

// ไปยังเคสที่ระบุ
function goToCase() {
    let id = $('#gotoCaseId').val();
    if(id) {
        window.location.href = 'documents.php?case_id=' + id;
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

// โหลดประเภทเอกสาร
function loadDocumentTypes() {
    $.get('../api/master/get.php', { type: 'document_type' }, function(data) {
        let options = '<option value="">เลือกประเภทเอกสาร</option>';
        if(Array.isArray(data)) {
            data.forEach(function(item) {
                options += '<option value="' + item.value + '">📄 ' + item.value + '</option>';
            });
        }
        // Fallback
        if(options === '<option value="">เลือกประเภทเอกสาร</option>') {
            options += '<option value="บัตรประชาชน">🪪 บัตรประชาชน</option>';
            options += '<option value="ทะเบียนบ้าน">🏠 ทะเบียนบ้าน</option>';
            options += '<option value="สลิปเงินเดือน">📄 สลิปเงินเดือน</option>';
            options += '<option value="Statement">📊 Statement</option>';
            options += '<option value="หนังสือรับรองเงินเดือน">📝 หนังสือรับรองเงินเดือน</option>';
            options += '<option value="สัญญาซื้อขาย">✍️ สัญญาซื้อขาย</option>';
            options += '<option value="สำเนาบัญชีธนาคาร">🏦 สำเนาบัญชีธนาคาร</option>';
            options += '<option value="อื่นๆ">📎 อื่นๆ</option>';
        }
        $('#doc_type').html(options);
    });
}

// โหลดสถานะเอกสาร
function loadDocumentStatus() {
    $.get('../api/document/get.php', { case_id: CASE_ID }, function(res) {
        if(res.success && res.data) {
            let d = res.data;
            $('#doc_status_1').val(d.doc_status_1 || '');
            $('#doc_status_2').val(d.doc_status_2 || '');
            $('#doc_status_3').val(d.doc_status_3 || '');
            $('#doc_bank_name').val(d.bank_name || '');
            $('#doc_bank_account').val(d.bank_account || '');
            $('#doc_precheck').val(d.precheck_status || '');
            $('#doc_debt_close').val(d.debt_close_status || '');
            updateDocSummary();
        }
    }, 'json');
}

// อัปเดตสรุปสถานะเอกสาร
function updateDocSummary() {
    let s1 = $('#doc_status_1').val() || '-';
    let s2 = $('#doc_status_2').val() || '-';
    let s3 = $('#doc_status_3').val() || '-';
    
    let html = 
    '<table class="table table-sm table-bordered mb-0">' +
        '<tr><td>เอกสารขั้นที่ 1</td><td>' + getStatusBadge(s1) + '</td></tr>' +
        '<tr><td>เอกสารขั้นที่ 2</td><td>' + getStatusBadge(s2) + '</td></tr>' +
        '<tr><td>เอกสารขั้นที่ 3</td><td>' + getStatusBadge(s3) + '</td></tr>' +
    '</table>';
    
    $('#docStatusSummary').html(html);
}

function getStatusBadge(status) {
    if(status === 'ผ่าน' || status === 'เรียบร้อย') return '<span class="badge bg-success">✅ ' + status + '</span>';
    if(status === 'ไม่ผ่าน' || status === 'ยกเลิก') return '<span class="badge bg-danger">❌ ' + status + '</span>';
    if(status === 'รอดำเนินการ') return '<span class="badge bg-warning text-dark">⏳ ' + status + '</span>';
    return '<span class="badge bg-secondary">' + status + '</span>';
}

// บันทึกสถานะเอกสาร
function saveDocumentStatus() {
    let data = {
        case_id: CASE_ID,
        doc_status_1: $('#doc_status_1').val(),
        doc_status_2: $('#doc_status_2').val(),
        doc_status_3: $('#doc_status_3').val(),
        bank_name: $('#doc_bank_name').val(),
        bank_account: $('#doc_bank_account').val(),
        precheck_status: $('#doc_precheck').val(),
        debt_close_status: $('#doc_debt_close').val()
    };
    
    if(!confirm('บันทึกสถานะเอกสาร?')) return;
    
    $.post('../api/document/update.php', JSON.stringify(data), function(res) {
        if(res.success) {
            alert('บันทึกสำเร็จ!');
            updateDocSummary();
        } else {
            alert('ผิดพลาด: ' + (res.message || ''));
        }
    }, 'json').fail(function(xhr) {
        alert('ไม่สามารถบันทึกได้ (Status: ' + xhr.status + ')');
    });
}

// อัปโหลดไฟล์
function uploadFile() {
    let docType = $('#doc_type').val();
    let fileInput = $('#uploadFile')[0];
    
    if(!docType) { alert('กรุณาเลือกประเภทเอกสาร'); return; }
    if(!fileInput.files || !fileInput.files[0]) { alert('กรุณาเลือกไฟล์'); return; }
    
    let file = fileInput.files[0];
    
    // ตรวจสอบ
    let allowed = ['jpg','jpeg','png','pdf','doc','docx','xls','xlsx'];
    let ext = file.name.split('.').pop().toLowerCase();
    if(!allowed.includes(ext)) {
        alert('ประเภทไฟล์ไม่ถูกต้อง (อนุญาต: ' + allowed.join(', ') + ')');
        return;
    }
    if(file.size > 5*1024*1024) {
        alert('ไฟล์ขนาดเกิน 5MB');
        return;
    }
    
    let formData = new FormData();
    formData.append('case_id', CASE_ID);
    formData.append('file_type', docType);
    formData.append('file', file);
    
    let btn = $('.btn-success');
    let origText = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);
    
    $.ajax({
        url: '../api/file/upload.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            btn.html(origText).prop('disabled', false);
            if(res.success) {
                alert('อัปโหลดสำเร็จ! 📄');
                $('#uploadFile').val('');
                loadFileList();
            } else {
                alert('ผิดพลาด: ' + (res.message || ''));
            }
        },
        error: function(xhr) {
            btn.html(origText).prop('disabled', false);
            alert('ไม่สามารถอัปโหลดได้ (Status: ' + xhr.status + ')');
        }
    });
}

// โหลดรายการไฟล์
function loadFileList() {
    $.get('../api/file/list.php', { case_id: CASE_ID }, function(res) {
        if(res.success && res.data && res.data.length > 0) {
            $('#fileCount').text(res.data.length);
            
            let html = '<div class="list-group list-group-flush">';
            res.data.forEach(function(f) {
                let isImage = f.file_path && f.file_path.match(/\.(jpg|jpeg|png|gif)$/i);
                
                html += 
                '<div class="list-group-item py-2 d-flex justify-content-between align-items-center">' +
                    '<div class="d-flex align-items-center">' +
                        '<span class="badge bg-' + getFileColor(f.file_type) + ' me-2" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">' +
                            '<i class="fas fa-' + getFileIcon(f.file_type) + '"></i>' +
                        '</span>' +
                        '<div>' +
                            '<small class="fw-bold">' + (f.file_type || 'ไม่ระบุ') + '</small>' +
                            '<br><small class="text-muted">' + formatDate(f.created_at) + '</small>' +
                        '</div>' +
                    '</div>' +
                    '<div class="btn-group btn-group-sm">';
                
                if(isImage) {
                    html += '<button class="btn btn-outline-info btn-sm" onclick="openImageViewer(\'../' + f.file_path + '\')" title="ดูรูป"><i class="fas fa-eye"></i></button>';
                } else {
                    html += '<a href="../' + f.file_path + '" target="_blank" class="btn btn-outline-info btn-sm" title="ดู"><i class="fas fa-eye"></i></a>';
                }
                
                html += '<button class="btn btn-outline-danger btn-sm" onclick="deleteFile(' + f.id + ')" title="ลบ"><i class="fas fa-trash"></i></button>' +
                    '</div>' +
                '</div>';
            });
            html += '</div>';
            $('#fileList').html(html);
        } else {
            $('#fileCount').text('0');
            $('#fileList').html(
                '<div class="text-center py-5 text-muted">' +
                    '<i class="fas fa-folder-open fa-4x mb-3"></i>' +
                    '<h6>ยังไม่มีเอกสาร</h6>' +
                    '<p class="small">เลือกประเภทเอกสารและไฟล์ด้านบนเพื่ออัปโหลด</p>' +
                '</div>'
            );
        }
    }, 'json').fail(function() {
        $('#fileList').html('<p class="text-danger text-center">❌ โหลดไม่สำเร็จ</p>');
    });
}

// ลบไฟล์
function deleteFile(id) {
    if(!confirm('ยืนยันการลบเอกสารนี้?')) return;
    $.post('../api/file/delete.php', JSON.stringify({id:id}), function(res) {
        if(res.success) loadFileList();
        else alert('ผิดพลาด: ' + (res.message || ''));
    }, 'json');
}

// เปิดรูปภาพ
function openImageViewer(src) {
    $('#viewerImage').attr('src', src);
    $('#btnViewFull').attr('href', src);
    $('#btnDownload').attr('href', src);
    let modal = new bootstrap.Modal(document.getElementById('imageViewerModal'));
    modal.show();
}

// Helpers
function getFileIcon(type) {
    if(!type) return 'file';
    if(type.includes('บัตร')) return 'id-card';
    if(type.includes('ทะเบียน')) return 'home';
    if(type.includes('สลิป')||type.includes('Statement')) return 'file-invoice';
    if(type.includes('รับรอง')) return 'file-contract';
    if(type.includes('สัญญา')) return 'file-signature';
    if(type.includes('บัญชี')||type.includes('ธนาคาร')) return 'university';
    return 'file-alt';
}

function getFileColor(type) {
    if(!type) return 'secondary';
    if(type.includes('บัตร')) return 'primary';
    if(type.includes('ทะเบียน')) return 'success';
    if(type.includes('สลิป')||type.includes('Statement')) return 'info';
    if(type.includes('รับรอง')) return 'warning';
    if(type.includes('สัญญา')) return 'danger';
    return 'secondary';
}

function formatDate(d) {
    if(!d) return '-';
    try { return new Date(d).toLocaleDateString('th-TH',{year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'}); } catch(e) { return d; }
}
</script>

<style>
.cursor-pointer { cursor: pointer; }
</style>

<?php
if($case_id) {
    $_SESSION['last_case_id'] = $case_id;
}
?>

<?php include_once '../includes/footer.php'; ?>