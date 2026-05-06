<?php
// includes/sidebar.php
ob_start();
if (!isset($_SESSION)) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// ✅ ดึง case_id จาก URL หลายรูปแบบ
$current_case_id = '';

// จาก case_detail.php?id=X
if ($current_page == 'case_detail.php' && isset($_GET['id'])) {
    $current_case_id = intval($_GET['id']);
}
// จากหน้าใดๆ ที่มี case_id=X
elseif (isset($_GET['case_id'])) {
    $current_case_id = intval($_GET['case_id']);
}
// จาก Session (ถ้าเคยเปิด case_detail ไว้)
elseif (isset($_SESSION['last_case_id'])) {
    $current_case_id = intval($_SESSION['last_case_id']);
}

// ✅ เก็บ case_id ใน session (เพื่อใช้งานข้ามหน้า)
if ($current_case_id) {
    $_SESSION['last_case_id'] = $current_case_id;
}

/**
 * สร้าง URL พร้อม case_id
 */
function url($page, $case_id = null)
{
    global $current_case_id;

    if ($case_id === null) {
        $case_id = $current_case_id;
    }

    if ($case_id) {
        return $page . '?case_id=' . $case_id;
    }

    return 'javascript:void(0)';
}

/**
 * สร้าง alert onclick เมื่อไม่มี case_id
 */
function noCaseAlert()
{
    global $current_case_id;
    return !$current_case_id ? ' onclick="alert(\'กรุณาเปิดเคสจากหน้ารายการเคสก่อน\'); return false;"' : '';
}

function isActive($page)
{
    global $current_page;
    return $current_page == $page ? 'active' : '';
}

function isActiveGroup($pages)
{
    global $current_page;
    return in_array($current_page, $pages) ? 'active' : '';
}
?>

