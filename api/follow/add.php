<?php
// api/follow/add.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// อ่านข้อมูล
$raw = file_get_contents("php://input");
$data = json_decode($raw);

// ตรวจสอบข้อมูล
if (!$data || empty($data->case_id) || empty($data->status)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
    exit();
}

// กำหนดค่าตัวแปร
$case_id = intval($data->case_id);
$step = isset($data->step) ? intval($data->step) : 1;
$status = trim($data->status);
$note = isset($data->note) ? trim($data->note) : '';

try {
    // เพิ่มข้อมูล - ใช้ ? แบบง่าย ไม่มีปัญหาเรื่อง named parameter
    $query = "INSERT INTO follow_logs (case_id, step, status, note) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$case_id, $step, $status, $note]);

    // อัปเดต follow_count ในตาราง cases
    $updateQuery = "UPDATE cases SET 
        follow_count = (SELECT COUNT(*) FROM follow_logs WHERE case_id = ?),
        follow_status = ?
        WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([$case_id, $status, $case_id]);

    echo json_encode([
        "success" => true,
        "message" => "บันทึกการติดตามครั้งที่ {$step} สำเร็จ!"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
    ]);
}
?>