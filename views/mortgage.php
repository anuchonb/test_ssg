<?php
// views/mortgage.php
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
            <h2>🏠 จำนอง - Case #<?php echo $case_id; ?></h2>
            <a href="case_detail.php?id=<?php echo $case_id; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>ข้อมูลการจำนอง</h5>
                    </div>
                    <div class="card-body">
                        <form id="mortgageForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">วันที่จำนอง <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="mortgage_date" required 
                                        value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ธนาคาร <span class="text-danger">*</span></label>
                                    <select class="form-select" id="mortgage_bank" required></select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ชื่อบัญชี</label>
                                    <input type="text" class="form-control" id="mortgage_account_name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">เลขที่บัญชี</label>
                                    <input type="text" class="form-control" id="mortgage_account_number">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">วงเงินอนุมัติ</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="mortgage_amount" step="0.01">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="saveMortgage()">
                                <i class="fas fa-save"></i> บันทึก
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>ประวัติการจำนอง</h5>
                    </div>
                    <div class="card-body" id="mortgageHistory"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/mortgage.js"></script>
<?php include_once '../includes/footer.php'; ?>