$(document).ready(function () {
  loadCaseInfo();
  loadCustomerDetail();
  $("#caseTabs button").on("shown.bs.tab", function (e) {
    let target = $(e.target).data("bs-target");
    switch (target) {
      case "#tab-follow":
        loadFollowList();
        break;
      case "#tab-kpi":
        loadKpiList();
        break;
      case "#tab-preapprove":
        loadPreapprove();
        break;
      case "#tab-documents":
        loadDocuments();
        break;
      case "#tab-bank":
        loadBankList();
        break;
      case "#tab-approval":
        loadApproval();
        break;
      case "#tab-debt":
        loadDebt();
        break;
      case "#tab-mortgage":
        loadMortgage();
        break;
      case "#tab-inspection":
        loadInspectionList();
        break;
    }
  });
  // set today date for inspection modal
  $("#inspection_date").val(new Date().toISOString().slice(0, 16));
});

// ========== CASE INFO ==========
function loadCaseInfo() {
  $.get(
    "../api/cases/get.php",
    {
      id: CASE_ID,
    },
    function (res) {
      if (res.success && res.data) {
        let c = res.data;
        $("#caseStatus").text(c.status || "-");
        $("#customerName").text(c.customer_name || "-");
        $("#ownerName").text(c.owner_name || "-");
        $("#caseDate").text(formatDate(c.created_at));
      }
    },
    "json",
  );
}

// ========== CUSTOMER ==========
function loadCustomerDetail() {
  $.get(
    "../api/cases/get_customer.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      if (res.success && res.data) {
        let c = res.data;
        let html =
          '<div class="row"><div class="col-md-6"><table class="table table-bordered table-sm">' +
          '<tr><th style="width:120px">ชื่อ</th><td>' +
          (c.name || "-") +
          "</td></tr>" +
          '<tr><th>เบอร์โทร</th><td><a href="tel:' +
          (c.phone || "") +
          '">' +
          (c.phone || "-") +
          "</a></td></tr>" +
          "<tr><th>Facebook</th><td>" +
          (c.facebook || "-") +
          "</td></tr>" +
          "<tr><th>Line</th><td>" +
          (c.line_id || "-") +
          "</td></tr>" +
          "<tr><th>ช่องทาง</th><td>" +
          (c.channel || "-") +
          "</td></tr>" +
          '<tr><th>เกรด</th><td><span class="badge bg-' +
          getGradeColor(c.grade) +
          '">' +
          (c.grade || "-") +
          "</span></td></tr>" +
          '</table></div><div class="col-md-6"><table class="table table-bordered table-sm">' +
          '<tr><th style="width:120px">โครงการ</th><td>' +
          (c.project_name || "-") +
          "</td></tr>" +
          "<tr><th>ราคา</th><td>" +
          (c.price ? numberFormat(c.price) + " บาท" : "-") +
          "</td></tr>" +
          "<tr><th>เงินทอน</th><td>" +
          (c.cashback ? numberFormat(c.cashback) + " บาท" : "-") +
          "</td></tr>" +
          "<tr><th>ลักษณะ</th><td>" +
          (c.living_type === "rent"
            ? "ปล่อยเช่า"
            : c.living_type === "live"
              ? "อยู่เอง"
              : "-") +
          "</td></tr>" +
          "<tr><th>โซน</th><td>" +
          (c.zone || "-") +
          "</td></tr>" +
          "<tr><th>ภาระหนี้</th><td>" +
          (c.debt_status === "have"
            ? '<span class="badge bg-warning">มี</span>'
            : c.debt_status === "none"
              ? '<span class="badge bg-success">ไม่มี</span>'
              : "-") +
          "</td></tr>" +
          "</table></div></div>" +
          '<table class="table table-bordered table-sm mt-2"><tr><th style="width:120px">บริษัท</th><td>' +
          (c.company_name || "-") +
          '</td><th style="width:120px">อายุงาน</th><td>' +
          (c.work_age_month ? c.work_age_month + " เดือน" : "-") +
          '</td><th style="width:120px">สวัสดิการ</th><td>' +
          (c.welfare || "-") +
          "</td></tr></table>";
        $("#customerDetail").html(html);
      } else {
        $("#customerDetail").html(
          '<div class="alert alert-warning">ไม่พบข้อมูลลูกค้า</div>',
        );
      }
    },
    "json",
  ).fail(function () {
    $("#customerDetail").html(
      '<div class="alert alert-danger">โหลดไม่สำเร็จ</div>',
    );
  });
}

// ========== FOLLOW ==========
function showFollowForm() {
  $("#follow_id, #follow_status, #follow_note, #follow_channel").val("");
  $.get(
    "../api/follow/get_count.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      $("#follow_step").val((res.count || 0) + 1);
      $("#followModal").modal("show");
    },
  );
}

// ✅ บันทึกการติดตาม (SweetAlert2)
function saveFollow() {
  let step = $("#follow_step").val();
  let status = $("#follow_status").val();
  let note = $("#follow_note").val() ? $("#follow_note").val().trim() : "";
  let channel = $("#follow_channel").val() || "";
  let followId = $("#follow_id").val() || "";

  // ❌ Validate
  if (!status) {
    Swal.fire({
      icon: "warning",
      title: "กรุณาเลือกสถานะ",
      toast: true,
      position: "top-end",
      timer: 2000,
      showConfirmButton: false,
    });
    $("#follow_status").focus();
    return;
  }

  // ✅ Confirm
  Swal.fire({
    title: "บันทึกการติดตาม?",
    html: `
            <div class="text-start">
                <p>📞 <strong>ครั้งที่ ${step}</strong></p>
                <p>📌 สถานะ: <strong>${getStatusLabel(status)}</strong></p>
                ${note ? "<p>📝 " + note + "</p>" : ""}
            </div>
        `,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#6c757d",
    confirmButtonText: '<i class="fas fa-save"></i> บันทึก',
    cancelButtonText: "ยกเลิก",
  }).then((result) => {
    if (result.isConfirmed) {
      // Disable button
      let btn = $(".modal-footer .btn-primary");
      let origText = btn.html();
      btn
        .html(
          '<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...',
        )
        .prop("disabled", true);

      let postData = {
        case_id: parseInt(CASE_ID),
        step: parseInt(step),
        status: status,
        note: note,
        channel: channel,
      };
      if (followId) postData.id = parseInt(followId);

      $.ajax({
        url: "../api/follow/add.php",
        type: "POST",
        data: JSON.stringify(postData),
        contentType: "application/json",
        dataType: "json",
        timeout: 15000,
        success: function (res) {
          btn.html(origText).prop("disabled", false);

          if (res.success) {
            $("#followModal").modal("hide");
            Swal.fire({
              icon: "success",
              title: "บันทึกสำเร็จ! 🎉",
              timer: 1500,
              showConfirmButton: false,
            });
            loadFollowList();
            loadCaseInfo();
          } else {
            Swal.fire({
              icon: "error",
              title: "ผิดพลาด",
              text: res.message || "ไม่สามารถบันทึกได้",
            });
          }
        },
        error: function (xhr, status, error) {
          btn.html(origText).prop("disabled", false);
          console.error("Follow save error:", status, error);

          let msg = "ไม่สามารถบันทึกได้";
          if (xhr.status === 0) msg = "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้";
          else if (xhr.status === 500) msg = "เซิร์ฟเวอร์ผิดพลาด";

          Swal.fire({
            icon: "error",
            title: "ผิดพลาด",
            text: msg + " (Status: " + xhr.status + ")",
          });
        },
      });
    }
  });
}

