<?php
// views/pre_approve.php
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
            <h2>📋 Pre-Approve - Case #<?php echo $case_id; ?></h2>
            <a href="case_detail.php?id=<?php echo $case_id; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>

        <div class="row">
            <!-- Pre-Approve Form -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>แบบฟอร์ม Pre-Approve</h5>
                    </div>
                    <div class="card-body">
                        <form id="preapproveForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                                    <select class="form-select" id="pa_status" required>
                                        <option value="processing">กำลังดำเนินการ</option>
                                        <option value="approved">อนุมัติ</option>
                                        <option value="rejected">ปฏิเสธ</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">วงเงินอนุมัติ</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="pa_amount" step="0.01">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">วงเงินห้อง</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="pa_room_amount" step="0.01">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">วงเงินประกัน</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="pa_insurance_amount" step="0.01">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">วงเงินเฟอร์นิเจอร์</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="pa_furniture_amount" step="0.01">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">วันที่ทำสัญญา</label>
                                <input type="date" class="form-control" id="pa_contract_date">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">วันที่โอน</label>
                                <input type="date" class="form-control" id="pa_transfer_date">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">หมายเหตุ</label>
                                <textarea class="form-control" id="pa_note" rows="3"></textarea>
                            </div>
                            
                            <button type="button" class="btn btn-primary" onclick="savePreapprove()">
                                <i class="fas fa-save"></i> บันทึก
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Customer Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>ข้อมูลลูกค้า</h5>
                    </div>
                    <div class="card-body" id="customerSummary"></div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>ประวัติ Pre-Approve</h5>
                    </div>
                    <div class="card-body" id="preapproveHistory"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/pre_approve.js"></script>
<?php include_once '../includes/footer.php'; ?>