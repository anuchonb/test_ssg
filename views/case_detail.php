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
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-customer">👤 ข้อมูลลูกค้า</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-follow">📞 ติดตาม</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kpi">✅ KPI</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-preapprove">📋 Pre-Approve</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-documents">📄 เอกสาร</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-bank">🏦 ส่งธนาคาร</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-approval">💰 อนุมัติ</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-debt">💳 ปิดหนี้</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-mortgage">🏠 จำนอง</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-inspection">🔍 ตรวจห้อง</button></li>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content">
            <!-- Tab 1: Customer Info -->
            <div class="tab-pane fade show active" id="tab-customer">
                <div class="card"><div class="card-header"><h5 class="mb-0">ข้อมูลลูกค้า</h5></div>
                    <div class="card-body" id="customerDetail"><div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">กำลังโหลด...</p></div></div>
                </div>
            </div>

            <!-- Tab 2: Follow -->
            <div class="tab-pane fade" id="tab-follow">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📞 บันทึกการติดตาม</h5>
                        <button class="btn btn-light btn-sm" onclick="showFollowForm()"><i class="fas fa-plus"></i> เพิ่มการติดตาม</button>
                    </div>
                    <div class="card-body" id="followList"><div class="text-center py-5"><div class="spinner-border text-primary"></div><p>กำลังโหลด...</p></div></div>
                </div>
            </div>

            <!-- Tab 3: KPI -->
            <div class="tab-pane fade" id="tab-kpi">
                <div class="card"><div class="card-header"><h5 class="mb-0">ผลการตรวจ KPI</h5></div>
                    <div class="card-body" id="kpiList"><div class="text-center py-4">กำลังโหลด...</div></div>
                </div>
            </div>

            <!-- Tab 4: Pre-Approve -->
            <div class="tab-pane fade" id="tab-preapprove">
                <div class="card"><div class="card-header"><h5 class="mb-0">Pre-Approve</h5></div>
                    <div class="card-body" id="preapproveContent"><div class="text-center py-4">กำลังโหลด...</div></div>
                </div>
            </div>

            <!-- Tab 5: Documents -->
            <div class="tab-pane fade" id="tab-documents">
                <div class="card"><div class="card-header"><h5 class="mb-0">เอกสาร & อัปโหลดไฟล์</h5></div>
                    <div class="card-body" id="documentsContent"><div class="text-center py-4">กำลังโหลด...</div></div>
                </div>
            </div>

            <!-- Tab 6: Bank -->
            <div class="tab-pane fade" id="tab-bank">
                <div class="card"><div class="card-header"><h5 class="mb-0">ส่งธนาคาร</h5></div>
                    <div class="card-body" id="bankContent"><div class="text-center py-4">กำลังโหลด...</div></div>
                </div>
            </div>

            <!-- Tab 7: Approval -->
            <div class="tab-pane fade" id="tab-approval">
                <div class="card"><div class="card-header"><h5 class="mb-0">ผลอนุมัติ</h5></div>
                    <div class="card-body" id="approvalContent"><div class="text-center py-4">กำลังโหลด...</div></div>
                </div>
            </div>

            <!-- Tab 8: Debt -->
            <div class="tab-pane fade" id="tab-debt">
                <div class="card"><div class="card-header"><h5 class="mb-0">ปิดหนี้</h5></div>
                    <div class="card-body" id="debtContent"><div class="text-center py-4">กำลังโหลด...</div></div>
                </div>
            </div>

            <!-- Tab 9: Mortgage -->
            <div class="tab-pane fade" id="tab-mortgage">
                <div class="card"><div class="card-header"><h5 class="mb-0">จำนอง</h5></div>
                    <div class="card-body" id="mortgageContent"><div class="text-center py-4">กำลังโหลด...</div></div>
                </div>
            </div>

            <!-- Tab 10: Inspection -->
            <div class="tab-pane fade" id="tab-inspection">
                <div class="card"><div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">ตรวจห้อง</h5>
                    <button class="btn btn-primary btn-sm" onclick="showInspectionForm()"><i class="fas fa-plus"></i> เพิ่มการตรวจ</button>
                </div>
                    <div class="card-body" id="inspectionContent"><div class="text-center py-4">กำลังโหลด...</div></div>
                </div>
            </div>
        </div><!-- /tab-content -->
    </div>
</div>

<!-- ========== MODALS ========== -->
<!-- Follow Modal -->
<div class="modal fade" id="followModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header bg-primary text-white"><h5 class="modal-title">📞 <span id="followModalTitle">เพิ่มการติดตาม</span></h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form><input type="hidden" id="follow_id"><input type="hidden" id="follow_case_id" value="<?php echo $case_id; ?>">
                <div class="row">
                    <div class="col-md-3 mb-3"><label class="form-label">ครั้งที่</label><input type="number" class="form-control" id="follow_step" readonly></div>
                    <div class="col-md-5 mb-3"><label class="form-label">สถานะ <span class="text-danger">*</span></label>
                        <select class="form-select" id="follow_status">
                            <option value="">เลือกสถานะ</option><option value="interested">✅ สนใจ</option><option value="high_interest">🌟 สนใจมาก</option><option value="pending">⏳ รอดำเนินการ</option><option value="negotiating">💬 กำลังต่อรอง</option><option value="site_visit">🏢 นัดดูห้อง</option><option value="document_submitted">📄 ส่งเอกสารแล้ว</option><option value="not_interested">❌ ไม่สนใจ</option><option value="cancelled">🚫 ยกเลิก</option>
                        </select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">ช่องทาง</label><select class="form-select" id="follow_channel"><option value="">เลือก</option><option value="phone">📞 โทรศัพท์</option><option value="line">💚 Line</option><option value="facebook">👍 Facebook</option><option value="onsite">🏢 พบลูกค้า</option></select></div>
                </div>
                <div class="mb-3"><label class="form-label">รายละเอียด</label><textarea class="form-control" id="follow_note" rows="4" placeholder="บันทึกการติดตาม..."></textarea></div>
            </form>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button><button type="button" class="btn btn-primary" onclick="saveFollow()">บันทึก</button></div>
    </div></div>
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
const USER_ID = <?php echo isset($_SESSION['user_id'])?$_SESSION['user_id']:0; ?>;

