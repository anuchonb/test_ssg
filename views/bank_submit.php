<?php
// views/bank_submit.php
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
            <h2>🏦 ส่งธนาคาร - Case #<?php echo $case_id; ?></h2>
            <a href="case_detail.php?case_id=<?php echo $case_id; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>บันทึกการส่งธนาคาร</h5>
                    </div>
                    <div class="card-body">
                        <form id="bankSubmitForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ธนาคาร <span class="text-danger">*</span></label>
                                    <select class="form-select" id="bank_name" required></select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">วันที่ส่ง <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="submit_date" required 
                                        value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">หมายเหตุ</label>
                                <textarea class="form-control" id="bank_note" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">แนบไฟล์</label>
                                <input type="file" class="form-control" id="bank_files" multiple>
                            </div>
                            
                            <button type="button" class="btn btn-primary" onclick="saveBankSubmit()">
                                <i class="fas fa-paper-plane"></i> บันทึกการส่ง
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>ประวัติการส่งธนาคาร</h5>
                    </div>
                    <div class="card-body">
                        <div id="bankHistory"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/bank_submit.js"></script>
<?php include_once '../includes/footer.php'; ?>