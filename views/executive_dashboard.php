<?php
// views/executive_dashboard.php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../includes/header.php';
include_once '../includes/sidebar.php';
include_once '../includes/auth_check.php';

// เฉพาะ admin
if(!checkRole('admin')) {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="main-content">
    <div class="container-fluid">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">📊 Executive Dashboard</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Executive</li>
                    </ol>
                </nav>
            </div>
            <div>
                <!-- Filter -->
                <select class="form-select form-select-sm d-inline-block" style="width: auto;" id="filterMonth" onchange="loadAllData()">
                    <option value="">ทุกเดือน</option>
                </select>
                <select class="form-select form-select-sm d-inline-block" style="width: auto;" id="filterYear" onchange="loadAllData()">
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                </select>
                <button class="btn btn-outline-success btn-sm" onclick="exportReport()">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>

        <!-- KPI Top Row -->
        <div class="row mb-3">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">เคสทั้งหมด</div>
                                <div class="h3 mb-0 font-weight-bold" id="kpiTotalCases">0</div>
                                <small class="text-muted"><span id="kpiTotalGrowth" class="text-success">↑ 0%</span> vs เดือนก่อน</small>
                            </div>
                            <div class="col-auto"><i class="fas fa-folder fa-3x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">อนุมัติ</div>
                                <div class="h3 mb-0 font-weight-bold" id="kpiApproved">0</div>
                                <small class="text-muted">อัตรา: <span id="kpiApprovalRate">0%</span></small>
                            </div>
                            <div class="col-auto"><i class="fas fa-check-circle fa-3x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">มูลค่ารวม</div>
                                <div class="h3 mb-0 font-weight-bold" id="kpiTotalValue">0</div>
                                <small class="text-muted">วงเงินอนุมัติรวม</small>
                            </div>
                            <div class="col-auto"><i class="fas fa-money-bill-wave fa-3x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Conversion Rate</div>
                                <div class="h3 mb-0 font-weight-bold" id="kpiConversion">0%</div>
                                <small class="text-muted">ลูกค้า → อนุมัติ</small>
                            </div>
                            <div class="col-auto"><i class="fas fa-chart-line fa-3x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row mb-3">
            <div class="col-xl-8 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header"><h6 class="mb-0">📈 แนวโน้มเคส vs อนุมัติ รายเดือน</h6></div>
                    <div class="card-body">
                        <div style="height: 350px;"><canvas id="trendChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header"><h6 class="mb-0">🎯 KPI ผ่าน/ไม่ผ่าน</h6></div>
                    <div class="card-body">
                        <div style="height: 350px;"><canvas id="kpiPieChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row mb-3">
            <div class="col-xl-6 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header"><h6 class="mb-0">🏆 Top 5 โครงการ (ตามจำนวนเคส)</h6></div>
                    <div class="card-body">
                        <div style="height: 300px;"><canvas id="projectChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header"><h6 class="mb-0">👥 Top 5 พนักงาน (ตามยอดอนุมัติ)</h6></div>
                    <div class="card-body">
                        <table class="table table-sm" id="topStaffTable">
                            <thead><tr><th>#</th><th>พนักงาน</th><th>เคส</th><th>อนุมัติ</th><th>มูลค่า</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 3 -->
        <div class="row mb-3">
            <div class="col-xl-4 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header"><h6 class="mb-0">📊 ช่องทางที่ลูกค้ามา</h6></div>
                    <div class="card-body">
                        <div style="height: 300px;"><canvas id="channelChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header"><h6 class="mb-0">📋 สรุปสถานะเคส</h6></div>
                    <div class="card-body" id="caseSummary"></div>
                </div>
            </div>
            <div class="col-xl-4 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header"><h6 class="mb-0">💡 สรุปยอดขาย</h6></div>
                    <div class="card-body text-center">
                        <h2 class="text-success" id="totalApprovedValue">฿0</h2>
                        <p class="text-muted">ยอดอนุมัติรวม</p>
                        <hr>
                        <div class="row">
                            <div class="col-6"><h5 id="avgPerCase">฿0</h5><small>เฉลี่ย/เคส</small></div>
                            <div class="col-6"><h5 id="avgPerStaff">฿0</h5><small>เฉลี่ย/พนักงาน</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// ============ EXECUTIVE DASHBOARD JS ============
let trendChart, kpiPieChart, projectChart, channelChart;

$(document).ready(function(){
    // Set filter options
    for(let m=1; m<=12; m++) $('#filterMonth').append(`<option value="${m}">${getMonthName(m)}</option>`);
    
    loadAllData();
});

function getMonthName(m) {
    return ['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'][m];
}

function loadAllData() {
    let month = $('#filterMonth').val();
    let year = $('#filterYear').val();
    let params = {};
    if(month) params.month = month;
    if(year) params.year = year;
    
    // Load KPI
    $.get('../api/dashboard/executive_kpi.php', params, function(res){
        if(res.success) {
            $('#kpiTotalCases').text(res.total_cases||0);
            $('#kpiApproved').text(res.approved_cases||0);
            $('#kpiTotalValue').text(numberFormat(res.total_value) + ' บาท');
            $('#kpiApprovalRate').text((res.approval_rate||0) + '%');
            $('#kpiConversion').text((res.conversion_rate||0) + '%');
            $('#kpiTotalGrowth').text((res.growth>=0?'↑':'↓') + ' ' + Math.abs(res.growth||0) + '%');
        }
    }, 'json');
    
    // Load Trend Chart
    $.get('../api/dashboard/executive_trend.php', params, function(res){
        if(res.success) renderTrendChart(res.data);
    }, 'json');
    
    // Load KPI Pie
    $.get('../api/dashboard/chart_kpi.php', function(res){
        if(res.success) renderKpiPie(res.data);
    }, 'json');
    
    // Load Project Chart
    $.get('../api/dashboard/executive_projects.php', params, function(res){
        if(res.success) renderProjectChart(res.data);
    }, 'json');
    
    // Load Channel Chart
    $.get('../api/dashboard/executive_channels.php', params, function(res){
        if(res.success) renderChannelChart(res.data);
    }, 'json');
    
    // Load Top Staff
    $.get('../api/dashboard/executive_staff.php', params, function(res){
        if(res.success) renderTopStaff(res.data);
    }, 'json');
    
    // Load Summary
    $.get('../api/dashboard/executive_summary.php', params, function(res){
        if(res.success) {
            $('#totalApprovedValue').text('฿' + numberFormat(res.total_value));
            $('#avgPerCase').text('฿' + numberFormat(res.avg_per_case));
            $('#avgPerStaff').text('฿' + numberFormat(res.avg_per_staff));
            renderCaseSummary(res.status_summary);
        }
    }, 'json');
}

function renderTrendChart(data) {
    if(trendChart) trendChart.destroy();
    let ctx = document.getElementById('trendChart').getContext('2d');
    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                { label: 'สร้างเคส', data: data.cases, borderColor: 'rgba(54,162,235,1)', backgroundColor:'rgba(54,162,235,0.1)', tension:0.3, fill:true },
                { label: 'อนุมัติ', data: data.approved, borderColor: 'rgba(75,192,192,1)', backgroundColor:'rgba(75,192,192,0.1)', tension:0.3, fill:true }
            ]
        },
        options: { responsive:true, maintainAspectRatio:false }
    });
}

