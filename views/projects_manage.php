<?php
// views/projects_manage.php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../includes/header.php';
include_once '../includes/sidebar.php';
include_once '../includes/auth_check.php';
include_once '../includes/functions.php';

// เฉพาะ admin เท่านั้น
if(!checkRole('admin')) {
    header("Location: dashboard.php");
    exit();
}

// Debug - เช็คว่า session ทำงาน
// echo "User: " . $_SESSION['user_name'] . " | Role: " . $_SESSION['user_role'];
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">🏢 จัดการโครงการ</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">จัดการโครงการ</li>
                    </ol>
                </nav>
            </div>
            <!-- ปุ่มเพิ่มโครงการ - แก้ไขให้ใช้ onclick แบบง่าย -->
            <button type="button" class="btn btn-primary" onclick="showProjectForm();">
                <i class="fas fa-plus"></i> เพิ่มโครงการใหม่
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">โครงการทั้งหมด</div>
                                <div class="h4 mb-0 font-weight-bold" id="statTotalProjects">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">ลูกค้าสนใจ</div>
                                <div class="h4 mb-0 font-weight-bold" id="statTotalCustomers">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">ราคาเฉลี่ย</div>
                                <div class="h4 mb-0 font-weight-bold" id="statAvgPrice">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tag fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">โซนทั้งหมด</div>
                                <div class="h4 mb-0 font-weight-bold" id="statTotalZones">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">รายการโครงการ</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="loadProjects()">
                        <i class="fas fa-sync"></i> รีเฟรช
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ชื่อโครงการ</th>
                                <th>ราคา</th>
                                <th>โซน</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="projectsTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Project Form Modal -->
<div class="modal fade" id="projectModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="projectModalTitle">
                    <i class="fas fa-building"></i> เพิ่มโครงการใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="projectForm">
                    <input type="hidden" id="project_id">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">ชื่อโครงการ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="project_name" required
                                   placeholder="เช่น The Metro สุขุมวิท">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ราคาเริ่มต้น <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="project_price" required
                                       step="0.01" min="0" placeholder="0.00">
                                <span class="input-group-text">บาท</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">โซน <span class="text-danger">*</span></label>
                            <select class="form-select" id="project_zone" required>
                                <option value="">เลือกโซน</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="saveProject()">
                    <i class="fas fa-save"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript แบบ inline เพื่อให้ทำงานแน่นอน -->
<script>
// เช็คว่า jQuery โหลดหรือยัง
if(typeof jQuery === 'undefined') {
    alert('jQuery not loaded! Please check includes/header.php');
}

// ประกาศฟังก์ชั่นแบบ global
var currentPage = 1;

// ฟังก์ชั่นแสดงฟอร์มเพิ่มโครงการ
function showProjectForm() {
    console.log('showProjectForm called');
    
    // Reset form
    $('#project_id').val('');
    $('#project_name').val('');
    $('#project_price').val('');
    $('#project_zone').val('');
    
    // เปลี่ยน title
    $('#projectModalTitle').html('<i class="fas fa-plus"></i> เพิ่มโครงการใหม่');
    
    // แสดง modal
    $('#projectModal').modal('show');
}

// ฟังก์ชั่นแก้ไขโครงการ
function editProject(id) {
    console.log('editProject called with id:', id);
    
    // เรียก API ดึงข้อมูล
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
                
                $('#projectModalTitle').html('<i class="fas fa-edit"></i> แก้ไขโครงการ');
                $('#projectModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading project:', error);
            alert('ไม่สามารถโหลดข้อมูลโครงการได้');
        }
    });
}

