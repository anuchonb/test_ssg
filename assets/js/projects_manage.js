// assets/js/projects_manage.js

let currentPage = 1;
let currentView = 'table';
let searchTimeout = null;

$(document).ready(function() {
    // Load initial data
    loadZones();
    loadProjects();
    loadProjectStats();
    
    // Toggle status label
    $('#project_status').on('change', function() {
        if($(this).is(':checked')) {
            $('#statusLabel').text('เปิดใช้งาน');
        } else {
            $('#statusLabel').text('ปิดใช้งาน');
        }
    });
    
    // Update view buttons
    updateViewButtons();
});

// ============ Load Data ============
function loadZones() {
    $.ajax({
        url: '../api/master/get.php',
        type: 'GET',
        data: { type: 'zone' },
        dataType: 'json',
        success: function(data) {
            let options = '<option value="">เลือกโซน</option>';
            let filterOptions = '<option value="">ทั้งหมด</option>';
            
            if(Array.isArray(data)) {
                data.forEach(item => {
                    options += `<option value="${item.value}">${item.value}</option>`;
                    filterOptions += `<option value="${item.value}">${item.value}</option>`;
                });
            }
            
            $('#project_zone').html(options);
            $('#filterZone').html(filterOptions);
        }
    });
}

function loadProjectStats() {
    $.ajax({
        url: '../api/projects/stats.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#statTotalProjects').text(response.total_projects || 0);
                $('#statTotalCustomers').text(response.total_customers || 0);
                $('#statAvgPrice').text(numberFormat(response.avg_price) + ' บาท');
                $('#statTotalZones').text(response.total_zones || 0);
            }
        }
    });
}

function loadProjects(page = 1) {
    currentPage = page;
    
    const search = $('#searchProject').val();
    const zone = $('#filterZone').val();
    const sortBy = $('#sortBy').val();
    const perPage = $('#perPage').val();
    
    // Show loading
    $('#projectsTableBody').html(`
        <tr>
            <td colspan="8" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </td>
        </tr>
    `);
    
    $.ajax({
        url: '../api/projects/list_full.php',
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            zone: zone,
            sort: sortBy
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                if(currentView === 'table') {
                    renderTable(response.data);
                } else {
                    renderCards(response.data);
                }
                renderPagination(response.pagination);
            }
        }
    });
}

