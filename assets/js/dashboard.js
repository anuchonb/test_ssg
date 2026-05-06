// assets/js/dashboard.js

let caseStatusChart = null;
let kpiChart = null;
let monthlyTrendChart = null;

$(document).ready(function() {
    // Update time
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
    
    // Set default year to current
    $('#chartYear').val(new Date().getFullYear());
    
    // Load dashboard data with slight delay for smooth loading
    setTimeout(() => {
        loadDashboardStats();
        loadCaseChart();
        loadKpiChart();
        loadRecentCases();
        loadRecentActivities();
        loadTopPerformers();
        loadMonthlyTrend();
    }, 300);
    
    // Auto refresh every 5 minutes
    setInterval(refreshDashboard, 300000);
    
    // Handle chart year change
    $('#chartYear').on('change', function() {
        loadCaseChart();
    });
});

function updateCurrentTime() {
    const now = new Date();
    const options = { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        hour12: false 
    };
    $('#currentTime').text('🕐 ' + now.toLocaleTimeString('th-TH', options));
}

function refreshDashboard() {
    loadDashboardStats();
    loadRecentCases();
    loadRecentActivities();
}

function loadDashboardStats() {
    $.ajax({
        url: '../api/dashboard/stats.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Stats Response:', response); // Debug
            
            if(response.success && response.data) {
                const stats = response.data;
                
                // Update counters with animation
                animateCounter('totalCases', stats.total_cases);
                animateCounter('approvedCases', stats.approved_cases);
                animateCounter('followingCases', stats.following_cases);
                animateCounter('cancelledCases', stats.cancelled_cases);
                animateCounter('totalCustomers', stats.total_customers);
                animateCounter('bankSubmitted', stats.bank_submitted);
                animateCounter('kpiPass', stats.kpi_pass);
                animateCounter('pendingDebt', stats.pending_debt);
                
                // Update text values
                $('#todayCases').text('+' + stats.today_cases);
                $('#approvalRate').text(stats.approval_rate + '%');
                $('#followToday').text(stats.follow_today);
                $('#kpiFail').text(stats.kpi_fail);
                
                // Color coding for approval rate
                updateApprovalRateColor(stats.approval_rate);
            } else {
                console.error('Invalid stats response:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Stats API Error:', error);
        }
    });
}

function animateCounter(elementId, targetValue) {
    const $element = $('#' + elementId);
    if(!$element.length) return;
    
    const startValue = parseInt($element.text()) || 0;
    const endValue = parseInt(targetValue) || 0;
    
    if(startValue === endValue) {
        $element.text(endValue);
        return;
    }
    
    const duration = 1000; // 1 second
    const steps = 30;
    const increment = (endValue - startValue) / steps;
    let currentStep = 0;
    
    const timer = setInterval(() => {
        currentStep++;
        const currentValue = Math.round(startValue + (increment * currentStep));
        
        if(currentStep >= steps || currentValue === endValue) {
            $element.text(endValue);
            clearInterval(timer);
        } else {
            $element.text(currentValue);
        }
    }, duration / steps);
}

function updateApprovalRateColor(rate) {
    const $element = $('#approvalRate');
    $element.removeClass('text-success text-warning text-danger');
    
    if(rate >= 70) {
        $element.addClass('text-success');
    } else if(rate >= 40) {
        $element.addClass('text-warning');
    } else {
        $element.addClass('text-danger');
    }
}

function loadCaseChart() {
    const year = $('#chartYear').val() || new Date().getFullYear();
    
    $.ajax({
        url: '../api/dashboard/chart_cases.php',
        type: 'GET',
        data: { year: year },
        dataType: 'json',
        success: function(response) {
            console.log('Case Chart Response:', response); // Debug
            
            if(response.success && response.data) {
                const data = response.data;
                
                // Destroy existing chart
                if(caseStatusChart) {
                    caseStatusChart.destroy();
                    caseStatusChart = null;
                }
                
                const ctx = document.getElementById('caseStatusChart');
                if(!ctx) {
                    console.error('Canvas element not found: caseStatusChart');
                    return;
                }
                
                caseStatusChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'ส่งเคส',
                                data: data.submitted,
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            },
                            {
                                label: 'อนุมัติ',
                                data: data.approved,
                                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            },
                            {
                                label: 'ยกเลิก/ไม่สนใจ',
                                data: data.cancelled,
                                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15
                                }
                            },
                            title: {
                                display: true,
                                text: 'สถิติเคสรายเดือน ปี ' + year,
                                font: {
                                    size: 16
                                },
                                padding: {
                                    bottom: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y + ' เคส';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        return value + ' เคส';
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            } else {
                console.error('Invalid chart data:', response);
                showChartError('caseStatusChart');
            }
        },
        error: function(xhr, status, error) {
            console.error('Chart API Error:', error);
            showChartError('caseStatusChart');
        }
    });
}