function loadFollowList() {
  $.get(
    "../api/follow/list.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      if (res.success && res.data && res.data.length > 0) {
        let html = '<div class="timeline">';
        res.data.forEach(function (f) {
          html +=
            '<div class="timeline-item"><div class="timeline-marker bg-' +
            getStatusColor(f.status) +
            '" style="width:30px;height:30px;left:10px;"></div>' +
            '<div class="timeline-content"><h6>ครั้งที่ ' +
            f.step +
            ": " +
            getStatusLabel(f.status) +
            '</h6><small class="text-muted">' +
            formatDate(f.created_at) +
            "</small>" +
            (f.note ? '<p class="mt-1 mb-0">' + f.note + "</p>" : "") +
            '<button class="btn btn-sm btn-outline-danger mt-1" onclick="deleteFollow(' +
            f.id +
            ')"><i class="fas fa-trash"></i></button></div></div>';
        });
        html += "</div>";
        $("#followList").html(html);
      } else {
        $("#followList").html(
          '<div class="text-center py-5 text-muted"><i class="fas fa-phone-slash fa-4x mb-3"></i><p>ยังไม่มีการติดตาม</p><button class="btn btn-primary" onclick="showFollowForm()">เพิ่มการติดตามครั้งแรก</button></div>',
        );
      }
    },
    "json",
  );
}

// ✅ ลบการติดตาม (SweetAlert2)
function deleteFollow(id) {
  Swal.fire({
    title: "ยืนยันการลบ?",
    text: "ประวัติการติดตามนี้จะถูกลบออก ไม่สามารถกู้คืนได้",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: '<i class="fas fa-trash"></i> ลบ',
    cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      // Loading
      Swal.fire({
        title: "กำลังลบ...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      $.ajax({
        url: "../api/follow/delete.php",
        type: "POST",
        data: JSON.stringify({
          id: id,
        }),
        contentType: "application/json",
        dataType: "json",
        timeout: 10000,
        success: function (res) {
          if (res.success) {
            Swal.fire({
              icon: "success",
              title: "ลบสำเร็จ!",
              text: "ลบประวัติการติดตามเรียบร้อยแล้ว",
              timer: 1500,
              showConfirmButton: false,
            });
            loadFollowList();
            loadCaseInfo();
          } else {
            Swal.fire({
              icon: "error",
              title: "ผิดพลาด",
              text: res.message || "ไม่สามารถลบได้",
            });
          }
        },
        error: function (xhr) {
          Swal.fire({
            icon: "error",
            title: "ผิดพลาด",
            text: "ไม่สามารถลบได้ (Status: " + xhr.status + ")",
          });
        },
      });
    }
  });
}

// ✅ บันทึก Pre-Approve (SweetAlert2)
function savePreapprove() {
  let status = $("#pa_status").val();
  let amount = $("#pa_amount").val();
  let note = $("#pa_note").val() || "";

  // ❌ Validate
  if (!status) {
    Swal.fire({
      icon: "warning",
      title: "กรุณาเลือกสถานะ",
      text: "ต้องเลือกอย่างน้อย 1 สถานะ (กำลังดำเนินการ / อนุมัติ / ปฏิเสธ)",
      toast: true,
      position: "top-end",
      timer: 3000,
      showConfirmButton: false,
    });
    return;
  }

  // ✅ Confirm
  let statusLabels = {
    processing: "⏳ กำลังดำเนินการ",
    approved: "✅ อนุมัติ",
    rejected: "❌ ปฏิเสธ",
  };
  let statusLabel = statusLabels[status] || status;

  Swal.fire({
    title: "บันทึก Pre-Approve?",
    html: `
            <div class="text-start">
                <p>📋 <strong>สถานะ:</strong> ${statusLabel}</p>
                <p>💰 <strong>วงเงิน:</strong> ${amount ? numberFormat(amount) + " บาท" : "ไม่ระบุ"}</p>
                ${note ? "<p>📝 <strong>หมายเหตุ:</strong> " + note + "</p>" : ""}
            </div>
        `,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#6c757d",
    confirmButtonText: '<i class="fas fa-save"></i> บันทึก',
    cancelButtonText: "ยกเลิก",
  }).then((result) => {
    if (result.isConfirmed) {
      // Loading
      Swal.fire({
        title: "กำลังบันทึก...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      let data = {
        case_id: CASE_ID,
        status: status,
        approved_amount: amount || 0,
        note: note,
      };

      $.ajax({
        url: "../api/preapprove/save.php",
        type: "POST",
        data: JSON.stringify(data),
        contentType: "application/json",
        dataType: "json",
        timeout: 10000,
        success: function (res) {
          if (res.success) {
            Swal.fire({
              icon: "success",
              title: "บันทึกสำเร็จ!",
              text: res.message || "Pre-Approve เรียบร้อย",
              timer: 2000,
              showConfirmButton: false,
            });
            loadPreapproveHistory();
          } else {
            Swal.fire({
              icon: "error",
              title: "ผิดพลาด",
              text: res.message || "ไม่สามารถบันทึกได้",
            });
          }
        },
        error: function (xhr) {
          Swal.fire({
            icon: "error",
            title: "ผิดพลาด",
            text: "ไม่สามารถบันทึกได้ (Status: " + xhr.status + ")",
          });
        },
      });
    }
  });
}

// Helper
function numberFormat(n) {
  if (!n && n !== 0) return "0";
  return new Intl.NumberFormat("th-TH", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(parseFloat(n));
}
// ========== KPI ==========
function loadKpiList() {
  $.get(
    "../api/kpi/list.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      if (res.success && res.data && res.data.length > 0) {
        let html =
          '<table class="table table-striped"><thead><tr><th>ผู้ตรวจ</th><th>ผล</th><th>เหตุผล</th><th>วันที่</th></tr></thead><tbody>';
        res.data.forEach(function (k) {
          html +=
            "<tr><td>" +
            (k.checker_name || "-") +
            "</td><td>" +
            (k.result === "pass"
              ? '<span class="badge bg-success">ผ่าน</span>'
              : '<span class="badge bg-danger">ไม่ผ่าน</span>') +
            "</td><td>" +
            (k.reason || "-") +
            "</td><td>" +
            formatDate(k.created_at) +
            "</td></tr>";
        });
        html += "</tbody></table>";
        $("#kpiList").html(html);
      } else {
        $("#kpiList").html(
          '<div class="text-center py-4 text-muted">ยังไม่มีการตรวจ KPI</div>',
        );
      }
    },
    "json",
  );
}

// ========== PRE-APPROVE ==========
function loadPreapprove() {
  let html =
    '<form><div class="row"><div class="col-md-6 mb-3"><label>สถานะ</label><select class="form-select" id="pa_status"><option value="processing">กำลังดำเนินการ</option><option value="approved">อนุมัติ</option><option value="rejected">ปฏิเสธ</option></select></div><div class="col-md-6 mb-3"><label>วงเงิน</label><input type="number" class="form-control" id="pa_amount" step="0.01"></div></div><div class="mb-3"><label>หมายเหตุ</label><textarea class="form-control" id="pa_note" rows="2"></textarea></div><button type="button" class="btn btn-primary" onclick="savePreapprove()">บันทึก</button></form>';
  $("#preapproveContent").html(html);
  $.get(
    "../api/preapprove/get.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      if (res.data) {
        $("#pa_status").val(res.data.status);
        $("#pa_amount").val(res.data.approved_amount);
        $("#pa_note").val(res.data.note || "");
      }
    },
    "json",
  );
}