$(document).ready(function(){
    loadCaseInfo();
    loadCustomerDetail();
    $('#caseTabs button').on('shown.bs.tab', function(e){
        let target = $(e.target).data('bs-target');
        switch(target){
            case '#tab-follow': loadFollowList(); break;
            case '#tab-kpi': loadKpiList(); break;
            case '#tab-preapprove': loadPreapprove(); break;
            case '#tab-documents': loadDocuments(); break;
            case '#tab-bank': loadBankList(); break;
            case '#tab-approval': loadApproval(); break;
            case '#tab-debt': loadDebt(); break;
            case '#tab-mortgage': loadMortgage(); break;
            case '#tab-inspection': loadInspectionList(); break;
        }
    });
    // set today date for inspection modal
    $('#inspection_date').val(new Date().toISOString().slice(0,16));
});

// ========== CASE INFO ==========
function loadCaseInfo(){
    $.get('../api/cases/get.php',{id:CASE_ID},function(res){
        if(res.success&&res.data){
            let c=res.data;
            $('#caseStatus').text(c.status||'-');
            $('#customerName').text(c.customer_name||'-');
            $('#ownerName').text(c.owner_name||'-');
            $('#caseDate').text(formatDate(c.created_at));
        }
    },'json');
}

// ========== CUSTOMER ==========
function loadCustomerDetail(){
    $.get('../api/cases/get_customer.php',{case_id:CASE_ID},function(res){
        if(res.success&&res.data){
            let c=res.data;
            let html='<div class="row"><div class="col-md-6"><table class="table table-bordered table-sm">'+
                '<tr><th style="width:120px">ชื่อ</th><td>'+(c.name||'-')+'</td></tr>'+
                '<tr><th>เบอร์โทร</th><td><a href="tel:'+(c.phone||'')+'">'+(c.phone||'-')+'</a></td></tr>'+
                '<tr><th>Facebook</th><td>'+(c.facebook||'-')+'</td></tr>'+
                '<tr><th>Line</th><td>'+(c.line_id||'-')+'</td></tr>'+
                '<tr><th>ช่องทาง</th><td>'+(c.channel||'-')+'</td></tr>'+
                '<tr><th>เกรด</th><td><span class="badge bg-'+getGradeColor(c.grade)+'">'+(c.grade||'-')+'</span></td></tr>'+
                '</table></div><div class="col-md-6"><table class="table table-bordered table-sm">'+
                '<tr><th style="width:120px">โครงการ</th><td>'+(c.project_name||'-')+'</td></tr>'+
                '<tr><th>ราคา</th><td>'+(c.price?numberFormat(c.price)+' บาท':'-')+'</td></tr>'+
                '<tr><th>เงินทอน</th><td>'+(c.cashback?numberFormat(c.cashback)+' บาท':'-')+'</td></tr>'+
                '<tr><th>ลักษณะ</th><td>'+(c.living_type==='rent'?'ปล่อยเช่า':c.living_type==='live'?'อยู่เอง':'-')+'</td></tr>'+
                '<tr><th>โซน</th><td>'+(c.zone||'-')+'</td></tr>'+
                '<tr><th>ภาระหนี้</th><td>'+(c.debt_status==='have'?'<span class="badge bg-warning">มี</span>':c.debt_status==='none'?'<span class="badge bg-success">ไม่มี</span>':'-')+'</td></tr>'+
                '</table></div></div>'+
                '<table class="table table-bordered table-sm mt-2"><tr><th style="width:120px">บริษัท</th><td>'+(c.company_name||'-')+'</td><th style="width:120px">อายุงาน</th><td>'+(c.work_age_month?c.work_age_month+' เดือน':'-')+'</td><th style="width:120px">สวัสดิการ</th><td>'+(c.welfare||'-')+'</td></tr></table>';
            $('#customerDetail').html(html);
        }else{
            $('#customerDetail').html('<div class="alert alert-warning">ไม่พบข้อมูลลูกค้า</div>');
        }
    },'json').fail(function(){$('#customerDetail').html('<div class="alert alert-danger">โหลดไม่สำเร็จ</div>');});
}

// ========== FOLLOW ==========
function showFollowForm(){
    $('#follow_id, #follow_status, #follow_note, #follow_channel').val('');
    $.get('../api/follow/get_count.php',{case_id:CASE_ID},function(res){
        $('#follow_step').val((res.count||0)+1);
        $('#followModal').modal('show');
    });
}
function saveFollow(){
    let status = $('#follow_status').val();
    if(!status){alert('กรุณาเลือกสถานะ');return;}
    if(!confirm('บันทึกการติดตาม?'))return;
    let data = {case_id:CASE_ID,step:parseInt($('#follow_step').val()),status:status,note:$('#follow_note').val().trim()};
    $.post('../api/follow/add.php',JSON.stringify(data),function(res){
        if(res.success){$('#followModal').modal('hide'); alert('บันทึกสำเร็จ!'); loadFollowList(); loadCaseInfo();}
        else alert('ผิดพลาด: '+res.message);
    },'json').fail(function(xhr){alert('ไม่สามารถบันทึกได้ (Status: '+xhr.status+')');});
}
function loadFollowList(){
    $.get('../api/follow/list.php',{case_id:CASE_ID},function(res){
        if(res.success&&res.data&&res.data.length>0){
            let html='<div class="timeline">';
            res.data.forEach(function(f){
                html+='<div class="timeline-item"><div class="timeline-marker bg-'+getStatusColor(f.status)+'" style="width:30px;height:30px;left:10px;"></div>'+
                '<div class="timeline-content"><h6>ครั้งที่ '+f.step+': '+getStatusLabel(f.status)+'</h6><small class="text-muted">'+formatDate(f.created_at)+'</small>'+
                (f.note?'<p class="mt-1 mb-0">'+f.note+'</p>':'')+
                '<button class="btn btn-sm btn-outline-danger mt-1" onclick="deleteFollow('+f.id+')"><i class="fas fa-trash"></i></button></div></div>';
            });
            html+='</div>'; $('#followList').html(html);
        }else{
            $('#followList').html('<div class="text-center py-5 text-muted"><i class="fas fa-phone-slash fa-4x mb-3"></i><p>ยังไม่มีการติดตาม</p><button class="btn btn-primary" onclick="showFollowForm()">เพิ่มการติดตามครั้งแรก</button></div>');
        }
    },'json');
}
function deleteFollow(id){
    if(!confirm('ลบการติดตามนี้?'))return;
    $.post('../api/follow/delete.php',JSON.stringify({id:id}),function(res){
        if(res.success){loadFollowList(); loadCaseInfo();}
    },'json');
}