// ✅ บันทึกโครงการ (SweetAlert2)
function saveProject() {
    let name = $('#project_name').val().trim();
    let price = $('#project_price').val();
    let zone = $('#project_zone').val();
    
    // Validate
    if (!name) {
        Swal.fire({ icon: 'warning', title: 'กรุณากรอกชื่อโครงการ', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
        $('#project_name').focus();
        return;
    }
    if (!price || price <= 0) {
        Swal.fire({ icon: 'warning', title: 'กรุณากรอกราคา', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
        $('#project_price').focus();
        return;
    }
    if (!zone) {
        Swal.fire({ icon: 'warning', title: 'กรุณาเลือกโซน', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
        return;
    }

    let isEdit = $('#project_id').val() ? true : false;

    Swal.fire({
        title: isEdit ? 'ยืนยันการแก้ไข?' : 'ยืนยันการเพิ่ม?',
        html: '<strong>' + name + '</strong><br>ราคา: ' + numberFormat(price) + ' บาท<br>โซน: ' + zone,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            // Disable button
            let btn = $('.modal-footer .btn-primary');
            let origText = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...').prop('disabled', true);

            let url = isEdit ? '../api/projects/update.php' : '../api/projects/create.php';
            let data = {
                id: $('#project_id').val(),
                name: name,
                price: price,
                zone: zone
            };

            $.ajax({
                url: url,
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                dataType: 'json',
                timeout: 10000,
                success: function(res) {
                    btn.html(origText).prop('disabled', false);
                    
                    if (res.success) {
                        $('#projectModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: res.message || 'บันทึกเรียบร้อย',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadProjects(currentPage);
                        loadProjectStats();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด',
                            text: res.message || 'ไม่สามารถบันทึกได้'
                        });
                    }
                },
                error: function(xhr) {
                    btn.html(origText).prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: 'ไม่สามารถบันทึกได้ (Status: ' + xhr.status + ')'
                    });
                }
            });
        }
    });
}

// ✅ ฟังก์ชั่นลบโครงการ (SweetAlert2)
function deleteProject(id) {
    Swal.fire({
        title: 'ยืนยันการลบโครงการ?',
        text: 'การลบไม่สามารถกู้คืนได้ ข้อมูลลูกค้าและเคสที่เกี่ยวข้องจะถูกโอนไปยังโครงการอื่น',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash"></i> ลบโครงการ',
        cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // แสดง loading
            Swal.fire({
                title: 'กำลังลบโครงการ...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '../api/projects/delete.php',
                type: 'POST',
                data: JSON.stringify({ id: id }),
                contentType: 'application/json',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบสำเร็จ!',
                            text: 'ลบโครงการเรียบร้อยแล้ว',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadProjects();
                        loadProjectStats();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด',
                            text: response.message || 'ไม่สามารถลบโครงการได้'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ (Status: ' + xhr.status + ')'
                    });
                }
            });
        }
    });
}

// ✅ ยืนยันลบโครงการ (ตรวจสอบลูกค้าก่อน)
function confirmDeleteProject(id) {
    // ตรวจสอบว่ามีลูกค้าหรือไม่
    $.ajax({
        url: '../api/projects/check_customers.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let count = response.customer_count || 0;
                let caseCount = response.case_count || 0;
                
                let warningText = '';
                if (count > 0) {
                    warningText = '<div class="alert alert-warning text-start mt-2">' +
                        '<i class="fas fa-exclamation-triangle"></i> ' +
                        'โครงการนี้มีลูกค้า <strong>' + count + ' คน</strong> และเคส <strong>' + caseCount + ' เคส</strong>' +
                        '<br>การลบจะโอนข้อมูลทั้งหมดไปยังโครงการว่าง</div>';
                }
                
                Swal.fire({
                    title: 'ยืนยันการลบโครงการ?',
                    html: warningText + '<p class="text-danger">การลบไม่สามารถกู้คืนได้</p>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '<i class="fas fa-trash"></i> ลบ',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteProject(id);
                    }
                });
            }
        }
    });
}

