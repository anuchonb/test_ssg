// assets/js/customers.js

let currentCustomerId = null;
let currentPage = 1;
let searchTimeout = null;

$(document).ready(function() {
    // Load initial data
    loadDropdowns();
    loadCustomers();
    loadCustomerStats();
    
    // Form validation - remove invalid class on input
    $(document).on('input', '.is-invalid', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Phone number formatting
    $('#phone').on('input', function() {
        let phone = $(this).val().replace(/[^0-9]/g, '');
        if(phone.length > 10) phone = phone.substring(0, 10);
        $(this).val(phone);
    });
    
    // Auto check customer when phone loses focus
    $('#phone').on('blur', function() {
        const phone = $(this).val().trim();
        if(phone.length >= 9 && !currentCustomerId) {
            checkCustomerByPhone(phone);
        }
    });
    
    // Show/hide create case button
    updateCreateCaseButton();
    
    // Prevent form submit on enter
    $('#customerForm').on('keydown', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            return false;
        }
    });
});

// ============ DROPDOWN LOADING ============
function loadDropdowns() {
    // Load channels
    loadDropdown('channel', '#channel');
    loadDropdown('channel', '#filterChannel');
    
    // Load projects
    loadProjects();
    loadProjectsFilter();
    
    // Load zones
    loadDropdown('zone', '#zone');
    
    // Load welfare
    loadDropdown('welfare', '#welfare');
}

function loadDropdown(type, selector) {
    $.ajax({
        url: '../api/master/get.php',
        type: 'GET',
        data: { type: type, active_only: 'true' },
        dataType: 'json',
        success: function(data) {
            let options = '<option value="">เลือก</option>';
            if(Array.isArray(data)) {
                data.forEach(item => {
                    options += `<option value="${item.value}">${item.value}</option>`;
                });
            }
            $(selector).html(options);
        },
        error: function() {
            console.error('Failed to load dropdown:', type);
            $(selector).html('<option value="">โหลดไม่สำเร็จ</option>');
        }
    });
}

function loadProjects() {
    $.ajax({
        url: '../api/projects/list.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                let options = '<option value="">เลือกโครงการ</option>';
                response.data.forEach(project => {
                    options += `<option value="${project.id}" data-price="${project.price || ''}">
                        ${project.name} ${project.price ? ' - ' + numberFormat(project.price) + ' บาท' : ''}
                    </option>`;
                });
                $('#project_id').html(options);
            }
        },
        error: function() {
            console.error('Failed to load projects');
            $('#project_id').html('<option value="">โหลดไม่สำเร็จ</option>');
        }
    });
}

function loadProjectsFilter() {
    $.ajax({
        url: '../api/projects/list.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                let options = '<option value="">ทั้งหมด</option>';
                response.data.forEach(project => {
                    options += `<option value="${project.id}">${project.name}</option>`;
                });
                $('#filterProject').html(options);
            }
        }
    });
}

// ============ CUSTOMER STATS ============
function loadCustomerStats() {
    $.ajax({
        url: '../api/customers/stats.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#statTotalCustomers').text(response.total || 0);
                $('#statGradeAPlus').text(response.grade_a_plus || 0);
                $('#statWithCases').text(response.with_cases || 0);
                $('#statToday').text(response.today || 0);
            }
        }
    });
}

// ============ CUSTOMER LIST ============
function loadCustomers(page = 1) {
    currentPage = page;
    
    const search = $('#searchInput').val();
    const channel = $('#filterChannel').val();
    const grade = $('#filterGrade').val();
    const project = $('#filterProject').val();
    const debt = $('#filterDebt').val();
    const perPage = $('#perPage').val() || 10;
    
    // Show loading
    showTableLoading();
    
    $.ajax({
        url: '../api/customers/list.php',
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            channel: channel,
            grade: grade,
            project_id: project,
            debt_status: debt
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                renderCustomersTable(response.data);
                renderPagination(response.pagination);
                $('#showingCount').text(response.data ? response.data.length : 0);
                $('#totalCount').text(response.pagination ? response.pagination.total : 0);
            } else {
                showTableEmpty();
            }
        },
        error: function(xhr, status, error) {
            console.error('Load customers error:', error);
            showTableError();
        }
    });
}

