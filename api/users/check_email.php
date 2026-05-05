<?php
// api/users/check_email.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$exclude_id = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : 0;

if($email) {
    try {
        // ตรวจสอบว่ามีอีเมลนี้ในระบบหรือไม่ (ยกเว้นตัวเอง)
        $query = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        if($exclude_id > 0) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        if($exclude_id > 0) {
            $stmt->bindParam(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        $result = $stmt->fetch();
        
        http_response_code(200);
        echo json_encode([
            "exists" => $result['count'] > 0,
            "message" => $result['count'] > 0 ? "อีเมลนี้มีในระบบแล้ว" : "อีเมลนี้ใช้ได้"
        ]);
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["exists" => false, "message" => "เกิดข้อผิดพลาด"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["exists" => false, "message" => "กรุณาระบุอีเมล"]);
}
?>