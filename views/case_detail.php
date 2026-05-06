<?php
// views/case_detail.php - Full Working Version
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
include_once '../includes/header.php';
include_once '../includes/sidebar.php';
include_once '../includes/auth_check.php';
include_once '../includes/functions.php';

$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;

if (!$case_id) {
    echo '<div class="main-content"><div class="container-fluid"><div class="alert alert-danger">ไม่พบ Case ID</div></div></div>';
    include_once '../includes/footer.php';
    exit();
}
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">📋 รายละเอียดเคส #<?php echo $case_id; ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="cases.php">เคสทั้งหมด</a></li>
                        <li class="breadcrumb-item active">Case #<?php echo $case_id; ?></li>
                    </ol>
                </nav>
            </div>
            <a href="cases.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> กลับ</a>
        </div>

        <!-- Case Info Bar -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3"><strong>สถานะ:</strong> <span id="caseStatus" class="badge bg-primary">...</span></div>
                    <div class="col-md-3"><strong>ลูกค้า:</strong> <span id="customerName">...</span></div>
                    <div class="col-md-3"><strong>เจ้าของ:</strong> <span id="ownerName">...</span></div>
                    <div class="col-md-3"><strong>วันที่:</strong> <span id="caseDate">...</span></div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-3" id="caseTabs" role="tablist">

            <?php if (in_array($_SESSION['user_role'], ['admin_page', 'admin'])): ?>
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-customer">👤 ข้อมูลลูกค้า</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-follow">📞 ติดตาม</button>
                </li>
            <?php endif; ?>

            <?php if (in_array($_SESSION['user_role'], ['kpi', 'admin'])): ?>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kpi">✅ KPI</button>
                </li>
            <?php endif; ?>

            <?php if (in_array($_SESSION['user_role'], ['support', 'admin'])): ?>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-preapprove">📋 Pre-Approve</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-documents">📄 เอกสาร</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-bank">🏦 ส่งธนาคาร</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-approval">💰 อนุมัติ</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-debt">💳 ปิดหนี้</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-mortgage">🏠 จำนอง</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-inspection">🔍 ตรวจห้อง</button>
                </li>
            <?php endif; ?>

        </ul>

        <!-- Tabs Content -->
        <div class="tab-content">
            <!-- Tab 1: Customer Info -->
            <div class="tab-pane fade show active" id="tab-customer">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">ข้อมูลลูกค้า</h5>
                    </div>
                    <div class="card-body" id="customerDetail">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-2">กำลังโหลด...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Follow -->
            <div class="tab-pane fade" id="tab-follow">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📞 บันทึกการติดตาม</h5>
                        <button class="btn btn-light btn-sm" onclick="showFollowForm()"><i class="fas fa-plus"></i> เพิ่มการติดตาม</button>
                    </div>
                    <div class="card-body" id="followList">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                            <p>กำลังโหลด...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: KPI -->
            <div class="tab-pane fade" id="tab-kpi">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">ผลการตรวจ KPI</h5>
                    </div>
                    <div class="card-body" id="kpiList">
                        <div class="text-center py-4">กำลังโหลด...</div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Pre-Approve -->
            <div class="tab-pane fade" id="tab-preapprove">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Pre-Approve</h5>
                    </div>
                    <div class="card-body" id="preapproveContent">
                        <div class="text-center py-4">กำลังโหลด...</div>
                    </div>
                </div>
            </div>

            <!-- Tab 5: Documents -->
            <div class="tab-pane fade" id="tab-documents">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">เอกสาร & อัปโหลดไฟล์</h5>
                    </div>
                    <div class="card-body" id="documentsContent">
                        <div class="text-center py-4">กำลังโหลด...</div>
                    </div>
                </div>
            </div>

            <!-- Tab 6: Bank -->
            <div class="tab-pane fade" id="tab-bank">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">ส่งธนาคาร</h5>
                    </div>
                    <div class="card-body" id="bankContent">
                        <div class="text-center py-4">กำลังโหลด...</div>
                    </div>
                </div>
            </div>

            <!-- Tab 7: Approval -->
            <div class="tab-pane fade" id="tab-approval">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">ผลอนุมัติ</h5>
                    </div>
                    <div class="card-body" id="approvalContent">
                        <div class="text-center py-4">กำลังโหลด...</div>
                    </div>
                </div>
            </div>

            <!-- Tab 8: Debt -->
            <div class="tab-pane fade" id="tab-debt">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">ปิดหนี้</h5>
                    </div>
                    <div class="card-body" id="debtContent">
                        <div class="text-center py-4">กำลังโหลด...</div>
                    </div>
                </div>
            </div>

            <!-- Tab 9: Mortgage -->
            <div class="tab-pane fade" id="tab-mortgage">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">จำนอง</h5>
                    </div>
                    <div class="card-body" id="mortgageContent">
                        <div class="text-center py-4">กำลังโหลด...</div>
                    </div>
                </div>
            </div>

            <!-- Tab 10: Inspection -->
            <div class="tab-pane fade" id="tab-inspection">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">ตรวจห้อง</h5>
                        <button class="btn btn-primary btn-sm" onclick="showInspectionForm()"><i class="fas fa-plus"></i> เพิ่มการตรวจ</button>
                    </div>
                    <div class="card-body" id="inspectionContent">
                        <div class="text-center py-4">กำลังโหลด...</div>
                    </div>
                </div>
            </div>
        </div><!-- /tab-content -->
    </div>