// ฟังก์ชั่นโหลดข้อมูลโครงการ
function loadProjects() {
    console.log('loadProjects called');
    
    $('#projectsTableBody').html(`
        <tr>
            <td colspan="5" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">กำลังโหลด...</p>
            </td>
        </tr>
    `);
    
    $.ajax({
        url: '../api/projects/list_full.php',
        type: 'GET',
        data: { page: currentPage, per_page: 50 },
        dataType: 'json',
        success: function(response) {
            console.log('Projects loaded:', response);
            if(response.success && response.data) {
                renderProjectsTable(response.data);
            } else {
                $('#projectsTableBody').html(`
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <p class="text-muted">ไม่พบข้อมูลโครงการ</p>
                            <button class="btn btn-primary btn-sm" onclick="showProjectForm()">
                                <i class="fas fa-plus"></i> เพิ่มโครงการ
                            </button>
                        </td>
                    </tr>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Load error:', error);
            $('#projectsTableBody').html(`
                <tr>
                    <td colspan="5" class="text-center py-5 text-danger">
                        <p>ไม่สามารถโหลดข้อมูลได้</p>
                        <button class="btn btn-sm btn-outline-primary" onclick="loadProjects()">
                            <i class="fas fa-redo"></i> ลองใหม่
                        </button>
                    </td>
                </tr>
            `);
        }
    });
}

// แสดงข้อมูลในตาราง
function renderProjectsTable(projects) {
    let html = '';
    
    projects.forEach(p => {
        html += `
            <tr>
                <td>${p.id}</td>
                <td>
                    <strong>${p.name}</strong>
                    <br><small class="text-muted">ID: ${p.id}</small>
                </td>
                <td>${numberFormat(p.price)} บาท</td>
                <td><span class="badge bg-info">${p.zone || '-'}</span></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editProject(${p.id})">
                        <i class="fas fa-edit"></i> แก้ไข
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteProject(${p.id})">
                        <i class="fas fa-trash"></i> ลบ
                    </button>
                </td>
            </tr>
        `;
    });
    
    $('#projectsTableBody').html(html);
}

// โหลดสถิติ
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

// โหลด dropdown โซน
function loadZones() {
    $.ajax({
        url: '../api/master/get.php',
        type: 'GET',
        data: { type: 'zone' },
        dataType: 'json',
        success: function(data) {
            let options = '<option value="">เลือกโซน</option>';
            if(Array.isArray(data)) {
                data.forEach(item => {
                    options += `<option value="${item.value}">${item.value}</option>`;
                });
            }
            $('#project_zone').html(options);
        },
        error: function() {
            // ถ้าโหลดจาก master ไม่ได้ ใช้โซนจาก projects
            $.ajax({
                url: '../api/projects/list.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let zones = new Set();
                    let options = '<option value="">เลือกโซน</option>';
                    
                    if(response.data) {
                        response.data.forEach(p => {
                            if(p.zone) zones.add(p.zone);
                        });
                        zones.forEach(zone => {
                            options += `<option value="${zone}">${zone}</option>`;
                        });
                    }
                    
                    options += '<option value="กรุงเทพ-กลาง">กรุงเทพ-กลาง</option>';
                    options += '<option value="กรุงเทพ-เหนือ">กรุงเทพ-เหนือ</option>';
                    options += '<option value="กรุงเทพ-ใต้">กรุงเทพ-ใต้</option>';
                    options += '<option value="กรุงเทพ-ตะวันออก">กรุงเทพ-ตะวันออก</option>';
                    options += '<option value="นนทบุรี">นนทบุรี</option>';
                    options += '<option value="ชลบุรี">ชลบุรี</option>';
                    
                    $('#project_zone').html(options);
                }
            });
        }
    });
}

// Format number
function numberFormat(number) {
    if(!number && number !== 0) return '0';
    return new Intl.NumberFormat('th-TH').format(parseFloat(number));
}

// ============ เริ่มต้นเมื่อโหลดหน้า ============
$(document).ready(function() {
    console.log('Document ready - Initializing projects page');
    
    // ตรวจสอบว่ามี Bootstrap modal หรือไม่
    if(typeof $.fn.modal === 'undefined') {
        console.error('Bootstrap modal not available!');
        // โหลด Bootstrap JS เพิ่ม
        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js';
        document.head.appendChild(script);
    }
    
    // โหลดข้อมูลเริ่มต้น
    loadZones();
    loadProjects();
    loadProjectStats();
    
    // ทดสอบว่าปุ่มทำงาน - เพิ่ม event listener โดยตรง
    $('button[onclick*="showProjectForm"]').on('click', function(e) {
        console.log('Button clicked via jQuery');
    });
});

// ทดสอบการทำงานเมื่อกดปุ่ม (fallback)
$(document).on('click', '.btn-primary[onclick*="showProjectForm"]', function() {
    console.log('Add project button clicked');
});
</script>

<?php include_once '../includes/footer.php'; ?>