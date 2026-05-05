// ============ SIDEBAR TOGGLE ============

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    // Toggle class
    sidebar.classList.toggle('collapsed');
    
    // เก็บสถานะ
    const isCollapsed = sidebar.classList.contains('collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
    
    // อัปเดตตำแหน่งปุ่ม
    updateToggleButton();
    
    // Mobile: toggle expanded
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('mobile-expanded');
    }
}

function updateToggleButton() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    if (toggleBtn) {
        const isCollapsed = sidebar.classList.contains('collapsed');
        const sidebarWidth = isCollapsed ? 70 : 250;
        toggleBtn.style.left = (sidebarWidth + (window.innerWidth <= 768 ? 10 : 5)) + 'px';
    }
}

// Initialize
$(document).ready(function() {
    // โหลดสถานะ sidebar
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    
    if (isCollapsed) {
        $('#sidebar').addClass('collapsed');
    }
    
    if (window.innerWidth <= 768) {
        $('#sidebar').addClass('collapsed');
    }
    
    updateToggleButton();
    
    // รีเซ็ตเมื่อ resize
    $(window).resize(function() {
        updateToggleButton();
    });
    
    // ปิด sidebar เมื่อคลิกนอก (มือถือ)
    $(document).on('click', function(e) {
        if (window.innerWidth <= 768) {
            const sidebar = $('#sidebar');
            const toggleBtn = $('#sidebarToggle');
            
            if (!sidebar.is(e.target) && sidebar.has(e.target).length === 0 
                && !toggleBtn.is(e.target)) {
                sidebar.addClass('collapsed');
                sidebar.removeClass('mobile-expanded');
            }
        }
    });
});

// Keyboard shortcut
$(document).keydown(function(e) {
    if (e.ctrlKey && e.key === 'b') {
        e.preventDefault();
        toggleSidebar();
    }
});

function showHelp() {
  Swal.fire({
    title: "📚 วิธีใช้งานระบบ CRM Condo",
    html: `
            <div class="text-start" style="max-height: 450px; overflow-y: auto;">
                
                <div class="card mb-2 border-primary">
                    <div class="card-body py-2">
                        <h6 class="text-primary mb-2">🔹 การจัดการลูกค้า</h6>
                        <ul class="mb-0 small">
                            <li>ไปที่เมนู <strong>"ข้อมูลลูกค้า"</strong> เพื่อเพิ่ม/แก้ไขข้อมูลลูกค้า</li>
                            <li>กรอก <strong>เบอร์โทร</strong> ระบบจะค้นหาลูกค้าเดิมอัตโนมัติ</li>
                            <li>กด <strong>"ส่งเคส"</strong> เพื่อสร้างเคสให้ลูกค้า</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mb-2 border-success">
                    <div class="card-body py-2">
                        <h6 class="text-success mb-2">🔹 การจัดการเคส</h6>
                        <ul class="mb-0 small">
                            <li>ดูเคสทั้งหมดที่ <strong>"เคสทั้งหมด"</strong></li>
                            <li>คลิก <strong>Case ID</strong> เพื่อเปิดรายละเอียดเคส</li>
                            <li>ในหน้าเคสมี <strong>10 Tabs</strong> สำหรับจัดการแต่ละขั้นตอน</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mb-2 border-info">
                    <div class="card-body py-2">
                        <h6 class="text-info mb-2">🔹 การติดตามลูกค้า</h6>
                        <ul class="mb-0 small">
                            <li>ไปที่ Tab <strong>"📞 ติดตาม"</strong> ในรายละเอียดเคส</li>
                            <li>กด <strong>"เพิ่มการติดตาม"</strong> เพื่อบันทึก</li>
                            <li>เลือกสถานะ: สนใจ, รอดำเนินการ, นัดดูห้อง, ไม่สนใจ, ยกเลิก</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mb-2 border-warning">
                    <div class="card-body py-2">
                        <h6 class="text-warning mb-2">🔹 ตรวจ KPI</h6>
                        <ul class="mb-0 small">
                            <li>ไปที่เมนู <strong>"ตรวจ KPI"</strong></li>
                            <li>ดูรายการรอตรวจ และกด <strong>"ตรวจ"</strong></li>
                            <li>เลือกผล: <strong>ผ่าน</strong> หรือ <strong>ไม่ผ่าน</strong> พร้อมเหตุผล</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mb-2 border-danger">
                    <div class="card-body py-2">
                        <h6 class="text-danger mb-2">🔹 ฝ่าย Support</h6>
                        <ul class="mb-0 small">
                            <li><strong>Pre-Approve:</strong> อนุมัติเบื้องต้น กำหนดวงเงิน</li>
                            <li><strong>เอกสาร:</strong> จัดการสถานะเอกสาร อัปโหลดไฟล์</li>
                            <li><strong>ส่งธนาคาร:</strong> บันทึกการส่งธนาคาร</li>
                            <li><strong>อนุมัติ:</strong> บันทึกผลอนุมัติ แยกวงเงินห้อง/ประกัน/เฟอร์นิเจอร์</li>
                            <li><strong>ปิดหนี้:</strong> เพิ่มรายการหนี้ คำนวณยอดรวม</li>
                            <li><strong>จำนอง:</strong> บันทึกข้อมูลจำนอง</li>
                            <li><strong>ตรวจห้อง:</strong> บันทึกผลตรวจห้อง 1-3 รอบ พร้อมรูปภาพ</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mb-2 border-secondary">
                    <div class="card-body py-2">
                        <h6 class="text-secondary mb-2">🔹 ผู้ดูแลระบบ (Admin)</h6>
                        <ul class="mb-0 small">
                            <li><strong>จัดการผู้ใช้:</strong> เพิ่ม/แก้ไข/ลบ ผู้ใช้งาน</li>
                            <li><strong>ข้อมูลหลัก:</strong> จัดการ dropdown (ช่องทาง, โซน, ธนาคาร, สวัสดิการ)</li>
                            <li><strong>จัดการโครงการ:</strong> เพิ่ม/แก้ไข/ลบ โครงการ</li>
                            <li><strong>ตั้งค่าระบบ:</strong> LINE Notify, สำรองข้อมูล, ตั้งค่าทั่วไป</li>
                            <li><strong>บันทึกระบบ:</strong> ดูประวัติการใช้งานทั้งหมด</li>
                        </ul>
                    </div>
                </div>
                
                <hr class="my-2">
                
                <h6 class="mb-2">⌨️ คีย์ลัด</h6>
                <table class="table table-sm table-bordered small mb-0">
                    <tr><td><kbd>Ctrl</kbd> + <kbd>B</kbd></td><td>ย่อ/ขยาย Sidebar</td></tr>
                    <tr><td><kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>L</kbd></td><td>ออกจากระบบ</td></tr>
                    <tr><td><kbd>Esc</kbd></td><td>ปิด Modal / Popup</td></tr>
                </table>
                
                <hr class="my-2">
                
                <h6 class="mb-2">📌 4 Roles ในระบบ</h6>
                <table class="table table-sm table-bordered small mb-0">
                    <tr><td>👑 <strong>Admin</strong></td><td>เห็นทุกอย่าง จัดการผู้ใช้ ตั้งค่าระบบ</td></tr>
                    <tr><td>📄 <strong>Admin Page</strong></td><td>เพิ่มลูกค้า สร้างเคส ติดตาม</td></tr>
                    <tr><td>✅ <strong>KPI</strong></td><td>ตรวจสอบคุณภาพการติดตาม</td></tr>
                    <tr><td>🔧 <strong>Support</strong></td><td>เอกสาร ธนาคาร อนุมัติ ปิดหนี้ จำนอง ตรวจห้อง</td></tr>
                </table>
                
            </div>
        `,
    icon: "info",
    confirmButtonText: "✅ เข้าใจแล้ว",
    confirmButtonColor: "#3085d6",
    width: "700px",
    customClass: {
      popup: "text-start",
    },
  });
}

