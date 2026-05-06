<?php
// api/users/create.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// เฉพาะ admin เท่านั้นที่เพิ่ม user ได้
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "คุณไม่มีสิทธิ์ในการเพิ่มผู้ใช้"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->name) && !empty($data->email) && !empty($data->password) && !empty($data->role)) {
    
    // ตรวจสอบ email ซ้ำ
    $check_query = "SELECT id FROM users WHERE email = :email LIMIT 1";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(":email", $data->email);
    $check_stmt->execute();
    
    if($check_stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "อีเมลนี้มีในระบบแล้ว"
        ]);
        exit();
    }
    
    // ตรวจสอบ role ที่อนุญาต
    $allowed_roles = ['admin_page', 'kpi', 'support', 'admin'];
    if(!in_array($data->role, $allowed_roles)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Role ไม่ถูกต้อง"
        ]);
        exit();
    }
    
    try {
        $query = "INSERT INTO users SET 
            name = :name,
            email = :email,
            password = :password,
            role = :role";

        $password_hash = password_hash($data->password, PASSWORD_DEFAULT); // ควรใช้ hash ใน production
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":name", $data->name);
        $stmt->bindParam(":email", $data->email);
        $stmt->bindParam(":password", $password_hash); // ใน production ใช้ password_hash()
        $stmt->bindParam(":role", $data->role);
        
        if($stmt->execute()) {
            $user_id = $db->lastInsertId();
            
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "เพิ่มผู้ใช้สำเร็จ",
                "user_id" => $user_id
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