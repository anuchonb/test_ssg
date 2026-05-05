<?php
// views/approval.php
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
            <h2>✅ ผลอนุมัติ - Case #<?php echo $case_id; ?></h2>
            <a href="case_detail.php?id=<?php echo $case_id; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>วงเงินอนุมัติ</h5>
                <button class="btn btn-copy" onclick="copyApprovalToClipboard()">
                    <i class="fab fa-line"></i> Copy ส่ง LINE
                </button>
            </div>
            <div class="card-body">
                <form id="approvalForm">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">วงเงินรวม</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="ap_total_amount" step="0.01" readonly>
                                <span class="input-group-text">บาท</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">วงเงินห้อง</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="ap_room_amount" step="0.01">
                                <span class="input-group-text">บาท</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">วงเงินประกัน</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="ap_insurance_amount" step="0.01">
                                <span class="input-group-text">บาท</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">วงเงินเฟอร์นิเจอร์</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="ap_furniture_amount" step="0.01">
                                <span class="input-group-text">บาท</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันเซ็นสัญญา</label>
                            <input type="datetime-local" class="form-control" id="ap_contract_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันโอน</label>
                            <input type="date" class="form-control" id="ap_transfer_date">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" id="ap_note" rows="3"></textarea>
                    </div>
                    
                    <button type="button" class="btn btn-primary" onclick="saveApproval()">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Approval Summary Card -->
        <div class="card mt-3" id="approvalSummaryCard" style="display:none;">
            <div class="card-header bg-success text-white">
                <h5>📋 สรุปผลอนุมัติ</h5>
            </div>
            <div class="card-body" id="approvalSummary"></div>
        </div>
    </div>
</div>

<script src="../assets/js/approval.js"></script>
<?php include_once '../includes/footer.php'; ?>