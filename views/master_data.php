<?php
// views/master_data.php
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
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">📊 จัดการข้อมูลหลัก (Master Data)</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="settings.php">ตั้งค่า</a></li>
                        <li class="breadcrumb-item active">ข้อมูลหลัก</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Master Data Tabs -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="masterDataTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-channel" type="button" onclick="loadMasterDataByType('channel')">
                            <i class="fas fa-bullhorn"></i> ช่องทาง
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-zone" type="button" onclick="loadMasterDataByType('zone')">
                            <i class="fas fa-map-marker-alt"></i> โซน
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-bank" type="button" onclick="loadMasterDataByType('bank')">
                            <i class="fas fa-university"></i> ธนาคาร
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-welfare" type="button" onclick="loadMasterDataByType('welfare')">
                            <i class="fas fa-hand-holding-heart"></i> สวัสดิการ
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-status" type="button" onclick="loadMasterDataByType('case_status')">
                            <i class="fas fa-tasks"></i> สถานะเคส
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-follow" type="button" onclick="loadMasterDataByType('follow_status')">
                            <i class="fas fa-phone"></i> สถานะติดตาม
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kpi" type="button" onclick="loadMasterDataByType('kpi_reason')">
                            <i class="fas fa-check-circle"></i> เหตุผล KPI
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="masterDataTabContent">
                    
                    <!-- ============ TAB 1: ช่องทาง ============ -->
                    <div class="tab-pane fade show active" id="tab-channel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>จัดการช่องทาง</h5>
                            <button class="btn btn-primary btn-sm" onclick="showAddForm('channel')">
                                <i class="fas fa-plus"></i> เพิ่มช่องทาง
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>ชื่อช่องทาง</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="channelTableBody">
                                    <tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> กำลังโหลด...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ============ TAB 2: โซน ============ -->
                    <div class="tab-pane fade" id="tab-zone">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>จัดการโซน</h5>
                            <button class="btn btn-primary btn-sm" onclick="showAddForm('zone')">
                                <i class="fas fa-plus"></i> เพิ่มโซน
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>ชื่อโซน</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="zoneTableBody">
                                    <tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> กำลังโหลด...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ============ TAB 3: ธนาคาร ============ -->
                    <div class="tab-pane fade" id="tab-bank">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>จัดการธนาคาร</h5>
                            <button class="btn btn-primary btn-sm" onclick="showAddForm('bank')">
                                <i class="fas fa-plus"></i> เพิ่มธนาคาร
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>ชื่อธนาคาร</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="bankTableBody">
                                    <tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> กำลังโหลด...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ============ TAB 4: สวัสดิการ ============ -->
                    <div class="tab-pane fade" id="tab-welfare">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>จัดการสวัสดิการ</h5>
                            <button class="btn btn-primary btn-sm" onclick="showAddForm('welfare')">
                                <i class="fas fa-plus"></i> เพิ่มสวัสดิการ
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>ชื่อสวัสดิการ</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="welfareTableBody">
                                    <tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> กำลังโหลด...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ============ TAB 5: สถานะเคส ============ -->
                    <div class="tab-pane fade" id="tab-status">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>จัดการสถานะเคส</h5>
                            <button class="btn btn-primary btn-sm" onclick="showAddForm('case_status')">
                                <i class="fas fa-plus"></i> เพิ่มสถานะ
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>ชื่อสถานะ</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="caseStatusTableBody">
                                    <tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> กำลังโหลด...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ============ TAB 6: สถานะติดตาม ============ -->
                    <div class="tab-pane fade" id="tab-follow">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>จัดการสถานะติดตาม</h5>
                            <button class="btn btn-primary btn-sm" onclick="showAddForm('follow_status')">
                                <i class="fas fa-plus"></i> เพิ่มสถานะติดตาม
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>ชื่อสถานะ</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="followStatusTableBody">
                                    <tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> กำลังโหลด...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ============ TAB 7: เหตุผล KPI ============ -->
                    <div class="tab-pane fade" id="tab-kpi">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>จัดการเหตุผล KPI</h5>
                            <button class="btn btn-primary btn-sm" onclick="showAddForm('kpi_reason')">
                                <i class="fas fa-plus"></i> เพิ่มเหตุผล
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>เหตุผล</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="kpiReasonTableBody">
                                    <tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> กำลังโหลด...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="masterDataModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="masterDataModalTitle">
                    <i class="fas fa-plus"></i> เพิ่มข้อมูล
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="masterDataForm">
                    <input type="hidden" id="md_id">
                    <input type="hidden" id="md_type">
                    
                    <div class="mb-3">
                        <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="md_value" required
                               placeholder="กรอกข้อมูล">
                        <div class="invalid-feedback">กรุณากรอกข้อมูล</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="md_is_active" checked>
                            <label class="form-check-label" for="md_is_active">
                                <span id="activeLabel">เปิดใช้งาน</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> ยกเลิก
                </button>
                <button type="button" class="btn btn-primary" onclick="saveMasterData()">
                    <i class="fas fa-save"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ============ MASTER DATA JAVASCRIPT ============
let currentType = '';

// ✅ Map type → table body ID
const tableMap = {
    'channel':      'channelTableBody',
    'zone':         'zoneTableBody',
    'bank':         'bankTableBody',
    'welfare':      'welfareTableBody',
    'case_status':  'caseStatusTableBody',
    'follow_status':'followStatusTableBody',
    'kpi_reason':   'kpiReasonTableBody'
};

const typeLabels = {
    'channel':      'ช่องทาง',
    'zone':         'โซน',
    'bank':         'ธนาคาร',
    'welfare':      'สวัสดิการ',
    'case_status':  'สถานะเคส',
    'follow_status':'สถานะติดตาม',
    'kpi_reason':   'เหตุผล KPI'
};