// ========== DOCUMENTS ==========
function loadDocuments() {
  let html =
    '<div class="row">' +
    // 🔹 ส่วนซ้าย: สถานะเอกสาร
    '<div class="col-md-7">' +
    '<div class="card mb-3">' +
    '<div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fas fa-clipboard-check"></i> สถานะเอกสาร</h6></div>' +
    '<div class="card-body">' +
    "<form>" +
    '<div class="row">' +
    '<div class="col-md-4 mb-2">' +
    '<label class="form-label"><small>เอกสารขั้นที่ 1</small></label>' +
    '<select class="form-select form-select-sm doc-status" id="doc_status_1">' +
    '<option value="">เลือก</option><option value="ผ่าน">✅ ผ่าน</option><option value="ไม่ผ่าน">❌ ไม่ผ่าน</option><option value="รอดำเนินการ">⏳ รอดำเนินการ</option></select></div>' +
    '<div class="col-md-4 mb-2">' +
    '<label class="form-label"><small>เอกสารขั้นที่ 2</small></label>' +
    '<select class="form-select form-select-sm doc-status" id="doc_status_2">' +
    '<option value="">เลือก</option><option value="ผ่าน">✅ ผ่าน</option><option value="ไม่ผ่าน">❌ ไม่ผ่าน</option></select></div>' +
    '<div class="col-md-4 mb-2">' +
    '<label class="form-label"><small>เอกสารขั้นที่ 3</small></label>' +
    '<select class="form-select form-select-sm doc-status" id="doc_status_3">' +
    '<option value="">เลือก</option><option value="เรียบร้อย">✅ เรียบร้อย</option><option value="ยกเลิก">❌ ยกเลิก</option></select></div>' +
    "</div>" +
    "<hr>" +
    '<div class="row">' +
    '<div class="col-md-6 mb-2">' +
    '<label class="form-label"><small>ธนาคาร</small></label>' +
    '<input type="text" class="form-control form-control-sm" id="doc_bank_name" placeholder="ชื่อธนาคาร"></div>' +
    '<div class="col-md-6 mb-2">' +
    '<label class="form-label"><small>เลขที่บัญชี</small></label>' +
    '<input type="text" class="form-control form-control-sm" id="doc_bank_account" placeholder="เลขบัญชี"></div>' +
    "</div>" +
    '<div class="row">' +
    '<div class="col-md-6 mb-2">' +
    '<label class="form-label"><small>Pre-Check</small></label>' +
    '<select class="form-select form-select-sm" id="doc_precheck"><option value="">เลือก</option><option value="ผ่าน">✅ ผ่าน</option><option value="ไม่ผ่าน">❌ ไม่ผ่าน</option></select></div>' +
    '<div class="col-md-6 mb-2">' +
    '<label class="form-label"><small>สถานะปิดหนี้</small></label>' +
    '<select class="form-select form-select-sm" id="doc_debt_close"><option value="">เลือก</option><option value="done">✅ ปิดแล้ว</option><option value="not_done">❌ ยังไม่ปิด</option></select></div>' +
    "</div>" +
    '<button type="button" class="btn btn-primary btn-sm mt-2" onclick="saveDocumentStatus()"><i class="fas fa-save"></i> บันทึกสถานะ</button>' +
    "</form>" +
    "</div>" +
    "</div>" +
    "</div>" +
    // 🔹 ส่วนขวา: อัปโหลดไฟล์
    '<div class="col-md-5">' +
    '<div class="card mb-3">' +
    '<div class="card-header bg-success text-white"><h6 class="mb-0"><i class="fas fa-upload"></i> อัปโหลดเอกสาร</h6></div>' +
    '<div class="card-body">' +
    '<div class="mb-2">' +
    '<label class="form-label"><small>ประเภทเอกสาร <span class="text-danger">*</span></small></label>' +
    '<select class="form-select form-select-sm" id="doc_type">' +
    '<option value="">เลือกประเภทเอกสาร</option>' +
    "</select>" +
    "</div>" +
    '<div class="mb-2">' +
    '<label class="form-label"><small>เลือกไฟล์ <span class="text-danger">*</span></label>' +
    '<input type="file" class="form-control form-control-sm" id="uploadFile">' +
    '<small class="text-muted">รองรับ .jpg, .png, .pdf (สูงสุด 5MB)</small>' +
    "</div>" +
    '<button class="btn btn-success btn-sm" onclick="uploadFile()"><i class="fas fa-upload"></i> อัปโหลด</button>' +
    "<hr>" +
    '<h6 class="mb-2">📁 เอกสารที่อัปโหลด (<span id="fileCount">0</span>)</h6>' +
    '<div id="fileList" style="max-height: 300px; overflow-y: auto;">กำลังโหลด...</div>' +
    "</div>" +
    "</div>" +
    "</div>" +
    "</div>";

  $("#documentsContent").html(html);

  // โหลด dropdown ประเภทเอกสาร
  loadDocumentTypes();
  // โหลดสถานะเอกสาร
  loadDocumentStatus();
  // โหลดรายการไฟล์
  loadFileList();
}

// โหลดประเภทเอกสารจาก master_dropdowns
function loadDocumentTypes() {
  $.ajax({
    url: "../api/master/get.php",
    type: "GET",
    data: {
      type: "document_type",
    },
    dataType: "json",
    success: function (data) {
      let options = '<option value="">เลือกประเภทเอกสาร</option>';
      if (Array.isArray(data)) {
        data.forEach(function (item) {
          options +=
            '<option value="' + item.value + '">📄 ' + item.value + "</option>";
        });
      }
      // ถ้าไม่มีข้อมูลจาก master ให้ใช้ค่าเริ่มต้น
      if (data.length === 0) {
        options += '<option value="บัตรประชาชน">📄 บัตรประชาชน</option>';
        options += '<option value="ทะเบียนบ้าน">📄 ทะเบียนบ้าน</option>';
        options += '<option value="สลิปเงินเดือน">📄 สลิปเงินเดือน</option>';
        options += '<option value="Statement">📄 Statement</option>';
        options +=
          '<option value="หนังสือรับรองเงินเดือน">📄 หนังสือรับรองเงินเดือน</option>';
        options += '<option value="สัญญาซื้อขาย">📄 สัญญาซื้อขาย</option>';
        options += '<option value="อื่นๆ">📄 อื่นๆ</option>';
      }
      $("#doc_type").html(options);
    },
    error: function () {
      // Fallback options
      let options = '<option value="">เลือกประเภทเอกสาร</option>';
      options += '<option value="บัตรประชาชน">📄 บัตรประชาชน</option>';
      options += '<option value="ทะเบียนบ้าน">📄 ทะเบียนบ้าน</option>';
      options += '<option value="สลิปเงินเดือน">📄 สลิปเงินเดือน</option>';
      options += '<option value="Statement">📄 Statement</option>';
      options += '<option value="อื่นๆ">📄 อื่นๆ</option>';
      $("#doc_type").html(options);
    },
  });
}

function loadDocumentStatus() {
  $.get(
    "../api/document/get.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      if (res.data) {
        let d = res.data;
        $("#doc_status_1").val(d.doc_status_1 || "");
        $("#doc_status_2").val(d.doc_status_2 || "");
        $("#doc_status_3").val(d.doc_status_3 || "");
        $("#doc_bank_name").val(d.bank_name || "");
        $("#doc_bank_account").val(d.bank_account || "");
        $("#doc_precheck").val(d.precheck_status || "");
        $("#doc_debt_close").val(d.debt_close_status || "");
      }
    },
    "json",
  );
}