function renderKpiPie(data) {
    if(kpiPieChart) kpiPieChart.destroy();
    let ctx = document.getElementById('kpiPieChart').getContext('2d');
    kpiPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['ผ่าน','ไม่ผ่าน','รอตรวจ'],
            datasets: [{ data: [data.pass, data.fail, data.pending], backgroundColor: ['rgba(75,192,192,0.7)','rgba(255,99,132,0.7)','rgba(255,206,86,0.7)'] }]
        },
        options: { responsive:true, maintainAspectRatio:false }
    });
}

function renderProjectChart(data) {
    if(projectChart) projectChart.destroy();
    let ctx = document.getElementById('projectChart').getContext('2d');
    projectChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{ label:'เคส', data:data.counts, backgroundColor:'rgba(102,126,234,0.7)' }]
        },
        options: { indexAxis:'y', responsive:true, maintainAspectRatio:false }
    });
}

function renderChannelChart(data) {
    if(channelChart) channelChart.destroy();
    let ctx = document.getElementById('channelChart').getContext('2d');
    channelChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.labels,
            datasets: [{ data: data.counts, backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b'] }]
        },
        options: { responsive:true, maintainAspectRatio:false }
    });
}

function renderTopStaff(data) {
    let html='';
    data.forEach(function(s,i){
        let medals=['🥇','🥈','🥉','4','5'];
        html+=`<tr><td>${medals[i]||i+1}</td><td>${s.name}</td><td>${s.cases}</td><td>${s.approved}</td><td>${numberFormat(s.value)}</td></tr>`;
    });
    $('#topStaffTable tbody').html(html||'<tr><td colspan="5" class="text-center">-</td></tr>');
}

function renderCaseSummary(data) {
    let html='<table class="table table-sm">';
    for(let s in data) {
        html+=`<tr><td>${s}</td><td class="text-end fw-bold">${data[s]}</td></tr>`;
    }
    html+='</table>';
    $('#caseSummary').html(html);
}

function numberFormat(n){ return n?new Intl.NumberFormat('th-TH').format(n):'0'; }
function exportReport() {
    let month = $('#filterMonth').val();
    let year = $('#filterYear').val();
    let url = '../api/dashboard/export_pdf.php?';
    
    if (month) url += 'month=' + month + '&';
    if (year) url += 'year=' + year;
    
    // เปิดหน้าใหม่
    window.open(url, '_blank');
}
</script>

<?php include_once '../includes/footer.php'; ?>