<?php
// api/bank/submit.php
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

$raw = file_get_contents("php://input");
$data = json_decode($raw);

if (!$data || empty($data->case_id) || empty($data->bank_name)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณากรอกข้อมูลให้ครบ (case_id, bank_name)"
    ]);
    exit();
}

try {
    $case_id = intval($data->case_id);
    $bank_name = trim($data->bank_name);
    $submit_date = !empty($data->submit_date) ? trim($data->submit_date) : date('Y-m-d');
    $note = isset($data->note) ? trim($data->note) : '';

    $query = "INSERT INTO bank_submissions (case_id, bank_name, submit_date, note) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$case_id, $bank_name, $submit_date, $note]);

    $message = "🏦 ส่งธนาคารแล้ว!\n━━━━━━━━━━━━━\nCase #{$case_id}\nธนาคาร: {$bank_name}\nวันที่: {$submit_date}";
    if (!empty($note)) $message .= "\n📝 {$note}";
    sendLineToAllAdmins($db, $message);

    // Log activity
    if (isset($_SESSION['user_id'])) {
        $action = "Bank submitted: {$bank_name} ({$submit_date})";
        $logQuery = "INSERT INTO case_activities (case_id, action, user_id) VALUES (?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([$case_id, $action, $_SESSION['user_id']]);
    }

    echo json_encode([
        "success" => true,
        "message" => "บันทึกการส่งธนาคาร {$bank_name} สำเร็จ!",
        "id" => $db->lastInsertId()
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>