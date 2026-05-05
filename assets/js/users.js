// assets/js/users.js

let currentUserId = null;
let searchTimeout = null;
let sessionUserId = 0;
let sessionUserRole = '';

$(document).ready(function() {
    // โหลดข้อมูล session ก่อน
    loadSessionData();
    
    loadUsers();
    loadUserStats();
    loadActivityLog();
    
    // Email check
    $('#user_email').on('blur', function() {
        const email = $(this).val().trim();
        if(email && !currentUserId) {
            checkEmailAvailability(email);
        }
    });
    
    // Password match check
    $('#user_confirm_password').on('input', function() {
        checkPasswordMatch();
    });
});

function loadSessionData() {
    $.ajax({
        url: '../api/auth/session.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                sessionUserId = response.user_id;
                sessionUserRole = response.user_role;
                console.log('Session loaded:', sessionUserId);
            }
        }
    });
}

function renderUsersTable(users) {
    let html = '';
    
    users.forEach(u => {
        const roleLabel = getRoleLabel(u.role);
        const roleColor = getRoleColor(u.role);
        // ใช้ CURRENT_USER_ID แทน <?php echo $_SESSION['user_id']; ?>
        const isCurrentUser = u.id == CURRENT_USER_ID;
        
        html += `
            <tr ${isCurrentUser ? 'class="table-active"' : ''}>
                <td>${u.id} ${isCurrentUser ? '<br><small class="text-muted">(คุณ)</small>' : ''}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-${roleColor} text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                             style="width: 35px; height: 35px; font-size: 14px;">
                            ${u.name ? u.name.charAt(0).toUpperCase() : '?'}
                        </div>
                        <div>
                            <strong>${u.name || 'ไม่ระบุ'}</strong>
                            <br><small class="text-muted">${u.email}</small>
                        </div>
                    </div>
                </td>
                <td>${u.email}</td>
                <td>
                    <span class="badge bg-${roleColor}">${roleLabel}</span>
                </td>
                <td>
                    <span class="badge bg-primary">${u.total_cases || 0} เคส</span>
                </td>
                <td><small>${formatDateThai(u.created_at)}</small></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="viewUser(${u.id})" title="ดูข้อมูล">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning" onclick="editUser(${u.id})" title="แก้ไข">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${!isCurrentUser ? `
                            <button class="btn btn-secondary" onclick="forceLogoutUser(${u.id}, '${escapeHtml(u.name)}')" title="บังคับออกจากระบบ">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                            <button class="btn btn-danger" onclick="confirmDeleteUser(${u.id}, '${escapeHtml(u.name)}', ${u.total_cases || 0})" title="ลบ">
                                <i class="fas fa-trash"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#usersTableBody').html(html);
}

function editUser(id) {
    if(id == CURRENT_USER_ID) {
        window.location.href = 'profile.php';
        return;
    }
    
    // ... rest of function
}

function confirmDeleteUser(id, name, caseCount) {
    if(id == CURRENT_USER_ID) {
        alert('ไม่สามารถลบบัญชีของตัวเองได้');
        return;
    }
    
    // ... rest of function
}