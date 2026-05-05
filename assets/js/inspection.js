// assets/js/inspection.js
const CASE_ID = new URLSearchParams(window.location.search).get('case_id');

$(document).ready(function() {
    loadInspections();
});

function showInspectionForm() {
    $('#inspection_id').val('');
    $('#inspectionForm')[0].reset();
    $('#inspection_date').val(new Date().toISOString().slice(0, 16));
    $('#inspectionModal').modal('show');
}

function loadInspections() {
    $.ajax({
        url: '../api/inspection/list.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-striped">';
                html += '<thead><tr><th>ครั้งที่</th><th>วันที่</th><th>สถานะ</th><th>หมายเหตุ</th><th>จัดการ</th></tr></thead><tbody>';
                
                response.data.forEach(i => {
                    html += `
                        <tr>
                            <td>ครั้งที่ ${i.round}</td>
                            <td>${formatDate(i.inspect_date)}</td>
                            <td>
                                <span class="badge bg-${i.status === 'pass' ? 'success' : 'danger'}">
                                    ${i.status === 'pass' ? 'ผ่าน' : 'ไม่ผ่าน'}
                                </span>
                            </td>
                            <td>${i.note || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="editInspection(${i.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>`;
                });
                
                html += '</tbody></table></div>';
                $('#inspectionList').html(html);
            } else {
                $('#inspectionList').html('<p class="text-muted text-center py-4">ยังไม่มีการตรวจห้อง</p>');
            }
        }
    });
}

function saveInspection() {
    if(!$('#inspection_round').val() || !$('#inspection_date').val()) {
        Swal.fire('กรุณากรอกข้อมูลให้ครบ', '', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'บันทึกการตรวจห้อง?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก'
    }).then((result) => {
        if(result.isConfirmed) {
            const formData = new FormData();
            formData.append('case_id', CASE_ID);
            formData.append('round', $('#inspection_round').val());
            formData.append('inspect_date', $('#inspection_date').val());
            formData.append('status', $('#inspection_status').val());
            formData.append('note', $('#inspection_note').val());
            
            if($('#inspection_id').val()) {
                formData.append('id', $('#inspection_id').val());
            }
            
            const files = $('#inspection_files')[0].files;
            for(let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            
            $.ajax({
                url: '../api/inspection/save.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#inspectionModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: 'บันทึกการตรวจห้องเรียบร้อย',
                            timer: 1500
                        });
                        loadInspections();
                    }
                }
            });
        }
    });
}