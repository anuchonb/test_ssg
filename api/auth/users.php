<?php
// api/auth/users.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

// ตรวจสอบสิทธิ์ (เฉพาะ admin)
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(array(
        "success" => false,
        "message" => "คุณไม่มีสิทธิ์ดูข้อมูลนี้"
    ));
    exit();
}

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT id, name, email, role, created_at 
              FROM users 
              ORDER BY created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $users = $stmt->fetchAll();
    
    // ลบ password ออกจาก response
    foreach($users as &$user) {
        unset($user['password']);
    }
    
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => $users,
        "total" => count($users)
    ));
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
    ));
}
?>