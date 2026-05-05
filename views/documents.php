<?php
// views/documents.php
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
            <h2>📄 เอกสาร - Case #<?php echo $case_id; ?></h2>
            <a href="case_detail.php?case_id=<?php echo $case_id; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>

        <div class="row">
            <!-- Document Status -->
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>สถานะเอกสาร</h5>
                    </div>
                    <div class="card-body">
                        <form id="documentStatusForm">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">เอกสารขั้นที่ 1</label>
                                    <select class="form-select" id="doc_status_1">
                                        <option value="">เลือก</option>
                                        <option value="ผ่าน">ผ่าน</option>
                                        <option value="ไม่ผ่าน">ไม่ผ่าน</option>
                                        <option value="รอดำเนินการ">รอดำเนินการ</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">เอกสารขั้นที่ 2</label>
                                    <select class="form-select" id="doc_status_2">
                                        <option value="">เลือก</option>
                                        <option value="ผ่าน">ผ่าน</option>
                                        <option value="ไม่ผ่าน">ไม่ผ่าน</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">เอกสารขั้นที่ 3</label>
                                    <select class="form-select" id="doc_status_3">
                                        <option value="">เลือก</option>
                                        <option value="เรียบร้อย">เรียบร้อย</option>
                                        <option value="ยกเลิก">ยกเลิก</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ธนาคาร</label>
                                    <select class="form-select" id="doc_bank_name"></select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">เลขที่บัญชี</label>
                                    <input type="text" class="form-control" id="doc_bank_account">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">สถานะ Pre-Check</label>
                                    <select class="form-select" id="doc_precheck_status">
                                        <option value="">เลือก</option>
                                        <option value="ผ่าน">ผ่าน</option>
                                        <option value="ไม่ผ่าน">ไม่ผ่าน</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">สถานะปิดหนี้</label>
                                    <select class="form-select" id="doc_debt_close_status">
                                        <option value="">เลือก</option>
                                        <option value="done">ปิดแล้ว</option>
                                        <option value="not_done">ยังไม่ปิด</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">หมายเหตุ</label>
                                <textarea class="form-control" id="doc_note" rows="2"></textarea>
                            </div>
                            
                            <button type="button" class="btn btn-primary" onclick="saveDocumentStatus()">
                                <i class="fas fa-save"></i> บันทึกสถานะ
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- File Upload -->
                <div class="card">
                    <div class="card-header">
                        <h5>อัปโหลดไฟล์</h5>
                    </div>
                    <div class="card-body">
                        <form id="fileUploadForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">ประเภทไฟล์</label>
                                <select class="form-select" id="file_type">
                                    <option value="บัตรประชาชน">บัตรประชาชน</option>
                                    <option value="ทะเบียนบ้าน">ทะเบียนบ้าน</option>
                                    <option value="สลิปเงินเดือน">สลิปเงินเดือน</option>
                                    <option value="Statement">Statement</option>
                                    <option value="สัญญาซื้อขาย">สัญญาซื้อขาย</option>
                                    <option value="อื่นๆ">อื่นๆ</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">เลือกไฟล์</label>
                                <input type="file" class="form-control" id="upload_file" required>
                                <small class="text-muted">รองรับ .jpg, .png, .pdf (สูงสุด 5MB)</small>
                            </div>
                            <button type="button" class="btn btn-success" onclick="uploadFile()">
                                <i class="fas fa-upload"></i> อัปโหลด
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- File List -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>ไฟล์ที่อัปโหลด</h5>
                    </div>
                    <div class="card-body">
                        <div id="fileList">
                            <p class="text-muted">กำลังโหลด...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/documents.js"></script>
<?php include_once '../includes/footer.php'; ?>