// ✅ บันทึกสถานะเอกสาร (SweetAlert2)
function saveDocumentStatus() {
  let data = {
    case_id: CASE_ID,
    doc_status_1: $("#doc_status_1").val(),
    doc_status_2: $("#doc_status_2").val(),
    doc_status_3: $("#doc_status_3").val(),
    bank_name: $("#doc_bank_name").val(),
    bank_account: $("#doc_bank_account").val(),
    precheck_status: $("#doc_precheck").val(),
    debt_close_status: $("#doc_debt_close").val(),
  };

  // ✅ Confirm
  Swal.fire({
    title: "บันทึกสถานะเอกสาร?",
    html: `
            <div class="text-start">
                <p>📄 <strong>ขั้นที่ 1:</strong> ${$("#doc_status_1").val() || "-"}</p>
                <p>📄 <strong>ขั้นที่ 2:</strong> ${$("#doc_status_2").val() || "-"}</p>
                <p>📄 <strong>ขั้นที่ 3:</strong> ${$("#doc_status_3").val() || "-"}</p>
                ${$("#doc_precheck").val() ? "<p>🔍 <strong>Pre-Check:</strong> " + $("#doc_precheck").val() + "</p>" : ""}
                ${$("#doc_debt_close").val() ? "<p>💳 <strong>ปิดหนี้:</strong> " + $("#doc_debt_close").val() + "</p>" : ""}
            </div>
        `,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#6c757d",
    confirmButtonText: '<i class="fas fa-save"></i> บันทึก',
    cancelButtonText: "ยกเลิก",
  }).then((result) => {
    if (result.isConfirmed) {
      // Loading
      Swal.fire({
        title: "กำลังบันทึก...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      $.ajax({
        url: "../api/document/update.php",
        type: "POST",
        data: JSON.stringify(data),
        contentType: "application/json",
        dataType: "json",
        timeout: 10000,
        success: function (res) {
          if (res.success) {
            Swal.fire({
              icon: "success",
              title: "บันทึกสำเร็จ!",
              text: "สถานะเอกสารถูกบันทึกเรียบร้อยแล้ว",
              timer: 2000,
              showConfirmButton: false,
            });
            updateDocSummary();
          } else {
            Swal.fire({
              icon: "error",
              title: "ผิดพลาด",
              text: res.message || "ไม่สามารถบันทึกได้",
            });
          }
        },
        error: function (xhr) {
          Swal.fire({
            icon: "error",
            title: "ผิดพลาด",
            text: "ไม่สามารถบันทึกได้ (Status: " + xhr.status + ")",
          });
        },
      });
    }
  });
}

function loadFileList() {
  $.get(
    "../api/file/list.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      if (res.success && res.data && res.data.length > 0) {
        let html = '<div class="list-group list-group-flush">';
        res.data.forEach(function (f) {
          // กำหนดสีตามประเภทไฟล์
          let iconColor = getFileIconColor(f.file_type);
          let fileIcon = getFileIcon(f.file_type);

          html +=
            '<div class="list-group-item py-2 px-0 d-flex justify-content-between align-items-center">' +
            '<div class="d-flex align-items-center">' +
            '<span class="badge bg-' +
            iconColor +
            ' me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">' +
            '<i class="fas fa-' +
            fileIcon +
            '"></i>' +
            "</span>" +
            "<div>" +
            '<small class="fw-bold">' +
            (f.file_type || "ไม่ระบุ") +
            "</small>" +
            '<br><small class="text-muted">' +
            formatDate(f.created_at) +
            "</small>" +
            "</div>" +
            "</div>" +
            '<div class="btn-group btn-group-sm">' +
            '<a href="../' +
            f.file_path +
            '" target="_blank" class="btn btn-outline-info btn-sm" title="ดู"><i class="fas fa-eye"></i></a>' +
            '<button class="btn btn-outline-danger btn-sm" onclick="deleteFile(' +
            f.id +
            ')" title="ลบ"><i class="fas fa-trash"></i></button>' +
            "</div>" +
            "</div>";
        });
        html += "</div>";
        $("#fileList").html(html);
        $("#fileCount").text(res.data.length);
      } else {
        $("#fileList").html(
          '<div class="text-center py-4 text-muted">' +
            '<i class="fas fa-folder-open fa-3x mb-2"></i>' +
            "<p>ยังไม่มีเอกสาร</p>" +
            "</div>",
        );
        $("#fileCount").text("0");
      }
    },
    "json",
  ).fail(function () {
    $("#fileList").html('<p class="text-danger">โหลดไม่สำเร็จ</p>');
  });
}

function uploadFile() {
  let docType = $("#doc_type").val();
  let fileInput = $("#uploadFile")[0];

  // ตรวจสอบ
  if (!docType) {
    Swal.fire({
      icon: "warning",
      title: "กรุณาเลือกประเภทเอกสาร",
      toast: true,
      position: "top-end",
      timer: 2000,
      showConfirmButton: false,
    });
    $("#doc_type").focus();
    return;
  }

  if (!fileInput.files || !fileInput.files[0]) {
    Swal.fire({
      icon: "warning",
      title: "กรุณาเลือกไฟล์",
      toast: true,
      position: "top-end",
      timer: 2000,
      showConfirmButton: false,
    });
    return;
  }

  let file = fileInput.files[0];

  // ตรวจสอบขนาด
  if (file.size > 5 * 1024 * 1024) {
    Swal.fire({
      icon: "warning",
      title: "ไฟล์ขนาดเกิน",
      text: "ไฟล์ " + file.name + " มีขนาดใหญ่เกิน 5MB",
      toast: true,
      position: "top-end",
      timer: 3000,
      showConfirmButton: false,
    });
    return;
  }

  // ✅ ตรวจสอบนามสกุลไฟล์ (SweetAlert2)
  let allowed = ["jpg", "jpeg", "png", "pdf", "doc", "docx", "xls", "xlsx"];
  let ext = file.name.split(".").pop().toLowerCase();
  if (!allowed.includes(ext)) {
    Swal.fire({
      icon: "warning",
      title: "ประเภทไฟล์ไม่ถูกต้อง",
      html: `
                <p>ไฟล์ <strong>${file.name}</strong> ไม่ได้รับอนุญาต</p>
                <p class="text-muted">ประเภทที่รองรับ: <strong>${allowed.join(", ")}</strong></p>
            `,
      toast: true,
      position: "top-end",
      timer: 4000,
      showConfirmButton: false,
    });
    return;
  }

  let formData = new FormData();
  formData.append("case_id", CASE_ID);
  formData.append("file_type", docType); // ส่งประเภทเอกสาร
  formData.append("file", file);

  // แสดง loading
  let btn = $(".btn-success");
  let origText = btn.html();
  btn
    .html(
      '<span class="spinner-border spinner-border-sm"></span> กำลังอัปโหลด...',
    )
    .prop("disabled", true);

  $.ajax({
    url: "../api/file/upload.php",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (res) {
      btn.html(origText).prop("disabled", false);

      if (res.success) {
        alert("อัปโหลดสำเร็จ! 📄");
        $("#uploadFile").val("");
        loadFileList();
      } else {
        alert("ผิดพลาด: " + (res.message || "ไม่สามารถอัปโหลดได้"));
      }
    },
    error: function (xhr) {
      btn.html(origText).prop("disabled", false);
      alert("ไม่สามารถอัปโหลดได้ (Status: " + xhr.status + ")");
    },
  });
}

// ✅ ลบไฟล์ (SweetAlert2)
function deleteFile(id) {
  Swal.fire({
    title: "ยืนยันการลบเอกสาร?",
    text: "เอกสารนี้จะถูกลบออกจากระบบ ไม่สามารถกู้คืนได้",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: '<i class="fas fa-trash"></i> ลบ',
    cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      // แสดง loading
      Swal.fire({
        title: "กำลังลบเอกสาร...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      $.ajax({
        url: "../api/file/delete.php",
        type: "POST",
        data: JSON.stringify({
          id: id,
        }),
        contentType: "application/json",
        dataType: "json",
        timeout: 10000,
        success: function (res) {
          if (res.success) {
            Swal.fire({
              icon: "success",
              title: "ลบสำเร็จ!",
              text: "เอกสารถูกลบเรียบร้อยแล้ว",
              timer: 1500,
              showConfirmButton: false,
            });
            loadFileList();
          } else {
            Swal.fire({
              icon: "error",
              title: "ผิดพลาด",
              text: res.message || "ไม่สามารถลบเอกสารได้",
            });
          }
        },
        error: function (xhr) {
          Swal.fire({
            icon: "error",
            title: "ผิดพลาด",
            text: "ไม่สามารถลบเอกสารได้ (Status: " + xhr.status + ")",
          });
        },
      });
    }
  });
}

