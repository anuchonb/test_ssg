// assets/js/case_detail.js

const CASE_ID = <?php echo $case_id; ?>;
let currentFollowCount = 0;

$(document).ready(function() {
    loadCaseInfo();
    loadCustomerDetail();
    
    // Load tab content when clicked
    $('#caseTabs button').on('click', function(e) {
        const target = $(this).data('bs-target');
        loadTabContent(target);
    });
});

// Load case basic info
function loadCaseInfo() {
    $.ajax({
        url: '../api/case/get.php',
        type: 'GET',
        data: { id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                const c = response.data;
                $('#caseStatus').text(c.status);
                $('#customerName').text(c.customer_name);
                $('#ownerName').text(c.owner_name);
                $('#caseDate').text(c.case_date);
            }
        }
    });
}

// Load customer detail (Tab 1)
function loadCustomerDetail() {
    $.ajax({
        url: '../api/case/get_customer.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                const c = response.data;
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr><th>ชื่อ</th><td>${c.name}</td></tr>
                                <tr><th>เบอร์โทร</th><td>${c.phone}</td></tr>
                                <tr><th>Facebook</th><td>${c.facebook || '-'}</td></tr>
                                <tr><th>Line ID</th><td>${c.line_id || '-'}</td></tr>
                                <tr><th>เพจ</th><td>${c.page_name || '-'}</td></tr>
                                <tr><th>ช่องทาง</th><td>${c.channel || '-'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr><th>เกรด</th><td>${c.grade || '-'}</td></tr>
                                <tr><th>โครงการ</th><td>${c.project_name || '-'}</td></tr>
                                <tr><th>ราคา</th><td>${numberFormat(c.price)}</td></tr>
                                <tr><th>เงินทอน</th><td>${numberFormat(c.cashback)}</td></tr>
                                <tr><th>ลักษณะ</th><td>${c.living_type === 'rent' ? 'ปล่อยเช่า' : 'อยู่เอง'}</td></tr>
                                <tr><th>โซน</th><td>${c.zone || '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tr><th>บริษัท</th><td>${c.company_name || '-'}</td></tr>
                                <tr><th>อายุงาน</th><td>${c.work_age_month ? c.work_age_month + ' เดือน' : '-'}</td></tr>
                                <tr><th>สวัสดิการ</th><td>${c.welfare || '-'}</td></tr>
                                <tr><th>ภาระหนี้</th><td>${c.debt_status === 'have' ? 'มี' : 'ไม่มี'}</td></tr>
                            </table>
                        </div>
                    </div>`;
                $('#customerDetail').html(html);
            }
        }
    });
}

// Load tab content dynamically
function loadTabContent(target) {
    switch(target) {
        case '#tab-follow':
            loadFollowList();
            break;
        case '#tab-kpi':
            loadKpiList();
            break;
        case '#tab-preapprove':
            loadPreapproveForm();
            break;
        case '#tab-documents':
            loadDocuments();
            break;
        case '#tab-bank':
            loadBankContent();
            break;
        case '#tab-approval':
            loadApprovalContent();
            break;
        case '#tab-debt':
            loadDebtContent();
            break;
        case '#tab-mortgage':
            loadMortgageContent();
            break;
        case '#tab-inspection':
            loadInspectionContent();
            break;
    }
}

// ============ FOLLOW MODULE ============
function showFollowForm() {
    // Get next follow count
    $.ajax({
        url: '../api/follow/get_count.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        success: function(response) {
            $('#follow_step').val(response.count + 1);
        }
    });
    
    loadFollowStatuses();
    $('#followModal').modal('show');
}

function loadFollowStatuses() {
    $.ajax({
        url: '../api/master/get.php',
        type: 'GET',
        data: { type: 'follow_status' },
        success: function(data) {
            let options = '<option value="">เลือกสถานะ</option>';
            data.forEach(item => {
                options += `<option value="${item.value}">${item.value}</option>`;
            });
            $('#follow_status').html(options);
        }
    });
}

function saveFollow() {
    Swal.fire({
        title: 'ยืนยันการบันทึก?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/follow/add.php',
                type: 'POST',
                data: JSON.stringify({
                    case_id: CASE_ID,
                    step: $('#follow_step').val(),
                    status: $('#follow_status').val(),
                    note: $('#follow_note').val()
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#followModal').modal('hide');
                        Swal.fire('สำเร็จ!', 'บันทึกการติดตามเรียบร้อย', 'success');
                        loadFollowList();
                        loadCaseInfo();
                    }
                }
            });
        }
    });
}

function loadFollowList() {
    $.ajax({
        url: '../api/follow/list.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) {
                let html = '<div class="timeline">';
                response.data.forEach(f => {
                    html += `
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>ครั้งที่ ${f.step} - ${f.status}</h6>
                                <small class="text-muted">${f.created_at}</small>
                                <p>${f.note || '-'}</p>
                            </div>
                        </div>`;
                });
                html += '</div>';
                $('#followList').html(html);
            } else {
                $('#followList').html('<p class="text-muted">ยังไม่มีการติดตาม</p>');
            }
        }
    });
}

// ============ KPI MODULE ============
function showKpiForm() {
    loadKpiReasons();
    $('#kpiModal').modal('show');
}

function loadKpiReasons() {
    $.ajax({
        url: '../api/master/get.php',
        type: 'GET',
        data: { type: 'kpi_reason' },
        success: function(data) {
            let options = '<option value="">เลือกเหตุผล</option>';
            data.forEach(item => {
                options += `<option value="${item.value}">${item.value}</option>`;
            });
            $('#kpi_reason').html(options);
        }
    });
}

function saveKpi() {
    Swal.fire({
        title: 'ยืนยันการตรวจ KPI?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/kpi/check.php',
                type: 'POST',
                data: JSON.stringify({
                    case_id: CASE_ID,
                    checker_id: <?php echo $_SESSION['user_id']; ?>,
                    result: $('#kpi_result').val(),
                    reason: $('#kpi_reason').val()
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#kpiModal').modal('hide');
                        Swal.fire('สำเร็จ!', 'บันทึกผล KPI เรียบร้อย', 'success');
                        loadKpiList();
                    }
                }
            });
        }
    });
}

function loadKpiList() {
    $.ajax({
        url: '../api/kpi/list.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) {
                let html = '<table class="table table-striped"><thead><tr><th>ผู้ตรวจ</th><th>ผล</th><th>เหตุผล</th><th>วันที่</th></tr></thead><tbody>';
                response.data.forEach(k => {
                    html += `
                        <tr>
                            <td>${k.checker_name}</td>
                            <td><span class="badge bg-${k.result === 'pass' ? 'success' : 'danger'}">${k.result === 'pass' ? 'ผ่าน' : 'ไม่ผ่าน'}</span></td>
                            <td>${k.reason || '-'}</td>
                            <td>${k.created_at}</td>
                        </tr>`;
                });
                html += '</tbody></table>';
                $('#kpiList').html(html);
            } else {
                $('#kpiList').html('<p class="text-muted">ยังไม่มีการตรวจ KPI</p>');
            }
        }
    });
}

// ============ PRE-APPROVE MODULE ============
function loadPreapproveForm() {
    $.ajax({
        url: '../api/preapprove/get.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            let html = `
                <form id="preapproveFormData">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">สถานะ</label>
                            <select class="form-select" id="pa_status">
                                <option value="processing">กำลังดำเนินการ</option>
                                <option value="approved">อนุมัติ</option>
                                <option value="rejected">ปฏิเสธ</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วงเงิน</label>
                            <input type="number" class="form-control" id="pa_amount" value="${response.data ? response.data.approved_amount : ''}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" id="pa_note" rows="2">${response.data ? response.data.note || '' : ''}</textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="savePreapprove()">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </form>`;
            $('#preapproveForm').html(html);
            
            if(response.data) {
                $('#pa_status').val(response.data.status);
            }
        }
    });
}

function savePreapprove() {
    Swal.fire({
        title: 'บันทึก Pre-Approve?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/preapprove/save.php',
                type: 'POST',
                data: JSON.stringify({
                    case_id: CASE_ID,
                    status: $('#pa_status').val(),
                    approved_amount: $('#pa_amount').val(),
                    note: $('#pa_note').val()
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire('สำเร็จ!', '', 'success');
                    }
                }
            });
        }
    });
}

// ============ HELPER FUNCTIONS ============
function numberFormat(number) {
    if(!number) return '-';
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
    }).format(number);
}