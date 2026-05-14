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
include_once '../../includes/line_notify.php';

$database = new Database();
$db = $database->getConnection();

// ✅ Debug: log ข้อมูลที่ได้รับ
$raw = file_get_contents("php://input");
// error_log("Follow Add Raw Input: " . $raw);

// ✅ ลอง decode JSON
$data = json_decode($raw);

// ✅ ถ้า JSON decode ไม่ได้ อาจเป็น FormData
if (!$data) {
    // ลองรับจาก $_POST (FormData)
    $case_id = isset($_POST['case_id']) ? intval($_POST['case_id']) : 0;
    $step = isset($_POST['step']) ? intval($_POST['step']) : 1;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $note = isset($_POST['note']) ? trim($_POST['note']) : '';
} else {
    // JSON
    $case_id = intval($data->case_id ?? 0);
    $step = intval($data->step ?? 1);
    $status = trim($data->status ?? '');
    $note = trim($data->note ?? '');
}

// ✅ ตรวจสอบข้อมูล
if (!$case_id) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณาระบุ Case ID",
        "debug" => ["case_id" => $case_id, "step" => $step, "status" => $status]
    ]);
    exit();
}

if (empty($status)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณาเลือกสถานะการติดตาม",
        "debug" => ["status" => $status]
    ]);
    exit();
}

try {
    // ✅ เพิ่มข้อมูล
    $query = "INSERT INTO follow_logs (case_id, step, status, note) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$case_id, $step, $status, $note]);

    // ✅ อัปเดต follow_count ใน cases
    $updateQuery = "UPDATE cases SET 
        follow_count = (SELECT COUNT(*) FROM follow_logs WHERE case_id = ?),
        follow_status = ?
        WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([$case_id, $status, $case_id]);

    sendLineToAllAdmins($db, "📞 ติดตามเคส #{$case_id}\n━━━━━━━━━━━━━\nครั้งที่: {$step}\nสถานะ: {$status}\n" . ($note ? "📝 {$note}" : ""));

    // ✅ Log activity
    if (isset($_SESSION['user_id'])) {
        $logQuery = "INSERT INTO case_activities (case_id, action, user_id) VALUES (?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $action = "Follow #{$step}: {$status}" . ($note ? " - {$note}" : "");
        $logStmt->execute([$case_id, $action, $_SESSION['user_id']]);
    }

    echo json_encode([
        "success" => true,
        "message" => "บันทึกการติดตามครั้งที่ {$step} สำเร็จ!",
        "data" => [
            "case_id" => $case_id,
            "step" => $step,
            "status" => $status
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "เกิดข้อผิดพลาดในฐานข้อมูล: " . $e->getMessage()
    ]);
}
?>