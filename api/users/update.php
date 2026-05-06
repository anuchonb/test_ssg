<?php
// api/users/update.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// เฉพาะ admin หรือเจ้าของ account
if(!isset($_SESSION['user_role']) || 
   ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_id'] != ($data->id ?? 0))) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "คุณไม่มีสิทธิ์ในการแก้ไข"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->name) && !empty($data->email)) {
    
    try {
        // ตรวจสอบ email ซ้ำ (ยกเว้นตัวเอง)
        $check_query = "SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(":email", $data->email);
        $check_stmt->bindParam(":id", $data->id);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            http_response_code(409);
            echo json_encode([
                "success" => false,
                "message" => "อีเมลนี้มีในระบบแล้ว"
            ]);
            exit();
        }
        
        $query = "UPDATE users SET 
            name = :name,
            email = :email";
        
        // ถ้ามีการเปลี่ยนรหัสผ่าน
        $hashed_password = null;
        if(!empty($data->password)) {
            $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);
            $query .= ", password = :password";
        }
        
        // เฉพาะ admin ที่เปลี่ยน role ได้
        if($_SESSION['user_role'] === 'admin' && !empty($data->role)) {
            $query .= ", role = :role";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":name", $data->name);
        $stmt->bindParam(":email", $data->email);
        $stmt->bindParam(":id", $data->id);
        
        if(!empty($data->password)) {
            $stmt->bindParam(":password", $hashed_password);
        }
        
        if($_SESSION['user_role'] === 'admin' && !empty($data->role)) {
            $stmt->bindParam(":role", $data->role);
        }
        
        if($stmt->execute()) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "อัพเดทข้อมูลสำเร็จ"
            ]);
        }
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
        ]);
    }
    
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"
    ]);
}
?>