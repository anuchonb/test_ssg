<?php
// api/auth/login.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// รับข้อมูล JSON
$data = json_decode(file_get_contents("php://input"));

// ตรวจสอบว่ามี email และ password
if(!empty($data->email) && !empty($data->password)) {
    try {
        // ค้นหาผู้ใช้จาก email
        $query = "SELECT id, name, email, password, role, created_at FROM users WHERE email = :email  LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":email", $data->email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            // ในระบบจริงควรใช้ password_verify()
            
            $password_hash = password_hash($data->password, PASSWORD_DEFAULT);
            
            if(password_verify($data->password, $user['password'])) {
                
                // เก็บข้อมูลลง Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();
                
                unset($user['password']);
                
                // สร้าง token (สำหรับการใช้ REST API)
                $token = bin2hex(random_bytes(32));
                
                // บันทึก log การ login
                $log_query = "INSERT INTO case_activities 
                              (case_id, action, user_id) 
                              VALUES (NULL, :action, :user_id)";
                $log_stmt = $db->prepare($log_query);
                $action = "User Login: {$user['email']}";
                $log_stmt->bindParam(":action", $action);
                $log_stmt->bindParam(":user_id", $user['id']);
                $log_stmt->execute();
                
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "message" => "เข้าสู่ระบบสำเร็จ",
                    "user" => array(
                        "id" => $user['id'],
                        "name" => $user['name'],
                        "email" => $user['email'],
                        "role" => $user['role']
                    ),
                    "token" => $token,
                    "permissions" => getUserPermissions($user['role'])
                ));
                
            } else {
                // รหัสผ่านไม่ถูกต้อง
                http_response_code(401);
                echo json_encode(array(
                    "success" => false,
                    "message" => "รหัสผ่านไม่ถูกต้อง"
                ));
            }
            
        } else {
            // ไม่พบผู้ใช้
            http_response_code(401);
            echo json_encode(array(
                "success" => false,
                "message" => "ไม่พบบัญชีผู้ใช้นี้"
            ));
        }
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(array(
            "success" => false,
            "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
        ));
    }
    
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "กรุณากรอกอีเมลและรหัสผ่าน"
    ));
}

/**
 * กำหนดสิทธิ์ตาม Role
 */
function getUserPermissions($role) {
    $permissions = array(
        'admin' => array(
            'can_view_all' => true,
            'can_create_customer' => true,
            'can_edit_customer' => true,
            'can_delete_customer' => true,
            'can_create_case' => true,
            'can_follow' => true,
            'can_kpi_check' => true,
            'can_preapprove' => true,
            'can_manage_documents' => true,
            'can_submit_bank' => true,
            'can_approve' => true,
            'can_manage_debt' => true,
            'can_manage_mortgage' => true,
            'can_inspect' => true,
            'can_manage_users' => true,
            'can_view_dashboard' => true,
            'can_export_data' => true
        ),
        'admin_page' => array(
            'can_view_all' => false,
            'can_create_customer' => true,
            'can_edit_customer' => true,
            'can_create_case' => true,
            'can_follow' => false,
            'can_kpi_check' => false,
            'can_preapprove' => false,
            'can_manage_documents' => false,
            'can_submit_bank' => false,
            'can_approve' => false,
            'can_manage_debt' => false,
            'can_manage_mortgage' => false,
            'can_inspect' => false,
            'can_view_dashboard' => false
        ),
        'kpi' => array(
            'can_view_all' => false,
            'can_create_customer' => false,
            'can_edit_customer' => false,
            'can_create_case' => false,
            'can_follow' => false,
            'can_kpi_check' => true,
            'can_preapprove' => false,
            'can_manage_documents' => false,
            'can_submit_bank' => false,
            'can_approve' => false,
            'can_manage_debt' => false,
            'can_manage_mortgage' => false,
            'can_inspect' => false,
            'can_view_dashboard' => true
        ),
        'support' => array(
            'can_view_all' => false,
            'can_create_customer' => false,
            'can_edit_customer' => false,
            'can_create_case' => false,
            'can_follow' => false,
            'can_kpi_check' => false,
            'can_preapprove' => true,
            'can_manage_documents' => true,
            'can_submit_bank' => true,
            'can_approve' => true,
            'can_manage_debt' => true,
            'can_manage_mortgage' => true,
            'can_inspect' => true,
            'can_view_dashboard' => true
        )
    );
    
    return isset($permissions[$role]) ? $permissions[$role] : array();
}
?>