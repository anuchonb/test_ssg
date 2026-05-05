<?php
// views/debt.php
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
            <h2>💳 ปิดหนี้ - Case #<?php echo $case_id; ?></h2>
            <a href="case_detail.php?id=<?php echo $case_id; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Debt Clearing Form -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>ข้อมูลการปิดหนี้</h5>
                    </div>
                    <div class="card-body">
                        <form id="debtForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">วันที่ปิดหนี้ <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="debt_clear_date" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">สถานที่</label>
                                    <input type="text" class="form-control" id="debt_location" 
                                        placeholder="เช่น ธนาคารกรุงเทพ สาขาสีลม">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">เจ้าหน้าที่</label>
                                    <input type="text" class="form-control" id="debt_staff_name">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">หมายเหตุ</label>
                                <textarea class="form-control" id="debt_note" rows="2"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Debt Items -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>รายการหนี้</h5>
                        <button class="btn btn-primary btn-sm" onclick="addDebtItem()">
                            <i class="fas fa-plus"></i> เพิ่มรายการ
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="debtItemsContainer">
                            <!-- Dynamic rows added here -->
                        </div>
                        <div class="mt-3">
                            <strong>ยอดรวมหนี้ทั้งหมด: </strong>
                            <span id="debtTotal" class="text-danger">0.00 บาท</span>
                        </div>
                        <button type="button" class="btn btn-success mt-3" onclick="saveDebt()">
                            <i class="fas fa-save"></i> บันทึกการปิดหนี้
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Copy to LINE -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>📋 สรุปหนี้</h5>
                    </div>
                    <div class="card-body">
                        <div id="debtSummary">
                            <p class="text-muted">กรุณาเพิ่มรายการหนี้</p>
                        </div>
                        <button class="btn btn-copy w-100" onclick="copyDebtToClipboard()">
                            <i class="fab fa-line"></i> Copy ส่ง LINE
                        </button>
                    </div>
                </div>
                
                <!-- Debt History -->
                <div class="card">
                    <div class="card-header">
                        <h5>ประวัติการปิดหนี้</h5>
                    </div>
                    <div class="card-body" id="debtHistory"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/debt.js"></script>
<?php include_once '../includes/footer.php'; ?>