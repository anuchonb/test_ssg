<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบ"]);
    exit();
}

try {
    $stmt = $db->prepare("UPDATE users SET line_id = ?, notify_enabled = ? WHERE id = ?");
    $stmt->execute([
        $data->line_id ?? null,
        $data->notify_enabled ?? 1,
        $_SESSION['user_id']
    ]);

    echo json_encode(["success" => true, "message" => "บันทึกสำเร็จ"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>