// ฟังก์ชั่นช่วยเหลือสำหรับแสดง icon ตามประเภทไฟล์
function getFileIcon(fileType) {
  if (!fileType) return "file";
  if (fileType.includes("บัตรประชาชน") || fileType.includes("ID"))
    return "id-card";
  if (fileType.includes("ทะเบียนบ้าน")) return "home";
  if (
    fileType.includes("สลิป") ||
    fileType.includes("เงินเดือน") ||
    fileType.includes("Statement")
  )
    return "file-invoice";
  if (fileType.includes("รับรอง")) return "file-contract";
  if (fileType.includes("สัญญา")) return "file-signature";
  if (fileType.includes("โฉนด")) return "scroll";
  if (fileType.includes("สมรส")) return "ring";
  if (fileType.includes("บัญชี") || fileType.includes("ธนาคาร"))
    return "university";
  return "file-alt";
}

function getFileIconColor(fileType) {
  if (!fileType) return "secondary";
  if (fileType.includes("บัตรประชาชน")) return "primary";
  if (fileType.includes("ทะเบียนบ้าน")) return "success";
  if (fileType.includes("สลิป") || fileType.includes("Statement"))
    return "info";
  if (fileType.includes("รับรอง")) return "warning";
  if (fileType.includes("สัญญา")) return "danger";
  return "secondary";
}

// ========== BANK (แก้ไขใหม่) ==========
function loadBankList() {
  let html =
    '<div class="row">' +
    // ฟอร์มส่งธนาคาร
    '<div class="col-md-6">' +
    '<div class="card">' +
    '<div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fas fa-university"></i> บันทึกการส่งธนาคาร</h6></div>' +
    '<div class="card-body">' +
    "<form>" +
    '<div class="mb-2">' +
    '<label class="form-label">ธนาคาร <span class="text-danger">*</span></label>' +
    '<select class="form-select" id="bank_name">' +
    '<option value="">เลือกธนาคาร</option>' +
    '<option value="ธนาคารกรุงเทพ">ธนาคารกรุงเทพ</option>' +
    '<option value="ธนาคารกสิกรไทย">ธนาคารกสิกรไทย</option>' +
    '<option value="ธนาคารกรุงไทย">ธนาคารกรุงไทย</option>' +
    '<option value="ธนาคารไทยพาณิชย์">ธนาคารไทยพาณิชย์</option>' +
    '<option value="ธนาคารออมสิน">ธนาคารออมสิน</option>' +
    '<option value="ธนาคารอาคารสงเคราะห์">ธนาคารอาคารสงเคราะห์</option>' +
    "</select>" +
    "</div>" +
    '<div class="mb-2">' +
    '<label class="form-label">วันที่ส่ง</label>' +
    '<input type="date" class="form-control" id="bank_date" value="' +
    getTodayDate() +
    '">' +
    "</div>" +
    '<div class="mb-2">' +
    '<label class="form-label">หมายเหตุ</label>' +
    '<textarea class="form-control" id="bank_note" rows="2" placeholder="หมายเหตุเพิ่มเติม"></textarea>' +
    "</div>" +
    '<button type="button" class="btn btn-primary" onclick="saveBank()"><i class="fas fa-save"></i> บันทึก</button>' +
    "</form>" +
    "</div>" +
    "</div>" +
    "</div>" +
    // ประวัติการส่งธนาคาร
    '<div class="col-md-6">' +
    '<div class="card">' +
    '<div class="card-header bg-info text-white"><h6 class="mb-0"><i class="fas fa-history"></i> ประวัติการส่งธนาคาร</h6></div>' +
    '<div class="card-body">' +
    '<div id="bankHistory" style="max-height: 400px; overflow-y: auto;">' +
    '<div class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> กำลังโหลด...</div>' +
    "</div>" +
    "</div>" +
    "</div>" +
    "</div>" +
    "</div>";

  $("#bankContent").html(html);

  // ✅ โหลดประวัติ
  loadBankHistory();
}

function saveBank() {
  let bankName = $("#bank_name").val();
  let submitDate = $("#bank_date").val();
  let note = $("#bank_note").val() || "";

  if (!bankName) {
    alert("กรุณาเลือกธนาคาร");
    $("#bank_name").focus();
    return;
  }

  if (!confirm("บันทึกการส่งธนาคาร " + bankName + "?")) return;

  let data = {
    case_id: CASE_ID,
    bank_name: bankName,
    submit_date: submitDate,
    note: note,
  };

  $.ajax({
    url: "../api/bank/submit.php",
    type: "POST",
    data: JSON.stringify(data),
    contentType: "application/json",
    dataType: "json",
    success: function (res) {
      console.log("Bank save response:", res);

      if (res.success) {
        alert("บันทึกสำเร็จ!");
        $("#bank_note").val("");
        // ✅ โหลดประวัติใหม่
        loadBankHistory();
      } else {
        alert("ผิดพลาด: " + (res.message || "ไม่สามารถบันทึกได้"));
      }
    },
    error: function (xhr, status, error) {
      console.error("Bank save error:", status, error);
      alert("ไม่สามารถบันทึกได้ (Status: " + xhr.status + ")");
    },
  });
}

function loadBankHistory() {
  console.log("Loading bank history for case:", CASE_ID);

  $.ajax({
    url: "../api/bank/list.php",
    type: "GET",
    data: {
      case_id: CASE_ID,
    },
    dataType: "json",
    timeout: 10000,
    success: function (res) {
      console.log("Bank history response:", res);

      if (res.success && res.data && res.data.length > 0) {
        let html = '<div class="list-group list-group-flush">';

        res.data.forEach(function (b, index) {
          html +=
            '<div class="list-group-item py-2">' +
            '<div class="d-flex justify-content-between align-items-start">' +
            "<div>" +
            "<strong>" +
            (b.bank_name || "ไม่ระบุ") +
            "</strong>" +
            '<br><small class="text-muted">📅 ' +
            (b.submit_date || "-") +
            "</small>" +
            (b.note ? "<br><small>" + b.note + "</small>" : "") +
            "</div>" +
            '<span class="badge bg-info">ครั้งที่ ' +
            (res.data.length - index) +
            "</span>" +
            "</div>" +
            "</div>";
        });

        html += "</div>";
        $("#bankHistory").html(html);
      } else {
        $("#bankHistory").html(
          '<div class="text-center py-4 text-muted">' +
            '<i class="fas fa-university fa-3x mb-2"></i>' +
            "<p>ยังไม่มีประวัติการส่งธนาคาร</p>" +
            "</div>",
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("Bank history error:", status, error);
      console.error("Response:", xhr.responseText);

      $("#bankHistory").html(
        '<div class="alert alert-danger py-2">' +
          "<small>❌ ไม่สามารถโหลดประวัติได้ (Status: " +
          xhr.status +
          ")</small>" +
          '<br><button class="btn btn-sm btn-outline-danger mt-1" onclick="loadBankHistory()">ลองใหม่</button>' +
          "</div>",
      );
    },
  });
}

// Helper: Get today date
function getTodayDate() {
  let today = new Date();
  let yyyy = today.getFullYear();
  let mm = String(today.getMonth() + 1).padStart(2, "0");
  let dd = String(today.getDate()).padStart(2, "0");
  return yyyy + "-" + mm + "-" + dd;
}

// ========== APPROVAL ==========
function loadApproval() {
  let html =
    '<form><div class="row">' +
    '<div class="col-md-3 mb-3"><label>วงเงินรวม</label><input type="number" class="form-control" id="ap_total" readonly></div>' +
    '<div class="col-md-3 mb-3"><label>วงเงินห้อง</label><input type="number" class="form-control" id="ap_room" oninput="calcApprovalTotal()"></div>' +
    '<div class="col-md-3 mb-3"><label>วงเงินประกัน</label><input type="number" class="form-control" id="ap_insurance" oninput="calcApprovalTotal()"></div>' +
    '<div class="col-md-3 mb-3"><label>วงเงินเฟอร์</label><input type="number" class="form-control" id="ap_furniture" oninput="calcApprovalTotal()"></div>' +
    '</div><div class="row"><div class="col-md-6 mb-3"><label>วันเซ็นสัญญา</label><input type="date" class="form-control" id="ap_contract"></div>' +
    '<div class="col-md-6 mb-3"><label>วันโอน</label><input type="date" class="form-control" id="ap_transfer"></div></div>' +
    '<div class="mb-3"><label>หมายเหตุ</label><textarea class="form-control" id="ap_note" rows="2"></textarea></div>' +
    '<button type="button" class="btn btn-primary" onclick="saveApproval()">บันทึก</button></form>';
  $("#approvalContent").html(html);
  $.get(
    "../api/approval/get.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      if (res.data) {
        let d = res.data;
        $("#ap_total").val(d.total_amount);
        $("#ap_room").val(d.room_amount);
        $("#ap_insurance").val(d.insurance_amount);
        $("#ap_furniture").val(d.furniture_amount);
        $("#ap_contract").val(
          d.contract_date ? d.contract_date.split(" ")[0] : "",
        );
        $("#ap_transfer").val(d.transfer_date);
        $("#ap_note").val(d.note || "");
      }
    },
    "json",
  );
}