<!-- ==================== SIDEBAR HTML ==================== -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="../assets/img/logo.png" width="100" alt="Logo" class="sidebar-logo">
        <h4>CRM Condo System</h4>
        <small class="text-white-50">v1.0</small>
    </div>

    <nav class="sidebar-nav">

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link <?php echo isActive('dashboard.php'); ?>" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>

        <!-- ✅ เพิ่มเมนู Executive Dashboard (เฉพาะ admin) -->
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo isActive('executive_dashboard.php'); ?>" 
            href="executive_dashboard.php">
                <i class="fas fa-chart-bar"></i>
                <span class="nav-text">Executive Dashboard</span>
                <small class="text-white-50 ms-auto"></small>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <?php endif; ?>

        <!-- Customer -->
        <?php if (in_array($_SESSION['user_role'], ['admin_page', 'admin'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('customers.php'); ?>" href="customers.php">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">ข้อมูลลูกค้า</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Cases -->
        <?php if (in_array($_SESSION['user_role'], ['admin_page', 'admin', 'support'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('cases.php'); ?>" href="cases.php">
                    <i class="fas fa-folder-open"></i>
                    <span class="nav-text">เคสทั้งหมด</span>
                    <span class="badge bg-warning ms-auto" id="menuCaseCount">0</span>
                </a>
            </li>
        <div class="sidebar-divider"></div>
        <?php endif; ?>


        <!-- ==================== เมนูที่มี case_id ==================== -->

        <!-- Case Detail -->
        <?php if (in_array($_SESSION['user_role'], ['admin_page', 'admin', 'support'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('case_detail.php'); ?>"
                    href="<?php echo url('case_detail.php'); ?>"
                    <?php echo noCaseAlert(); ?>>
                    <i class="fas fa-search"></i>
                    <span class="nav-text">รายละเอียดเคส</span>
                    <small class="text-white-50 ms-auto"><?php echo $current_case_id ? '#' . $current_case_id : 'เลือกเคส'; ?></small>
                </a>
            </li>
        <?php endif; ?>

        <!-- Follow -->
        <?php if (in_array($_SESSION['user_role'], ['admin_page', 'admin'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('follow.php'); ?>"
                    href="<?php echo url('follow.php'); ?>"
                    <?php echo noCaseAlert(); ?>>
                    <i class="fas fa-phone-alt"></i>
                    <span class="nav-text">ติดตามลูกค้า</span>
                </a>
            </li>

        <div class="sidebar-divider"></div>
        <?php endif; ?>

        <!-- KPI -->
        <?php if (in_array($_SESSION['user_role'], ['kpi', 'admin'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('kpi_check.php'); ?>" href="kpi_check.php">
                    <i class="fas fa-check-circle"></i>
                    <span class="nav-text">ตรวจ KPI</span>
                    <span class="badge bg-danger ms-auto" id="menuKpiCount">0</span>
                </a>
            </li>

        <div class="sidebar-divider"></div>
        <?php endif; ?>

        <!-- ==================== SUPPORT MODULES ==================== -->
        <?php if (in_array($_SESSION['user_role'], ['support', 'admin'])): ?>
            <li class="sidebar-heading"><span>📋 ฝ่าย Support</span></li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('pre_approve.php'); ?>"
                    href="<?php echo url('pre_approve.php'); ?>"
                    <?php echo noCaseAlert(); ?>>
                    <i class="fas fa-clipboard-check"></i>
                    <span class="nav-text">Pre-Approve</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('documents.php'); ?>"
                    href="<?php echo url('documents.php'); ?>"
                    <?php echo noCaseAlert(); ?>>
                    <i class="fas fa-file-alt"></i>
                    <span class="nav-text">เอกสาร</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('bank_submit.php'); ?>"
                    href="<?php echo url('bank_submit.php'); ?>"
                    <?php echo noCaseAlert(); ?>>
                    <i class="fas fa-university"></i>
                    <span class="nav-text">ส่งธนาคาร</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('approval.php'); ?>"
                    href="<?php echo url('approval.php'); ?>"
                    <?php echo noCaseAlert(); ?>>
                    <i class="fas fa-check-double"></i>
                    <span class="nav-text">ผลอนุมัติ</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('debt.php'); ?>"
                    href="<?php echo url('debt.php'); ?>"
                    <?php echo noCaseAlert(); ?>>
                    <i class="fas fa-money-bill-wave"></i>
                    <span class="nav-text">ปิดหนี้</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('mortgage.php'); ?>"
                    href="<?php echo url('mortgage.php'); ?>"
                    <?php echo noCaseAlert(); ?>>
                    <i class="fas fa-home"></i>
                    <span class="nav-text">จำนอง</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('inspection.php'); ?>"
                    href="<?php echo url('inspection.php'); ?>"
                    <?php echo noCaseAlert(); ?>>
                    <i class="fas fa-search"></i>
                    <span class="nav-text">ตรวจห้อง</span>
                </a>
            </li>

        <div class="sidebar-divider"></div>
        <?php endif; ?>

        <!-- ==================== ADMIN MODULES ==================== -->
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
            <li class="sidebar-heading"><span>⚙️ ผู้ดูแลระบบ</span></li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('users.php'); ?>" href="users.php">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-text">จัดการผู้ใช้</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('master_data.php'); ?>" href="master_data.php">
                    <i class="fas fa-database"></i>
                    <span class="nav-text">ข้อมูลหลัก</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('projects_manage.php'); ?>" href="projects_manage.php">
                    <i class="fas fa-city"></i>
                    <span class="nav-text">จัดการโครงการ</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('settings.php'); ?>" href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span class="nav-text">ตั้งค่าระบบ</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo isActive('logs.php'); ?>" href="logs.php">
                    <i class="fas fa-history"></i>
                    <span class="nav-text">บันทึกระบบ</span>
                </a>
            </li>

        <div class="sidebar-divider"></div>

        <?php endif; ?>

        <!-- ==================== USER MENU ==================== -->
        <li class="sidebar-heading"><span>👤 ผู้ใช้งาน</span></li>

        <li class="nav-item">
            <a class="nav-link <?php echo isActive('profile.php'); ?>" href="profile.php">
                <i class="fas fa-user-circle"></i>
                <span class="nav-text">ข้อมูลส่วนตัว</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo isActive('change_password.php'); ?>" href="change_password.php">
                <i class="fas fa-lock"></i>
                <span class="nav-text">เปลี่ยนรหัสผ่าน</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#" onclick="showHelp()">
                <i class="fas fa-question-circle"></i>
                <span class="nav-text">ช่วยเหลือ</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-text">ออกจากระบบ</span>
            </a>
        </li>

    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar"><?php echo mb_substr($user_name, 0, 1, 'UTF-8'); ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="user-role">
                    <?php
                    $roles = ['admin' => '👑 Admin', 'admin_page' => '📄 Admin Page', 'kpi' => '✅ KPI', 'support' => '🔧 Support'];
                    echo $roles[$_SESSION['user_role']] ?? $_SESSION['user_role'];
                    ?>
                </div>
            </div>
            <div class="dropdown dropup">
                <button class="btn btn-link text-white p-0" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle text-primary"></i> ข้อมูลส่วนตัว</a></li>
                    <li><a class="dropdown-item" href="change_password.php"><i class="fas fa-key text-warning"></i> เปลี่ยนรหัสผ่าน</a></li>
                    <?php if ($_SESSION['user_role'] == 'admin'): ?>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog text-secondary"></i> ตั้งค่าระบบ</a></li>
                    <?php endif; ?>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/sidebar.js"></script>