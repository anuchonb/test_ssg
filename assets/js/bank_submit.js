// assets/js/bank_submit.js
const CASE_ID = new URLSearchParams(window.location.search).get('case_id');

$(document).ready(function() {
    loadBanks();
    loadBankHistory();
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
            $('#bank_name').html(options);
        }
    });
}

function loadBankHistory() {
    $.ajax({
        url: '../api/bank/list.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) {
                let html = '<div class="list-group">';
                response.data.forEach(b => {
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">${b.bank_name}</h6>
                                <small>${b.submit_date}</small>
                            </div>
                            <p class="mb-1">${b.note || 'ไม่มีหมายเหตุ'}</p>
                            <small class="text-muted">${formatDate(b.created_at)}</small>
                        </div>`;
                });
                html += '</div>';
                $('#bankHistory').html(html);
            } else {
                $('#bankHistory').html('<p class="text-muted">ยังไม่มีการส่งธนาคาร</p>');
            }
        }
    });
}

function saveBankSubmit() {
    if(!$('#bank_name').val() || !$('#submit_date').val()) {
        Swal.fire('กรุณากรอกข้อมูลให้ครบ', '', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'ยืนยันการส่งธนาคาร?',
        html: `
            <p>ธนาคาร: <strong>${$('#bank_name').val()}</strong></p>
            <p>วันที่: <strong>${$('#submit_date').val()}</strong></p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/bank/submit.php',
                type: 'POST',
                data: JSON.stringify({
                    case_id: CASE_ID,
                    bank_name: $('#bank_name').val(),
                    submit_date: $('#submit_date').val(),
                    note: $('#bank_note').val()
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: 'บันทึกการส่งธนาคารเรียบร้อย',
                            timer: 1500
                        });
                        $('#bankSubmitForm')[0].reset();
                        loadBankHistory();
                    }
                }
            });
        }
    });
}