function showTableLoading() {
    $('#customersTableBody').html(`
        <tr>
            <td colspan="11" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">กำลังโหลดข้อมูล...</p>
            </td>
        </tr>
    `);
}

function showTableEmpty() {
    $('#customersTableBody').html(`
        <tr>
            <td colspan="11" class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">ไม่พบข้อมูลลูกค้า</p>
                <button class="btn btn-primary btn-sm" onclick="showCustomerForm()">
                    <i class="fas fa-plus"></i> เพิ่มลูกค้าใหม่
                </button>
            </td>
        </tr>
    `);
}

function showTableError() {
    $('#customersTableBody').html(`
        <tr>
            <td colspan="11" class="text-center py-5 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <p>ไม่สามารถโหลดข้อมูลได้</p>
                <button class="btn btn-outline-primary btn-sm" onclick="loadCustomers()">
                    <i class="fas fa-redo"></i> ลองใหม่
                </button>
            </td>
        </tr>
    `);
}

function renderCustomersTable(customers) {
    if(!customers || customers.length === 0) {
        showTableEmpty();
        return;
    }
    
    let html = '';
    customers.forEach(c => {
        const gradeBadge = c.grade 
            ? `<span class="badge bg-${getGradeColor(c.grade)}">${c.grade}</span>` 
            : '<span class="badge bg-secondary">-</span>';
        
        const debtBadge = c.debt_status === 'have' 
            ? '<span class="badge bg-warning text-dark">มีหนี้</span>' 
            : c.debt_status === 'none' 
                ? '<span class="badge bg-success">ไม่มีหนี้</span>' 
                : '<span class="badge bg-secondary">-</span>';
        
        const caseBadge = c.case_status 
            ? `<span class="badge bg-${getCaseStatusColor(c.case_status)}">${c.case_status}</span>` 
            : '<span class="badge bg-secondary">ไม่มีเคส</span>';
        
        html += `
            <tr>
                <td><small class="text-muted">${c.customer_code || '-'}</small></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-${c.grade ? getGradeColor(c.grade) : 'secondary'} text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                             style="width: 32px; height: 32px; font-size: 14px; flex-shrink: 0;">
                            ${c.name ? c.name.charAt(0).toUpperCase() : '?'}
                        </div>
                        <div>
                            <div class="fw-semibold">${c.name || 'ไม่ระบุ'}</div>
                            ${c.facebook ? `<small class="text-primary"><i class="fab fa-facebook"></i> ${escapeHtml(c.facebook)}</small>` : ''}
                            ${c.line_id ? `<br><small class="text-success"><i class="fab fa-line"></i> ${escapeHtml(c.line_id)}</small>` : ''}
                        </div>
                    </div>
                </td>
                <td>
                    <a href="tel:${c.phone}" class="text-decoration-none">${c.phone || '-'}</a>
                </td>
                <td>${c.channel || '-'}</td>
                <td>${gradeBadge}</td>
                <td>${c.project_name || '-'}</td>
                <td class="text-end">${c.price ? numberFormat(c.price) + ' บาท' : '-'}</td>
                <td>${debtBadge}</td>
                <td>${caseBadge}</td>
                <td><small>${formatDateThai(c.created_at)}</small></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="editCustomer(${c.id})" title="แก้ไข">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${!c.case_id ? `
                            <button class="btn btn-success" onclick="createCase(${c.id})" title="ส่งเคส">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        ` : `
                            <button class="btn btn-warning" onclick="viewCase(${c.case_id})" title="ดูเคส">
                                <i class="fas fa-eye"></i>
                            </button>
                        `}
                        <button class="btn btn-danger" onclick="confirmDeleteCustomer(${c.id}, '${escapeHtml(c.name)}')" title="ลบ">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#customersTableBody').html(html);
}

function renderPagination(pagination) {
    if(!pagination || pagination.total_pages <= 1) {
        $('#pagination').html('');
        return;
    }
    
    let html = '';
    
    // Previous button
    html += `<li class="page-item ${!pagination.has_prev ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadCustomers(${pagination.current_page - 1}); return false;">
            <i class="fas fa-chevron-left"></i>
        </a>
    </li>`;
    
    // Page numbers
    for(let i = 1; i <= pagination.total_pages; i++) {
        if(i === 1 || i === pagination.total_pages || 
           (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadCustomers(${i}); return false;">${i}</a>
            </li>`;
        } else if(i === pagination.current_page - 3 || i === pagination.current_page + 3) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Next button
    html += `<li class="page-item ${!pagination.has_next ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadCustomers(${pagination.current_page + 1}); return false;">
            <i class="fas fa-chevron-right"></i>
        </a>
    </li>`;
    
    $('#pagination').html(html);
}

// ============ CUSTOMER CRUD ============
function showCustomerForm() {
    resetCustomerForm();
    $('#customerModalTitle').html('<i class="fas fa-user-plus"></i> เพิ่มลูกค้าใหม่');
    $('#btnCreateCase').hide();
    $('#phoneStatus').html('<i class="fas fa-search"></i>');
    $('#phoneFeedback').html('');
    $('#customerModal').modal('show');
}

function editCustomer(id) {
    // Show loading
    Swal.fire({
        title: 'กำลังโหลดข้อมูล...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../api/customers/get.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if(response.success && response.data) {
                fillCustomerForm(response.data);
                $('#customerModalTitle').html('<i class="fas fa-edit"></i> แก้ไขข้อมูลลูกค้า');
                $('#btnCreateCase').show();
                $('#customerModal').modal('show');
            } else {
                Swal.fire('ผิดพลาด!', 'ไม่พบข้อมูลลูกค้า', 'error');
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('ผิดพลาด!', 'ไม่สามารถโหลดข้อมูลได้', 'error');
        }
    });
}

