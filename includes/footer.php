<?php
// includes/footer.php
// ปิด main-content div ที่เปิดใน header.php
?>

    </div><!-- /.container-fluid -->
</div><!-- /.main-content -->

<!-- ============ FOOTER ============ -->
<footer class="footer mt-auto py-3 bg-light" style="margin-left: 250px; transition: all 0.3s;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small class="text-muted">
                    &copy; <?php echo date('Y'); ?> CRM Condo System v1.0 | 
                    พัฒนาโดย ChonieDev | 
                    เวอร์ชั่นล่าสุด: <?php echo date('d/m/Y H:i'); ?>
                </small>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-muted">
                    <i class="fas fa-server"></i> 
                    <?php 
                    // แสดงข้อมูลเซิร์ฟเวอร์ (ถ้ามี)
                    if(isset($_SESSION['user_name'])) {
                        echo 'ผู้ใช้: ' . htmlspecialchars($_SESSION['user_name']) . ' | ';
                    }
                    ?>
                    <span id="serverTime"></span>
                </small>
            </div>
        </div>
    </div>
</footer>

<!-- ============ Bootstrap JS ============ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ============ Moment.js (สำหรับจัดการวันที่) ============ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/th.js"></script>

<!-- ============ COMMON JAVASCRIPT FUNCTIONS ============ -->
<script>
// ========== GLOBAL VARIABLES ==========
const BASE_URL = '<?php echo dirname($_SERVER['PHP_SELF']); ?>';
const API_URL = '../api/';
const CURRENT_PAGE = '<?php echo basename($_SERVER['PHP_SELF']); ?>';

// ========== AJAX SETUP ==========
$.ajaxSetup({
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    },
    timeout: 30000, // 30 seconds timeout
    error: function(xhr, status, error) {
        // Handle 401 Unauthorized
        if(xhr.status === 401) {
            Swal.fire({
                icon: 'warning',
                title: 'เซสชันหมดอายุ',
                text: 'กรุณาเข้าสู่ระบบใหม่',
                confirmButtonText: 'เข้าสู่ระบบ',
                allowOutsideClick: false
            }).then(() => {
                window.location.href = '../index.php?session=expired';
            });
        }
        // Handle 403 Forbidden
        else if(xhr.status === 403) {
            Swal.fire({
                icon: 'error',
                title: 'ไม่มีสิทธิ์',
                text: 'คุณไม่มีสิทธิ์ในการดำเนินการนี้',
                confirmButtonText: 'ตกลง'
            });
        }
        // Handle 500 Server Error
        else if(xhr.status === 500) {
            console.error('Server Error:', xhr.responseText);
        }
    }
});

// ========== GLOBAL FUNCTIONS ==========

/**
 * แสดง Loading
 */
function showGlobalLoading(message = 'กำลังโหลด...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

/**
 * ซ่อน Loading
 */
function hideGlobalLoading() {
    Swal.close();
}

/**
 * แสดงข้อความสำเร็จ
 */
function showSuccess(message = 'ดำเนินการสำเร็จ', timer = 2000) {
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: message,
        timer: timer,
        showConfirmButton: false
    });
}

/**
 * แสดงข้อความผิดพลาด
 */
function showError(message = 'เกิดข้อผิดพลาด') {
    Swal.fire({
        icon: 'error',
        title: 'ผิดพลาด!',
        text: message,
        confirmButtonText: 'ตกลง'
    });
}

/**
 * แสดงข้อความเตือน
 */
function showWarning(message = 'คำเตือน') {
    Swal.fire({
        icon: 'warning',
        title: 'คำเตือน!',
        text: message,
        confirmButtonText: 'ตกลง'
    });
}

/**
 * แสดง Toast Notification
 */