// ✅ ประกาศ logout ใน global scope
function logout() {
  if (confirm("ยืนยันการออกจากระบบ?")) {
    window.location.href = "../index.php?logout=1";
  }
}

// Initialize
$(document).ready(function () {
  // Load sidebar state
  const isCollapsed = localStorage.getItem("sidebarCollapsed") === "true";
  if (isCollapsed) {
    $("#sidebar").addClass("collapsed");
  }

  // Update toggle button
  updateToggleButton();

  // Load stats
  loadSidebarStats();
  setInterval(loadSidebarStats, 30000);

  // Highlight active menu
  highlightActiveMenu();

  // Handle responsive
  handleResponsive();
});

// Highlight active menu
function highlightActiveMenu() {
  const currentPage = window.location.pathname.split("/").pop();
  $(".sidebar-nav .nav-link").each(function () {
    const href = $(this).attr("href");
    if (href && href.includes(currentPage)) {
      $(this).addClass("active");
    }
  });
  if (currentPage === "case_detail.php") {
    $('.sidebar-nav .nav-link[href*="cases.php"]').addClass("active");
  }
}

// Handle responsive
function handleResponsive() {
  if (window.innerWidth <= 768) {
    $("#sidebar").addClass("collapsed");
    updateToggleButton();
  }
}

// ============ ⌨️ KEYBOARD SHORTCUTS ============

// ✅ รอให้ DOM โหลดเสร็จก่อน
$(document).ready(function () {
  // ✅ ใช้ keydown event
  $(document).on("keydown", function (e) {
    // Ctrl + B = Toggle Sidebar
    if (e.ctrlKey && e.key === "b") {
      e.preventDefault();
      toggleSidebar();
      return false;
    }

    // Ctrl + Shift + L = Logout
    if (e.ctrlKey && e.shiftKey && e.key === "L") {
      e.preventDefault();
      logout();
      return false;
    }

    // Escape = Close Modal
    if (e.key === "Escape") {
      // ปิด Bootstrap Modal
      $(".modal").modal("hide");
      // ปิด SweetAlert2
      if (typeof Swal !== "undefined") {
        Swal.close();
      }
    }
  });
});

// Logout Function
function logout() {
  Swal.fire({
    title: "ยืนยันการออกจากระบบ?",
    text: "คุณต้องการออกจากระบบใช่หรือไม่?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: '<i class="fas fa-sign-out-alt"></i> ออกจากระบบ',
    cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading
      Swal.fire({
        title: "กำลังออกจากระบบ...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      // Call logout API
      $.ajax({
        url: "../api/auth/logout.php",
        type: "POST",
        dataType: "json",
        success: function (response) {
          if (response.success) {
            Swal.fire({
              icon: "success",
              title: "ออกจากระบบสำเร็จ",
              timer: 1000,
              showConfirmButton: false,
              willClose: () => {
                window.location.href = "../index.php?logout=success";
              },
            });
          }
        },
        error: function () {
          // Redirect anyway
          window.location.href = "../index.php";
        },
      });
    }
  });
}

// Prevent sidebar from closing on large screens
$(document).on("click", ".sidebar-nav .nav-link", function (e) {
  if (window.innerWidth <= 768) {
    // On mobile, collapse sidebar after clicking
    setTimeout(() => {
      $("#sidebar").addClass("collapsed");
      updateToggleButton();
    }, 300);
  }
});
