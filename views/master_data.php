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
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-channel" type="button">
                            <i class="fas fa-bullhorn"></i> ช่องทาง
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-zone" type="button">
                            <i class="fas fa-map-marker-alt"></i> โซน
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-bank" type="button">
                            <i class="fas fa-university"></i> ธนาคาร
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-welfare" type="button">
                            <i class="fas fa-hand-holding-heart"></i> สวัสดิการ
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-status" type="button">
                            <i class="fas fa-tasks"></i> สถานะเคส
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-follow" type="button">
                            <i class="fas fa-phone"></i> สถานะติดตาม
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kpi" type="button">
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
                                        <th>วันที่สร้าง</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="channelTableBody">
                                    <tr><td colspan="5" class="text-center">กำลังโหลด...</td></tr>
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
                                        <th>วันที่สร้าง</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="zoneTableBody">
                                    <tr><td colspan="5" class="text-center">กำลังโหลด...</td></tr>
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
                                        <th>วันที่สร้าง</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="bankTableBody">
                                    <tr><td colspan="5" class="text-center">กำลังโหลด...</td></tr>
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
                                        <th>วันที่สร้าง</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="welfareTableBody">
                                    <tr><td colspan="5" class="text-center">กำลังโหลด...</td></tr>
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
                                        <th>วันที่สร้าง</th>
                                        <th">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="caseStatusTableBody">
                                    <tr><td colspan="5" class="text-center">กำลังโหลด...</td></tr>
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
                                        <th>วันที่สร้าง</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="followStatusTableBody">
                                    <tr><td colspan="5" class="text-center">กำลังโหลด...</td></tr>
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
                                        <th>วันที่สร้าง</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="kpiReasonTableBody">
                                    <tr><td colspan="5" class="text-center">กำลังโหลด...</td></tr>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteMasterDataModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">ยืนยันการลบ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการลบข้อมูลนี้ใช่หรือไม่?</p>
                <input type="hidden" id="deleteMdId">
                <input type="hidden" id="deleteMdType">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" onclick="deleteMasterData()">
                    <i class="fas fa-trash"></i> ลบ
                </button>
            </div>
        </div>
    </div>
</div>
<script>
// ============ MASTER DATA JAVASCRIPT (แก้ไขใหม่) ============
let currentType = '';

// Map type to table body ID
const tableMap = {
    'channel': 'channelTableBody',
    'zone': 'zoneTableBody',
    'bank': 'bankTableBody',
    'welfare': 'welfareTableBody',
    'case_status': 'caseStatusTableBody',
    'follow_status': 'followStatusTableBody',
    'kpi_reason': 'kpiReasonTableBody'
};

// Type labels
const typeLabels = {
    'channel': 'ช่องทาง',
    'zone': 'โซน',
    'bank': 'ธนาคาร',
    'welfare': 'สวัสดิการ',
    'case_status': 'สถานะเคส',
    'follow_status': 'สถานะติดตาม',
    'kpi_reason': 'เหตุผล KPI'
};

$(document).ready(function() {
    console.log('Master Data Page Loaded');
    
    // โหลดข้อมูลทั้งหมด
    loadAllMasterData();
    
    // โหลดข้อมูลเมื่อคลิก tab
    $('#masterDataTabs button').on('shown.bs.tab', function(e) {
        const target = $(e.target).data('bs-target');
        const type = target.replace('#tab-', '');
        console.log('Tab changed to:', type);
        loadMasterDataByType(type);
    });
});

// โหลดข้อมูลทั้งหมด
function loadAllMasterData() {
    const types = ['channel', 'zone', 'bank', 'welfare', 'case_status', 'follow_status', 'kpi_reason'];
    types.forEach(function(type) {
        loadMasterDataByType(type);
    });
}