function fillCustomerForm(customer) {
    resetCustomerForm();
    currentCustomerId = customer.id;
    
    $('#customer_id').val(customer.id);
    $('#customer_code').val(customer.customer_code);
    $('#name').val(customer.name);
    $('#phone').val(customer.phone);
    $('#facebook').val(customer.facebook);
    $('#line_id').val(customer.line_id);
    $('#page_name').val(customer.page_name);
    $('#channel').val(customer.channel);
    $('#grade').val(customer.grade);
    $('#project_id').val(customer.project_id);
    $('#price').val(customer.price);
    $('#cashback').val(customer.cashback);
    $('#living_type').val(customer.living_type);
    $('#zone').val(customer.zone);
    $('#company_name').val(customer.company_name);
    $('#work_age_month').val(customer.work_age_month);
    $('#welfare').val(customer.welfare);
    $('#debt_status').val(customer.debt_status);
    
    // Update phone status
    $('#phoneStatus').html('<i class="fas fa-check text-success"></i>');
    $('#phoneFeedback').html('<small class="text-success">✅ แก้ไขข้อมูลลูกค้า</small>');
    
    updateCreateCaseButton();
}

function resetCustomerForm() {
    currentCustomerId = null;
    $('#customerForm')[0].reset();
    $('#customer_id').val('');
    $('#customer_code').val('');
    $('#phoneStatus').html('<i class="fas fa-search"></i>');
    $('#phoneFeedback').html('');
    $('#btnCreateCase').hide();
    $('.is-invalid').removeClass('is-invalid');
}