// ========== KPI ==========
function loadKpiList(){
    $.get('../api/kpi/list.php',{case_id:CASE_ID},function(res){
        if(res.success&&res.data&&res.data.length>0){
            let html='<table class="table table-striped"><thead><tr><th>ผู้ตรวจ</th><th>ผล</th><th>เหตุผล</th><th>วันที่</th></tr></thead><tbody>';
            res.data.forEach(function(k){
                html+='<tr><td>'+(k.checker_name||'-')+'</td><td>'+(k.result==='pass'?'<span class="badge bg-success">ผ่าน</span>':'<span class="badge bg-danger">ไม่ผ่าน</span>')+'</td><td>'+(k.reason||'-')+'</td><td>'+formatDate(k.created_at)+'</td></tr>';
            });
            html+='</tbody></table>'; $('#kpiList').html(html);
        }else{
            $('#kpiList').html('<div class="text-center py-4 text-muted">ยังไม่มีการตรวจ KPI</div>');
        }
    },'json');
}

// ========== PRE-APPROVE ==========
function loadPreapprove(){
    let html='<form><div class="row"><div class="col-md-6 mb-3"><label>สถานะ</label><select class="form-select" id="pa_status"><option value="processing">กำลังดำเนินการ</option><option value="approved">อนุมัติ</option><option value="rejected">ปฏิเสธ</option></select></div><div class="col-md-6 mb-3"><label>วงเงิน</label><input type="number" class="form-control" id="pa_amount" step="0.01"></div></div><div class="mb-3"><label>หมายเหตุ</label><textarea class="form-control" id="pa_note" rows="2"></textarea></div><button type="button" class="btn btn-primary" onclick="savePreapprove()">บันทึก</button></form>';
    $('#preapproveContent').html(html);
    $.get('../api/preapprove/get.php',{case_id:CASE_ID},function(res){
        if(res.data){ $('#pa_status').val(res.data.status); $('#pa_amount').val(res.data.approved_amount); $('#pa_note').val(res.data.note||''); }
    },'json');
}
function savePreapprove(){
    let data={case_id:CASE_ID,status:$('#pa_status').val(),approved_amount:$('#pa_amount').val(),note:$('#pa_note').val()};
    $.post('../api/preapprove/save.php',JSON.stringify(data),function(res){
        alert(res.success?'บันทึกสำเร็จ!':'ผิดพลาด: '+res.message);
    },'json');
}

// ========== DOCUMENTS ==========
function loadDocuments(){
    let html = 
    '<div class="row">' +
        // 🔹 ส่วนซ้าย: สถานะเอกสาร
        '<div class="col-md-7">' +
            '<div class="card mb-3">' +
                '<div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fas fa-clipboard-check"></i> สถานะเอกสาร</h6></div>' +
                '<div class="card-body">' +
                    '<form>' +
                        '<div class="row">' +
                            '<div class="col-md-4 mb-2">' +
                                '<label class="form-label"><small>เอกสารขั้นที่ 1</small></label>' +
                                '<select class="form-select form-select-sm doc-status" id="doc_status_1">' +
                                    '<option value="">เลือก</option><option value="ผ่าน">✅ ผ่าน</option><option value="ไม่ผ่าน">❌ ไม่ผ่าน</option><option value="รอดำเนินการ">⏳ รอดำเนินการ</option></select></div>' +
                            '<div class="col-md-4 mb-2">' +
                                '<label class="form-label"><small>เอกสารขั้นที่ 2</small></label>' +
                                '<select class="form-select form-select-sm doc-status" id="doc_status_2">' +
                                    '<option value="">เลือก</option><option value="ผ่าน">✅ ผ่าน</option><option value="ไม่ผ่าน">❌ ไม่ผ่าน</option></select></div>' +
                            '<div class="col-md-4 mb-2">' +
                                '<label class="form-label"><small>เอกสารขั้นที่ 3</small></label>' +
                                '<select class="form-select form-select-sm doc-status" id="doc_status_3">' +
                                    '<option value="">เลือก</option><option value="เรียบร้อย">✅ เรียบร้อย</option><option value="ยกเลิก">❌ ยกเลิก</option></select></div>' +
                        '</div>' +
                        '<hr>' +
                        '<div class="row">' +
                            '<div class="col-md-6 mb-2">' +
                                '<label class="form-label"><small>ธนาคาร</small></label>' +
                                '<input type="text" class="form-control form-control-sm" id="doc_bank_name" placeholder="ชื่อธนาคาร"></div>' +
                            '<div class="col-md-6 mb-2">' +
                                '<label class="form-label"><small>เลขที่บัญชี</small></label>' +
                                '<input type="text" class="form-control form-control-sm" id="doc_bank_account" placeholder="เลขบัญชี"></div>' +
                        '</div>' +
                        '<div class="row">' +
                            '<div class="col-md-6 mb-2">' +
                                '<label class="form-label"><small>Pre-Check</small></label>' +
                                '<select class="form-select form-select-sm" id="doc_precheck"><option value="">เลือก</option><option value="ผ่าน">✅ ผ่าน</option><option value="ไม่ผ่าน">❌ ไม่ผ่าน</option></select></div>' +
                            '<div class="col-md-6 mb-2">' +
                                '<label class="form-label"><small>สถานะปิดหนี้</small></label>' +
                                '<select class="form-select form-select-sm" id="doc_debt_close"><option value="">เลือก</option><option value="done">✅ ปิดแล้ว</option><option value="not_done">❌ ยังไม่ปิด</option></select></div>' +
                        '</div>' +
                        '<button type="button" class="btn btn-primary btn-sm mt-2" onclick="saveDocumentStatus()"><i class="fas fa-save"></i> บันทึกสถานะ</button>' +
                    '</form>' +
                '</div>' +
            '</div>' +
        '</div>' +
        
        // 🔹 ส่วนขวา: อัปโหลดไฟล์
        '<div class="col-md-5">' +
            '<div class="card mb-3">' +
                '<div class="card-header bg-success text-white"><h6 class="mb-0"><i class="fas fa-upload"></i> อัปโหลดเอกสาร</h6></div>' +
                '<div class="card-body">' +
                    '<div class="mb-2">' +
                        '<label class="form-label"><small>ประเภทเอกสาร <span class="text-danger">*</span></small></label>' +
                        '<select class="form-select form-select-sm" id="doc_type">' +
                            '<option value="">เลือกประเภทเอกสาร</option>' +
                        '</select>' +
                    '</div>' +
                    '<div class="mb-2">' +
                        '<label class="form-label"><small>เลือกไฟล์ <span class="text-danger">*</span></label>' +
                        '<input type="file" class="form-control form-control-sm" id="uploadFile">' +
                        '<small class="text-muted">รองรับ .jpg, .png, .pdf (สูงสุด 5MB)</small>' +
                    '</div>' +
                    '<button class="btn btn-success btn-sm" onclick="uploadFile()"><i class="fas fa-upload"></i> อัปโหลด</button>' +
                    '<hr>' +
                    '<h6 class="mb-2">📁 เอกสารที่อัปโหลด (<span id="fileCount">0</span>)</h6>' +
                    '<div id="fileList" style="max-height: 300px; overflow-y: auto;">กำลังโหลด...</div>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
    
    $('#documentsContent').html(html);
    
    // โหลด dropdown ประเภทเอกสาร
    loadDocumentTypes();
    // โหลดสถานะเอกสาร
    loadDocumentStatus();
    // โหลดรายการไฟล์
    loadFileList();
}

