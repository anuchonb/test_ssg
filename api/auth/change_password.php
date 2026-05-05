<?php
// api/auth/change_password.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

// ตรวจสอบการ login
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(array(
        "success" => false,
        "message" => "กรุณาเข้าสู่ระบบก่อน"
    ));
    exit();
}

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->current_password) && !empty($data->new_password)) {
    
    try {
        // ตรวจสอบรหัสผ่านปัจจุบัน
        $query = "SELECT password FROM users WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $_SESSION['user_id']);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if($data->current_password !== $user['password']) {
            http_response_code(400);
            echo json_encode(array(
                "success" => false,
                "message" => "รหัสผ่านปัจจุบันไม่ถูกต้อง"
            ));
            exit();
        }
        
        // เปลี่ยนรหัสผ่าน
        $update_query = "UPDATE users SET password = :password WHERE id = :id";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bindParam(":password", $data->new_password);
        $update_stmt->bindParam(":id", $_SESSION['user_id']);
        $update_stmt->execute();
        
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => "เปลี่ยนรหัสผ่านสำเร็จ"
        ));
        
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
        "message" => "กรุณากรอกรหัสผ่านทั้งสองช่อง"
    ));
}
?>