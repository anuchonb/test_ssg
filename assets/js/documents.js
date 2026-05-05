// assets/js/documents.js
const CASE_ID = new URLSearchParams(window.location.search).get('case_id');

$(document).ready(function() {
    loadBanks();
    loadDocumentStatus();
    loadFileList();
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
            $('#doc_bank_name').html(options);
        }
    });
}

function loadDocumentStatus() {
    $.ajax({
        url: '../api/document/get.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.data) {
                const d = response.data;
                $('#doc_status_1').val(d.doc_status_1);
                $('#doc_status_2').val(d.doc_status_2);
                $('#doc_status_3').val(d.doc_status_3);
                $('#doc_bank_name').val(d.bank_name);
                $('#doc_bank_account').val(d.bank_account);
                $('#doc_precheck_status').val(d.precheck_status);
                $('#doc_debt_close_status').val(d.debt_close_status);
                $('#doc_note').val(d.note);
            }
        }
    });
}

function saveDocumentStatus() {
    Swal.fire({
        title: 'บันทึกสถานะเอกสาร?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/document/update.php',
                type: 'POST',
                data: JSON.stringify({
                    case_id: CASE_ID,
                    doc_status_1: $('#doc_status_1').val(),
                    doc_status_2: $('#doc_status_2').val(),
                    doc_status_3: $('#doc_status_3').val(),
                    bank_name: $('#doc_bank_name').val(),
                    bank_account: $('#doc_bank_account').val(),
                    precheck_status: $('#doc_precheck_status').val(),
                    debt_close_status: $('#doc_debt_close_status').val(),
                    note: $('#doc_note').val()
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire('สำเร็จ!', 'บันทึกสถานะเอกสารเรียบร้อย', 'success');
                    }
                }
            });
        }
    });
}

function uploadFile() {
    const fileInput = $('#upload_file')[0];
    if(!fileInput.files[0]) {
        Swal.fire('กรุณาเลือกไฟล์', '', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('case_id', CASE_ID);
    formData.append('file_type', $('#file_type').val());
    formData.append('file', fileInput.files[0]);
    
    $.ajax({
        url: '../api/file/upload.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                Swal.fire('สำเร็จ!', 'อัปโหลดไฟล์เรียบร้อย', 'success');
                $('#fileUploadForm')[0].reset();
                loadFileList();
            } else {
                Swal.fire('ผิดพลาด!', response.message, 'error');
            }
        }
    });
}

function loadFileList() {
    $.ajax({
        url: '../api/file/list.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) {
                let html = '<ul class="list-group">';
                response.data.forEach(f => {
                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file me-2"></i>
                                ${f.file_type}<br>
                                <small class="text-muted">${formatDate(f.created_at)}</small>
                            </div>
                            <div>
                                <a href="${f.file_path}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteFile(${f.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </li>`;
                });
                html += '</ul>';
                $('#fileList').html(html);
            } else {
                $('#fileList').html('<p class="text-muted">ยังไม่มีไฟล์</p>');
            }
        }
    });
}

function deleteFile(id) {
    Swal.fire({
        title: 'ลบไฟล์?',
        text: 'คุณต้องการลบไฟล์นี้ใช่หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/file/delete.php',
                type: 'POST',
                data: JSON.stringify({ id: id }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire('ลบแล้ว!', '', 'success');
                        loadFileList();
                    }
                }
            });
        }
    });
}