function loadKpiChart() {
    $.ajax({
        url: '../api/dashboard/chart_kpi.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('KPI Chart Response:', response); // Debug
            
            if(response.success && response.data) {
                const data = response.data;
                
                // Destroy existing chart
                if(kpiChart) {
                    kpiChart.destroy();
                    kpiChart = null;
                }
                
                const ctx = document.getElementById('kpiChart');
                if(!ctx) {
                    console.error('Canvas element not found: kpiChart');
                    return;
                }
                
                kpiChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            `ผ่าน (${data.pass} ราย)`,
                            `ไม่ผ่าน (${data.fail} ราย)`, 
                            `รอตรวจ (${data.pending} ราย)`
                        ],
                        datasets: [{
                            data: [data.pass, data.fail, data.pending],
                            backgroundColor: data.colors,
                            borderColor: data.borderColors,
                            borderWidth: 2,
                            hoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12
                                    },
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        return data.labels.map((label, i) => ({
                                            text: label,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: data.datasets[0].borderColor[i],
                                            lineWidth: 2,
                                            hidden: false,
                                            index: i
                                        }));
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: 'KPI 30 วันล่าสุด',
                                font: {
                                    size: 14
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const value = context.parsed;
                                        const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return context.label.split(' (')[0] + ': ' + value + ' ราย (' + percent + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.error('Invalid KPI data:', response);
                showChartError('kpiChart');
            }
        },
        error: function(xhr, status, error) {
            console.error('KPI Chart API Error:', error);
            showChartError('kpiChart');
        }
    });
}

function loadMonthlyTrend() {
    $.ajax({
        url: '../api/dashboard/chart_cases.php',
        type: 'GET',
        data: { year: new Date().getFullYear() },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data) {
                // Additional trend processing can be done here
                // This can be used for a line chart showing trends
            }
        }
    });
}

function loadRecentCases() {
    $.ajax({
        url: '../api/dashboard/recent_cases.php',
        type: 'GET',
        data: { limit: 5 },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data && response.data.length > 0) {
                let html = '';
                response.data.forEach(c => {
                    const statusClass = getStatusClass(c.status);
                    html += `
                        <tr>
                            <td>
                                <a href="case_detail.php?case_id=${c.id}" class="text-decoration-none fw-bold">
                                    #${c.id}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                         style="width: 28px; height: 28px; font-size: 11px;">
                                        ${c.customer_name ? c.customer_name.charAt(0) : '?'}
                                    </div>
                                    ${c.customer_name || 'ไม่ระบุ'}
                                </div>
                            </td>
                            <td><span class="badge bg-${statusClass}">${c.status}</span></td>
                            <td><small class="text-muted">${formatDateThai(c.created_at)}</small></td>
                        </tr>`;
                });
                $('#recentCases').html(html);
            } else {
                $('#recentCases').html(`
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <i class="fas fa-inbox text-muted mb-2"></i>
                            <p class="text-muted mb-0">ไม่มีเคสล่าสุด</p>
                        </td>
                    </tr>
                `);
            }
        },
        error: function() {
            $('#recentCases').html('<tr><td colspan="4" class="text-center text-danger">โหลดข้อมูลไม่สำเร็จ</td></tr>');
        }
    });
}

function loadRecentActivities() {
    $.ajax({
        url: '../api/dashboard/recent_activities.php',
        type: 'GET',
        data: { limit: 10 },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data && response.data.length > 0) {
                let html = '<div class="timeline">';
                response.data.forEach((a, index) => {
                    const icon = getActivityIcon(a.action);
                    const isLast = index === response.data.length - 1;
                    html += `
                        <div class="timeline-item">
                            <div class="timeline-marker bg-${icon.color} d-flex align-items-center justify-content-center">
                                <i class="fas fa-${icon.icon} fa-xs text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <p class="mb-1">${a.action}</p>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>${a.user_name || 'System'}
                                    </small>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>${formatTimeAgo(a.created_at)}
                                    </small>
                                </div>
                            </div>
                        </div>`;
                });
                html += '</div>';
                $('#recentActivities').html(html);
            } else {
                $('#recentActivities').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">ไม่มีกิจกรรมล่าสุด</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#recentActivities').html('<p class="text-center text-danger py-4">โหลดข้อมูลไม่สำเร็จ</p>');
        }
    });
}

