// assets/js/kpi_check.js

$(document).ready(function() {
    loadKpiReasons();
    loadPendingKpi();
    loadKpiStats();
    loadKpiHistory();
});

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

function loadPendingKpi() {
    $.ajax({
        url: '../api/kpi/pending.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                let html = '';
                response.data.forEach(c => {
                    html += `
                        <tr>
                            <td>#${c.case_id}</td>
                            <td>${c.customer_name}</td>
                            <td>${c.phone}</td>
                            <td>${c.owner_name}</td>
                            <td>${formatDate(c.created_at)}</td>
                            <td><span class="badge bg-warning">รอตรวจ</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="showKpiCheck(${c.case_id}, '${c.customer_name}', '${c.phone}')">
                                    <i class="fas fa-check"></i> ตรวจ
                                </button>
                            </td>
                        </tr>`;
                });
                $('#pendingKpiBody').html(html || '<tr><td colspan="7" class="text-center">ไม่มีรายการรอตรวจ</td></tr>');
            }
        }
    });
}

function loadKpiStats() {
    $.ajax({
        url: '../api/kpi/stats.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#pendingKpi').text(response.pending);
            $('#passKpi').text(response.pass);
            $('#failKpi').text(response.fail);
            $('#totalKpi').text(response.total);
        }
    });
}

function loadKpiHistory() {
    const dateFilter = $('#filterDate').val();
    const resultFilter = $('#filterResult').val();
    
    $.ajax({
        url: '../api/kpi/history.php',
        type: 'GET',
        data: { 
            date: dateFilter,
            result: resultFilter
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                let html = '';
                response.data.forEach(k => {
                    html += `
                        <tr>
                            <td>${formatDate(k.created_at)}</td>
                            <td>#${k.case_id}</td>
                            <td>${k.customer_name}</td>
                            <td>${k.checker_name}</td>
                            <td>
                                <span class="badge bg-${k.result === 'pass' ? 'success' : 'danger'}">
                                    ${k.result === 'pass' ? 'ผ่าน' : 'ไม่ผ่าน'}
                                </span>
                            </td>
                            <td>${k.reason || '-'}</td>
                        </tr>`;
                });
                $('#kpiHistoryBody').html(html || '<tr><td colspan="6" class="text-center">ไม่มีข้อมูล</td></tr>');
            }
        }
    });
}

function showKpiCheck(caseId, customerName, phone) {
    $('#kpi_case_id').val(caseId);
    $('#kpiCaseId').text(caseId);
    $('#kpiCaseInfo').html(`
        <div class="alert alert-info">
            <strong>ลูกค้า:</strong> ${customerName}<br>
            <strong>เบอร์โทร:</strong> ${phone}
        </div>
    `);
    $('#kpi_result').val('');
    $('#kpi_reason').val('');
    $('#kpi_note').val('');
    $('#kpiCheckModal').modal('show');
}

function submitKpiCheck() {
    if(!$('#kpi_result').val()) {
        Swal.fire('กรุณาเลือกผลการตรวจ', '', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'ยืนยันการตรวจ KPI?',
        text: `ผลการตรวจ: ${$('#kpi_result').val() === 'pass' ? 'ผ่าน' : 'ไม่ผ่าน'}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/kpi/check.php',
                type: 'POST',
                data: JSON.stringify({
                    case_id: $('#kpi_case_id').val(),
                    checker_id: <?php echo $_SESSION['user_id']; ?>,
                    result: $('#kpi_result').val(),
                    reason: $('#kpi_reason').val(),
                    note: $('#kpi_note').val()
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#kpiCheckModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: 'บันทึกผล KPI เรียบร้อย',
                            timer: 1500
                        });
                        loadPendingKpi();
                        loadKpiStats();
                        loadKpiHistory();
                    }
                }
            });
        }
    });
}