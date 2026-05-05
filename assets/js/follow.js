// assets/js/follow.js
const CASE_ID = new URLSearchParams(window.location.search).get('case_id');

$(document).ready(function() {
    loadFollowStatuses();
    loadFollowData();
    loadFollowStats();
});

function loadFollowStatuses() {
    $.ajax({
        url: '../api/master/get.php',
        type: 'GET',
        data: { type: 'follow_status' },
        dataType: 'json',
        success: function(data) {
            let options = '<option value="">เลือกสถานะ</option>';
            data.forEach(item => {
                options += `<option value="${item.value}">${getStatusLabel(item.value)}</option>`;
            });
            $('#follow_status').html(options);
        }
    });
}

function getStatusLabel(status) {
    const labels = {
        'interested': 'สนใจ',
        'pending': 'รอดำเนินการ',
        'document_submitted': 'ส่งเอกสารแล้ว',
        'negotiating': 'กำลังต่อรอง',
        'cancelled': 'ยกเลิก',
        'not_qualified': 'ไม่ผ่านคุณสมบัติ',
        'not_interested': 'ไม่สนใจ',
        'high_interest': 'สนใจมาก',
        'bank_submitted': 'ส่งธนาคารแล้ว',
        'waiting_approval': 'รออนุมัติ',
        'approved': 'อนุมัติแล้ว',
        'transferred': 'โอนแล้ว',
        'site_visit': 'นัดดูห้อง'
    };
    return labels[status] || status;
}

function loadFollowData() {
    $.ajax({
        url: '../api/follow/list.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                renderFollowTimeline(response.data);
                renderFollowTable(response.data);
            }
        }
    });
}

function renderFollowTimeline(follows) {
    if(follows.length === 0) {
        $('#followTimeline').html('<p class="text-muted text-center py-4">ยังไม่มีการติดตาม</p>');
        return;
    }
    
    let html = '<div class="timeline">';
    follows.forEach((f, index) => {
        const statusClass = getStatusClass(f.status);
        html += `
            <div class="timeline-item">
                <div class="timeline-marker bg-${statusClass}"></div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-1">ครั้งที่ ${f.step}: ${getStatusLabel(f.status)}</h6>
                        <small class="text-muted">${formatDate(f.created_at)}</small>
                    </div>
                    <p class="mb-1">${f.note || 'ไม่มีบันทึก'}</p>
                    ${f.next_date ? `<small class="text-info">นัดหมายครั้งต่อไป: ${formatDate(f.next_date)}</small>` : ''}
                </div>
            </div>`;
    });
    html += '</div>';
    $('#followTimeline').html(html);
}

function renderFollowTable(follows) {
    let html = '';
    follows.forEach(f => {
        html += `
            <tr>
                <td>ครั้งที่ ${f.step}</td>
                <td>${formatDate(f.created_at)}</td>
                <td><span class="badge bg-${getStatusClass(f.status)}">${getStatusLabel(f.status)}</span></td>
                <td>${truncateText(f.note, 50)}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editFollow(${f.id})" title="แก้ไข">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteFollow(${f.id})" title="ลบ">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`;
    });
    $('#followTableBody').html(html || '<tr><td colspan="5" class="text-center">ไม่มีข้อมูล</td></tr>');
}

function loadFollowStats() {
    $.ajax({
        url: '../api/follow/stats.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#totalFollows').text(response.total);
                $('#interestedCount').text(response.interested);
                $('#pendingCount').text(response.pending);
                $('#rejectedCount').text(response.rejected);
            }
        }
    });
}

function showFollowForm() {
    $('#follow_id').val('');
    $('#followForm')[0].reset();
    $('#followModalTitle').text('เพิ่มการติดตาม');
    
    // Get next follow count
    $.ajax({
        url: '../api/follow/get_count.php',
        type: 'GET',
        data: { case_id: CASE_ID },
        success: function(response) {
            $('#follow_step').val(response.count + 1);
        }
    });
    
    $('#followFormModal').modal('show');
}

function editFollow(id) {
    $.ajax({
        url: '../api/follow/get.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                const f = response.data;
                $('#follow_id').val(f.id);
                $('#follow_step').val(f.step);
                $('#follow_status').val(f.status);
                $('#follow_note').val(f.note);
                $('#followModalTitle').text('แก้ไขการติดตาม');
                $('#followFormModal').modal('show');
            }
        }
    });
}

function saveFollow() {
    if(!$('#follow_step').val() || !$('#follow_status').val()) {
        Swal.fire('กรุณากรอกข้อมูลให้ครบ', '', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'ยืนยันการบันทึก?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            const formData = new FormData();
            formData.append('case_id', CASE_ID);
            formData.append('step', $('#follow_step').val());
            formData.append('status', $('#follow_status').val());
            formData.append('note', $('#follow_note').val());
            formData.append('next_date', $('#follow_next_date').val());
            
            if($('#follow_id').val()) {
                formData.append('id', $('#follow_id').val());
            }
            
            // Handle file upload
            const files = $('#follow_file')[0].files;
            for(let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            
            $.ajax({
                url: '../api/follow/add.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#followFormModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: 'บันทึกการติดตามเรียบร้อย',
                            timer: 1500
                        });
                        loadFollowData();
                        loadFollowStats();
                    }
                }
            });
        }
    });
}

function deleteFollow(id) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: 'คุณต้องการลบรายการติดตามนี้ใช่หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/follow/delete.php',
                type: 'POST',
                data: JSON.stringify({ id: id }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire('ลบแล้ว!', '', 'success');
                        loadFollowData();
                        loadFollowStats();
                    }
                }
            });
        }
    });
}

function getStatusClass(status) {
    const classes = {
        'interested': 'primary',
        'high_interest': 'success',
        'pending': 'warning',
        'negotiating': 'info',
        'document_submitted': 'info',
        'bank_submitted': 'primary',
        'waiting_approval': 'warning',
        'approved': 'success',
        'transferred': 'success',
        'cancelled': 'danger',
        'not_qualified': 'danger',
        'not_interested': 'secondary'
    };
    return classes[status] || 'secondary';
}

function formatDate(dateString) {
    if(!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function truncateText(text, maxLength) {
    if(!text) return '-';
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}