function loadTopPerformers() {
    $.ajax({
        url: '../api/dashboard/top_performers.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data && response.data.length > 0) {
                let html = '';
                response.data.forEach((p, index) => {
                    const medal = index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : (index + 1);
                    const closeRate = parseFloat(p.close_rate) || 0;
                    const kpiRate = parseFloat(p.kpi_pass_rate) || 0;
                    
                    html += `
                        <tr>
                            <td class="text-center" style="font-size: 20px;">${medal}</td>
                            <td>
                                <strong>${p.name}</strong>
                                <br><small class="text-muted">Admin Page</small>
                            </td>
                            <td class="text-center">${p.total_cases}</td>
                            <td class="text-center">${p.approved_cases}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                        <div class="progress-bar bg-${closeRate >= 70 ? 'success' : closeRate >= 40 ? 'warning' : 'danger'}" 
                                             style="width: ${closeRate}%"></div>
                                    </div>
                                    <small>${closeRate}%</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-${kpiRate >= 80 ? 'success' : 'warning'}">
                                    ${kpiRate}%
                                </span>
                            </td>
                        </tr>`;
                });
                $('#topPerformers').html(html);
            } else {
                $('#topPerformers').html(`
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-user-friends text-muted mb-2"></i>
                            <p class="text-muted mb-0">ไม่มีข้อมูลพนักงาน</p>
                        </td>
                    </tr>
                `);
            }
        },
        error: function() {
            $('#topPerformers').html('<tr><td colspan="6" class="text-center text-danger">โหลดข้อมูลไม่สำเร็จ</td></tr>');
        }
    });
}

function showChartError(chartId) {
    const ctx = document.getElementById(chartId);
    if(!ctx) return;
    
    const parent = ctx.parentElement;
    parent.innerHTML = `
        <div class="d-flex align-items-center justify-content-center" style="height: 300px;">
            <div class="text-center text-muted">
                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                <p>ไม่สามารถโหลดกราฟได้</p>
                <button class="btn btn-sm btn-outline-primary" onclick="loadCaseChart()">
                    <i class="fas fa-redo"></i> ลองใหม่
                </button>
            </div>
        </div>`;
}

// Helper Functions
function getStatusClass(status) {
    const classes = {
        'ส่งเคส': 'primary',
        'กำลังติดตาม': 'warning',
        'อนุมัติ': 'success',
        'ยกเลิก': 'danger',
        'ไม่สนใจ': 'secondary',
        'วงเงินไม่ถึง': 'info'
    };
    return classes[status] || 'secondary';
}

function getActivityIcon(action) {
    if(!action) return { icon: 'circle', color: 'secondary' };
    
    if(action.includes('Case') || action.includes('เคส')) return { icon: 'folder-plus', color: 'primary' };
    if(action.includes('Follow') || action.includes('ติดตาม')) return { icon: 'phone', color: 'info' };
    if(action.includes('KPI')) return { icon: 'check-circle', color: 'success' };
    if(action.includes('Pre-Approve')) return { icon: 'clipboard-check', color: 'warning' };
    if(action.includes('Bank') || action.includes('ธนาคาร')) return { icon: 'university', color: 'secondary' };
    if(action.includes('Login') || action.includes('เข้าสู่ระบบ')) return { icon: 'sign-in-alt', color: 'success' };
    if(action.includes('Logout') || action.includes('ออกจากระบบ')) return { icon: 'sign-out-alt', color: 'danger' };
    if(action.includes('Document') || action.includes('เอกสาร')) return { icon: 'file-alt', color: 'info' };
    if(action.includes('Status')) return { icon: 'edit', color: 'warning' };
    
    return { icon: 'circle', color: 'secondary' };
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

function formatTimeAgo(dateString) {
    if(!dateString) return '-';
    const date = new Date(dateString);
    const now = new Date();
    const diffSeconds = Math.floor((now - date) / 1000);
    
    if(diffSeconds < 0) return 'ในอนาคต';
    if(diffSeconds < 60) return 'เมื่อสักครู่';
    if(diffSeconds < 3600) return Math.floor(diffSeconds / 60) + ' นาทีที่แล้ว';
    if(diffSeconds < 86400) return Math.floor(diffSeconds / 3600) + ' ชั่วโมงที่แล้ว';
    if(diffSeconds < 2592000) return Math.floor(diffSeconds / 86400) + ' วันที่แล้ว';
    if(diffSeconds < 31536000) return Math.floor(diffSeconds / 2592000) + ' เดือนที่แล้ว';
    
    return formatDateThai(dateString);
}