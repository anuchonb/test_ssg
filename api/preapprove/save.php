<?php
// api/preapprove/save.php
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

$raw = file_get_contents("php://input");
$data = json_decode($raw);

// ตรวจสอบข้อมูล
if (!$data || empty($data->case_id)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณาระบุ Case ID"
    ]);
    exit();
}

try {
    $case_id = intval($data->case_id);
    $status = isset($data->status) ? trim($data->status) : 'processing';
    $approved_amount = isset($data->approved_amount) ? floatval($data->approved_amount) : 0;
    $note = isset($data->note) ? trim($data->note) : '';

    // ตรวจสอบสถานะที่อนุญาต
    $allowed_status = ['processing', 'approved', 'rejected'];
    if (!in_array($status, $allowed_status)) {
        $status = 'processing';
    }

    // ตรวจสอบว่ามีข้อมูลเดิมหรือไม่
    $checkQuery = "SELECT id FROM pre_approvals WHERE case_id = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$case_id]);

    if ($checkStmt->rowCount() > 0) {
        // Update ข้อมูลเดิม
        $query = "UPDATE pre_approvals SET 
            status = ?, 
            approved_amount = ?, 
            note = ?
            WHERE case_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$status, $approved_amount, $note, $case_id]);
        
        $message = "อัปเดต Pre-Approve สำเร็จ!";
    } else {
        // Insert ข้อมูลใหม่
        $query = "INSERT INTO pre_approvals (case_id, status, approved_amount, note) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$case_id, $status, $approved_amount, $note]);
        
        $message = "บันทึก Pre-Approve สำเร็จ!";
    }

    // Log activity
    if (isset($_SESSION['user_id'])) {
        $status_labels = [
            'processing' => 'กำลังดำเนินการ',
            'approved' => 'อนุมัติ',
            'rejected' => 'ปฏิเสธ'
        ];
        $status_label = isset($status_labels[$status]) ? $status_labels[$status] : $status;
        
        $action = "Pre-Approve {$status_label}" . ($approved_amount > 0 ? " วงเงิน " . number_format($approved_amount) . " บาท" : "");
        
        $logQuery = "INSERT INTO case_activities (case_id, action, user_id) VALUES (?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([$case_id, $action, $_SESSION['user_id']]);
    }

    echo json_encode([
        "success" => true,
        "message" => $message
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
    ]);
}
?>