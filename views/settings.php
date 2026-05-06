<?php
// views/settings.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../includes/header.php';
include_once '../includes/sidebar.php';
include_once '../includes/auth_check.php';
include_once '../includes/functions.php';

// เฉพาะ admin เท่านั้น
if (!checkRole('admin')) {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">⚙️ ตั้งค่าระบบ</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">ตั้งค่าระบบ</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Settings Tabs -->
        <div class="row">
            <div class="col-xl-3 mb-3">
                <!-- Side Menu -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="settingsMenu">
                            <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#tab-general">
                                <i class="fas fa-cog me-2"></i> ตั้งค่าทั่วไป
                            </a>
                            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tab-notification">
                                <i class="fas fa-bell me-2"></i> การแจ้งเตือน
                            </a>
                            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tab-line">
                                <i class="fab fa-line me-2"></i> LINE Notify
                            </a>
                            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tab-backup">
                                <i class="fas fa-database me-2"></i> สำรองข้อมูล
                            </a>
                            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tab-system">
                                <i class="fas fa-server me-2"></i> ข้อมูลระบบ
                            </a>
                            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tab-about">
                                <i class="fas fa-info-circle me-2"></i> เกี่ยวกับ
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9">
                <div class="tab-content">

                    <!-- ============ TAB 1: ตั้งค่าทั่วไป ============ -->
                    <div class="tab-pane fade show active" id="tab-general">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-cog"></i> ตั้งค่าทั่วไป</h5>
                            </div>
                            <div class="card-body">
                                <form id="generalSettingsForm">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">ชื่อระบบ</label>
                                            <input type="text" class="form-control" id="system_name" value="CRM Condo System">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">ชื่อบริษัท</label>
                                            <input type="text" class="form-control" id="company_name" placeholder="ชื่อบริษัท">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">ภาษาเริ่มต้น</label>
                                            <select class="form-select" id="default_language">
                                                <option value="th" selected>ไทย (Thai)</option>
                                                <option value="en">English</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Timezone</label>
                                            <select class="form-select" id="timezone">
                                                <option value="Asia/Bangkok" selected>Asia/Bangkok (GMT+7)</option>
                                                <option value="Asia/Singapore">Asia/Singapore (GMT+8)</option>
                                                <option value="Asia/Tokyo">Asia/Tokyo (GMT+9)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">จำนวนรายการต่อหน้า</label>
                                            <select class="form-select" id="items_per_page">
                                                <option value="10">10</option>
                                                <option value="25" selected>25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">รูปแบบวันที่</label>
                                            <select class="form-select" id="date_format">
                                                <option value="d/m/Y" selected>วัน/เดือน/ปี (31/12/2026)</option>
                                                <option value="Y-m-d">ปี-เดือน-วัน (2026-12-31)</option>
                                                <option value="m/d/Y">เดือน/วัน/ปี (12/31/2026)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">รูปแบบสกุลเงิน</label>
                                            <select class="form-select" id="currency_format">
                                                <option value="thb" selected>บาท (THB)</option>
                                                <option value="usd">USD ($)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enable_register" checked>
                                            <label class="form-check-label" for="enable_register">อนุญาตให้ลงทะเบียนผู้ใช้ใหม่</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enable_maintenance">
                                            <label class="form-check-label" for="enable_maintenance">โหมดซ่อมบำรุง</label>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary" onclick="saveAllSettings()">
                                        <i class="fas fa-save"></i> บันทึกการตั้งค่าทั้งหมด
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ============ TAB 2: การแจ้งเตือน ============ -->
                    <div class="tab-pane fade" id="tab-notification">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-bell"></i> ตั้งค่าการแจ้งเตือน</h5>
                            </div>
                            <div class="card-body">
                                <form id="notificationSettingsForm">
                                    <h6 class="mb-3">การแจ้งเตือนทางอีเมล</h6>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="email_new_case" checked>
                                            <label class="form-check-label" for="email_new_case">แจ้งเตือนเมื่อมีเคสใหม่</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="email_kpi_fail" checked>
                                            <label class="form-check-label" for="email_kpi_fail">แจ้งเตือนเมื่อ KPI ไม่ผ่าน</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="email_approval" checked>
                                            <label class="form-check-label" for="email_approval">แจ้งเตือนเมื่อมีการอนุมัติ</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="email_transfer" checked>
                                            <label class="form-check-label" for="email_transfer">แจ้งเตือนก่อนวันโอน 3 วัน</label>
                                        </div>
                                    </div>

                                    <hr>

                                    <h6 class="mb-3">การแจ้งเตือนในระบบ</h6>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="system_new_case" checked>
                                            <label class="form-check-label" for="system_new_case">แสดงป๊อปอัพเมื่อมีเคสใหม่</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="system_follow_reminder" checked>
                                            <label class="form-check-label" for="system_follow_reminder">แจ้งเตือนเมื่อถึงเวลาติดตามลูกค้า</label>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary" onclick="saveAllSettings()">
                                        <i class="fas fa-save"></i> บันทึกการตั้งค่าทั้งหมด
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ============ TAB 3: LINE Notify ============ -->
                    <div class="tab-pane fade" id="tab-line">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fab fa-line"></i> ตั้งค่า LINE Notify</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>วิธีใช้งาน LINE Notify:</strong><br>
                                    1. เข้าไปที่ <a href="https://notify-bot.line.me/th/" target="_blank">LINE Notify</a><br>
                                    2. กด "สร้าง Token" และเลือกกลุ่มที่ต้องการส่งข้อความ<br>
                                    3. คัดลอก Token มาใส่ในช่องด้านล่าง
                                </div>

                                <form id="lineSettingsForm">
                                    <div class="mb-3">
                                        <label class="form-label">LINE Notify Token</label>
                                        <input type="text" class="form-control" id="line_token"
                                            placeholder="กรอก LINE Notify Token" style="font-family: monospace;">
                                        <div class="form-text">Token จะถูกเก็บอย่างปลอดภัย</div>
                                    </div>

                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-success" onclick="testLineNotify()">
                                                <i class="fab fa-line"></i> ทดสอบส่งข้อความ
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">การแจ้งเตือนที่ส่งผ่าน LINE</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="line_new_case">
                                            <label class="form-check-label" for="line_new_case">แจ้งเตือนเมื่อมีเคสใหม่</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="line_approval" checked>
                                            <label class="form-check-label" for="line_approval">แจ้งเตือนผลอนุมัติ</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="line_transfer" checked>
                                            <label class="form-check-label" for="line_transfer">แจ้งเตือนวันโอน</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="line_daily_report">
                                            <label class="form-check-label" for="line_daily_report">ส่งสรุปประจำวัน (18:00 น.)</label>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-primary" onclick="saveAllSettings()">
                                                <i class="fas fa-save"></i> บันทึกการตั้งค่าทั้งหมด
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ============ TAB 4: สำรองข้อมูล ============ -->
                    <div class="tab-pane fade" id="tab-backup">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-database"></i> สำรองและกู้คืนข้อมูล</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>คำแนะนำ:</strong> ควรสำรองข้อมูลอย่างน้อยสัปดาห์ละครั้ง
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body text-center py-4">
                                                <i class="fas fa-download fa-3x text-primary mb-3"></i>
                                                <h5>สำรองข้อมูล</h5>
                                                <p class="text-muted">ดาวน์โหลดไฟล์สำรองฐานข้อมูล</p>
                                                <button class="btn btn-primary" onclick="backupDatabase()">
                                                    <i class="fas fa-download"></i> สำรองข้อมูลตอนนี้
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body text-center py-4">
                                                <i class="fas fa-upload fa-3x text-warning mb-3"></i>
                                                <h5>กู้คืนข้อมูล</h5>
                                                <p class="text-muted">อัปโหลดไฟล์สำรองเพื่อกู้คืนฐานข้อมูล</p>
                                                <form id="restoreForm" enctype="multipart/form-data">
                                                    <div class="mb-2">
                                                        <input type="file" class="form-control" id="restoreFile" accept=".sql,.zip">
                                                    </div>
                                                    <button type="button" class="btn btn-warning" onclick="restoreDatabase()">
                                                        <i class="fas fa-upload"></i> กู้คืนข้อมูล
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6>ประวัติการสำรองข้อมูล</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>วันที่</th>
                                                <th>ชื่อไฟล์</th>
                                                <th>ขนาดไฟล์</th>
                                                <th>ผู้ใช้</th>
                                                <th>จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="backupHistory">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <div class="spinner-border spinner-border-sm"></div> กำลังโหลด...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ TAB 5: ข้อมูลระบบ ============ -->
                    <div class="tab-pane fade" id="tab-system">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-server"></i> ข้อมูลระบบ</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="200">PHP Version</th>
                                        <td><?php echo phpversion(); ?></td>
                                    </tr>
                                    <tr>
                                        <th>MySQL Version</th>
                                        <td id="mysqlVersion">กำลังโหลด...</td>
                                    </tr>
                                    <tr>
                                        <th>Server Software</th>
                                        <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>ระบบปฏิบัติการ</th>
                                        <td><?php echo PHP_OS; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Memory Limit</th>
                                        <td><?php echo ini_get('memory_limit'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Upload Max Filesize</th>
                                        <td><?php echo ini_get('upload_max_filesize'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Post Max Size</th>
                                        <td><?php echo ini_get('post_max_size'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Max Execution Time</th>
                                        <td><?php echo ini_get('max_execution_time'); ?> วินาที</td>
                                    </tr>
                                    <tr>
                                        <th>Session Timeout</th>
                                        <td>30 นาที</td>
                                    </tr>
                                </table>

                                <h6 class="mt-4">สถิติฐานข้อมูล</h6>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ตาราง</th>
                                            <th>จำนวนข้อมูล</th>
                                            <th>ขนาด</th>
                                        </tr>
                                    </thead>
                                    <tbody id="databaseStats">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">กำลังโหลด...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ============ TAB 6: เกี่ยวกับ ============ -->
                    <div class="tab-pane fade" id="tab-about">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> เกี่ยวกับระบบ</h5>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-building fa-4x text-primary mb-3"></i>
                                <h3>CRM Condo System</h3>
                                <p class="text-muted">ระบบจัดการคอนโดและสินเชื่อ</p>

                                <table class="table table-borderless text-start mx-auto" style="max-width: 400px;">
                                    <tr>
                                        <td><strong>เวอร์ชั่น:</strong></td>
                                        <td>v1.0.0</td>
                                    </tr>
                                    <tr>
                                        <td><strong>วันที่เผยแพร่:</strong></td>
                                        <td>4 พฤษภาคม 2026</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ผู้พัฒนา:</strong></td>
                                        <td>นายอนุชน วทานิยโรจน์</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ติดต่อ:</strong></td>
                                        <td>anuchonb@gmail.com</td>
                                    </tr>
                                    <tr>
                                        <td><strong>เทคโนโลยี:</strong></td>
                                        <td>PHP, MySQL, Bootstrap 5, jQuery, SweetAlert2</td>
                                    </tr>
                                </table>

                                <div class="mt-3">
                                    <a href="dashboard.php" class="btn btn-primary">
                                        <i class="fas fa-home"></i> กลับหน้า Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ✅ โหลดเมื่อเปิดหน้า

    $(document).ready(function() {
        loadAllSettings();
        loadBackupHistory();

        // Activate first menu item
        $('#settingsMenu a:first-child').addClass('active');

        // Handle menu click
        $('#settingsMenu a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
            $('#settingsMenu a').removeClass('active');
            $(this).addClass('active');
        });

        // Load system info
        loadSystemInfo();
        loadDatabaseStats();
    });

    // ✅ โหลดค่าตั้งค่าเมื่อเปิดหน้า
    function loadAllSettings() {
        $.get('../api/settings/get_settings.php', function(res) {
            if (res.success && res.data) {
                let s = res.data;

                // Tab ทั่วไป
                $('#system_name').val(s.system_name || 'CRM Condo System');
                $('#company_name').val(s.company_name || '');
                $('#default_language').val(s.language || 'th');
                $('#timezone').val(s.timezone || 'Asia/Bangkok');
                $('#items_per_page').val(s.items_per_page || '25');
                $('#date_format').val(s.date_format || 'd/m/Y');
                $('#currency_format').val(s.currency_format || 'thb');
                $('#enable_register').prop('checked', s.enable_register == '1');
                $('#enable_maintenance').prop('checked', s.enable_maintenance == '1');

                // Tab การแจ้งเตือน
                $('#email_new_case').prop('checked', s.email_new_case == '1');
                $('#email_kpi_fail').prop('checked', s.email_kpi_fail == '1');
                $('#email_approval').prop('checked', s.email_approval == '1');
                $('#email_transfer').prop('checked', s.email_transfer == '1');
                $('#system_new_case').prop('checked', s.system_new_case == '1');
                $('#system_follow_reminder').prop('checked', s.system_follow_reminder == '1');

                // Tab LINE
                $('#line_token').val(s.line_token || '');
                $('#line_new_case').prop('checked', s.line_new_case == '1');
                $('#line_approval').prop('checked', s.line_approval == '1');
                $('#line_transfer').prop('checked', s.line_transfer == '1');
                $('#line_daily_report').prop('checked', s.line_daily_report == '1');

                // console.log('Settings loaded');
            }
        }, 'json');
    }

    // ✅ บันทึกค่าตั้งค่าทั้งหมด
    function saveAllSettings() {
        // เก็บข้อมูลทุก Tab ใน object เดียว
        let data = {
            // ทั่วไป
            system_name: $('#system_name').val(),
            company_name: $('#company_name').val(),
            language: $('#default_language').val(),
            timezone: $('#timezone').val(),
            items_per_page: $('#items_per_page').val(),
            date_format: $('#date_format').val(),
            currency_format: $('#currency_format').val(),
            enable_register: $('#enable_register').is(':checked') ? '1' : '0',
            enable_maintenance: $('#enable_maintenance').is(':checked') ? '1' : '0',

            // การแจ้งเตือน
            email_new_case: $('#email_new_case').is(':checked') ? '1' : '0',
            email_kpi_fail: $('#email_kpi_fail').is(':checked') ? '1' : '0',
            email_approval: $('#email_approval').is(':checked') ? '1' : '0',
            email_transfer: $('#email_transfer').is(':checked') ? '1' : '0',
            system_new_case: $('#system_new_case').is(':checked') ? '1' : '0',
            system_follow_reminder: $('#system_follow_reminder').is(':checked') ? '1' : '0',

            // LINE
            line_token: $('#line_token').val(),
            line_new_case: $('#line_new_case').is(':checked') ? '1' : '0',
            line_approval: $('#line_approval').is(':checked') ? '1' : '0',
            line_transfer: $('#line_transfer').is(':checked') ? '1' : '0',
            line_daily_report: $('#line_daily_report').is(':checked') ? '1' : '0'
        };

        Swal.fire({
            title: 'บันทึกการตั้งค่า?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save"></i> บันทึก',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังบันทึก...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '../api/settings/save_settings.php',
                    type: 'POST',
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    dataType: 'json',
                    timeout: 10000,
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'บันทึกสำเร็จ!',
                                text: 'การตั้งค่าถูกบันทึกเรียบร้อย',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ผิดพลาด',
                                text: res.message || ''
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด',
                            text: 'Status: ' + xhr.status
                        });
                    }
                });
            }
        });
    }

    // ============ GENERAL SETTINGS ============
    function saveGeneralSettings() {
        Swal.fire({
            title: 'บันทึกการตั้งค่า?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                showToast('บันทึกการตั้งค่าทั่วไปเรียบร้อย', 'success');
            }
        });
    }

    // ============ NOTIFICATION SETTINGS ============
    function saveNotificationSettings() {
        Swal.fire({
            title: 'บันทึกการตั้งค่า?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                showToast('บันทึกการตั้งค่าการแจ้งเตือนเรียบร้อย', 'success');
            }
        });
    }

    // ============ LINE SETTINGS ============
    function testLineNotify() {
        const token = $('#line_token').val().trim();

        if (!token) {
            Swal.fire('กรุณากรอก LINE Notify Token', '', 'warning');
            return;
        }

        Swal.fire({
            title: 'กำลังส่งข้อความทดสอบ...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '../api/settings/test_line.php',
            type: 'POST',
            data: JSON.stringify({
                token: token
            }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('สำเร็จ!', 'ส่งข้อความทดสอบเรียบร้อย กรุณาตรวจสอบ LINE', 'success');
                } else {
                    Swal.fire('ผิดพลาด!', response.message || 'ไม่สามารถส่งข้อความได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด!', 'ไม่สามารถเชื่อมต่อ LINE Notify ได้', 'error');
            }
        });
    }

    function saveLineSettings() {
        Swal.fire({
            title: 'บันทึกการตั้งค่า LINE?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                showToast('บันทึกการตั้งค่า LINE เรียบร้อย', 'success');
            }
        });
    }

    // ============ BACKUP ============
    function backupDatabase() {
        Swal.fire({
            title: 'ยืนยันการสำรองข้อมูล?',
            text: 'ระบบจะสร้างไฟล์สำรองฐานข้อมูล',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'สำรองข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังสำรองข้อมูล...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // เปิดหน้าต่างดาวน์โหลด
                window.open('../api/settings/backup.php', '_blank');

                setTimeout(() => {
                    Swal.fire('สำเร็จ!', 'กำลังดาวน์โหลดไฟล์สำรองข้อมูล', 'success');
                }, 2000);
            }
        });
    } +

    function formatDate(dateString) {
        if (!dateString) return '-';
        try {
            return new Date(dateString).toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return dateString;
        }
    }

    // ✅ โหลดประวัติการสำรองข้อมูล (แก้ไขใหม่)
    function loadBackupHistory() {
        console.log('Loading backup history...');

        $.ajax({
            url: '../api/settings/backup_history.php',
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(res) {
                console.log('Backup history response:', res);

                if (res.success && res.data && res.data.length > 0) {
                    let html = '';
                    res.data.forEach(function(item, index) {
                        html +=
                            '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td><small>' + (item.created_at || '-') + '</small></td>' +
                            '<td>' + (item.filename || '-') + '</td>' +
                            '<td>' + (item.size_display || '-') + '</td>' +
                            '<td>' + (item.user_name || '-') + '</td>' +
                            '<td><button class="btn btn-sm btn-outline-info" onclick="alert(\'ไฟล์: ' + (item.filename || '') + '\')"><i class="fas fa-info-circle"></i></button></td>' +
                            '</tr>';
                    });
                    $('#backupHistory').html(html);
                } else {
                    $('#backupHistory').html(
                        '<tr><td colspan="6" class="text-center text-muted py-3">' +
                        '<i class="fas fa-inbox fa-2x mb-2"></i><p>ยังไม่มีประวัติการสำรองข้อมูล</p>' +
                        '</td></tr>'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('Backup history error:', status, error);
                console.error('Response:', xhr.responseText);
                $('#backupHistory').html(
                    '<tr><td colspan="6" class="text-center text-danger py-3">' +
                    '❌ ไม่สามารถโหลดประวัติได้ (Status: ' + xhr.status + ')' +
                    '</td></tr>'
                );
            }
        });
    }

    function restoreDatabase() {
        const fileInput = $('#restoreFile')[0];
        if (!fileInput.files[0]) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกไฟล์',
                text: 'เลือกไฟล์ .sql ที่ได้จากการสำรองข้อมูล'
            });
            return;
        }

        const file = fileInput.files[0];

        // ตรวจสอบนามสกุล
        if (!file.name.endsWith('.sql')) {
            Swal.fire({
                icon: 'warning',
                title: 'ไฟล์ไม่ถูกต้อง',
                text: 'กรุณาเลือกไฟล์ .sql เท่านั้น'
            });
            return;
        }

        Swal.fire({
            title: '⚠️ ยืนยันการกู้คืนข้อมูล?',
            html: `
            <div class="text-start">
                <p class="text-danger"><strong>คำเตือน:</strong></p>
                <ul>
                    <li>ข้อมูลปัจจุบันจะถูกแทนที่ด้วยข้อมูลจากไฟล์สำรอง</li>
                    <li>การกระทำนี้ไม่สามารถยกเลิกได้</li>
                    <li>ระบบจะสำรองข้อมูลปัจจุบันก่อนกู้คืน</li>
                </ul>
                <p>ไฟล์: <strong>${file.name}</strong></p>
                <p>ขนาด: <strong>${(file.size / 1024).toFixed(2)} KB</strong></p>
            </div>
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fas fa-upload"></i> กู้คืนข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('file', file);

                Swal.fire({
                    title: 'กำลังกู้คืนข้อมูล...',
                    html: '<div class="progress" style="height: 20px;"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div></div><p class="mt-2">กรุณารอสักครู่ ห้ามปิดหน้านี้</p>',
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                $.ajax({
                    url: '../api/settings/restore.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    timeout: 300000, // 5 นาที
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'กู้คืนสำเร็จ!',
                                html: `
                                <div class="text-start">
                                    <p>✅ สำเร็จ: ${response.data.success_count} คำสั่ง</p>
                                    ${response.data.error_count > 0 ? '<p class="text-warning">⚠️ มีข้อผิดพลาด: ' + response.data.error_count + ' คำสั่ง</p>' : ''}
                                    <p>📁 สำรองก่อนกู้คืน: ${response.data.backup_before}</p>
                                </div>
                            `,
                                confirmButtonText: 'รีเฟรชหน้า',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ผิดพลาด!',
                                text: response.message || 'ไม่สามารถกู้คืนได้'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Restore error:', status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด!',
                            text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ (Status: ' + xhr.status + ')'
                        });
                    }
                });
            }
        });
    }

    // ============ SYSTEM INFO ============
    function loadSystemInfo() {
        $.ajax({
            url: '../api/settings/system_info.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#mysqlVersion').text(response.mysql_version || 'N/A');
                }
            }
        });
    }

    function loadDatabaseStats() {
        $.ajax({
            url: '../api/settings/database_stats.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    let html = '';
                    response.data.forEach(table => {
                        html += `
                        <tr>
                            <td>${table.name}</td>
                            <td>${numberFormat(table.rows)}</td>
                            <td>${table.size || '-'}</td>
                        </tr>`;
                    });
                    $('#databaseStats').html(html);
                }
            }
        });
    }

    // ============ HELPERS ============
    function showToast(message, icon = 'success') {
        Swal.fire({
            icon: icon,
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function numberFormat(number) {
        if (!number && number !== 0) return '0';
        return new Intl.NumberFormat('th-TH').format(number);
    }
</script>

<?php include_once '../includes/footer.php'; ?>