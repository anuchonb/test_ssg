<?php
// api/kpi/check.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/database.php';
include_once '../../includes/line_notify.php';

$database = new Database();
$db = $database->getConnection();

// อ่านข้อมูล
$raw = file_get_contents("php://input");
$data = json_decode($raw);

// ตรวจสอบข้อมูล
if (!$data || empty($data->case_id) || empty($data->result)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณากรอกข้อมูลให้ครบ (case_id, result)"
    ]);
    exit();
}

try {
    $case_id = intval($data->case_id);
    $checker_id = isset($data->checker_id) ? intval($data->checker_id) : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0);
    $result = trim($data->result);
    $reason = isset($data->reason) ? trim($data->reason) : '';
    $note = isset($data->note) ? trim($data->note) : '';

    // ตรวจสอบ result ถูกต้อง
    if (!in_array($result, ['pass', 'fail'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ผลการตรวจไม่ถูกต้อง (pass/fail)"]);
        exit();
    }

    // ✅ ตรวจสอบว่าเคยตรวจแล้วหรือไม่
    $checkQuery = "SELECT id FROM kpi_checks WHERE case_id = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$case_id]);

    if ($checkStmt->rowCount() > 0) {
        // ✅ อัปเดต
        $query = "UPDATE kpi_checks SET checker_id = ?, result = ?, reason = ? WHERE case_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$checker_id, $result, $reason, $case_id]);
        $message = "อัปเดตผล KPI สำเร็จ!";
    } else {
        // ✅ เพิ่มใหม่
        $query = "INSERT INTO kpi_checks (case_id, checker_id, result, reason) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$case_id, $checker_id, $result, $reason]);
        $message = "บันทึกผล KPI สำเร็จ!";
    }

    if ($result === 'fail') {
        $reasonText = $reason ? "เหตุผล: {$reason}" : "";
        sendLineToCaseOwner($db, $case_id, "❌ KPI ไม่ผ่าน!\n━━━━━━━━━━━━━\nCase #{$case_id}\n{$reasonText}");
        sendLineToAllAdmins($db, "❌ KPI ไม่ผ่าน #{$case_id}\n{$reasonText}");
    }

    // ✅ บันทึก Activity Log
    if (isset($_SESSION['user_id'])) {
        $action = "KPI Check: " . ($result === 'pass' ? '✅ ผ่าน' : '❌ ไม่ผ่าน') . ($reason ? " - {$reason}" : "");
        $logQuery = "INSERT INTO case_activities (case_id, action, user_id) VALUES (?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([$case_id, $action, $_SESSION['user_id']]);
    }

    echo json_encode([
        "success" => true,
        "message" => $message,
        "data" => [
            "case_id" => $case_id,
            "result" => $result,
            "reason" => $reason
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
    ]);
}
?>