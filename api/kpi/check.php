<?php
// api/kpi/check.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
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
if(!$data) {
    echo json_encode([
        "success" => false, 
        "message" => "ข้อมูลไม่ถูกต้อง"
    ]);
    exit();
}

if(empty($data->case_id)) {
    echo json_encode([
        "success" => false, 
        "message" => "กรุณาระบุ Case ID"
    ]);
    exit();
}

if(empty($data->result)) {
    echo json_encode([
        "success" => false, 
        "message" => "กรุณาเลือกผลการตรวจ (ผ่าน/ไม่ผ่าน)"
    ]);
    exit();
}

try {
    $case_id = intval($data->case_id);
    $checker_id = isset($data->checker_id) ? intval($data->checker_id) : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1);
    $result = trim($data->result);
    $reason = isset($data->reason) ? trim($data->reason) : '';
    $note = isset($data->note) ? trim($data->note) : '';

    // Insert KPI check - ใช้ ? แบบง่าย
    $query = "INSERT INTO kpi_checks (case_id, checker_id, result, reason) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$case_id, $checker_id, $result, $reason]);

    // Log activity
    if(isset($_SESSION['user_id'])) {
        $action = "KPI Check: " . ($result === 'pass' ? 'ผ่าน' : 'ไม่ผ่าน') . ($reason ? " - {$reason}" : "");
        $logQuery = "INSERT INTO case_activities (case_id, action, user_id) VALUES (?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([$case_id, $action, $_SESSION['user_id']]);
    }

    echo json_encode([
        "success" => true,
        "message" => "บันทึกผล KPI เรียบร้อย!"
    ]);

} catch(PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>