function showToast(message, icon = 'info', position = 'top-end') {
    const Toast = Swal.mixin({
        toast: true,
        position: position,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    Toast.fire({
        icon: icon,
        title: message
    });
}

/**
 * ยืนยันการกระทำ
 */
function confirmAction(title, text, callback, confirmText = 'ยืนยัน', cancelText = 'ยกเลิก') {
    Swal.fire({
        title: title || 'ยืนยันการดำเนินการ?',
        text: text || '',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText
    }).then((result) => {
        if(result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}

/**
 * ยืนยันการลบ
 */
function confirmDelete(title, text, callback) {
    Swal.fire({
        title: title || 'ยืนยันการลบ?',
        text: text || 'การลบไม่สามารถกู้คืนได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash"></i> ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if(result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}

// ========== FORMATTING FUNCTIONS ==========

/**
 * จัดรูปแบบตัวเลข
 */
function numberFormat(number, decimals = 2) {
    if(number === null || number === undefined || number === '') return '0';
    return new Intl.NumberFormat('th-TH', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(parseFloat(number));
}

/**
 * จัดรูปแบบสกุลเงิน
 */
function currencyFormat(number) {
    return numberFormat(number) + ' บาท';
}

/**
 * จัดรูปแบบวันที่แบบไทย
 */
function formatDateThai(dateString, format = 'short') {
    if(!dateString) return '-';
    
    const date = new Date(dateString);
    if(isNaN(date.getTime())) return '-';
    
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    
    if(format === 'datetime') {
        options.hour = '2-digit';
        options.minute = '2-digit';
    } else if(format === 'time') {
        return date.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
    } else if(format === 'full') {
        options.hour = '2-digit';
        options.minute = '2-digit';
        options.second = '2-digit';
    }
    
    return date.toLocaleDateString('th-TH', options);
}

/**
 * แสดงเวลาผ่านไปแล้ว
 */
function timeAgo(dateString) {
    if(!dateString) return '-';
    
    moment.locale('th');
    return moment(dateString).fromNow();
}

/**
 * Escape HTML
 */
function escapeHtml(text) {
    if(!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * ตัดข้อความยาว
 */
function truncateText(text, maxLength = 50) {
    if(!text) return '-';
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

// ========== UTILITY FUNCTIONS ==========

/**
 * Copy to Clipboard
 */
function copyToClipboard(text, showAlert = true) {
    if(navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            if(showAlert) {
                showToast('คัดลอกเรียบร้อย!', 'success');
            }
        }).catch(() => {
            fallbackCopy(text, showAlert);
        });
    } else {
        fallbackCopy(text, showAlert);
    }
}

function fallbackCopy(text, showAlert) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        if(showAlert) {
            showToast('คัดลอกเรียบร้อย!', 'success');
        }
    } catch(err) {
        if(showAlert) {
            showError('ไม่สามารถคัดลอกได้');
        }
    }
    
    document.body.removeChild(textarea);
}

/**
 * Serialize Form to JSON
 */
function formToJSON(formElement) {
    const formData = new FormData(formElement);
    const data = {};
    
    formData.forEach((value, key) => {
        // Handle multiple values with same key
        if(data[key] !== undefined) {
            if(!Array.isArray(data[key])) {
                data[key] = [data[key]];
            }
            data[key].push(value);
        } else {
            data[key] = value;
        }
    });
    
    return data;
}

/**
 * Debounce Function
 */
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Get URL Parameter
 */
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

// ========== FORM VALIDATION ==========

/**
 * Validate Email
 */
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/**
 * Validate Thai Phone Number
 */
function isValidPhone(phone) {
    const cleaned = phone.replace(/[^0-9]/g, '');
    return cleaned.length >= 9 && cleaned.length <= 10 && cleaned.startsWith('0');
}

/**
 * Validate Required Field
 */
function isRequired(value) {
    return value !== null && value !== undefined && value.toString().trim() !== '';
}

// ========== UI HELPERS ==========

/**
 * Toggle Password Visibility
 */
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if(input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

/**
 * Scroll to Element
 */
function scrollToElement(elementId, offset = 0) {
    const element = document.getElementById(elementId);
    if(element) {
        const top = element.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top: top, behavior: 'smooth' });
    }
}

/**
 * Scroll to Top
 */
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ========== SESSION MANAGEMENT ==========

// Auto logout timer (30 minutes)
let logoutTimer;
const LOGOUT_TIMEOUT = 30 * 60 * 1000; // 30 minutes

function resetLogoutTimer() {
    clearTimeout(logoutTimer);
    logoutTimer = setTimeout(() => {
        Swal.fire({
            icon: 'warning',
            title: 'เซสชันกำลังจะหมดอายุ',
            text: 'คุณไม่ได้ใช้งานนานเกิน 30 นาที',
            showCancelButton: true,
            confirmButtonText: 'ใช้งานต่อ',
            cancelButtonText: 'ออกจากระบบ',
            timer: 60000,
            timerProgressBar: true,
            allowOutsideClick: false
        }).then((result) => {
            if(result.isConfirmed) {
                resetLogoutTimer();
                // Refresh session
                $.get(API_URL + 'auth/check.php');
            } else if(result.dismiss === Swal.DismissReason.timer) {
                // Auto logout
                window.location.href = '../logout.php';
            } else {
                window.location.href = '../logout.php';
            }
        });
    }, LOGOUT_TIMEOUT);
}

// Start timer and reset on user activity
$(document).ready(function() {
    resetLogoutTimer();
    
    // Reset timer on user activity
    ['click', 'keypress', 'scroll', 'mousemove'].forEach(event => {
        document.addEventListener(event, () => {
            resetLogoutTimer();
        }, { passive: true });
    });
});

// ========== NOTIFICATION CHECKER ==========
function checkNotifications() {
    $.ajax({
        url: API_URL + 'dashboard/sidebar_stats.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                updateNotificationBadges(response);
            }
        }
    });
}

function updateNotificationBadges(data) {
    if(data.pending_cases > 0) {
        $('#menuCaseCount').text(data.pending_cases).show();
    } else {
        $('#menuCaseCount').hide();
    }
    
    if(data.pending_kpi > 0) {
        $('#menuKpiCount').text(data.pending_kpi).show();
    } else {
        $('#menuKpiCount').hide();
    }
}

// Check notifications every 30 seconds
setInterval(checkNotifications, 30000);
checkNotifications();

// ========== SERVER TIME ==========
function updateServerTime() {
    const now = new Date();
    const options = { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        hour12: false 
    };
    $('#serverTime').text('🕐 ' + now.toLocaleTimeString('th-TH', options));
}

updateServerTime();
setInterval(updateServerTime, 1000);

// ========== PAGE LOAD COMPLETE ==========
$(document).ready(function() {
    console.log('CRM Condo System v1.0 - Page Loaded');
    console.log('Page:', CURRENT_PAGE);
    console.log('Time:', new Date().toLocaleString('th-TH'));
    
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Remove loading overlay if exists
    $('#loadingOverlay').fadeOut(300);
});

// ========== KEYBOARD SHORTCUTS ==========
$(document).keydown(function(e) {
    // Ctrl + Shift + L: Logout
    if(e.ctrlKey && e.shiftKey && e.key === 'L') {
        e.preventDefault();
        confirmAction('ยืนยันการออกจากระบบ?', '', function() {
            window.location.href = '../logout.php';
        });
    }
    
    // Escape: Close all modals
    if(e.key === 'Escape') {
        $('.modal').modal('hide');
        Swal.close();
    }
    
    // Ctrl + Home: Go to Dashboard
    if(e.ctrlKey && e.key === 'Home') {
        e.preventDefault();
        window.location.href = 'dashboard.php';
    }
    
    // F5 or Ctrl + R: Prevent accidental refresh with unsaved data
    if((e.key === 'F5' || (e.ctrlKey && e.key === 'r')) && window.hasUnsavedChanges) {
        e.preventDefault();
        confirmAction('มีข้อมูลที่ยังไม่ได้บันทึก', 'คุณต้องการออกจากหน้านี้ใช่หรือไม่?', function() {
            window.hasUnsavedChanges = false;
            location.reload();
        });
    }
});

// ========== PREVENT ACCIDENTAL NAVIGATION ==========
window.addEventListener('beforeunload', function(e) {
    if(window.hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = 'มีข้อมูลที่ยังไม่ได้บันทึก คุณต้องการออกจากหน้านี้หรือไม่?';
        return e.returnValue;
    }
});

// ========== PRINT FUNCTION ==========
function printPage() {
    window.print();
}

// ========== FULLSCREEN TOGGLE ==========
function toggleFullscreen() {
    if(!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => {
            console.log('Fullscreen error:', err);
        });
    } else {
        document.exitFullscreen();
    }
}

// ========== CONSOLE WARNING ==========
console.log('%c⚠️ คำเตือนเพื่อความปลอดภัย', 'color: red; font-size: 20px; font-weight: bold;');
console.log('%cนี่เป็นฟีเจอร์สำหรับนักพัฒนา หากมีคนบอกให้คุณคัดลอก/วางข้อความที่นี่ อาจเป็นการหลอกลวงเพื่อขโมยข้อมูลได้', 'font-size: 14px;');
console.log('%cห้ามใส่รหัสผ่านหรือข้อมูลสำคัญในช่องนี้เด็ดขาด', 'color: orange; font-size: 14px;');

// ========== ERROR HANDLING ==========
window.onerror = function(message, source, lineno, colno, error) {
    console.error('JavaScript Error:', {
        message: message,
        source: source,
        line: lineno,
        column: colno,
        error: error
    });
    
    // ซ่อน error จากผู้ใช้ในการใช้งานจริง
    return true;
};

// ========== AJAX ERROR HANDLER ==========
$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
    // จัดการ error ทั่วไป (ยกเว้นที่จัดการเฉพาะแล้ว)
    if(jqxhr.status === 0) {
        console.error('Network Error: ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
    } else if(jqxhr.status >= 500) {
        console.error('Server Error:', jqxhr.status, thrownError);
    }
});
</script>
</body>
</html>