// โหลดประเภทเอกสารจาก master_dropdowns
function loadDocumentTypes() {
    $.ajax({
        url: '../api/master/get.php',
        type: 'GET',
        data: { type: 'document_type' },
        dataType: 'json',
        success: function(data) {
            let options = '<option value="">เลือกประเภทเอกสาร</option>';
            if (Array.isArray(data)) {
                data.forEach(function(item) {
                    options += '<option value="' + item.value + '">📄 ' + item.value + '</option>';
                });
            }
            // ถ้าไม่มีข้อมูลจาก master ให้ใช้ค่าเริ่มต้น
            if (data.length === 0) {
                options += '<option value="บัตรประชาชน">📄 บัตรประชาชน</option>';
                options += '<option value="ทะเบียนบ้าน">📄 ทะเบียนบ้าน</option>';
                options += '<option value="สลิปเงินเดือน">📄 สลิปเงินเดือน</option>';
                options += '<option value="Statement">📄 Statement</option>';
                options += '<option value="หนังสือรับรองเงินเดือน">📄 หนังสือรับรองเงินเดือน</option>';
                options += '<option value="สัญญาซื้อขาย">📄 สัญญาซื้อขาย</option>';
                options += '<option value="อื่นๆ">📄 อื่นๆ</option>';
            }
            $('#doc_type').html(options);
        },
        error: function() {
            // Fallback options
            let options = '<option value="">เลือกประเภทเอกสาร</option>';
            options += '<option value="บัตรประชาชน">📄 บัตรประชาชน</option>';
            options += '<option value="ทะเบียนบ้าน">📄 ทะเบียนบ้าน</option>';
            options += '<option value="สลิปเงินเดือน">📄 สลิปเงินเดือน</option>';
            options += '<option value="Statement">📄 Statement</option>';
            options += '<option value="อื่นๆ">📄 อื่นๆ</option>';
            $('#doc_type').html(options);
        }
    });
}

function loadDocumentStatus(){
    $.get('../api/document/get.php', {case_id: CASE_ID}, function(res){
        if(res.data){
            let d = res.data;
            $('#doc_status_1').val(d.doc_status_1 || '');
            $('#doc_status_2').val(d.doc_status_2 || '');
            $('#doc_status_3').val(d.doc_status_3 || '');
            $('#doc_bank_name').val(d.bank_name || '');
            $('#doc_bank_account').val(d.bank_account || '');
            $('#doc_precheck').val(d.precheck_status || '');
            $('#doc_debt_close').val(d.debt_close_status || '');
        }
    }, 'json');
}

function saveDocumentStatus(){
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
    
    $.post('../api/document/update.php', JSON.stringify(data), function(res){
        alert(res.success ? 'บันทึกสถานะเอกสารสำเร็จ!' : 'ผิดพลาด: ' + (res.message || ''));
    }, 'json').fail(function(xhr) {
        alert('ไม่สามารถบันทึกได้ (Status: ' + xhr.status + ')');
    });
}

function loadFileList(){
    $.get('../api/file/list.php', {case_id: CASE_ID}, function(res){
        if(res.success && res.data && res.data.length > 0){
            let html = '<div class="list-group list-group-flush">';
            res.data.forEach(function(f){
                // กำหนดสีตามประเภทไฟล์
                let iconColor = getFileIconColor(f.file_type);
                let fileIcon = getFileIcon(f.file_type);
                
                html += 
                '<div class="list-group-item py-2 px-0 d-flex justify-content-between align-items-center">' +
                    '<div class="d-flex align-items-center">' +
                        '<span class="badge bg-' + iconColor + ' me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">' +
                            '<i class="fas fa-' + fileIcon + '"></i>' +
                        '</span>' +
                        '<div>' +
                            '<small class="fw-bold">' + (f.file_type || 'ไม่ระบุ') + '</small>' +
                            '<br><small class="text-muted">' + formatDate(f.created_at) + '</small>' +
                        '</div>' +
                    '</div>' +
                    '<div class="btn-group btn-group-sm">' +
                        '<a href="../' + f.file_path + '" target="_blank" class="btn btn-outline-info btn-sm" title="ดู"><i class="fas fa-eye"></i></a>' +
                        '<button class="btn btn-outline-danger btn-sm" onclick="deleteFile(' + f.id + ')" title="ลบ"><i class="fas fa-trash"></i></button>' +
                    '</div>' +
                '</div>';
            });
            html += '</div>';
            $('#fileList').html(html);
            $('#fileCount').text(res.data.length);
        } else {
            $('#fileList').html(
                '<div class="text-center py-4 text-muted">' +
                    '<i class="fas fa-folder-open fa-3x mb-2"></i>' +
                    '<p>ยังไม่มีเอกสาร</p>' +
                '</div>'
            );
            $('#fileCount').text('0');
        }
    }, 'json').fail(function() {
        $('#fileList').html('<p class="text-danger">โหลดไม่สำเร็จ</p>');
    });
}

function uploadFile(){
    let docType = $('#doc_type').val();
    let fileInput = $('#uploadFile')[0];
    
    // ตรวจสอบ
    if(!docType) {
        alert('กรุณาเลือกประเภทเอกสาร');
        $('#doc_type').focus();
        return;
    }
    
    if(!fileInput.files || !fileInput.files[0]) {
        alert('กรุณาเลือกไฟล์');
        return;
    }
    
    let file = fileInput.files[0];
    
    // ตรวจสอบขนาด
    if(file.size > 5 * 1024 * 1024) {
        alert('ไฟล์มีขนาดเกิน 5MB');
        return;
    }
    
    // ตรวจสอบนามสกุล
    let allowed = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
    let ext = file.name.split('.').pop().toLowerCase();
    if(!allowed.includes(ext)) {
        alert('ประเภทไฟล์ไม่ถูกต้อง (อนุญาต: ' + allowed.join(', ') + ')');
        return;
    }
    
    let formData = new FormData();
    formData.append('case_id', CASE_ID);
    formData.append('file_type', docType);  // ส่งประเภทเอกสาร
    formData.append('file', file);
    
    // แสดง loading
    let btn = $('.btn-success');
    let origText = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm"></span> กำลังอัปโหลด...').prop('disabled', true);
    
    $.ajax({
        url: '../api/file/upload.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res){
            btn.html(origText).prop('disabled', false);
            
            if(res.success){
                alert('อัปโหลดสำเร็จ! 📄');
                $('#uploadFile').val('');
                loadFileList();
            } else {
                alert('ผิดพลาด: ' + (res.message || 'ไม่สามารถอัปโหลดได้'));
            }
        },
        error: function(xhr){
            btn.html(origText).prop('disabled', false);
            alert('ไม่สามารถอัปโหลดได้ (Status: ' + xhr.status + ')');
        }
    });
}

