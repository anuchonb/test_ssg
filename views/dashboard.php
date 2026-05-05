<?php
// views/dashboard.php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../includes/header.php';
include_once '../includes/sidebar.php';
include_once '../includes/auth_check.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1">👋 สวัสดี, <?php echo $_SESSION['user_name']; ?></h3>
                                <p class="mb-0">
                                    <i class="fas fa-calendar-alt"></i> 
                                    <?php echo date('d F Y', strtotime('+543 years')); ?> | 
                                    <span id="currentTime"></span>
                                </p>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark p-2">
                                    <i class="fas fa-user-tag"></i>
                                    Role: <?php echo ucfirst($_SESSION['user_role']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    เคสทั้งหมด
                                </div>
                                <div class="h3 mb-0 font-weight-bold" id="totalCases">0</div>
                                <div class="small text-muted mt-2">
                                    <span id="todayCases">+0</span> วันนี้
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-folder fa-3x text-primary opacity-25"></i>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    อนุมัติแล้ว
                                </div>
                                <div class="h3 mb-0 font-weight-bold" id="approvedCases">0</div>
                                <div class="small text-muted mt-2">
                                    อัตราการอนุมัติ <span id="approvalRate">0%</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-3x text-success opacity-25"></i>
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
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    กำลังติดตาม
                                </div>
                                <div class="h3 mb-0 font-weight-bold" id="followingCases">0</div>
                                <div class="small text-muted mt-2">
                                    ต้องติดตามวันนี้ <span id="followToday">0</span> ราย
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-phone fa-3x text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-danger shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    ยกเลิก/ไม่สนใจ
                                </div>
                                <div class="h3 mb-0 font-weight-bold" id="cancelledCases">0</div>
                                <div class="small text-muted mt-2">
                                    KPI ไม่ผ่าน <span id="kpiFail">0</span> ราย
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-3x text-danger opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row Stats -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    ลูกค้าทั้งหมด
                                </div>
                                <div class="h3 mb-0 font-weight-bold" id="totalCustomers">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-3x text-info opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-secondary shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    ส่งธนาคารแล้ว
                                </div>
                                <div class="h3 mb-0 font-weight-bold" id="bankSubmitted">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-university fa-3x text-secondary opacity-25"></i>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    KPI ผ่าน
                                </div>
                                <div class="h3 mb-0 font-weight-bold" id="kpiPass">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-star fa-3x text-success opacity-25"></i>
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
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    รอปิดหนี้
                                </div>
                                <div class="h3 mb-0 font-weight-bold" id="pendingDebt">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-3x text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Case Status Chart -->
            <div class="col-xl-8 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📊 กราฟแสดงสถานะเคสรายเดือน</h5>
                        <select class="form-select form-select-sm" style="width: auto;" id="chartYear" onchange="loadCaseChart()">
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <!-- ใส่ container จำกัดความสูง -->
                        <div style="position: relative; height: 350px; max-height: 400px;">
                            <canvas id="caseStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Chart -->
            <div class="col-xl-4 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <h5 class="mb-0">🎯 KPI ภาพรวม</h5>
                    </div>
                    <div class="card-body">
                        <!-- ใส่ container จำกัดความสูง -->
                        <div style="position: relative; height: 350px; max-height: 400px;">
                            <canvas id="kpiChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Cases & Activity -->
        <div class="row">
            <!-- Recent Cases -->
            <div class="col-xl-6 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📋 เคสล่าสุด</h5>
                        <a href="cases.php" class="btn btn-sm btn-primary">ดูทั้งหมด</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Case ID</th>
                                        <th>ลูกค้า</th>
                                        <th>สถานะ</th>
                                        <th>วันที่</th>
                                    </tr>
                                </thead>
                                <tbody id="recentCases"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="col-xl-6 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <h5 class="mb-0">🔔 กิจกรรมล่าสุด</h5>
                    </div>
                    <div class="card-body">
                        <div id="recentActivities" style="max-height: 400px; overflow-y: auto;">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">🏆 Top Performers</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>อันดับ</th>
                                        <th>พนักงาน</th>
                                        <th>เคสทั้งหมด</th>
                                        <th>อนุมัติ</th>
                                        <th>อัตราการปิด</th>
                                        <th>KPI ผ่าน</th>
                                    </tr>
                                </thead>
                                <tbody id="topPerformers"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="../assets/js/dashboard.js"></script>

<?php include_once '../includes/footer.php'; ?>