// assets/js/mortgage.js
const CASE_ID = new URLSearchParams(window.location.search).get('case_id');

$(document).ready(function() {
    loadBanks();
    loadMortgageHistory();
    loadMortgageData();
});

function loadBanks() {
    $.ajax({
        url: '../api/master/get.php',
        type: 'GET',
        data: { type: 'bank' },
        success: function(data) {
            let options = '<option value="">เลือกธนาคาร</option>';
            data.forEach(item => {
                options += `<option value="${item.value}">${item.value}</option>`;
            });
            $('#mortgage_bank').html(options);
        }
    });
}

function loadMortgageData() {
    $.ajax({
        url: '../api/mortgage/get.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.data) {
                const m = response.data;
                $('#mortgage_date').val(m.mortgage_date);
                $('#mortgage_bank').val(m.bank_name);
                $('#mortgage_account_name').val(m.account_name);
                $('#mortgage_account_number').val(m.account_number);
                $('#mortgage_amount').val(m.approved_amount);
            }
        }
    });
}

function loadMortgageHistory() {
    $.ajax({
        url: '../api/mortgage/list.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) {
                let html = '<div class="list-group">';
                response.data.forEach(m => {
                    html += `
                        <div class="list-group-item">
                            <h6>${m.bank_name}</h6>
                            <p class="mb-1">วงเงิน: ${numberFormat(m.approved_amount)} บาท</p>
                            <small>วันที่: ${m.mortgage_date}</small>
                        </div>`;
                });
                html += '</div>';
                $('#mortgageHistory').html(html);
            } else {
                $('#mortgageHistory').html('<p class="text-muted">ยังไม่มีประวัติ</p>');
            }
        }
    });
}

function saveMortgage() {
    if(!$('#mortgage_date').val() || !$('#mortgage_bank').val()) {
        Swal.fire('กรุณากรอกข้อมูลให้ครบ', '', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'บันทึกการจำนอง?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/mortgage/save.php',
                type: 'POST',
                data: JSON.stringify({
                    case_id: CASE_ID,
                    mortgage_date: $('#mortgage_date').val(),
                    bank_name: $('#mortgage_bank').val(),
                    account_name: $('#mortgage_account_name').val(),
                    account_number: $('#mortgage_account_number').val(),
                    approved_amount: $('#mortgage_amount').val()
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: 'บันทึกการจำนองเรียบร้อย',
                            timer: 1500
                        });
                        loadMortgageHistory();
                    }
                }
            });
        }
    });
}