function deleteFile(id){
    if(!confirm('ยืนยันการลบเอกสารนี้?')) return;
    
    $.post('../api/file/delete.php', JSON.stringify({id: id}), function(res){
        if(res.success) {
            loadFileList();
        } else {
            alert('ผิดพลาด: ' + (res.message || ''));
        }
    }, 'json').fail(function() {
        alert('ไม่สามารถลบได้');
    });
}

// ฟังก์ชั่นช่วยเหลือสำหรับแสดง icon ตามประเภทไฟล์
function getFileIcon(fileType) {
    if(!fileType) return 'file';
    if(fileType.includes('บัตรประชาชน') || fileType.includes('ID')) return 'id-card';
    if(fileType.includes('ทะเบียนบ้าน')) return 'home';
    if(fileType.includes('สลิป') || fileType.includes('เงินเดือน') || fileType.includes('Statement')) return 'file-invoice';
    if(fileType.includes('รับรอง')) return 'file-contract';
    if(fileType.includes('สัญญา')) return 'file-signature';
    if(fileType.includes('โฉนด')) return 'scroll';
    if(fileType.includes('สมรส')) return 'ring';
    if(fileType.includes('บัญชี') || fileType.includes('ธนาคาร')) return 'university';
    return 'file-alt';
}

function getFileIconColor(fileType) {
    if(!fileType) return 'secondary';
    if(fileType.includes('บัตรประชาชน')) return 'primary';
    if(fileType.includes('ทะเบียนบ้าน')) return 'success';
    if(fileType.includes('สลิป') || fileType.includes('Statement')) return 'info';
    if(fileType.includes('รับรอง')) return 'warning';
    if(fileType.includes('สัญญา')) return 'danger';
    return 'secondary';
}

// ========== BANK (แก้ไขใหม่) ==========
function loadBankList(){
    let html = 
    '<div class="row">' +
        // ฟอร์มส่งธนาคาร
        '<div class="col-md-6">' +
            '<div class="card">' +
                '<div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fas fa-university"></i> บันทึกการส่งธนาคาร</h6></div>' +
                '<div class="card-body">' +
                    '<form>' +
                        '<div class="mb-2">' +
                            '<label class="form-label">ธนาคาร <span class="text-danger">*</span></label>' +
                            '<select class="form-select" id="bank_name">' +
                                '<option value="">เลือกธนาคาร</option>' +
                                '<option value="ธนาคารกรุงเทพ">ธนาคารกรุงเทพ</option>' +
                                '<option value="ธนาคารกสิกรไทย">ธนาคารกสิกรไทย</option>' +
                                '<option value="ธนาคารกรุงไทย">ธนาคารกรุงไทย</option>' +
                                '<option value="ธนาคารไทยพาณิชย์">ธนาคารไทยพาณิชย์</option>' +
                                '<option value="ธนาคารออมสิน">ธนาคารออมสิน</option>' +
                                '<option value="ธนาคารอาคารสงเคราะห์">ธนาคารอาคารสงเคราะห์</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="mb-2">' +
                            '<label class="form-label">วันที่ส่ง</label>' +
                            '<input type="date" class="form-control" id="bank_date" value="' + getTodayDate() + '">' +
                        '</div>' +
                        '<div class="mb-2">' +
                            '<label class="form-label">หมายเหตุ</label>' +
                            '<textarea class="form-control" id="bank_note" rows="2" placeholder="หมายเหตุเพิ่มเติม"></textarea>' +
                        '</div>' +
                        '<button type="button" class="btn btn-primary" onclick="saveBank()"><i class="fas fa-save"></i> บันทึก</button>' +
                    '</form>' +
                '</div>' +
            '</div>' +
        '</div>' +
        // ประวัติการส่งธนาคาร
        '<div class="col-md-6">' +
            '<div class="card">' +
                '<div class="card-header bg-info text-white"><h6 class="mb-0"><i class="fas fa-history"></i> ประวัติการส่งธนาคาร</h6></div>' +
                '<div class="card-body">' +
                    '<div id="bankHistory" style="max-height: 400px; overflow-y: auto;">' +
                        '<div class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> กำลังโหลด...</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
    
    $('#bankContent').html(html);
    
    // ✅ โหลดประวัติ
    loadBankHistory();
}

function saveBank(){
    let bankName = $('#bank_name').val();
    let submitDate = $('#bank_date').val();
    let note = $('#bank_note').val() || '';
    
    if(!bankName) {
        alert('กรุณาเลือกธนาคาร');
        $('#bank_name').focus();
        return;
    }
    
    if(!confirm('บันทึกการส่งธนาคาร ' + bankName + '?')) return;
    
    let data = {
        case_id: CASE_ID,
        bank_name: bankName,
        submit_date: submitDate,
        note: note
    };
    
    $.ajax({
        url: '../api/bank/submit.php',
        type: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        dataType: 'json',
        success: function(res) {
            console.log('Bank save response:', res);
            
            if(res.success) {
                alert('บันทึกสำเร็จ!');
                $('#bank_note').val('');
                // ✅ โหลดประวัติใหม่
                loadBankHistory();
            } else {
                alert('ผิดพลาด: ' + (res.message || 'ไม่สามารถบันทึกได้'));
            }
        },
        error: function(xhr, status, error) {
            console.error('Bank save error:', status, error);
            alert('ไม่สามารถบันทึกได้ (Status: ' + xhr.status + ')');
        }
    });
}