function renderTable(projects) {
    if(!projects || projects.length === 0) {
        $('#projectsTableBody').html(`
            <tr>
                <td colspan="8" class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <p class="text-muted">ไม่พบข้อมูลโครงการ</p>
                    <button class="btn btn-primary btn-sm" onclick="showProjectForm()">
                        <i class="fas fa-plus"></i> เพิ่มโครงการ
                    </button>
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    projects.forEach((p, index) => {
        const customerCount = p.customer_count || 0;
        const caseCount = p.case_count || 0;
        
        html += `
            <tr>
                <td>${p.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                             style="width: 35px; height: 35px; font-size: 14px;">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">${p.name}</div>
                            <small class="text-muted">${p.zone || '-'}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="fw-bold">${numberFormat(p.price)} บาท</span>
                </td>
                <td>
                    <span class="badge bg-info">${p.zone || '-'}</span>
                </td>
                <td>
                    <span class="badge bg-primary">${customerCount} คน</span>
                </td>
                <td>
                    <span class="badge bg-warning text-dark">${caseCount} เคส</span>
                </td>
                <td><small>${formatDateThai(p.created_at)}</small></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="viewProject(${p.id})" title="ดูรายละเอียด">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning" onclick="editProject(${p.id})" title="แก้ไข">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="confirmDeleteProject(${p.id})" title="ลบ">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#projectsTableBody').html(html);
}

function renderCards(projects) {
    if(!projects || projects.length === 0) {
        $('#projectsCardContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <p class="text-muted">ไม่พบข้อมูลโครงการ</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    projects.forEach(p => {
        const customerCount = p.customer_count || 0;
        const caseCount = p.case_count || 0;
        
        html += `
            <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
                <div class="card h-100 project-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">${p.name}</h6>
                            <span class="badge bg-info">${p.zone || '-'}</span>
                        </div>
                        <h5 class="text-primary mb-3">${numberFormat(p.price)} บาท</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <small><i class="fas fa-users"></i> ${customerCount} คน</small>
                            <small><i class="fas fa-folder"></i> ${caseCount} เคส</small>
                        </div>
                        <div class="progress mb-3" style="height: 5px;">
                            <div class="progress-bar bg-success" style="width: ${p.occupancy_rate || 0}%"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">${formatDateThai(p.created_at)}</small>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-info" onclick="editProject(${p.id})" title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger" onclick="confirmDeleteProject(${p.id})" title="ลบ">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#projectsCardContainer').html(html);
}

function renderPagination(pagination) {
    if(!pagination || pagination.total_pages <= 1) {
        $('#pagination').html('');
        return;
    }
    
    let html = '';
    
    html += `<li class="page-item ${!pagination.has_prev ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadProjects(${pagination.current_page - 1}); return false;">
            <i class="fas fa-chevron-left"></i>
        </a>
    </li>`;
    
    for(let i = 1; i <= pagination.total_pages; i++) {
        if(i === 1 || i === pagination.total_pages || 
           (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadProjects(${i}); return false;">${i}</a>
            </li>`;
        } else if(i === pagination.current_page - 3 || i === pagination.current_page + 3) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    html += `<li class="page-item ${!pagination.has_next ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadProjects(${pagination.current_page + 1}); return false;">
            <i class="fas fa-chevron-right"></i>
        </a>
    </li>`;
    
    $('#pagination').html(html);
}

// ============ CRUD Operations ============
function showProjectForm() {
    resetProjectForm();
    $('#projectModalTitle').html('<i class="fas fa-plus"></i> เพิ่มโครงการใหม่');
    $('#projectModal').modal('show');
}

function editProject(id) {
    $.ajax({
        url: '../api/projects/get.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                const p = response.data;
                $('#project_id').val(p.id);
                $('#project_name').val(p.name);
                $('#project_price').val(p.price);
                $('#project_zone').val(p.zone);
                $('#project_type').val(p.type || '');
                $('#total_units').val(p.total_units || '');
                $('#available_units').val(p.available_units || '');
                $('#completion_year').val(p.completion_year || '');
                $('#project_address').val(p.address || '');
                $('#project_description').val(p.description || '');
                $('#project_status').prop('checked', p.is_active == 1);
                $('#statusLabel').text(p.is_active == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน');
                
                $('#projectModalTitle').html('<i class="fas fa-edit"></i> แก้ไขโครงการ');
                $('#projectModal').modal('show');
            }
        }
    });
}

function resetProjectForm() {
    $('#project_id').val('');
    $('#projectForm')[0].reset();
    $('#project_status').prop('checked', true);
    $('#statusLabel').text('เปิดใช้งาน');
}

function saveProject() {
    // Validate
    const name = $('#project_name').val().trim();
    const price = $('#project_price').val();
    const zone = $('#project_zone').val();
    
    if(!name) {
        $('#project_name').addClass('is-invalid');
        Swal.fire('กรุณากรอกชื่อโครงการ', '', 'warning');
        return;
    }
    
    if(!price || price <= 0) {
        $('#project_price').addClass('is-invalid');
        Swal.fire('กรุณากรอกราคา', '', 'warning');
        return;
    }
    
    if(!zone) {
        $('#project_zone').addClass('is-invalid');
        Swal.fire('กรุณาเลือกโซน', '', 'warning');
        return;
    }
    
    const isEdit = $('#project_id').val() ? true : false;
    
    Swal.fire({
        title: isEdit ? 'ยืนยันการแก้ไข?' : 'ยืนยันการเพิ่ม?',
        text: `โครงการ: ${name}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            const url = isEdit ? '../api/projects/update.php' : '../api/projects/create.php';
            
            $.ajax({
                url: url,
                type: 'POST',
                data: JSON.stringify({
                    id: $('#project_id').val(),
                    name: name,
                    price: price,
                    zone: zone,
                    type: $('#project_type').val(),
                    total_units: $('#total_units').val(),
                    available_units: $('#available_units').val(),
                    completion_year: $('#completion_year').val(),
                    address: $('#project_address').val(),
                    description: $('#project_description').val(),
                    is_active: $('#project_status').is(':checked') ? 1 : 0
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#projectModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadProjects(currentPage);
                        loadProjectStats();
                    } else {
                        Swal.fire('ผิดพลาด!', response.message, 'error');
                    }
                }
            });
        }
    });
}

function confirmDeleteProject(id) {
    // Check if project has customers
    $.ajax({
        url: '../api/projects/check_customers.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                const count = response.customer_count || 0;
                if(count > 0) {
                    $('#projectDeleteInfo').html(`
                        <i class="fas fa-exclamation-circle"></i> 
                        โครงการนี้มีลูกค้า ${count} คน และเคส ${response.case_count || 0} เคสที่เกี่ยวข้อง
                        <br>การลบจะส่งผลต่อข้อมูลทั้งหมด
                    `).show();
                } else {
                    $('#projectDeleteInfo').hide();
                }
                $('#deleteProjectId').val(id);
                $('#deleteModal').modal('show');
            }
        }
    });
}

function deleteProject() {
    const id = $('#deleteProjectId').val();
    
    $.ajax({
        url: '../api/projects/delete.php',
        type: 'POST',
        data: JSON.stringify({ id: id }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            $('#deleteModal').modal('hide');
            if(response.success) {
                Swal.fire('ลบแล้ว!', 'ลบโครงการเรียบร้อย', 'success');
                loadProjects(currentPage);
                loadProjectStats();
            } else {
                Swal.fire('ผิดพลาด!', response.message, 'error');
            }
        }
    });
}

// ============ View Functions ============
function viewProject(id) {
    window.location.href = `case_detail.php?project_id=${id}`;
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
    loadProjects(currentPage);
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

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadProjects(1);
    }, 500);
}

function resetFilters() {
    $('#searchProject').val('');
    $('#filterZone').val('');
    $('#sortBy').val('newest');
    loadProjects(1);
}

// Helper Functions
function formatDateThai(dateString) {
    if(!dateString) return '-';
    const date = new Date(dateString);
    if(isNaN(date.getTime())) return '-';
    return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function numberFormat(number) {
    if(!number && number !== 0) return '0';
    return new Intl.NumberFormat('th-TH').format(parseFloat(number));
}