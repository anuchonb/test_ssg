<?php
// views/cases.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../includes/header.php';
include_once '../includes/sidebar.php';
include_once '../includes/auth_check.php';
include_once '../includes/functions.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">📋 เคสทั้งหมด</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">เคสทั้งหมด</li>
                    </ol>
                </nav>
            </div>
            <a href="customers.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> สร้างเคสใหม่
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-primary shadow h-100 cursor-pointer" onclick="filterCases('')" style="cursor: pointer;">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ทั้งหมด</div>
                                <div class="h5 mb-0 font-weight-bold" id="statAll">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-folder fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-info shadow h-100 cursor-pointer" onclick="filterCases('ส่งเคส')" style="cursor: pointer;">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">ส่งเคส</div>
                                <div class="h5 mb-0 font-weight-bold" id="statSubmitted">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-warning shadow h-100 cursor-pointer" onclick="filterCases('กำลังติดตาม')" style="cursor: pointer;">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">กำลังติดตาม</div>
                                <div class="h5 mb-0 font-weight-bold" id="statFollowing">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-phone fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-success shadow h-100 cursor-pointer" onclick="filterCases('อนุมัติ')" style="cursor: pointer;">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">อนุมัติ</div>
                                <div class="h5 mb-0 font-weight-bold" id="statApproved">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-danger shadow h-100 cursor-pointer" onclick="filterCases('ยกเลิก')" style="cursor: pointer;">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">ยกเลิก</div>
                                <div class="h5 mb-0 font-weight-bold" id="statCancelled">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-secondary shadow h-100 cursor-pointer" onclick="filterCases('ไม่สนใจ')" style="cursor: pointer;">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">ไม่สนใจ</div>
                                <div class="h5 mb-0 font-weight-bold" id="statNotInterested">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-meh fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">ค้นหา</label>
                        <input type="text" class="form-control" id="searchInput"
                            placeholder="ชื่อ, เบอร์, Case ID..." onkeyup="debounceSearch()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">สถานะ</label>
                        <select class="form-select" id="filterStatus" onchange="loadCases()">
                            <option value="">ทั้งหมด</option>
                            <option value="ส่งเคส">ส่งเคส</option>
                            <option value="กำลังติดตาม">กำลังติดตาม</option>
                            <option value="อนุมัติ">อนุมัติ</option>
                            <option value="ยกเลิก">ยกเลิก</option>
                            <option value="ไม่สนใจ">ไม่สนใจ</option>
                            <option value="วงเงินไม่ถึง">วงเงินไม่ถึง</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">เกรดลูกค้า</label>
                        <select class="form-select" id="filterGrade" onchange="loadCases()">
                            <option value="">ทั้งหมด</option>
                            <option value="A+">A+</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ช่วงวันที่</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="filterDateFrom" onchange="loadCases()">
                            <span class="input-group-text">ถึง</span>
                            <input type="date" class="form-control" id="filterDateTo" onchange="loadCases()">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-secondary w-100" onclick="resetFilters()">
                            <i class="fas fa-redo"></i> รีเซ็ต
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cases Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">รายการเคส</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="loadCases()">
                    <i class="fas fa-sync"></i> รีเฟรช
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Case ID</th>
                                <th>ลูกค้า</th>
                                <th>เบอร์โทร</th>
                                <th>โครงการ</th>
                                <th>เกรด</th>
                                <th>สถานะ</th>
                                <th>ติดตาม</th>
                                <th>วันที่</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="casesTableBody">
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            แสดง <span id="showingCount">0</span> จาก <span id="totalCount">0</span> รายการ
                        </small>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ============ CASES JAVASCRIPT ============

    let currentPage = 1;
    let searchTimeout = null;

    $(document).ready(function() {
        // console.log('Cases page loaded');

        // โหลดข้อมูล
        loadCaseStats();
        loadCases();

        // Auto refresh ทุก 60 วิ
        // setInterval(loadCases, 60000);
    });

    function loadCaseStats() {
        $.ajax({
            url: '../api/cases/stats.php',
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                // console.log('Stats:', response);
                if (response.success) {
                    $('#statAll').text(response.all || 0);
                    $('#statSubmitted').text(response.submitted || 0);
                    $('#statFollowing').text(response.following || 0);
                    $('#statApproved').text(response.approved || 0);
                    $('#statCancelled').text(response.cancelled || 0);
                    $('#statNotInterested').text(response.not_interested || 0);
                }
            },
            error: function(xhr, status, error) {
                // console.error('Stats error:', status, error);
            }
        });
    }

    function loadCases(page) {
        page = page || 1;
        currentPage = page;

        const search = $('#searchInput').val() || '';
        const status = $('#filterStatus').val() || '';
        const grade = $('#filterGrade').val() || '';
        const dateFrom = $('#filterDateFrom').val() || '';
        const dateTo = $('#filterDateTo').val() || '';

        // Show loading
        $('#casesTableBody').html(
            '<tr><td colspan="9" class="text-center py-5">' +
            '<div class="spinner-border text-primary" role="status"></div>' +
            '<p class="mt-2">กำลังโหลดข้อมูล...</p>' +
            '</td></tr>'
        );

        $.ajax({
            url: '../api/cases/list.php',
            type: 'GET',
            data: {
                page: page,
                per_page: 20,
                search: search,
                status: status,
                grade: grade,
                date_from: dateFrom,
                date_to: dateTo
            },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Cases API Response:', response);

                if (response.success && response.data && response.data.length > 0) {
                    renderCasesTable(response.data);
                    $('#showingCount').text(response.data.length);
                    $('#totalCount').text(response.pagination ? response.pagination.total : response.data.length);

                    if (response.pagination) {
                        renderPagination(response.pagination);
                    }
                } else {
                    showEmptyTable();
                }
            },
            error: function(xhr, status, error) {
                // console.error('Load cases error:', status, error);
                // console.error('Response:', xhr.responseText);

                $('#casesTableBody').html(
                    '<tr><td colspan="9" class="text-center py-5 text-danger">' +
                    '<i class="fas fa-exclamation-triangle fa-2x mb-3"></i>' +
                    '<p>ไม่สามารถโหลดข้อมูลได้</p>' +
                    '<p class="small text-muted">Error: ' + status + '</p>' +
                    '<button class="btn btn-outline-primary btn-sm" onclick="loadCases()">' +
                    '<i class="fas fa-redo"></i> ลองใหม่</button>' +
                    '</td></tr>'
                );
            }
        });
    }

    function renderCasesTable(cases) {
        let html = '';

        cases.forEach(function(c) {
            const statusBadge = getStatusBadge(c.status);
            const gradeBadge = c.grade ? '<span class="badge bg-' + getGradeColor(c.grade) + '">' + c.grade + '</span>' : '-';
            const followText = c.follow_count > 0 ? '<span class="badge bg-info">' + c.follow_count + ' ครั้ง</span>' : '<span class="text-muted">ยังไม่ติดตาม</span>';

            html += '<tr>' +
                '<td><a href="case_detail.php?case_id=' + c.id + '" class="fw-bold">#' + c.id + '</a></td>' +
                '<td>' +
                '<strong>' + (c.customer_name || 'ไม่ระบุ') + '</strong>' +
                '<br><small class="text-muted">' + (c.customer_code || '') + '</small>' +
                '</td>' +
                '<td>' + (c.phone || '-') + '</td>' +
                '<td>' + (c.project_name || '-') + '</td>' +
                '<td>' + gradeBadge + '</td>' +
                '<td>' + statusBadge + '</td>' +
                '<td>' + followText + '</td>' +
                '<td><small>' + formatDate(c.created_at) + '</small></td>' +
                '<td>' +
                '<div class="btn-group btn-group-sm">' +
                '<a href="case_detail.php?case_id=' + c.id + '" class="btn btn-info" title="ดูรายละเอียด">' +
                '<i class="fas fa-eye"></i>' +
                '</a>' +
                '<button class="btn btn-warning" onclick="changeStatus(' + c.id + ', \'' + (c.status || '') + '\')" title="เปลี่ยนสถานะ">' +
                '<i class="fas fa-edit"></i>' +
                '</button>' +
                '</div>' +
                '</td>' +
                '</tr>';
        });

        $('#casesTableBody').html(html);
    }

    function showEmptyTable() {
        $('#casesTableBody').html(
            '<tr><td colspan="9" class="text-center py-5 text-muted">' +
            '<i class="fas fa-inbox fa-3x mb-3"></i>' +
            '<p>ไม่พบข้อมูลเคส</p>' +
            '<a href="customers.php" class="btn btn-primary btn-sm">' +
            '<i class="fas fa-plus"></i> สร้างเคสใหม่</a>' +
            '</td></tr>'
        );
        $('#showingCount').text('0');
        $('#totalCount').text('0');
        $('#pagination').html('');
    }

    function renderPagination(pagination) {
        if (!pagination || pagination.total_pages <= 1) {
            $('#pagination').html('');
            return;
        }

        let html = '';

        // Previous
        html += '<li class="page-item ' + (!pagination.has_prev ? 'disabled' : '') + '">' +
            '<a class="page-link" href="#" onclick="loadCases(' + (pagination.current_page - 1) + '); return false;">' +
            '<i class="fas fa-chevron-left"></i></a></li>';

        // Pages
        for (let i = 1; i <= pagination.total_pages; i++) {
            if (i === 1 || i === pagination.total_pages || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
                html += '<li class="page-item ' + (i === pagination.current_page ? 'active' : '') + '">' +
                    '<a class="page-link" href="#" onclick="loadCases(' + i + '); return false;">' + i + '</a></li>';
            } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next
        html += '<li class="page-item ' + (!pagination.has_next ? 'disabled' : '') + '">' +
            '<a class="page-link" href="#" onclick="loadCases(' + (pagination.current_page + 1) + '); return false;">' +
            '<i class="fas fa-chevron-right"></i></a></li>';

        $('#pagination').html(html);
    }

    function filterCases(status) {
        $('#filterStatus').val(status);
        loadCases(1);
        loadCaseStats();
    }

    function resetFilters() {
        $('#searchInput').val('');
        $('#filterStatus').val('');
        $('#filterGrade').val('');
        $('#filterDateFrom').val('');
        $('#filterDateTo').val('');
        currentPage = 1;
        loadCases();
        loadCaseStats();
    }

    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadCases(1);
        }, 500);
    }

    // ✅ เปลี่ยนสถานะเคส (SweetAlert2 + Dropdown)
    function changeStatus(caseId, currentStatus) {
        // สถานะที่เปลี่ยนได้
        const statusOptions = [{
                value: 'ส่งเคส',
                label: '📤 ส่งเคส'
            },
            {
                value: 'กำลังติดตาม',
                label: '📞 กำลังติดตาม'
            },
            {
                value: 'อนุมัติ',
                label: '✅ อนุมัติ'
            },
            {
                value: 'ยกเลิก',
                label: '❌ ยกเลิก'
            },
            {
                value: 'ไม่สนใจ',
                label: '🚫 ไม่สนใจ'
            },
            {
                value: 'วงเงินไม่ถึง',
                label: '💰 วงเงินไม่ถึง'
            }
        ];

        // สร้าง option HTML
        let optionsHtml = '';
        statusOptions.forEach(function(opt) {
            let selected = (opt.value === currentStatus) ? 'selected' : '';
            optionsHtml += `<option value="${opt.value}" ${selected}>${opt.label}</option>`;
        });

        Swal.fire({
            title: 'เปลี่ยนสถานะเคส #' + caseId,
            html: `
            <div class="text-start">
                <p><strong>สถานะปัจจุบัน:</strong> <span class="badge bg-primary">${currentStatus || 'ไม่ระบุ'}</span></p>
                <div class="mb-3">
                    <label class="form-label">สถานะใหม่</label>
                    <select class="form-select" id="swalNewStatus">
                        ${optionsHtml}
                    </select>
                </div>
                <div id="cancelReasonDiv" style="display:none;">
                    <label class="form-label">เหตุผลการยกเลิก</label>
                    <textarea class="form-control" id="swalCancelReason" rows="2" placeholder="ระบุเหตุผล..."></textarea>
                </div>
            </div>
        `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-save"></i> เปลี่ยนสถานะ',
            cancelButtonText: 'ยกเลิก',
            didOpen: function() {
                // แสดง/ซ่อนเหตุผลยกเลิก
                $('#swalNewStatus').on('change', function() {
                    if ($(this).val() === 'ยกเลิก') {
                        $('#cancelReasonDiv').slideDown();
                    } else {
                        $('#cancelReasonDiv').slideUp();
                    }
                });

                // ถ้าสถานะปัจจุบันเป็นยกเลิก ให้แสดงเหตุผลเลย
                if (currentStatus === 'ยกเลิก') {
                    $('#cancelReasonDiv').slideDown();
                }
            },
            preConfirm: function() {
                const newStatus = $('#swalNewStatus').val();

                if (!newStatus) {
                    Swal.showValidationMessage('กรุณาเลือกสถานะใหม่');
                    return false;
                }

                if (newStatus === currentStatus) {
                    Swal.showValidationMessage('สถานะใหม่เหมือนกับสถานะปัจจุบัน');
                    return false;
                }

                const cancelReason = $('#swalCancelReason').val();
                if (newStatus === 'ยกเลิก' && !cancelReason) {
                    Swal.showValidationMessage('กรุณาระบุเหตุผลการยกเลิก');
                    return false;
                }

                return {
                    newStatus: newStatus,
                    cancelReason: cancelReason
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Loading
                Swal.fire({
                    title: 'กำลังบันทึก...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '../api/cases/update_status.php',
                    type: 'POST',
                    data: JSON.stringify({
                        case_id: caseId,
                        status: result.value.newStatus,
                        cancel_reason: result.value.cancelReason || ''
                    }),
                    contentType: 'application/json',
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'เปลี่ยนสถานะสำเร็จ!',
                                text: 'สถานะ: ' + result.value.newStatus,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            loadCases(currentPage);
                            loadCaseStats();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ผิดพลาด',
                                text: response.message || 'ไม่สามารถเปลี่ยนสถานะได้'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด',
                            text: 'ไม่สามารถเปลี่ยนสถานะได้'
                        });
                    }
                });
            }
        });
    }

    // Helper functions
    function getStatusBadge(status) {
        const badges = {
            'ส่งเคส': '<span class="badge bg-primary">ส่งเคส</span>',
            'กำลังติดตาม': '<span class="badge bg-warning text-dark">กำลังติดตาม</span>',
            'อนุมัติ': '<span class="badge bg-success">อนุมัติ</span>',
            'ยกเลิก': '<span class="badge bg-danger">ยกเลิก</span>',
            'ไม่สนใจ': '<span class="badge bg-secondary">ไม่สนใจ</span>',
            'วงเงินไม่ถึง': '<span class="badge bg-info">วงเงินไม่ถึง</span>'
        };
        return badges[status] || '<span class="badge bg-light text-dark">' + (status || 'ไม่ระบุ') + '</span>';
    }

    function getGradeColor(grade) {
        const colors = {
            'A+': 'success',
            'A': 'primary',
            'B': 'warning'
        };
        return colors[grade] || 'secondary';
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;
            return date.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        } catch (e) {
            return dateString;
        }
    }
</script>

<?php include_once '../includes/footer.php'; ?>