$(document).ready(function() {
    // โหลดเฉพาะ Tab แรก (channel)
    loadMasterDataByType('channel');
    
    // ✅ เปลี่ยนป้าย activeLabel
    $('#md_is_active').on('change', function() {
        $('#activeLabel').text($(this).is(':checked') ? 'เปิดใช้งาน' : 'ปิดใช้งาน');
    });
});

// ✅ โหลดข้อมูลตามประเภท
function loadMasterDataByType(type) {
    const tableBodyId = tableMap[type];
    if (!tableBodyId) return;

    // แสดง loading
    $('#' + tableBodyId).html(
        '<tr><td colspan="4" class="text-center py-4">' +
        '<div class="spinner-border spinner-border-sm text-primary"></div> กำลังโหลด...</td></tr>'
    );

    // ✅ เรียก API
    $.get('../api/master/get.php', { type: type }, function(data) {
        let html = '';
        
        if (Array.isArray(data) && data.length > 0) {
            data.forEach(function(item) {
                let statusBadge = item.is_active == 1 
                    ? '<span class="badge bg-success">เปิดใช้งาน</span>' 
                    : '<span class="badge bg-secondary">ปิดใช้งาน</span>';

                html += 
                '<tr>' +
                    '<td>' + item.id + '</td>' +
                    '<td>' + escapeHtml(item.value) + '</td>' +
                    '<td>' + statusBadge + '</td>' +
                    '<td>' +
                        '<button class="btn btn-sm btn-warning me-1" onclick="editMasterData(' + item.id + ',\'' + type + '\',\'' + escapeHtml(item.value) + '\',' + item.is_active + ')">' +
                            '<i class="fas fa-edit"></i>' +
                        '</button>' +
                        '<button class="btn btn-sm btn-danger" onclick="deleteMasterData(' + item.id + ',\'' + type + '\')">' +
                            '<i class="fas fa-trash"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>';
            });
        } else {
            html = '<tr><td colspan="4" class="text-center py-4 text-muted">' +
                   '<i class="fas fa-inbox fa-2x mb-2"></i><p>ไม่พบข้อมูล ' + (typeLabels[type] || '') + '</p></td></tr>';
        }
        
        $('#' + tableBodyId).html(html);
    }).fail(function(xhr) {
        $('#' + tableBodyId).html(
            '<tr><td colspan="4" class="text-center py-4 text-danger">' +
            '❌ ไม่สามารถโหลดข้อมูลได้ (Status: ' + xhr.status + ')<br>' +
            '<button class="btn btn-sm btn-outline-primary mt-2" onclick="loadMasterDataByType(\'' + type + '\')">ลองใหม่</button></td></tr>'
        );
    });
}

// ✅ แสดงฟอร์มเพิ่ม
function showAddForm(type) {
    currentType = type;
    $('#md_id').val('');
    $('#md_type').val(type);
    $('#md_value').val('');
    $('#md_is_active').prop('checked', true);
    $('#activeLabel').text('เปิดใช้งาน');
    $('#masterDataModalTitle').html('<i class="fas fa-plus"></i> เพิ่ม' + (typeLabels[type] || type));
    $('#masterDataModal').modal('show');
}

// ✅ แก้ไขข้อมูล
function editMasterData(id, type, value, isActive) {
    currentType = type;
    $('#md_id').val(id);
    $('#md_type').val(type);
    $('#md_value').val(value);
    $('#md_is_active').prop('checked', isActive == 1);
    $('#activeLabel').text(isActive == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน');
    $('#masterDataModalTitle').html('<i class="fas fa-edit"></i> แก้ไข' + (typeLabels[type] || type));
    $('#masterDataModal').modal('show');
}

// ✅ บันทึกข้อมูล (SweetAlert2)
function saveMasterData() {
    let value = $('#md_value').val().trim();
    if (!value) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกข้อมูล',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
        $('#md_value').focus();
        return;
    }

    let postData = {
        id: $('#md_id').val() || 0,
        type: $('#md_type').val(),
        value: value,
        is_active: $('#md_is_active').is(':checked') ? 1 : 0
    };

    Swal.fire({
        title: 'ยืนยันการบันทึก?',
        text: 'บันทึกข้อมูล: ' + value,
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

            $.ajax({
                url: '../api/master/save.php',
                type: 'POST',
                data: JSON.stringify(postData),
                contentType: 'application/json',
                dataType: 'json',
                timeout: 10000,
                success: function(res) {
                    btn.html(origText).prop('disabled', false);
                    
                    if (res.success) {
                        $('#masterDataModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadMasterDataByType(postData.type);
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

// ✅ ลบข้อมูล (SweetAlert2)
function deleteMasterData(id, type) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: 'ข้อมูลนี้จะถูกลบออกจากระบบ ไม่สามารถกู้คืนได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash"></i> ลบ',
        cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // แสดง loading
            Swal.fire({
                title: 'กำลังลบ...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '../api/master/delete.php',
                type: 'POST',
                data: JSON.stringify({ id: id }),
                contentType: 'application/json',
                dataType: 'json',
                timeout: 10000,
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบสำเร็จ!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadMasterDataByType(type);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด',
                            text: res.message || 'ไม่สามารถลบได้'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: 'ไม่สามารถลบข้อมูลได้ (Status: ' + xhr.status + ')'
                    });
                }
            });
        }
    });
}

// ✅ Helpers
function escapeHtml(text) {
    if (!text) return '';
    let div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php include_once '../includes/footer.php'; ?>