function calcApprovalTotal() {
  let total =
    (parseFloat($("#ap_room").val()) || 0) +
    (parseFloat($("#ap_insurance").val()) || 0) +
    (parseFloat($("#ap_furniture").val()) || 0);
  $("#ap_total").val(total);
}

function saveApproval() {
  let data = {
    case_id: CASE_ID,
    total_amount: $("#ap_total").val(),
    room_amount: $("#ap_room").val(),
    insurance_amount: $("#ap_insurance").val(),
    furniture_amount: $("#ap_furniture").val(),
    contract_date: $("#ap_contract").val(),
    transfer_date: $("#ap_transfer").val(),
    note: $("#ap_note").val(),
  };
  $.post(
    "../api/approval/save.php",
    JSON.stringify(data),
    function (res) {
      alert(res.success ? "บันทึกสำเร็จ!" : "ผิดพลาด: " + res.message);
    },
    "json",
  );
}

// ========== DEBT (with dynamic items) ==========
let debtItemCount = 0;

function loadDebt() {
  let html =
    '<form><div class="row"><div class="col-md-6 mb-3"><label>วันที่ปิดหนี้</label><input type="datetime-local" class="form-control" id="debt_date"></div><div class="col-md-6 mb-3"><label>สถานที่</label><input type="text" class="form-control" id="debt_location"></div></div><div class="row"><div class="col-md-6 mb-3"><label>เจ้าหน้าที่</label><input type="text" class="form-control" id="debt_staff"></div></div><div class="mb-3"><label>หมายเหตุ</label><textarea class="form-control" id="debt_note" rows="2"></textarea></div><h6>รายการหนี้ <button type="button" class="btn btn-sm btn-success" onclick="addDebtItem()">+</button></h6><div id="debtItemsContainer"></div><div class="mt-2"><strong>รวม: <span id="debtTotal">0</span> บาท</strong></div><button type="button" class="btn btn-primary mt-3" onclick="saveDebt()">บันทึก</button></form>';
  $("#debtContent").html(html);
  // load existing debt data
  $.get(
    "../api/debt/get.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      if (res.data) {
        let d = res.data;
        $("#debt_date").val(d.clear_date ? d.clear_date.replace(" ", "T") : "");
        $("#debt_location").val(d.location || "");
        $("#debt_staff").val(d.staff_name || "");
        $("#debt_note").val(d.note || "");
        if (d.items)
          d.items.forEach((item) => addDebtItem(item.detail, item.amount));
      }
    },
    "json",
  );
}

function addDebtItem(detail = "", amount = "") {
  debtItemCount++;
  let html =
    '<div class="row mb-2 debt-item" id="debtRow' +
    debtItemCount +
    '"><div class="col-md-6"><input type="text" class="form-control" placeholder="รายละเอียดหนี้" value="' +
    detail +
    '" id="debt_detail_' +
    debtItemCount +
    '"></div><div class="col-md-4"><input type="number" class="form-control debt-amount" placeholder="จำนวนเงิน" value="' +
    amount +
    '" step="0.01" id="debt_amount_' +
    debtItemCount +
    '" oninput="calcDebtTotal()"></div><div class="col-md-2"><button class="btn btn-danger" onclick="$(\'#debtRow' +
    debtItemCount +
    "').remove();calcDebtTotal();\">ลบ</button></div></div>";
  $("#debtItemsContainer").append(html);
}

function calcDebtTotal() {
  let total = 0;
  $(".debt-amount").each(function () {
    total += parseFloat($(this).val()) || 0;
  });
  $("#debtTotal").text(numberFormat(total));
}

// ✅ บันทึกการปิดหนี้ (SweetAlert2)
function saveDebt() {
  let items = [];
  let hasError = false;

  $(".debt-item").each(function () {
    let detail = $(this).find('input[id^="debt_detail_"]').val();
    let amount = $(this).find('input[id^="debt_amount_"]').val();

    if (!detail || !amount) {
      hasError = true;
      return false;
    }
    items.push({
      detail: detail,
      amount: parseFloat(amount),
    });
  });

  // ❌ ข้อมูลไม่ครบ
  if (hasError || items.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "ข้อมูลไม่ครบถ้วน",
      text: "กรุณากรอกรายละเอียดและจำนวนเงินให้ครบทุกรายการ",
      toast: true,
      position: "top-end",
      timer: 3000,
      showConfirmButton: false,
    });
    return;
  }

  // คำนวณยอดรวม
  let totalAmount = items.reduce(
    (sum, item) => sum + parseFloat(item.amount || 0),
    0,
  );

  // ✅ Confirm
  Swal.fire({
    title: "บันทึกการปิดหนี้?",
    html: `
            <div class="text-start">
                <p>📋 <strong>จำนวนรายการ:</strong> ${items.length} รายการ</p>
                <p>💰 <strong>ยอดรวม:</strong> ${numberFormat(totalAmount)} บาท</p>
                ${$("#debt_location").val() ? "<p>📍 <strong>สถานที่:</strong> " + $("#debt_location").val() + "</p>" : ""}
                ${$("#debt_staff").val() ? "<p>👤 <strong>เจ้าหน้าที่:</strong> " + $("#debt_staff").val() + "</p>" : ""}
            </div>
        `,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#6c757d",
    confirmButtonText: '<i class="fas fa-save"></i> บันทึก',
    cancelButtonText: "ยกเลิก",
  }).then((result) => {
    if (result.isConfirmed) {
      // Loading
      Swal.fire({
        title: "กำลังบันทึก...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      let data = {
        case_id: CASE_ID,
        clear_date: $("#debt_date").val(),
        location: $("#debt_location").val(),
        staff_name: $("#debt_staff").val(),
        note: $("#debt_note").val(),
        items: items,
      };

      $.ajax({
        url: "../api/debt/save.php",
        type: "POST",
        data: JSON.stringify(data),
        contentType: "application/json",
        dataType: "json",
        timeout: 10000,
        success: function (res) {
          if (res.success) {
            Swal.fire({
              icon: "success",
              title: "บันทึกสำเร็จ!",
              text: "บันทึกการปิดหนี้เรียบร้อยแล้ว 💳",
              timer: 2000,
              showConfirmButton: false,
            });
            loadDebtHistory();
          } else {
            Swal.fire({
              icon: "error",
              title: "ผิดพลาด",
              text: res.message || "ไม่สามารถบันทึกได้",
            });
          }
        },
        error: function (xhr) {
          Swal.fire({
            icon: "error",
            title: "ผิดพลาด",
            text: "ไม่สามารถบันทึกได้ (Status: " + xhr.status + ")",
          });
        },
      });
    }
  });
}

