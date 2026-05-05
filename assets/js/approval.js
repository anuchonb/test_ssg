// assets/js/approval.js
const CASE_ID = new URLSearchParams(window.location.search).get('case_id');

$(document).ready(function() {
    loadApprovalData();
    
    // Auto-calculate total
    $('#ap_room_amount, #ap_insurance_amount, #ap_furniture_amount').on('input', function() {
        calculateTotalApproval();
    });
});

function calculateTotalApproval() {
    const room = parseFloat($('#ap_room_amount').val()) || 0;
    const insurance = parseFloat($('#ap_insurance_amount').val()) || 0;
    const furniture = parseFloat($('#ap_furniture_amount').val()) || 0;
    const total = room + insurance + furniture;
    $('#ap_total_amount').val(total);
}

function loadApprovalData() {
    $.ajax({
        url: '../api/approval/get.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.data) {
                const a = response.data;
                $('#ap_total_amount').val(a.total_amount);
                $('#ap_room_amount').val(a.room_amount);
                $('#ap_insurance_amount').val(a.insurance_amount);
                $('#ap_furniture_amount').val(a.furniture_amount);
                $('#ap_note').val(a.note);
                
                if(a.contract_date) {
                    $('#ap_contract_date').val(a.contract_date.replace(' ', 'T'));
                }
                if(a.transfer_date) {
                    $('#ap_transfer_date').val(a.transfer_date);
                }
                
                showApprovalSummary(a);
            }
        }
    });
}

function saveApproval() {
    Swal.fire({
        title: 'บันทึกผลอนุมัติ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/approval/save.php',
                type: 'POST',
                data: JSON.stringify({
                    case_id: CASE_ID,
                    total_amount: $('#ap_total_amount').val(),
                    room_amount: $('#ap_room_amount').val(),
                    insurance_amount: $('#ap_insurance_amount').val(),
                    furniture_amount: $('#ap_furniture_amount').val(),
                    contract_date: $('#ap_contract_date').val(),
                    transfer_date: $('#ap_transfer_date').val(),
                    note: $('#ap_note').val()
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: 'บันทึกผลอนุมัติเรียบร้อย',
                            timer: 1500
                        });
                        
                        // Show summary card
                        showApprovalSummary({
                            total_amount: $('#ap_total_amount').val(),
                            room_amount: $('#ap_room_amount').val(),
                            insurance_amount: $('#ap_insurance_amount').val(),
                            furniture_amount: $('#ap_furniture_amount').val(),
                            contract_date: $('#ap_contract_date').val(),
                            transfer_date: $('#ap_transfer_date').val()
                        });
                    }
                }
            });
        }
    });
}

function showApprovalSummary(data) {
    $('#approvalSummaryCard').show();
    $('#approvalSummary').html(`
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th>วงเงินห้อง</th><td>${numberFormat(data.room_amount)} บาท</td></tr>
                    <tr><th>วงเงินประกัน</th><td>${numberFormat(data.insurance_amount)} บาท</td></tr>
                    <tr><th>วงเงินเฟอร์นิเจอร์</th><td>${numberFormat(data.furniture_amount)} บาท</td></tr>
                    <tr class="table-success"><th>วงเงินรวม</th><td><strong>${numberFormat(data.total_amount)} บาท</strong></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th>วันเซ็นสัญญา</th><td>${formatDate(data.contract_date)}</td></tr>
                    <tr><th>วันโอน</th><td>${data.transfer_date || '-'}</td></tr>
                </table>
            </div>
        </div>
    `);
}

function copyApprovalToClipboard() {
    const roomAmount = $('#ap_room_amount').val() || 0;
    const insuranceAmount = $('#ap_insurance_amount').val() || 0;
    const furnitureAmount = $('#ap_furniture_amount').val() || 0;
    const totalAmount = $('#ap_total_amount').val() || 0;
    const transferDate = $('#ap_transfer_date').val() || '-';
    
    const text = `📋 ผลอนุมัติ Case #${CASE_ID}\n` +
                 `💰 วงเงินห้อง: ${numberFormat(roomAmount)} บาท\n` +
                 `🛡️ วงเงินประกัน: ${numberFormat(insuranceAmount)} บาท\n` +
                 `🪑 วงเงินเฟอร์นิเจอร์: ${numberFormat(furnitureAmount)} บาท\n` +
                 `💵 วงเงินรวม: ${numberFormat(totalAmount)} บาท\n` +
                 `📅 วันโอน: ${transferDate}`;
    
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'คัดลอกแล้ว!',
            text: 'คัดลอกข้อความไปยังคลิปบอร์ดเรียบร้อย',
            timer: 1500
        });
    });
}

function numberFormat(number) {
    if(!number) return '0';
    return new Intl.NumberFormat('th-TH').format(number);
}

function formatDate(dateString) {
    if(!dateString) return '-';
    return new Date(dateString).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}