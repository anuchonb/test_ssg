<?php
// api/settings/save_settings.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/database.php';

// เฉพาะ admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "ไม่มีสิทธิ์"]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "ไม่มีข้อมูล"]);
    exit();
}

try {
    $db->beginTransaction();

    foreach ($data as $key => $value) {
        $setting_value = $key . ':' . $value;

        // ตรวจสอบว่ามี key นี้อยู่แล้วหรือไม่
        $checkStmt = $db->prepare("SELECT id FROM master_dropdowns WHERE type = 'system_settings' AND value LIKE ?");
        $checkStmt->execute([$key . ':%']);

        if ($checkStmt->rowCount() > 0) {
            // ✅ Update
            $updateStmt = $db->prepare("UPDATE master_dropdowns SET value = ? WHERE type = 'system_settings' AND value LIKE ?");
            $updateStmt->execute([$setting_value, $key . ':%']);
        } else {
            // ✅ Insert
            $insertStmt = $db->prepare("INSERT INTO master_dropdowns (type, value, is_active) VALUES ('system_settings', ?, 1)");
            $insertStmt->execute([$setting_value]);
        }
    }

    $db->commit();

    echo json_encode([
        "success" => true,
        "message" => "บันทึกการตั้งค่าสำเร็จ!"
    ]);

} catch (PDOException $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
    ]);
}
?>