// assets/js/cases.js

let currentPage = 1;
let currentStatus = '';
let currentView = 'table';
let searchTimeout = null;

$(document).ready(function() {
    // Initial load
    loadCaseStats();
    loadOwners();
    loadCases();
    
    // Show/hide cancel reason
    $('#newStatus').on('change', function() {
        if($(this).val() === 'ยกเลิก') {
            $('#cancelReasonDiv').slideDown();
        } else {
            $('#cancelReasonDiv').slideUp();
        }
    });
    
    // Set active view button
    updateViewButtons();
});

function loadCaseStats() {
    $.ajax({
        url: '../api/cases/stats.php',
        type: 'GET',
        data: {
            owner: $('#filterOwner').val(),
            grade: $('#filterGrade').val(),
            date_from: $('#filterDateFrom').val(),
            date_to: $('#filterDateTo').val()
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#statAll').text(response.all || 0);
                $('#statSubmitted').text(response.submitted || 0);
                $('#statFollowing').text(response.following || 0);
                $('#statApproved').text(response.approved || 0);
                $('#statCancelled').text(response.cancelled || 0);
                $('#statNotInterested').text(response.not_interested || 0);
            }
        }
    });
}

function loadOwners() {
    $.ajax({
        url: '../api/users/list.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                let options = '<option value="">ทั้งหมด</option>';
                response.data.forEach(user => {
                    if(user.role === 'admin_page') {
                        options += `<option value="${user.id}">${user.name}</option>`;
                    }
                });
                $('#filterOwner').html(options);
            }
        }
    });
}

function loadCases(page = 1) {
    currentPage = page;
    
    const search = $('#searchInput').val();
    const status = $('#filterStatus').val();
    const owner = $('#filterOwner').val();
    const grade = $('#filterGrade').val();
    const dateFrom = $('#filterDateFrom').val();
    const dateTo = $('#filterDateTo').val();
    const perPage = $('#perPage').val();
    
    // Show loading
    if(currentView === 'table') {
        $('#casesTableBody').html(`
            <tr>
                <td colspan="10" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `);
    }
    
    $.ajax({
        url: '../api/cases/list.php',
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            status: status,
            owner: owner,
            grade: grade,
            date_from: dateFrom,
            date_to: dateTo
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                if(currentView === 'table') {
                    renderTableView(response.data);
                } else {
                    renderCardView(response.data);
                }
                
                renderPagination(response.pagination);
                $('#showingCount').text(response.data.length);
                $('#totalCount').text(response.pagination.total);
            }
        }
    });
}

function renderTableView(cases) {
    if(cases.length === 0) {
        $('#casesTableBody').html(`
            <tr>
                <td colspan="10" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">ไม่พบรายการเคส</p>
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    cases.forEach(c => {
        const statusBadge = getStatusBadge(c.status);
        const followHtml = getFollowHtml(c.follow_count, c.follow_status);
        
        html += `
            <tr>
                <td>
                    <a href="case_detail.php?case_id=${c.id}" class="fw-bold text-decoration-none">
                        #${c.id}
                    </a>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                             style="width: 30px; height: 30px; font-size: 12px;">
                            ${c.customer_name.charAt(0)}
                        </div>
                        <div>
                            <div class="fw-semibold">${c.customer_name}</div>
                            <small class="text-muted">${c.customer_code || ''}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <small>${c.phone}</small>
                    ${c.facebook ? `<br><small class="text-muted"><i class="fab fa-facebook"></i> ${c.facebook}</small>` : ''}
                </td>
                <td>${c.project_name || '-'}</td>
                <td>
                    ${c.grade ? `<span class="badge bg-${getGradeColor(c.grade)}">${c.grade}</span>` : '-'}
                </td>
                <td>${c.owner_name}</td>
                <td>${statusBadge}</td>
                <td>${followHtml}</td>
                <td>
                    <small>${formatDate(c.created_at, 'short')}</small>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="case_detail.php?case_id=${c.id}" class="btn btn-info" title="ดูรายละเอียด">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="btn btn-warning" onclick="changeStatus(${c.id}, '${c.status}')" title="เปลี่ยนสถานะ">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="confirmDelete(${c.id})" title="ลบ">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#casesTableBody').html(html);
}

function renderCardView(cases) {
    if(cases.length === 0) {
        $('#casesCardContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">ไม่พบรายการเคส</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    cases.forEach(c => {
        const statusBadge = getStatusBadge(c.status);
        
        html += `
            <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">
                                <a href="case_detail.php?case_id=${c.id}" class="text-decoration-none">
                                    #${c.id}
                                </a>
                            </h6>
                            ${statusBadge}
                        </div>
                        <h6 class="mb-1">${c.customer_name}</h6>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-phone"></i> ${c.phone}<br>
                            <i class="fas fa-building"></i> ${c.project_name || '-'}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">${c.owner_name}</small>
                            <small>${formatDate(c.created_at, 'short')}</small>
                        </div>
                        <div class="mt-2">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-${getProgressColor(c.status)}" 
                                     style="width: ${getProgressWidth(c.status)}%"></div>
                            </div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between">
                            <small>ตาม ${c.follow_count} ครั้ง</small>
                            <div class="btn-group btn-group-sm">
                                <a href="case_detail.php?case_id=${c.id}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#casesCardContainer').html(html);
}

function renderPagination(pagination) {
    if(pagination.total_pages <= 1) {
        $('#pagination').html('');
        return;
    }
    
    let html = '';
    
    // Previous
    html += `<li class="page-item ${!pagination.has_prev ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadCases(${pagination.current_page - 1}); return false;">
            <i class="fas fa-chevron-left"></i>
        </a>
    </li>`;
    
    // Page numbers
    for(let i = 1; i <= pagination.total_pages; i++) {
        if(
            i === 1 || 
            i === pagination.total_pages || 
            (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)
        ) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadCases(${i}); return false;">${i}</a>
            </li>`;
        } else if(
            i === pagination.current_page - 3 || 
            i === pagination.current_page + 3
        ) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Next
    html += `<li class="page-item ${!pagination.has_next ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadCases(${pagination.current_page + 1}); return false;">
            <i class="fas fa-chevron-right"></i>
        </a>
    </li>`;
    
    $('#pagination').html(html);
}

function changeView(view) {
    currentView = view;
    
    if(view === 'table') {
        $('#tableView').show();
        $('#cardView').hide();
    } else {
        $('#tableView').hide();
        $('#cardView').show();
    }
    
    updateViewButtons();
    loadCases(currentPage);
}

function updateViewButtons() {
    if(currentView === 'table') {
        $('#btnTableView').addClass('active');
        $('#btnCardView').removeClass('active');
    } else {
        $('#btnTableView').removeClass('active');
        $('#btnCardView').addClass('active');
    }
}

function changeStatus(caseId, currentStatus) {
    $('#statusCaseId').val(caseId);
    $('#newStatus').val(currentStatus);
    $('#cancelReason').val('');
    $('#cancelReasonDiv').hide();
    $('#statusModal').modal('show');
}

function saveStatus() {
    const caseId = $('#statusCaseId').val();
    const newStatus = $('#newStatus').val();
    const cancelReason = $('#cancelReason').val();
    
    if(!newStatus) {
        Swal.fire('กรุณาเลือกสถานะ', '', 'warning');
        return;
    }
    
    if(newStatus === 'ยกเลิก' && !cancelReason) {
        Swal.fire('กรุณาระบุเหตุผลการยกเลิก', '', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะ?',
        text: `เปลี่ยนเป็น "${newStatus}"`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '../api/cases/update_status.php',
                type: 'POST',
                data: JSON.stringify({
                    case_id: caseId,
                    status: newStatus,
                    cancel_reason: cancelReason
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#statusModal').modal('hide');
                        Swal.fire('สำเร็จ!', 'เปลี่ยนสถานะเรียบร้อย', 'success');
                        loadCases(currentPage);
                        loadCaseStats();
                    }
                }
            });
        }
    });
}

