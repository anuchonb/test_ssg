<?php
// views/inspection.php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../includes/header.php';
include_once '../includes/sidebar.php';

$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🔍 ตรวจห้อง - Case #<?php echo $case_id; ?></h2>
            <a href="case_detail.php?id=<?php echo $case_id; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>บันทึกการตรวจห้อง</h5>
                        <button class="btn btn-light btn-sm" onclick="showInspectionForm()">
                            <i class="fas fa-plus"></i> เพิ่มการตรวจ
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="inspectionList">
                            <p class="text-muted">กำลังโหลด...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inspection Modal -->
<div class="modal fade" id="inspectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">เพิ่มการตรวจห้อง</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="inspectionForm">
                    <input type="hidden" id="inspection_id">
                    <div class="mb-3">
                        <label class="form-label">ครั้งที่ <span class="text-danger">*</span></label>
                        <select class="form-select" id="inspection_round" required>
                            <option value="1">ครั้งที่ 1</option>
                            <option value="2">ครั้งที่ 2</option>
                            <option value="3">ครั้งที่ 3</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">วันที่ตรวจ <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="inspection_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                        <select class="form-select" id="inspection_status" required>
                            <option value="pass">✅ ผ่าน</option>
                            <option value="fail">❌ ไม่ผ่าน</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" id="inspection_note" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รูปภาพ</label>
                        <input type="file" class="form-control" id="inspection_files" multiple accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="saveInspection()">
                    <i class="fas fa-save"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/inspection.js"></script>
<?php include_once '../includes/footer.php'; ?>