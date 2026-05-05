<?php
// api/logs/clear.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "ไม่มีสิทธิ์"]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
$days = isset($data->days) ? intval($data->days) : 30;

try {
    $query = "DELETE FROM case_activities WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':days', $days, PDO::PARAM_INT);
    $stmt->execute();
    
    $deleted = $stmt->rowCount();
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "ลบบันทึกที่เก่ากว่า {$days} วัน จำนวน {$deleted} รายการ"
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>