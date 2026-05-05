<?php
// views/follow.php
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
            <h2>📞 ติดตามลูกค้า - Case #<?php echo $case_id; ?></h2>
            <div>
                <a href="case_detail.php?id=<?php echo $case_id; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> กลับหน้าเคส
                </a>
                <button class="btn btn-primary ms-2" onclick="showFollowForm()">
                    <i class="fas fa-plus"></i> เพิ่มการติดตาม
                </button>
            </div>
        </div>

        <!-- Follow Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>ติดตามทั้งหมด</h6>
                        <h3 id="totalFollows">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>สนใจ</h6>
                        <h3 id="interestedCount">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>อยู่ระหว่างตัดสินใจ</h6>
                        <h3 id="pendingCount">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6>ไม่สนใจ/ยกเลิก</h6>
                        <h3 id="rejectedCount">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Follow Timeline -->
        <div class="card">
            <div class="card-header">
                <h5>📋 Timeline การติดตาม</h5>
            </div>
            <div class="card-body">
                <div id="followTimeline">
                    <div class="text-center py-5">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Follow History Table -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>📊 ประวัติการติดตาม</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="followTable">
                        <thead>
                            <tr>
                                <th>ครั้งที่</th>
                                <th>วันที่</th>
                                <th>สถานะ</th>
                                <th>บันทึก</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="followTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Follow Modal -->
<div class="modal fade" id="followFormModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="followModalTitle">เพิ่มการติดตาม</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="followForm">
                    <input type="hidden" id="follow_id">
                    <input type="hidden" id="follow_case_id" value="<?php echo $case_id; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ครั้งที่ <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="follow_step" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                            <select class="form-select" id="follow_status" required>
                                <option value="">เลือกสถานะ</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">รายละเอียดการติดตาม</label>
                        <textarea class="form-control" id="follow_note" rows="4" 
                            placeholder="บันทึกรายละเอียดการติดต่อ..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ไฟล์แนบ</label>
                        <input type="file" class="form-control" id="follow_file" multiple>
                        <small class="text-muted">รองรับไฟล์ .jpg, .png, .pdf (สูงสุด 5MB)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">นัดหมายครั้งต่อไป</label>
                        <input type="datetime-local" class="form-control" id="follow_next_date">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="saveFollow()">
                    <i class="fas fa-save"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/follow.js"></script>
<?php include_once '../includes/footer.php'; ?>