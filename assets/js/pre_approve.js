// assets/js/pre_approve.js
const CASE_ID = new URLSearchParams(window.location.search).get('case_id');

$(document).ready(function() {
    loadCustomerSummary();
    loadPreapproveData();
    loadPreapproveHistory();
    
    // Auto-calculate total
    $('#pa_room_amount, #pa_insurance_amount, #pa_furniture_amount').on('input', function() {
        calculateTotal();
    });
});

function calculateTotal() {
    const room = parseFloat($('#pa_room_amount').val()) || 0;
    const insurance = parseFloat($('#pa_insurance_amount').val()) || 0;
    const furniture = parseFloat($('#pa_furniture_amount').val()) || 0;
    const total = room + insurance + furniture;
    $('#pa_amount').val(total);
}

function loadCustomerSummary() {
    $.ajax({
        url: '../api/case/get_customer.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                const c = response.data;
                $('#customerSummary').html(`
                    <p><strong>ชื่อ:</strong> ${c.name}</p>
                    <p><strong>เบอร์:</strong> ${c.phone}</p>
                    <p><strong>โครงการ:</strong> ${c.project_name || '-'}</p>
                    <p><strong>ราคา:</strong> ${numberFormat(c.price)}</p>
                    <p><strong>เกรด:</strong> ${c.grade || '-'}</p>
                    <p><strong>ภาระหนี้:</strong> ${c.debt_status === 'have' ? 'มี' : 'ไม่มี'}</p>
                `);
            }
        }
    });
}

function loadPreapproveData() {
    $.ajax({
        url: '../api/preapprove/get.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.data) {
                const p = response.data;
                $('#pa_status').val(p.status);
                $('#pa_amount').val(p.approved_amount);
                $('#pa_note').val(p.note);
                if(p.contract_date) {
                    $('#pa_contract_date').val(p.contract_date.split(' ')[0]);
                }
                if(p.transfer_date) {
                    $('#pa_transfer_date').val(p.transfer_date);
                }
            }
        }
    });
}

function loadPreapproveHistory() {
    $.ajax({
        url: '../api/preapprove/history.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) {
                let html = '<ul class="list-group">';
                response.data.forEach(p => {
                    html += `
                        <li class="list-group-item">
                            <small class="text-muted">${formatDate(p.created_at)}</small><br>
                            <span class="badge bg-${getStatusColor(p.status)}">${getStatusText(p.status)}</span>
                            ${p.approved_amount ? ' - ' + numberFormat(p.approved_amount) : ''}
                        </li>`;
                });
                html += '</ul>';
                $('#preapproveHistory').html(html);
            } else {
                $('#preapproveHistory').html('<p class="text-muted">ไม่มีประวัติ</p>');
            }
        }
    });
}

function savePreapprove() {
    Swal.fire({
        title: 'บันทึก Pre-Approve?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/preapprove/save.php',
                type: 'POST',
                data: JSON.stringify({
                    case_id: CASE_ID,
                    status: $('#pa_status').val(),
                    approved_amount: $('#pa_amount').val(),
                    room_amount: $('#pa_room_amount').val(),
                    insurance_amount: $('#pa_insurance_amount').val(),
                    furniture_amount: $('#pa_furniture_amount').val(),
                    contract_date: $('#pa_contract_date').val(),
                    transfer_date: $('#pa_transfer_date').val(),
                    note: $('#pa_note').val()
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: 'บันทึก Pre-Approve เรียบร้อย',
                            timer: 1500
                        });
                        loadPreapproveHistory();
                    }
                }
            });
        }
    });
}

function getStatusColor(status) {
    const colors = {
        'processing': 'warning',
        'approved': 'success',
        'rejected': 'danger'
    };
    return colors[status] || 'secondary';
}

function getStatusText(status) {
    const texts = {
        'processing': 'กำลังดำเนินการ',
        'approved': 'อนุมัติ',
        'rejected': 'ปฏิเสธ'
    };
    return texts[status] || status;
}

function numberFormat(number) {
    if(!number) return '0.00';
    return new Intl.NumberFormat('th-TH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(number);
}