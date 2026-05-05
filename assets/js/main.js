let currentCustomerId = null;

// Check customer by phone number (Auto-fill feature)
function checkCustomerByPhone(phone) {
    if(phone.length >= 9) {
        $.ajax({
            url: '../api/customer/get.php',
            type: 'GET',
            data: { phone: phone },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    // Auto-fill form with existing customer data
                    const customer = response.data;
                    fillCustomerForm(customer);
                    currentCustomerId = customer.id;
                    
                    $('#phoneFeedback').html(
                        '<span class="text-success">✅ พบข้อมูลลูกค้าเดิม</span>'
                    );
                    $('#createCaseBtn').prop('disabled', false);
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'พบข้อมูลลูกค้า',
                        text: 'ระบบพบข้อมูลลูกค้าเดิม ต้องการอัพเดทข้อมูลหรือไม่?',
                        showCancelButton: true,
                        confirmButtonText: 'อัพเดท',
                        cancelButtonText: 'ไม่'
                    });
                } else {
                    $('#phoneFeedback').html(
                        '<span class="text-muted">🆕 ลูกค้าใหม่</span>'
                    );
                    currentCustomerId = null;
                    $('#createCaseBtn').prop('disabled', true);
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด',
                    text: 'ไม่สามารถตรวจสอบข้อมูลลูกค้าได้'
                });
            }
        });
    }
}

// Fill form with customer data
function fillCustomerForm(customer) {
    $('#name').val(customer.name);
    $('#facebook').val(customer.facebook);
    $('#line_id').val(customer.line_id);
    $('#page_name').val(customer.page_name);
    $('#channel').val(customer.channel);
    $('#grade').val(customer.grade);
    $('#project_id').val(customer.project_id);
    $('#price').val(customer.price);
    $('#cashback').val(customer.cashback);
    $('#living_type').val(customer.living_type);
    $('#zone').val(customer.zone);
    $('#company_name').val(customer.company_name);
    $('#work_age_month').val(customer.work_age_month);
    $('#welfare').val(customer.welfare);
    $('#debt_status').val(customer.debt_status);
}

// Save customer
function saveCustomer() {
    Swal.fire({
        title: 'ยืนยันการบันทึก?',
        text: "คุณต้องการบันทึกข้อมูลลูกค้านี้หรือไม่?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = {
                name: $('#name').val(),
                phone: $('#phone').val(),
                facebook: $('#facebook').val(),
                line_id: $('#line_id').val(),
                page_name: $('#page_name').val(),
                channel: $('#channel').val(),
                grade: $('#grade').val(),
                project_id: $('#project_id').val(),
                price: $('#price').val(),
                cashback: $('#cashback').val(),
                living_type: $('#living_type').val(),
                zone: $('#zone').val(),
                company_name: $('#company_name').val(),
                work_age_month: $('#work_age_month').val(),
                welfare: $('#welfare').val(),
                debt_status: $('#debt_status').val(),
                created_by: 1 // Should be dynamic from session
            };
            
            $.ajax({
                url: '../api/customer/create.php',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        currentCustomerId = response.customer_id;
                        $('#createCaseBtn').prop('disabled', false);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: 'บันทึกข้อมูลลูกค้าเรียบร้อยแล้ว',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: 'ไม่สามารถบันทึกข้อมูลได้'
                    });
                }
            });
        }
    });
}

// Create case
function createCase() {
    if(!currentCustomerId) {
        Swal.fire({
            icon: 'warning',
            title: 'คำเตือน',
            text: 'กรุณาบันทึกข้อมูลลูกค้าก่อนส่งเคส'
        });
        return;
    }
    
    Swal.fire({
        title: 'ยืนยันการส่งเคส?',
        text: "คุณต้องการส่งเคสให้ลูกค้าคนนี้หรือไม่?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ส่งเคส',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../api/case/create.php',
                type: 'POST',
                data: JSON.stringify({
                    customer_id: currentCustomerId,
                    owner_id: 1 // Should be dynamic from session
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ส่งเคสสำเร็จ!',
                            text: 'Case ID: ' + response.case_id,
                            confirmButtonText: 'ตกลง'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: 'ไม่สามารถสร้างเคสได้'
                    });
                }
            });
        }
    });
}

// Load dropdown options
function loadDropdowns() {
    // Load channels
    $.getJSON('../api/master/get.php?type=channel', function(data) {
        const select = $('#channel');
        data.forEach(item => {
            select.append(`<option value="${item.value}">${item.value}</option>`);
        });
    });
    
    // Load projects
    $.getJSON('../api/master/get.php?type=project', function(data) {
        const select = $('#project_id');
        data.forEach(item => {
            select.append(`<option value="${item.id}">${item.name}</option>`);
        });
    });
    
    // Load zones
    $.getJSON('../api/master/get.php?type=zone', function(data) {
        const select = $('#zone');
        data.forEach(item => {
            select.append(`<option value="${item.value}">${item.value}</option>`);
        });
    });
    
    // Load welfare options
    $.getJSON('../api/master/get.php?type=welfare', function(data) {
        const select = $('#welfare');
        data.forEach(item => {
            select.append(`<option value="${item.value}">${item.value}</option>`);
        });
    });
}

// Initialize on page load
$(document).ready(function() {
    loadDropdowns();
    
    // Phone number formatting
    $('#phone').on('input', function() {
        let phone = $(this).val().replace(/[^0-9]/g, '');
        if(phone.length > 10) phone = phone.substring(0, 10);
        $(this).val(phone);
    });
});