// โหลดข้อมูลตามประเภท
function loadMasterDataByType(type) {
    const tableBodyId = tableMap[type];
    if(!tableBodyId) {
        console.error('No table mapping for type:', type);
        return;
    }
    
    console.log('Loading data for type:', type);
    
    // แสดง loading
    $('#' + tableBodyId).html(
        '<tr><td colspan="5" class="text-center py-3">' +
        '<div class="spinner-border spinner-border-sm text-primary"></div> กำลังโหลด...</td></tr>'
    );
    
    // เรียก API
    $.ajax({
        url: '../api/master/get.php',
        type: 'GET',
        data: { type: type },
        dataType: 'json',
        timeout: 10000, // 10 seconds timeout
        success: function(response) {
            console.log('Response for ' + type + ':', response);
            
            // ตรวจสอบว่า response เป็น array โดยตรง
            let data = response;
            if(response && response.data) {
                data = response.data;
            }
            
            if(Array.isArray(data) && data.length > 0) {
                renderTable(tableBodyId, data, type);
            } else {
                showEmpty(tableBodyId, type);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading ' + type + ':', status, error);
            console.error('Response:', xhr.responseText);
            
            $('#' + tableBodyId).html(
                '<tr><td colspan="5" class="text-center py-4 text-danger">' +
                '<i class="fas fa-exclamation-triangle"></i> ไม่สามารถโหลดข้อมูลได้<br>' +
                '<small class="text-muted">Status: ' + status + '</small><br>' +
                '<button class="btn btn-sm btn-outline-primary mt-2" onclick="loadMasterDataByType(\'' + type + '\')">' +
                '<i class="fas fa-redo"></i> ลองใหม่</button>' +
                '</td></tr>'
            );
        }
    });
}

// แสดงข้อมูลในตาราง
function renderTable(tableBodyId, data, type) {
    let html = '';
    
    data.forEach(function(item, index) {
        const statusBadge = item.is_active == 1 
            ? '<span class="badge bg-success">เปิดใช้งาน</span>' 
            : '<span class="badge bg-secondary">ปิดใช้งาน</span>';
        
        const createdAt = item.created_at ? formatDate(item.created_at) : '-';
        
        html += '<tr>' +
            '<td>' + item.id + '</td>' +
            '<td>' + escapeHtml(item.value) + '</td>' +
            '<td>' + statusBadge + '</td>' +
            '<td><small>' + createdAt + '</small></td>' +
            '<td>' +
                '<button class="btn btn-sm btn-warning me-1" onclick="editMasterData(' + item.id + ', \'' + type + '\', \'' + escapeHtml(item.value) + '\', ' + item.is_active + ')">' +
                    '<i class="fas fa-edit"></i>' +
                '</button>' +
                '<button class="btn btn-sm btn-danger" onclick="confirmDelete(' + item.id + ', \'' + type + '\')">' +
                    '<i class="fas fa-trash"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';
    });
    
    $('#' + tableBodyId).html(html);
}

// แสดงเมื่อไม่มีข้อมูล
function showEmpty(tableBodyId, type) {
    const label = typeLabels[type] || type;
    $('#' + tableBodyId).html(
        '<tr><td colspan="5" class="text-center py-4 text-muted">' +
        '<i class="fas fa-inbox fa-2x mb-2"></i>' +
        '<p>ไม่พบข้อมูล' + label + '</p>' +
        '<button class="btn btn-primary btn-sm" onclick="showAddForm(\'' + type + '\')">' +
        '<i class="fas fa-plus"></i> เพิ่ม' + label + '</button>' +
        '</td></tr>'
    );
}

// แสดงฟอร์มเพิ่ม
function showAddForm(type) {
    currentType = type;
    $('#md_id').val('');
    $('#md_type').val(type);
    $('#md_value').val('');
    $('#md_is_active').prop('checked', true);
    
    const label = typeLabels[type] || type;
    $('#masterDataModalTitle').html('<i class="fas fa-plus"></i> เพิ่ม' + label);
    $('#masterDataModal').modal('show');
}

// แก้ไขข้อมูล
function editMasterData(id, type, value, isActive) {
    currentType = type;
    $('#md_id').val(id);
    $('#md_type').val(type);
    $('#md_value').val(value);
    $('#md_is_active').prop('checked', isActive == 1);
    
    const label = typeLabels[type] || type;
    $('#masterDataModalTitle').html('<i class="fas fa-edit"></i> แก้ไข' + label);
    $('#masterDataModal').modal('show');
}

// บันทึกข้อมูล
function saveMasterData() {
    const id = $('#md_id').val();
    const type = $('#md_type').val();
    const value = $('#md_value').val().trim();
    const isActive = $('#md_is_active').is(':checked') ? 1 : 0;
    
    // Validate
    if(!value) {
        alert('กรุณากรอกข้อมูล');
        $('#md_value').focus();
        return;
    }
    
    // Disable button
    const saveBtn = $('.modal-footer .btn-primary');
    const originalText = saveBtn.html();
    saveBtn.html('<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...').prop('disabled', true);
    
    // Prepare data
    const postData = {
        type: type,
        value: value,
        is_active: isActive
    };
    
    if(id) {
        postData.id = parseInt(id);
    }
    
    console.log('Saving data:', postData);
    
    // Call API
    $.ajax({
        url: '../api/master/save.php',
        type: 'POST',
        data: JSON.stringify(postData),
        contentType: 'application/json',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Save response:', response);
            saveBtn.html(originalText).prop('disabled', false);
            
            if(response.success) {
                $('#masterDataModal').modal('hide');
                alert(response.message || 'บันทึกสำเร็จ!');
                loadMasterDataByType(type);
            } else {
                alert('ผิดพลาด: ' + (response.message || 'ไม่สามารถบันทึกได้'));
            }
        },
        error: function(xhr, status, error) {
            console.error('Save error:', status, error);
            saveBtn.html(originalText).prop('disabled', false);
            
            let msg = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้';
            try {
                const resp = JSON.parse(xhr.responseText);
                msg = resp.message || msg;
            } catch(e) {}
            
            alert('ผิดพลาด: ' + msg);
        }
    });
}

// ยืนยันการลบ
function confirmDelete(id, type) {
    if(confirm('ยืนยันการลบข้อมูลนี้? การลบไม่สามารถกู้คืนได้')) {
        // เรียก API ลบ
        $.ajax({
            url: '../api/master/delete.php',
            type: 'POST',
            data: JSON.stringify({ id: parseInt(id) }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    alert('ลบสำเร็จ!');
                    loadMasterDataByType(type);
                } else {
                    alert('ผิดพลาด: ' + (response.message || 'ไม่สามารถลบได้'));
                }
            },
            error: function() {
                alert('ไม่สามารถลบข้อมูลได้');
            }
        });
    }
}

// Helper functions
function formatDate(dateString) {
    if(!dateString) return '-';
    try {
        const date = new Date(dateString);
        if(isNaN(date.getTime())) return dateString;
        return date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch(e) {
        return dateString;
    }
}

function escapeHtml(text) {
    if(!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php include_once '../includes/footer.php'; ?>