function confirmDelete(caseId) {
    $('#deleteCaseId').val(caseId);
    $('#deleteModal').modal('show');
}

function deleteCase() {
    const caseId = $('#deleteCaseId').val();
    
    $.ajax({
        url: '../api/cases/delete.php',
        type: 'POST',
        data: JSON.stringify({ case_id: caseId }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#deleteModal').modal('hide');
                Swal.fire('ลบแล้ว!', 'ลบเคสเรียบร้อย', 'success');
                loadCases(currentPage);
                loadCaseStats();
            } else {
                Swal.fire('ผิดพลาด!', response.message, 'error');
            }
        }
    });
}

function filterCases(status) {
    $('#filterStatus').val(status);
    loadCaseStats();
    loadCases(1);
}

function resetFilters() {
    $('#searchInput').val('');
    $('#filterStatus').val('');
    $('#filterOwner').val('');
    $('#filterGrade').val('');
    $('#filterDateFrom').val('');
    $('#filterDateTo').val('');
    currentPage = 1;
    loadCaseStats();
    loadCases();
}

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadCases(1);
        loadCaseStats();
    }, 500);
}

function exportCases() {
    const search = $('#searchInput').val();
    const status = $('#filterStatus').val();
    const owner = $('#filterOwner').val();
    const grade = $('#filterGrade').val();
    const dateFrom = $('#filterDateFrom').val();
    const dateTo = $('#filterDateTo').val();
    
    window.location.href = `../api/cases/export.php?search=${search}&status=${status}&owner=${owner}&grade=${grade}&date_from=${dateFrom}&date_to=${dateTo}`;
}

// Helper Functions
function getStatusBadge(status) {
    const badges = {
        'ส่งเคส': '<span class="badge bg-primary">ส่งเคส</span>',
        'กำลังติดตาม': '<span class="badge bg-warning text-dark">กำลังติดตาม</span>',
        'อนุมัติ': '<span class="badge bg-success">อนุมัติ</span>',
        'ยกเลิก': '<span class="badge bg-danger">ยกเลิก</span>',
        'ไม่สนใจ': '<span class="badge bg-secondary">ไม่สนใจ</span>',
        'วงเงินไม่ถึง': '<span class="badge bg-info">วงเงินไม่ถึง</span>'
    };
    return badges[status] || `<span class="badge bg-light text-dark">${status}</span>`;
}

function getFollowHtml(count, status) {
    if(count === 0) return '<span class="text-muted">ยังไม่ติดตาม</span>';
    
    let color = 'warning';
    if(status === 'approved' || status === 'transferred') color = 'success';
    else if(status === 'cancelled' || status === 'not_interested') color = 'danger';
    
    return `<span class="badge bg-${color}">${count} ครั้ง</span>`;
}

function getGradeColor(grade) {
    const colors = {
        'A+': 'success',
        'A': 'primary',
        'B': 'warning'
    };
    return colors[grade] || 'secondary';
}

function getProgressColor(status) {
    const colors = {
        'ส่งเคส': 'primary',
        'กำลังติดตาม': 'warning',
        'อนุมัติ': 'success',
        'ยกเลิก': 'danger',
        'ไม่สนใจ': 'secondary'
    };
    return colors[status] || 'primary';
}

function getProgressWidth(status) {
    const widths = {
        'ส่งเคส': 25,
        'กำลังติดตาม': 50,
        'อนุมัติ': 100,
        'ยกเลิก': 100,
        'ไม่สนใจ': 100
    };
    return widths[status] || 0;
}