<?php
// api/line/disconnect.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false]);
    exit();
}

try {
    $stmt = $db->prepare("UPDATE users SET line_user_id = NULL, line_display_name = NULL, line_connected_at = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    echo json_encode(["success" => true, "message" => "ยกเลิกการเชื่อมต่อแล้ว"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false]);
}
?>