// ========== MORTGAGE ==========
function loadMortgage() {
  let html =
    '<form><div class="row"><div class="col-md-6 mb-3"><label>วันที่จำนอง</label><input type="date" class="form-control" id="mort_date"></div><div class="col-md-6 mb-3"><label>ธนาคาร</label><select class="form-select" id="mort_bank"><option value="">เลือก</option><option>ธนาคารกรุงเทพ</option><option>ธนาคารกสิกรไทย</option><option>ธนาคารกรุงไทย</option><option>ธนาคารไทยพาณิชย์</option><option>ธนาคารออมสิน</option><option>ธนาคารอาคารสงเคราะห์</option></select></div></div><div class="row"><div class="col-md-6 mb-3"><label>ชื่อบัญชี</label><input type="text" class="form-control" id="mort_account_name"></div><div class="col-md-6 mb-3"><label>เลขบัญชี</label><input type="text" class="form-control" id="mort_account_number"></div></div><div class="row"><div class="col-md-6 mb-3"><label>วงเงินอนุมัติ</label><input type="number" class="form-control" id="mort_amount" step="0.01"></div></div><button type="button" class="btn btn-primary" onclick="saveMortgage()">บันทึก</button></form><hr><h6>ประวัติจำนอง</h6><div id="mortgageHistory"></div>';
  $("#mortgageContent").html(html);
  loadMortgageHistory();
  $.get(
    "../api/mortgage/get.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      if (res.data) {
        let m = res.data;
        $("#mort_date").val(m.mortgage_date);
        $("#mort_bank").val(m.bank_name);
        $("#mort_account_name").val(m.account_name);
        $("#mort_account_number").val(m.account_number);
        $("#mort_amount").val(m.approved_amount);
      }
    },
    "json",
  );
}

// ✅ บันทึกการจำนอง (SweetAlert2)
function saveMortgage() {
  let bankName = $("#mort_bank").val();
  let mortgageDate = $("#mort_date").val();
  let amount = $("#mort_amount").val();

  // ❌ Validate
  if (!bankName) {
    Swal.fire({
      icon: "warning",
      title: "กรุณาเลือกธนาคาร",
      toast: true,
      position: "top-end",
      timer: 2000,
      showConfirmButton: false,
    });
    $("#mort_bank").focus();
    return;
  }

  if (!mortgageDate) {
    Swal.fire({
      icon: "warning",
      title: "กรุณาระบุวันที่จำนอง",
      toast: true,
      position: "top-end",
      timer: 2000,
      showConfirmButton: false,
    });
    $("#mort_date").focus();
    return;
  }

  // ✅ Confirm
  Swal.fire({
    title: "บันทึกการจำนอง?",
    html: `
            <div class="text-start">
                <p>🏦 <strong>ธนาคาร:</strong> ${bankName}</p>
                <p>📅 <strong>วันที่:</strong> ${mortgageDate}</p>
                ${amount ? "<p>💰 <strong>วงเงิน:</strong> " + numberFormat(amount) + " บาท</p>" : ""}
                <p>🏠 <strong>บัญชี:</strong> ${$("#mort_account_number").val() || "-"}</p>
            </div>
        `,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#6c757d",
    confirmButtonText: '<i class="fas fa-save"></i> บันทึก',
    cancelButtonText: "ยกเลิก",
  }).then((result) => {
    if (result.isConfirmed) {
      // Loading
      Swal.fire({
        title: "กำลังบันทึก...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      let data = {
        case_id: CASE_ID,
        mortgage_date: mortgageDate,
        bank_name: bankName,
        account_name: $("#mort_account_name").val(),
        account_number: $("#mort_account_number").val(),
        approved_amount: amount || 0,
      };

      $.ajax({
        url: "../api/mortgage/save.php",
        type: "POST",
        data: JSON.stringify(data),
        contentType: "application/json",
        dataType: "json",
        timeout: 10000,
        success: function (res) {
          if (res.success) {
            Swal.fire({
              icon: "success",
              title: "บันทึกสำเร็จ!",
              text: "บันทึกการจำนองเรียบร้อยแล้ว 🏠",
              timer: 2000,
              showConfirmButton: false,
            });
            loadMortgageHistory();
          } else {
            Swal.fire({
              icon: "error",
              title: "ผิดพลาด",
              text: res.message || "ไม่สามารถบันทึกได้",
            });
          }
        },
        error: function (xhr) {
          Swal.fire({
            icon: "error",
            title: "ผิดพลาด",
            text: "ไม่สามารถบันทึกได้ (Status: " + xhr.status + ")",
          });
        },
      });
    }
  });
}

function loadMortgageHistory() {
  $.get(
    "../api/mortgage/list.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      if (res.success && res.data && res.data.length > 0) {
        let html = '<ul class="list-group">';
        res.data.forEach(function (m) {
          html +=
            '<li class="list-group-item"><strong>' +
            m.bank_name +
            "</strong> - " +
            m.mortgage_date +
            "<br>วงเงิน: " +
            numberFormat(m.approved_amount) +
            " บาท</li>";
        });
        html += "</ul>";
        $("#mortgageHistory").html(html);
      } else {
        $("#mortgageHistory").html('<p class="text-muted">ไม่มีประวัติ</p>');
      }
    },
    "json",
  );
}

// ========== INSPECTION ==========
function showInspectionForm() {
  $("#inspection_id").val("");
  $("#inspectionForm")[0].reset();
  $("#inspection_date").val(new Date().toISOString().slice(0, 16));
  $("#imagePreview").html("");
  $("#existingImages").hide().html("");
  $("#inspectionModalTitle").text("เพิ่มการตรวจห้อง");
  $("#inspectionModal").modal("show");
}

// พรีวิวรูปภาพก่อนอัปโหลด
function previewInspectionImages() {
  let files = $("#inspection_photos")[0].files;
  let html = "";

  if (files.length > 5) {
    alert("อัปโหลดได้สูงสุด 5 รูป");
    $("#inspection_photos").val("");
    return;
  }

  for (let i = 0; i < files.length; i++) {
    let file = files[i];

    // ตรวจสอบประเภทไฟล์
    if (!file.type.match("image.*")) {
      alert("กรุณาเลือกไฟล์รูปภาพเท่านั้น");
      continue;
    }

    // ตรวจสอบขนาด
    if (file.size > 5 * 1024 * 1024) {
      alert("ไฟล์ " + file.name + " มีขนาดเกิน 5MB");
      continue;
    }

    let reader = new FileReader();
    reader.onload = (function (f) {
      return function (e) {
        html +=
          '<div class="col-md-4 col-sm-6 mb-2">' +
          '<div class="position-relative">' +
          '<img src="' +
          e.target.result +
          '" class="img-thumbnail" style="height: 120px; width: 100%; object-fit: cover;">' +
          '<span class="position-absolute top-0 end-0 badge bg-primary m-1">' +
          (i + 1) +
          "</span>" +
          '<small class="d-block text-truncate">' +
          f.name +
          "</small>" +
          "</div>" +
          "</div>";
        $("#imagePreview").html(html);
      };
    })(file);
    reader.readAsDataURL(file);
  }
}