</div>

<!-- ========== MODALS ========== -->
<!-- Follow Modal -->
<div class="modal fade" id="followModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">📞 <span id="followModalTitle">เพิ่มการติดตาม</span></h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form><input type="hidden" id="follow_id"><input type="hidden" id="follow_case_id" value="<?php echo $case_id; ?>">
                    <div class="row">
                        <div class="col-md-3 mb-3"><label class="form-label">ครั้งที่</label><input type="number" class="form-control" id="follow_step" readonly></div>
                        <div class="col-md-5 mb-3"><label class="form-label">สถานะ <span class="text-danger">*</span></label>
                            <select class="form-select" id="follow_status">
                                <option value="">เลือกสถานะ</option>
                                <option value="interested">✅ สนใจ</option>
                                <option value="high_interest">🌟 สนใจมาก</option>
                                <option value="pending">⏳ รอดำเนินการ</option>
                                <option value="negotiating">💬 กำลังต่อรอง</option>
                                <option value="site_visit">🏢 นัดดูห้อง</option>
                                <option value="document_submitted">📄 ส่งเอกสารแล้ว</option>
                                <option value="not_interested">❌ ไม่สนใจ</option>
                                <option value="cancelled">🚫 ยกเลิก</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label">ช่องทาง</label><select class="form-select" id="follow_channel">
                                <option value="">เลือก</option>
                                <option value="phone">📞 โทรศัพท์</option>
                                <option value="line">💚 Line</option>
                                <option value="facebook">👍 Facebook</option>
                                <option value="onsite">🏢 พบลูกค้า</option>
                            </select></div>
                    </div>
                    <div class="mb-3"><label class="form-label">รายละเอียด</label><textarea class="form-control" id="follow_note" rows="4" placeholder="บันทึกการติดตาม..."></textarea></div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button><button type="button" class="btn btn-primary" onclick="saveFollow()">บันทึก</button></div>
        </div>
    </div>
</div>

<!-- Inspection Modal -->
<div class="modal fade" id="inspectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">🔍 <span id="inspectionModalTitle">เพิ่มการตรวจห้อง</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="inspectionForm" enctype="multipart/form-data">
                    <input type="hidden" id="inspection_id">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ครั้งที่ <span class="text-danger">*</span></label>
                            <select class="form-select" id="inspection_round">
                                <option value="1">ครั้งที่ 1</option>
                                <option value="2">ครั้งที่ 2</option>
                                <option value="3">ครั้งที่ 3</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">วันที่ตรวจ <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="inspection_date">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                            <select class="form-select" id="inspection_status">
                                <option value="pass">✅ ผ่าน</option>
                                <option value="fail">❌ ไม่ผ่าน (พบ defect)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ / จุดที่พบ</label>
                        <textarea class="form-control" id="inspection_note" rows="3"
                            placeholder="บันทึกจุดที่ตรวจพบ เช่น รอยร้าวที่ผนัง, สีไม่เรียบร้อย, กระจกมีรอย..."></textarea>
                    </div>

                    <!-- อัปโหลดรูปภาพ -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="mb-2"><i class="fas fa-camera"></i> รูปภาพประกอบการตรวจ</h6>

                            <div class="mb-2">
                                <label class="form-label"><small>อัปโหลดรูปภาพ (สูงสุด 5 รูป)</small></label>
                                <input type="file" class="form-control" id="inspection_photos"
                                    accept="image/*" multiple onchange="previewInspectionImages()">
                                <small class="text-muted">รองรับ .jpg, .png (สูงสุด 5MB ต่อรูป)</small>
                            </div>

                            <!-- พรีวิวรูปภาพ -->
                            <div id="imagePreview" class="row mt-2"></div>

                            <!-- รูปภาพเดิม (แสดงตอนแก้ไข) -->
                            <div id="existingImages" class="row mt-2" style="display:none;">
                                <h6 class="col-12">รูปภาพเดิม:</h6>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="saveInspection()">
                    <i class="fas fa-save"></i> บันทึกผลตรวจ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========== JAVASCRIPT ========== -->
<script>
const CASE_ID = <?php echo $case_id; ?>;
const USER_ID = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; ?>;
</script>
<script src="../assets/js/case_detail.js"></script>

<?php include_once '../includes/footer.php'; ?>