function checkCustomerByPhone(phone) {
    if(!phone || phone.length < 9) {
        $('#phoneStatus').html('<i class="fas fa-search"></i>');
        $('#phoneFeedback').html('');
        return;
    }
    
    // Don't check if editing same customer
    if(currentCustomerId) return;
    
    $('#phoneStatus').html('<i class="fas fa-spinner fa-spin"></i>');
    
    $.ajax({
        url: '../api/customers/get.php',
        type: 'GET',
        data: { phone: phone },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                const customer = response.data;
                
                $('#phoneStatus').html('<i class="fas fa-check text-success"></i>');
                $('#phoneFeedback').html(`
                    <small class="text-success">
                        ✅ พบข้อมูล ${customer.name} | 
                        <a href="#" onclick="fillCustomerForm(response.data); return false;" class="text-primary">
                            คลิกเพื่อแก้ไข
                        </a>
                    </small>
                `);
                
                // Store for later use
                window.foundCustomer = customer;
            } else {
                $('#phoneStatus').html('<i class="fas fa-plus text-primary"></i>');
                $('#phoneFeedback').html('<small class="text-muted">🆕 ลูกค้าใหม่</small>');
                window.foundCustomer = null;
            }
        },
        error: function() {
            $('#phoneStatus').html('<i class="fas fa-search"></i>');
            $('#phoneFeedback').html('');
        }
    });
}

function saveCustomer() {
    // Validate required fields
    const name = $('#name').val().trim();
    const phone = $('#phone').val().trim();
    
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    
    // Validate name
    if(!name) {
        $('#name').addClass('is-invalid');
        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกชื่อ-นามสกุล',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
        $('#name').focus();
        return;
    }
    
    // Validate phone
    if(!phone || phone.length < 9) {
        $('#phone').addClass('is-invalid');
        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกเบอร์โทรให้ถูกต้อง',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
        $('#phone').focus();
        return;
    }
    
    // Collect form data
    const formData = {
        name: name,
        phone: phone,
        facebook: $('#facebook').val().trim(),
        line_id: $('#line_id').val().trim(),
        page_name: $('#page_name').val().trim(),
        channel: $('#channel').val(),
        grade: $('#grade').val(),
        project_id: $('#project_id').val() || null,
        price: $('#price').val() || null,
        cashback: $('#cashback').val() || null,
        living_type: $('#living_type').val(),
        zone: $('#zone').val(),
        company_name: $('#company_name').val().trim(),
        work_age_month: $('#work_age_month').val() || null,
        welfare: $('#welfare').val(),
        debt_status: $('#debt_status').val()
    };
    
    const isEdit = currentCustomerId ? true : false;
    const url = isEdit ? '../api/customers/update.php' : '../api/customers/create.php';
    
    if(isEdit) {
        formData.id = currentCustomerId;
    }
    
    // Confirm
    const confirmTitle = isEdit ? 'ยืนยันการแก้ไขข้อมูล?' : 'ยืนยันการเพิ่มลูกค้าใหม่?';
    const confirmText = `ชื่อ: ${name}<br>เบอร์: ${phone}`;
    
    Swal.fire({
        title: confirmTitle,
        html: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fas fa-save"></i> บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'กำลังบันทึก...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: url,
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        if(!currentCustomerId && response.customer_id) {
                            currentCustomerId = response.customer_id;
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: response.message || 'บันทึกข้อมูลเรียบร้อย',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        $('#btnCreateCase').show();
                        updateCreateCaseButton();
                        loadCustomers(currentPage);
                        loadCustomerStats();
                        
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด!',
                            text: response.message || 'ไม่สามารถบันทึกข้อมูลได้'
                        });
                    }
                },
                error: function(xhr) {
                    let message = 'ไม่สามารถบันทึกข้อมูลได้';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด!',
                        text: message
                    });
                }
            });
        }
    });
}

function createCaseFromCustomer() {
    if(!currentCustomerId) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาบันทึกข้อมูลลูกค้าก่อน',
            text: 'ต้องบันทึกข้อมูลลูกค้าก่อนจึงจะส่งเคสได้'
        });
        return;
    }
    
    createCase(currentCustomerId);
}

