// assets/js/debt.js
const CASE_ID = new URLSearchParams(window.location.search).get('case_id');
let debtItemCount = 0;
let currentDebtId = null;

$(document).ready(function() {
    loadDebtData();
    loadDebtHistory();
});

function addDebtItem(detail = '', amount = '') {
    debtItemCount++;
    const html = `
        <div class="row mb-2 debt-item-row" id="debtItemRow${debtItemCount}">
            <div class="col-md-6">
                <input type="text" class="form-control" placeholder="รายละเอียดหนี้" 
                    value="${detail}" id="debt_detail_${debtItemCount}">
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="number" class="form-control debt-amount" placeholder="จำนวนเงิน" 
                        value="${amount}" step="0.01" id="debt_amount_${debtItemCount}"
                        onchange="calculateTotalDebt()">
                    <span class="input-group-text">บาท</span>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-danger" onclick="removeDebtItem(${debtItemCount})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>`;
    $('#debtItemsContainer').append(html);
}

function removeDebtItem(id) {
    $(`#debtItemRow${id}`).remove();
    calculateTotalDebt();
}

function calculateTotalDebt() {
    let total = 0;
    $('.debt-amount').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    $('#debtTotal').text(numberFormat(total) + ' บาท');
    updateDebtSummary();
}

function updateDebtSummary() {
    let summary = '<ul class="list-unstyled">';
    let total = 0;
    
    $('.debt-item-row').each(function() {
        const detail = $(this).find('input[id^="debt_detail_"]').val();
        const amount = parseFloat($(this).find('input[id^="debt_amount_"]').val()) || 0;
        if(detail) {
            summary += `<li>📌 ${detail}: ${numberFormat(amount)} บาท</li>`;
            total += amount;
        }
    });
    
    summary += `<li class="mt-2"><strong>💰 รวม: ${numberFormat(total)} บาท</strong></li>`;
    summary += '</ul>';
    
    $('#debtSummary').html(summary);
}

function saveDebt() {
    // Validate
    if($('.debt-item-row').length === 0) {
        Swal.fire('กรุณาเพิ่มรายการหนี้', '', 'warning');
        return;
    }
    
    const items = [];
    $('.debt-item-row').each(function() {
        const detail = $(this).find('input[id^="debt_detail_"]').val();
        const amount = $(this).find('input[id^="debt_amount_"]').val();
        if(detail && amount) {
            items.push({ detail: detail, amount: amount });
        }
    });
    
    if(items.length === 0) {
        Swal.fire('กรุณากรอกรายละเอียดและจำนวนเงิน', '', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'บันทึกการปิดหนี้?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/debt/save.php',
                type: 'POST',
                data: JSON.stringify({
                    case_id: CASE_ID,
                    clear_date: $('#debt_clear_date').val(),
                    location: $('#debt_location').val(),
                    staff_name: $('#debt_staff_name').val(),
                    note: $('#debt_note').val(),
                    items: items
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: 'บันทึกการปิดหนี้เรียบร้อย',
                            timer: 1500
                        });
                        loadDebtHistory();
                    }
                }
            });
        }
    });
}

function loadDebtData() {
    $.ajax({
        url: '../api/debt/get.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.data) {
                const d = response.data;
                currentDebtId = d.id;
                $('#debt_clear_date').val(d.clear_date ? d.clear_date.replace(' ', 'T') : '');
                $('#debt_location').val(d.location);
                $('#debt_staff_name').val(d.staff_name);
                $('#debt_note').val(d.note);
                
                if(d.items) {
                    d.items.forEach(item => {
                        addDebtItem(item.detail, item.amount);
                    });
                }
            } else {
                // Add empty row
                addDebtItem();
            }
        }
    });
}

function loadDebtHistory() {
    $.ajax({
        url: '../api/debt/history.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) {
                let html = '<div class="list-group">';
                response.data.forEach(d => {
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <h6>ปิดหนี้เมื่อ ${formatDate(d.clear_date)}</h6>
                            </div>
                            <small>สถานที่: ${d.location || '-'}</small><br>
                            <small>จนท.: ${d.staff_name || '-'}</small>
                        </div>`;
                });
                html += '</div>';
                $('#debtHistory').html(html);
            } else {
                $('#debtHistory').html('<p class="text-muted">ยังไม่มีประวัติ</p>');
            }
        }
    });
}

function copyDebtToClipboard() {
    let text = `💳 ปิดหนี้ Case #${CASE_ID}\n`;
    
    $('.debt-item-row').each(function() {
        const detail = $(this).find('input[id^="debt_detail_"]').val();
        const amount = $(this).find('input[id^="debt_amount_"]').val();
        if(detail && amount) {
            text += `📌 ${detail}: ${numberFormat(amount)} บาท\n`;
        }
    });
    
    let total = 0;
    $('.debt-amount').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    text += `💰 รวม: ${numberFormat(total)} บาท`;
    
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'คัดลอกแล้ว!',
            timer: 1500
        });
    });
}