function loadBankHistory() {
    console.log('Loading bank history for case:', CASE_ID);
    
    $.ajax({
        url: '../api/bank/list.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        timeout: 10000,
        success: function(res) {
            console.log('Bank history response:', res);
            
            if(res.success && res.data && res.data.length > 0) {
                let html = '<div class="list-group list-group-flush">';
                
                res.data.forEach(function(b, index) {
                    html += 
                    '<div class="list-group-item py-2">' +
                        '<div class="d-flex justify-content-between align-items-start">' +
                            '<div>' +
                                '<strong>' + (b.bank_name || 'ไม่ระบุ') + '</strong>' +
                                '<br><small class="text-muted">📅 ' + (b.submit_date || '-') + '</small>' +
                                (b.note ? '<br><small>' + b.note + '</small>' : '') +
                            '</div>' +
                            '<span class="badge bg-info">ครั้งที่ ' + (res.data.length - index) + '</span>' +
                        '</div>' +
                    '</div>';
                });
                
                html += '</div>';
                $('#bankHistory').html(html);
            } else {
                $('#bankHistory').html(
                    '<div class="text-center py-4 text-muted">' +
                        '<i class="fas fa-university fa-3x mb-2"></i>' +
                        '<p>ยังไม่มีประวัติการส่งธนาคาร</p>' +
                    '</div>'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('Bank history error:', status, error);
            console.error('Response:', xhr.responseText);
            
            $('#bankHistory').html(
                '<div class="alert alert-danger py-2">' +
                    '<small>❌ ไม่สามารถโหลดประวัติได้ (Status: ' + xhr.status + ')</small>' +
                    '<br><button class="btn btn-sm btn-outline-danger mt-1" onclick="loadBankHistory()">ลองใหม่</button>' +
                '</div>'
            );
        }
    });
}

// Helper: Get today date
function getTodayDate() {
    let today = new Date();
    let yyyy = today.getFullYear();
    let mm = String(today.getMonth() + 1).padStart(2, '0');
    let dd = String(today.getDate()).padStart(2, '0');
    return yyyy + '-' + mm + '-' + dd;
}

// ========== APPROVAL ==========
function loadApproval(){
    let html='<form><div class="row">'+
        '<div class="col-md-3 mb-3"><label>วงเงินรวม</label><input type="number" class="form-control" id="ap_total" readonly></div>'+
        '<div class="col-md-3 mb-3"><label>วงเงินห้อง</label><input type="number" class="form-control" id="ap_room" oninput="calcApprovalTotal()"></div>'+
        '<div class="col-md-3 mb-3"><label>วงเงินประกัน</label><input type="number" class="form-control" id="ap_insurance" oninput="calcApprovalTotal()"></div>'+
        '<div class="col-md-3 mb-3"><label>วงเงินเฟอร์</label><input type="number" class="form-control" id="ap_furniture" oninput="calcApprovalTotal()"></div>'+
        '</div><div class="row"><div class="col-md-6 mb-3"><label>วันเซ็นสัญญา</label><input type="date" class="form-control" id="ap_contract"></div>'+
        '<div class="col-md-6 mb-3"><label>วันโอน</label><input type="date" class="form-control" id="ap_transfer"></div></div>'+
        '<div class="mb-3"><label>หมายเหตุ</label><textarea class="form-control" id="ap_note" rows="2"></textarea></div>'+
        '<button type="button" class="btn btn-primary" onclick="saveApproval()">บันทึก</button></form>';
    $('#approvalContent').html(html);
    $.get('../api/approval/get.php',{case_id:CASE_ID},function(res){
        if(res.data){
            let d=res.data;
            $('#ap_total').val(d.total_amount); $('#ap_room').val(d.room_amount); $('#ap_insurance').val(d.insurance_amount);
            $('#ap_furniture').val(d.furniture_amount); $('#ap_contract').val(d.contract_date?d.contract_date.split(' ')[0]:'');
            $('#ap_transfer').val(d.transfer_date); $('#ap_note').val(d.note||'');
        }
    },'json');
}
function calcApprovalTotal(){
    let total = (parseFloat($('#ap_room').val())||0)+(parseFloat($('#ap_insurance').val())||0)+(parseFloat($('#ap_furniture').val())||0);
    $('#ap_total').val(total);
}
function saveApproval(){
    let data={
        case_id:CASE_ID, total_amount:$('#ap_total').val(), room_amount:$('#ap_room').val(),
        insurance_amount:$('#ap_insurance').val(), furniture_amount:$('#ap_furniture').val(),
        contract_date:$('#ap_contract').val(), transfer_date:$('#ap_transfer').val(), note:$('#ap_note').val()
    };
    $.post('../api/approval/save.php',JSON.stringify(data),function(res){
        alert(res.success?'บันทึกสำเร็จ!':'ผิดพลาด: '+res.message);
    },'json');
}

// ========== DEBT (with dynamic items) ==========
let debtItemCount = 0;
function loadDebt(){
    let html='<form><div class="row"><div class="col-md-6 mb-3"><label>วันที่ปิดหนี้</label><input type="datetime-local" class="form-control" id="debt_date"></div><div class="col-md-6 mb-3"><label>สถานที่</label><input type="text" class="form-control" id="debt_location"></div></div><div class="row"><div class="col-md-6 mb-3"><label>เจ้าหน้าที่</label><input type="text" class="form-control" id="debt_staff"></div></div><div class="mb-3"><label>หมายเหตุ</label><textarea class="form-control" id="debt_note" rows="2"></textarea></div><h6>รายการหนี้ <button type="button" class="btn btn-sm btn-success" onclick="addDebtItem()">+</button></h6><div id="debtItemsContainer"></div><div class="mt-2"><strong>รวม: <span id="debtTotal">0</span> บาท</strong></div><button type="button" class="btn btn-primary mt-3" onclick="saveDebt()">บันทึก</button></form>';
    $('#debtContent').html(html);
    // load existing debt data
    $.get('../api/debt/get.php',{case_id:CASE_ID},function(res){
        if(res.data){
            let d=res.data;
            $('#debt_date').val(d.clear_date?d.clear_date.replace(' ','T'):'');
            $('#debt_location').val(d.location||'');
            $('#debt_staff').val(d.staff_name||'');
            $('#debt_note').val(d.note||'');
            if(d.items) d.items.forEach(item=>addDebtItem(item.detail,item.amount));
        }
    },'json');
}
function addDebtItem(detail='',amount=''){
    debtItemCount++;
    let html='<div class="row mb-2 debt-item" id="debtRow'+debtItemCount+'"><div class="col-md-6"><input type="text" class="form-control" placeholder="รายละเอียดหนี้" value="'+detail+'" id="debt_detail_'+debtItemCount+'"></div><div class="col-md-4"><input type="number" class="form-control debt-amount" placeholder="จำนวนเงิน" value="'+amount+'" step="0.01" id="debt_amount_'+debtItemCount+'" oninput="calcDebtTotal()"></div><div class="col-md-2"><button class="btn btn-danger" onclick="$(\'#debtRow'+debtItemCount+'\').remove();calcDebtTotal();">ลบ</button></div></div>';
    $('#debtItemsContainer').append(html);
}
function calcDebtTotal(){
    let total=0;
    $('.debt-amount').each(function(){ total+=parseFloat($(this).val())||0; });
    $('#debtTotal').text(numberFormat(total));
}
function saveDebt(){
    let items=[];
    $('.debt-item').each(function(){
        let detail=$(this).find('input[id^="debt_detail_"]').val();
        let amount=$(this).find('input[id^="debt_amount_"]').val();
        if(detail&&amount) items.push({detail:detail,amount:amount});
    });
    let data={case_id:CASE_ID, clear_date:$('#debt_date').val(), location:$('#debt_location').val(), staff_name:$('#debt_staff').val(), note:$('#debt_note').val(), items:items};
    $.post('../api/debt/save.php',JSON.stringify(data),function(res){
        alert(res.success?'บันทึกสำเร็จ!':'ผิดพลาด: '+res.message);
    },'json');
}

// ========== MORTGAGE ==========
function loadMortgage(){
    let html='<form><div class="row"><div class="col-md-6 mb-3"><label>วันที่จำนอง</label><input type="date" class="form-control" id="mort_date"></div><div class="col-md-6 mb-3"><label>ธนาคาร</label><select class="form-select" id="mort_bank"><option value="">เลือก</option><option>ธนาคารกรุงเทพ</option><option>ธนาคารกสิกรไทย</option><option>ธนาคารกรุงไทย</option><option>ธนาคารไทยพาณิชย์</option><option>ธนาคารออมสิน</option><option>ธนาคารอาคารสงเคราะห์</option></select></div></div><div class="row"><div class="col-md-6 mb-3"><label>ชื่อบัญชี</label><input type="text" class="form-control" id="mort_account_name"></div><div class="col-md-6 mb-3"><label>เลขบัญชี</label><input type="text" class="form-control" id="mort_account_number"></div></div><div class="row"><div class="col-md-6 mb-3"><label>วงเงินอนุมัติ</label><input type="number" class="form-control" id="mort_amount" step="0.01"></div></div><button type="button" class="btn btn-primary" onclick="saveMortgage()">บันทึก</button></form><hr><h6>ประวัติจำนอง</h6><div id="mortgageHistory"></div>';
    $('#mortgageContent').html(html);
    loadMortgageHistory();
    $.get('../api/mortgage/get.php',{case_id:CASE_ID},function(res){
        if(res.data){
            let m=res.data;
            $('#mort_date').val(m.mortgage_date); $('#mort_bank').val(m.bank_name);
            $('#mort_account_name').val(m.account_name); $('#mort_account_number').val(m.account_number);
            $('#mort_amount').val(m.approved_amount);
        }
    },'json');
}
function saveMortgage(){
    let data={case_id:CASE_ID, mortgage_date:$('#mort_date').val(), bank_name:$('#mort_bank').val(), account_name:$('#mort_account_name').val(), account_number:$('#mort_account_number').val(), approved_amount:$('#mort_amount').val()};
    $.post('../api/mortgage/save.php',JSON.stringify(data),function(res){
        alert(res.success?'บันทึกสำเร็จ!':'ผิดพลาด: '+res.message);
        if(res.success) loadMortgageHistory();
    },'json');
}
function loadMortgageHistory(){
    $.get('../api/mortgage/list.php',{case_id:CASE_ID},function(res){
        if(res.success&&res.data&&res.data.length>0){
            let html='<ul class="list-group">';
            res.data.forEach(function(m){ html+='<li class="list-group-item"><strong>'+m.bank_name+'</strong> - '+m.mortgage_date+'<br>วงเงิน: '+numberFormat(m.approved_amount)+' บาท</li>'; });
            html+='</ul>'; $('#mortgageHistory').html(html);
        }else{ $('#mortgageHistory').html('<p class="text-muted">ไม่มีประวัติ</p>'); }
    },'json');
}

// ========== INSPECTION ==========
function showInspectionForm(){
    $('#inspection_id').val('');
    $('#inspectionForm')[0].reset();
    $('#inspection_date').val(new Date().toISOString().slice(0, 16));
    $('#imagePreview').html('');
    $('#existingImages').hide().html('');
    $('#inspectionModalTitle').text('เพิ่มการตรวจห้อง');
    $('#inspectionModal').modal('show');
}

// พรีวิวรูปภาพก่อนอัปโหลด
function previewInspectionImages() {
    let files = $('#inspection_photos')[0].files;
    let html = '';
    
    if (files.length > 5) {
        alert('อัปโหลดได้สูงสุด 5 รูป');
        $('#inspection_photos').val('');
        return;
    }
    
    for (let i = 0; i < files.length; i++) {
        let file = files[i];
        
        // ตรวจสอบประเภทไฟล์
        if (!file.type.match('image.*')) {
            alert('กรุณาเลือกไฟล์รูปภาพเท่านั้น');
            continue;
        }
        
        // ตรวจสอบขนาด
        if (file.size > 5 * 1024 * 1024) {
            alert('ไฟล์ ' + file.name + ' มีขนาดเกิน 5MB');
            continue;
        }
        
        let reader = new FileReader();
        reader.onload = (function(f) {
            return function(e) {
                html += 
                '<div class="col-md-4 col-sm-6 mb-2">' +
                    '<div class="position-relative">' +
                        '<img src="' + e.target.result + '" class="img-thumbnail" style="height: 120px; width: 100%; object-fit: cover;">' +
                        '<span class="position-absolute top-0 end-0 badge bg-primary m-1">' + (i + 1) + '</span>' +
                        '<small class="d-block text-truncate">' + f.name + '</small>' +
                    '</div>' +
                '</div>';
                $('#imagePreview').html(html);
            };
        })(file);
        reader.readAsDataURL(file);
    }
}

function saveInspection(){
    let round = $('#inspection_round').val();
    let inspect_date = $('#inspection_date').val();
    let status = $('#inspection_status').val();
    let note = $('#inspection_note').val().trim();
    
    if (!inspect_date) {
        alert('กรุณาระบุวันที่ตรวจ');
        return;
    }
    
    if (!confirm('บันทึกผลตรวจห้องครั้งที่ ' + round + '?\nสถานะ: ' + (status === 'pass' ? 'ผ่าน' : 'ไม่ผ่าน'))) {
        return;
    }
    
    // ใช้ FormData เพื่อส่งรูปภาพ
    let formData = new FormData();
    formData.append('case_id', CASE_ID);
    formData.append('round', round);
    formData.append('inspect_date', inspect_date);
    formData.append('status', status);
    formData.append('note', note);
    
    // เพิ่มรูปภาพ
    let files = $('#inspection_photos')[0].files;
    for (let i = 0; i < files.length; i++) {
        formData.append('photos[]', files[i]);
    }
    
    // Disable button
    let btn = $('.modal-footer .btn-primary');
    let origText = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...').prop('disabled', true);
    
    $.ajax({
        url: '../api/inspection/save.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res){
            btn.html(origText).prop('disabled', false);
            
            if (res.success) {
                $('#inspectionModal').modal('hide');
                alert('บันทึกผลตรวจห้องครั้งที่ ' + round + ' สำเร็จ! 🔍');
                loadInspectionList();
            } else {
                alert('ผิดพลาด: ' + (res.message || 'ไม่สามารถบันทึกได้'));
            }
        },
        error: function(xhr){
            btn.html(origText).prop('disabled', false);
            alert('ไม่สามารถบันทึกได้ (Status: ' + xhr.status + ')');
        }
    });
}

// เปิดรูปภาพใน Modal (Bootstrap 5)
function openImageModal(imageSrc, title) {
    let modalHtml = 
    '<div class="modal fade" id="imageViewerModal" tabindex="-1">' +
        '<div class="modal-dialog modal-xl modal-dialog-centered">' +
            '<div class="modal-content bg-dark">' +
                '<div class="modal-header border-0">' +
                    (title ? '<h6 class="modal-title text-white">' + title + '</h6>' : '') +
                    '<button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>' +
                '</div>' +
                '<div class="modal-body text-center p-2">' +
                    '<img src="' + imageSrc + '" class="img-fluid" style="max-height: 85vh;" onerror="this.src=\'../assets/img/no-image.png\'">' +
                '</div>' +
                '<div class="modal-footer border-0 justify-content-center">' +
                    '<a href="' + imageSrc + '" target="_blank" class="btn btn-light btn-sm"><i class="fas fa-external-link-alt"></i> ดูรูปเต็ม</a>' +
                    '<a href="' + imageSrc + '" download class="btn btn-primary btn-sm"><i class="fas fa-download"></i> ดาวน์โหลด</a>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
    
    $('#imageViewerModal').remove();
    $('body').append(modalHtml);
    
    let modal = new bootstrap.Modal(document.getElementById('imageViewerModal'));
    modal.show();
    
    $('#imageViewerModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function loadInspectionList(){
    $.get('../api/inspection/list.php', { case_id: CASE_ID }, function(res){
        if (res.success && res.data && res.data.length > 0) {
            let html = '<div class="row">';
            
            res.data.forEach(function(i){
                let statusBadge = i.status === 'pass' 
                    ? '<span class="badge bg-success">✅ ผ่าน</span>' 
                    : '<span class="badge bg-danger">❌ ไม่ผ่าน</span>';
                
                // แสดงรูปภาพ (ถ้ามี)
                let imagesHtml = '';
                // แสดงรูปภาพ (คลิกเพื่อดูใหญ่)
                if (i.photos && i.photos.length > 0) {
                    imagesHtml = '<div class="row mt-2">';
                    i.photos.forEach(function(photo) {
                        imagesHtml += 
                        '<div class="col-md-3 col-sm-4 col-6 mb-2">' +
                            '<a href="#" onclick="openImageModal(\'../' + photo + '\'); return false;" ' +
                            'title="คลิกเพื่อดูรูปใหญ่" style="cursor: pointer;">' +
                                '<img src="../' + photo + '" class="img-thumbnail" ' +
                                    'style="height: 100px; width: 100%; object-fit: cover; transition: transform 0.2s;" ' +
                                    'onmouseover="this.style.transform=\'scale(1.05)\'" ' +
                                    'onmouseout="this.style.transform=\'scale(1)\'" ' +
                                    'onerror="this.src=\'../assets/img/no-image.png\'">' +
                            '</a>' +
                        '</div>';
                    });
                    imagesHtml += '</div>';
                }
                
                html += 
                '<div class="col-md-6 mb-3">' +
                    '<div class="card h-100">' +
                        '<div class="card-header d-flex justify-content-between align-items-center py-2">' +
                            '<h6 class="mb-0">ครั้งที่ ' + i.round + '</h6>' +
                            statusBadge +
                        '</div>' +
                        '<div class="card-body py-2">' +
                            '<p class="mb-1"><small><i class="far fa-calendar"></i> ' + formatDate(i.inspect_date) + '</small></p>' +
                            (i.note ? '<p class="mb-1"><small>' + i.note + '</small></p>' : '') +
                            imagesHtml +
                        '</div>' +
                    '</div>' +
                '</div>';
            });
            
            html += '</div>';
            $('#inspectionContent').html(html);
        } else {
            $('#inspectionContent').html(
                '<div class="text-center py-5 text-muted">' +
                    '<i class="fas fa-search fa-4x mb-3"></i>' +
                    '<h5>ยังไม่มีการตรวจห้อง</h5>' +
                    '<p>คลิกปุ่ม "เพิ่มการตรวจ" เพื่อบันทึกผลตรวจห้อง</p>' +
                    '<button class="btn btn-primary" onclick="showInspectionForm()">' +
                        '<i class="fas fa-plus"></i> เพิ่มการตรวจครั้งแรก</button>' +
                '</div>'
            );
        }
    }, 'json').fail(function() {
        $('#inspectionContent').html('<div class="alert alert-danger">โหลดข้อมูลไม่สำเร็จ</div>');
    });
}

// ========== HELPERS ==========
function getGradeColor(g){ return {'A+':'success','A':'primary','B':'warning'}[g]||'secondary';}
function getStatusColor(s){ return {'interested':'primary','high_interest':'success','pending':'warning','not_interested':'danger','cancelled':'dark'}[s]||'secondary';}
function getStatusLabel(s){
    let labels={'interested':'สนใจ','high_interest':'สนใจมาก','pending':'รอดำเนินการ','negotiating':'กำลังต่อรอง','site_visit':'นัดดูห้อง','document_submitted':'ส่งเอกสาร','waiting_approval':'รออนุมัติ','not_interested':'ไม่สนใจ','cancelled':'ยกเลิก'};
    return labels[s]||s;
}
function formatDate(d){
    if(!d) return '-';
    try{ return new Date(d).toLocaleDateString('th-TH',{year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){return d;}
}
function numberFormat(n){
    if(!n&&n!==0) return '0';
    return new Intl.NumberFormat('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2}).format(parseFloat(n));
}
</script>

<?php include_once '../includes/footer.php'; ?>