function createCase(customerId) {
    Swal.fire({
        title: 'ยืนยันการส่งเคส?',
        text: 'คุณต้องการสร้างเคสให้ลูกค้าคนนี้ใช่หรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fas fa-paper-plane"></i> ส่งเคส',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            Swal.fire({
                title: 'กำลังสร้างเคส...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '../api/cases/create.php',
                type: 'POST',
                data: JSON.stringify({
                    customer_id: customerId,
                    owner_id: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; ?>
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#customerModal').modal('hide');
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'ส่งเคสสำเร็จ!',
                            text: `Case #${response.case_id} ถูกสร้างเรียบร้อย`,
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: '<i class="fas fa-eye"></i> ดูรายละเอียดเคส',
                            cancelButtonText: '<i class="fas fa-times"></i> ปิด'
                        }).then((result) => {
                            if(result.isConfirmed) {
                                window.location.href = `case_detail.php?id=${response.case_id}`;
                            } else {
                                loadCustomers(currentPage);
                                loadCustomerStats();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด!',
                            text: response.message || 'ไม่สามารถสร้างเคสได้'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด!',
                        text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                    });
                }
            });
        }
    });
}

function viewCase(caseId) {
    window.location.href = `case_detail.php?id=${caseId}`;
}

function confirmDeleteCustomer(id, name) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        html: `คุณต้องการลบข้อมูลของ <strong>${name}</strong> ใช่หรือไม่?<br>
               <span class="text-danger">การลบจะไม่สามารถกู้คืนได้!</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash"></i> ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed) {
            deleteCustomer(id);
        }
    });
}

function deleteCustomer(id) {
    Swal.fire({
        title: 'กำลังลบ...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../api/customers/delete.php',
        type: 'POST',
        data: JSON.stringify({ id: id }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'ลบสำเร็จ!',
                    text: 'ลบข้อมูลลูกค้าเรียบร้อย',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // If deleted customer is current in form, reset
                if(currentCustomerId === id) {
                    resetCustomerForm();
                    $('#customerModal').modal('hide');
                }
                
                loadCustomers(currentPage);
                loadCustomerStats();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด!',
                    text: response.message || 'ไม่สามารถลบได้'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด!',
                text: 'ไม่สามารถลบข้อมูลได้'
            });
        }
    });
}

function updateCreateCaseButton() {
    if(currentCustomerId) {
        $('#btnCreateCase').show();
    } else {
        $('#btnCreateCase').hide();
    }
}

// ============ FILTER & SEARCH ============
function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadCustomers(1);
    }, 500);
}

function resetFilters() {
    $('#searchInput').val('');
    $('#filterChannel').val('');
    $('#filterGrade').val('');
    $('#filterProject').val('');
    $('#filterDebt').val('');
    currentPage = 1;
    loadCustomers();
    loadCustomerStats();
}

function exportCustomers() {
    const search = $('#searchInput').val();
    const channel = $('#filterChannel').val();
    const grade = $('#filterGrade').val();
    const project = $('#filterProject').val();
    const debt = $('#filterDebt').val();
    
    // Build URL
    let url = `../api/customers/export.php?`;
    if(search) url += `search=${encodeURIComponent(search)}&`;
    if(channel) url += `channel=${encodeURIComponent(channel)}&`;
    if(grade) url += `grade=${encodeURIComponent(grade)}&`;
    if(project) url += `project_id=${project}&`;
    if(debt) url += `debt_status=${debt}&`;
    
    window.open(url, '_blank');
}

// ============ HELPER FUNCTIONS ============
function getGradeColor(grade) {
    const colors = { 'A+': 'success', 'A': 'primary', 'B': 'warning' };
    return colors[grade] || 'secondary';
}

function getCaseStatusColor(status) {
    const colors = {
        'ส่งเคส': 'primary',
        'กำลังติดตาม': 'warning',
        'อนุมัติ': 'success',
        'ยกเลิก': 'danger',
        'ไม่สนใจ': 'secondary',
        'วงเงินไม่ถึง': 'info'
    };
    return colors[status] || 'secondary';
}

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
    return new Intl.NumberFormat('th-TH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(parseFloat(number));
}

function escapeHtml(text) {
    if(!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============ KEYBOARD SHORTCUTS ============
$(document).keydown(function(e) {
    // Ctrl + N: New Customer
    if(e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        showCustomerForm();
    }
    
    // Ctrl + F: Focus Search
    if(e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        $('#searchInput').focus();
    }
    
    // Escape: Close Modal
    if(e.key === 'Escape') {
        $('#customerModal').modal('hide');
        $('#deleteModal').modal('hide');
    }
});