<?php
// api/users/get.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// ตรวจสอบการ login
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบ"]);
    exit();
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($user_id) {
    try {
        $query = "SELECT id, name, email, role, created_at 
                  FROM users 
                  WHERE id = :id 
                  LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            // ไม่ส่ง password กลับไป
            unset($user['password']);
            
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "data" => $user
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "ไม่พบผู้ใช้"
            ]);
        }
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "เกิดข้อผิดพลาด"
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณาระบุ ID"
    ]);
}
?>