// ✅ บันทึกผลตรวจห้อง (SweetAlert2)
function saveInspection() {
  let round = $("#inspection_round").val();
  let date = $("#inspection_date").val();
  let status = $("#inspection_status").val();
  let note = $("#inspection_note").val().trim();

  // ❌ Validate
  if (!date) {
    Swal.fire({
      icon: "warning",
      title: "กรุณาระบุวันที่ตรวจ",
      toast: true,
      position: "top-end",
      timer: 2000,
      showConfirmButton: false,
    });
    $("#inspection_date").focus();
    return;
  }

  // ✅ Confirm
  let statusLabel = status === "pass" ? "✅ ผ่าน" : "❌ ไม่ผ่าน (พบ defect)";
  let photoCount = $("#inspection_photos")[0].files.length;

  Swal.fire({
    title: "บันทึกผลตรวจห้อง?",
    html: `
            <div class="text-start">
                <p>🔍 <strong>ครั้งที่:</strong> ${round}</p>
                <p>📅 <strong>วันที่:</strong> ${date}</p>
                <p>📊 <strong>สถานะ:</strong> ${statusLabel}</p>
                ${note ? "<p>📝 <strong>หมายเหตุ:</strong> " + note + "</p>" : ""}
                ${photoCount > 0 ? "<p>📸 <strong>รูปภาพ:</strong> " + photoCount + " รูป</p>" : ""}
            </div>
        `,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#6c757d",
    confirmButtonText: '<i class="fas fa-save"></i> บันทึก',
    cancelButtonText: "ยกเลิก",
  }).then((result) => {
    if (result.isConfirmed) {
      // Loading
      Swal.fire({
        title: "กำลังบันทึก...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      // สร้าง FormData (รองรับรูปภาพ)
      let formData = new FormData();
      formData.append("case_id", CASE_ID);
      formData.append("round", round);
      formData.append("inspect_date", date);
      formData.append("status", status);
      formData.append("note", note);

      // เพิ่มรูปภาพ
      let files = $("#inspection_photos")[0].files;
      for (let i = 0; i < files.length; i++) {
        formData.append("photos[]", files[i]);
      }

      $.ajax({
        url: "../api/inspection/save.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        timeout: 30000,
        success: function (res) {
          if (res.success) {
            Swal.fire({
              icon: "success",
              title: "บันทึกสำเร็จ!",
              text: "บันทึกผลตรวจห้องครั้งที่ " + round + " เรียบร้อยแล้ว 🔍",
              timer: 2000,
              showConfirmButton: false,
            });
            // ล้างฟอร์ม
            $("#inspection_note").val("");
            $("#inspection_photos").val("");
            $("#imagePreview").html("");
            // โหลดรายการใหม่
            loadInspectionList();
          } else {
            Swal.fire({
              icon: "error",
              title: "ผิดพลาด",
              text: res.message || "ไม่สามารถบันทึกได้",
            });
          }
        },
        error: function (xhr) {
          Swal.fire({
            icon: "error",
            title: "ผิดพลาด",
            text: "ไม่สามารถบันทึกได้ (Status: " + xhr.status + ")",
          });
        },
      });
    }
  });
}

// เปิดรูปภาพใน Modal (Bootstrap 5)
function openImageModal(imageSrc, title) {
  let modalHtml =
    '<div class="modal fade" id="imageViewerModal" tabindex="-1">' +
    '<div class="modal-dialog modal-xl modal-dialog-centered">' +
    '<div class="modal-content bg-dark">' +
    '<div class="modal-header border-0">' +
    (title ? '<h6 class="modal-title text-white">' + title + "</h6>" : "") +
    '<button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>' +
    "</div>" +
    '<div class="modal-body text-center p-2">' +
    '<img src="' +
    imageSrc +
    '" class="img-fluid" style="max-height: 85vh;" onerror="this.src=\'../assets/img/no-image.png\'">' +
    "</div>" +
    '<div class="modal-footer border-0 justify-content-center">' +
    '<a href="' +
    imageSrc +
    '" target="_blank" class="btn btn-light btn-sm"><i class="fas fa-external-link-alt"></i> ดูรูปเต็ม</a>' +
    '<a href="' +
    imageSrc +
    '" download class="btn btn-primary btn-sm"><i class="fas fa-download"></i> ดาวน์โหลด</a>' +
    "</div>" +
    "</div>" +
    "</div>" +
    "</div>";

  $("#imageViewerModal").remove();
  $("body").append(modalHtml);

  let modal = new bootstrap.Modal(document.getElementById("imageViewerModal"));
  modal.show();

  $("#imageViewerModal").on("hidden.bs.modal", function () {
    $(this).remove();
  });
}

function loadInspectionList() {
  $.get(
    "../api/inspection/list.php",
    {
      case_id: CASE_ID,
    },
    function (res) {
      if (res.success && res.data && res.data.length > 0) {
        let html = '<div class="row">';

        res.data.forEach(function (i) {
          let statusBadge =
            i.status === "pass"
              ? '<span class="badge bg-success">✅ ผ่าน</span>'
              : '<span class="badge bg-danger">❌ ไม่ผ่าน</span>';

          // แสดงรูปภาพ (ถ้ามี)
          let imagesHtml = "";
          // แสดงรูปภาพ (คลิกเพื่อดูใหญ่)
          if (i.photos && i.photos.length > 0) {
            imagesHtml = '<div class="row mt-2">';
            i.photos.forEach(function (photo) {
              imagesHtml +=
                '<div class="col-md-3 col-sm-4 col-6 mb-2">' +
                '<a href="#" onclick="openImageModal(\'../' +
                photo +
                "'); return false;\" " +
                'title="คลิกเพื่อดูรูปใหญ่" style="cursor: pointer;">' +
                '<img src="../' +
                photo +
                '" class="img-thumbnail" ' +
                'style="height: 100px; width: 100%; object-fit: cover; transition: transform 0.2s;" ' +
                "onmouseover=\"this.style.transform='scale(1.05)'\" " +
                "onmouseout=\"this.style.transform='scale(1)'\" " +
                "onerror=\"this.src='../assets/img/no-image.png'\">" +
                "</a>" +
                "</div>";
            });
            imagesHtml += "</div>";
          }

          html +=
            '<div class="col-md-6 mb-3">' +
            '<div class="card h-100">' +
            '<div class="card-header d-flex justify-content-between align-items-center py-2">' +
            '<h6 class="mb-0">ครั้งที่ ' +
            i.round +
            "</h6>" +
            statusBadge +
            "</div>" +
            '<div class="card-body py-2">' +
            '<p class="mb-1"><small><i class="far fa-calendar"></i> ' +
            formatDate(i.inspect_date) +
            "</small></p>" +
            (i.note
              ? '<p class="mb-1"><small>' + i.note + "</small></p>"
              : "") +
            imagesHtml +
            "</div>" +
            "</div>" +
            "</div>";
        });

        html += "</div>";
        $("#inspectionContent").html(html);
      } else {
        $("#inspectionContent").html(
          '<div class="text-center py-5 text-muted">' +
            '<i class="fas fa-search fa-4x mb-3"></i>' +
            "<h5>ยังไม่มีการตรวจห้อง</h5>" +
            '<p>คลิกปุ่ม "เพิ่มการตรวจ" เพื่อบันทึกผลตรวจห้อง</p>' +
            '<button class="btn btn-primary" onclick="showInspectionForm()">' +
            '<i class="fas fa-plus"></i> เพิ่มการตรวจครั้งแรก</button>' +
            "</div>",
        );
      }
    },
    "json",
  ).fail(function () {
    $("#inspectionContent").html(
      '<div class="alert alert-danger">โหลดข้อมูลไม่สำเร็จ</div>',
    );
  });
}

// ========== HELPERS ==========
function getGradeColor(g) {
  return (
    {
      "A+": "success",
      A: "primary",
      B: "warning",
    }[g] || "secondary"
  );
}

function getStatusColor(s) {
  return (
    {
      interested: "primary",
      high_interest: "success",
      pending: "warning",
      not_interested: "danger",
      cancelled: "dark",
    }[s] || "secondary"
  );
}
// ✅ Helper
function getStatusLabel(status) {
  const labels = {
    interested: "✅ สนใจ",
    high_interest: "🌟 สนใจมาก",
    pending: "⏳ รอดำเนินการ",
    negotiating: "💬 กำลังต่อรอง",
    site_visit: "🏢 นัดดูห้อง",
    document_submitted: "📄 ส่งเอกสารแล้ว",
    waiting_approval: "🏦 รออนุมัติ",
    not_interested: "❌ ไม่สนใจ",
    cancelled: "🚫 ยกเลิก",
  };
  return labels[status] || status;
}

function formatDate(d) {
  if (!d) return "-";
  try {
    return new Date(d).toLocaleDateString("th-TH", {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  } catch (e) {
    return d;
  }
}

function numberFormat(n) {
  if (!n && n !== 0) return "0";
  return new Intl.NumberFormat("th-TH", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(parseFloat(n));
}
