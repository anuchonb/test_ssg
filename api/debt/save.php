<?php
// api/debt/save.php
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

if (!$data || empty($data->case_id)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "กรุณาระบุ Case ID"]);
    exit();
}

try {
    $db->beginTransaction();

    $case_id = intval($data->case_id);
    $clear_date = !empty($data->clear_date) ? trim($data->clear_date) : date('Y-m-d H:i:s');
    $location = isset($data->location) ? trim($data->location) : '';
    $staff_name = isset($data->staff_name) ? trim($data->staff_name) : '';
    $note = isset($data->note) ? trim($data->note) : '';

    // ลบรายการหนี้เก่า (ถ้ามี)
    $oldDebt = $db->prepare("SELECT id FROM debt_clearings WHERE case_id = ?");
    $oldDebt->execute([$case_id]);
    $oldDebtId = $oldDebt->fetchColumn();

    if ($oldDebtId) {
        $db->prepare("DELETE FROM debt_items WHERE debt_id = ?")->execute([$oldDebtId]);
        $db->prepare("DELETE FROM debt_clearings WHERE id = ?")->execute([$oldDebtId]);
    }

    // เพิ่ม debt clearing
    $query = "INSERT INTO debt_clearings (case_id, clear_date, location, staff_name, note) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$case_id, $clear_date, $location, $staff_name, $note]);
    $debt_id = $db->lastInsertId();

    // เพิ่มรายการหนี้
    if (!empty($data->items) && is_array($data->items)) {
        $itemQuery = "INSERT INTO debt_items (debt_id, detail, amount) VALUES (?, ?, ?)";
        $itemStmt = $db->prepare($itemQuery);
        
        foreach ($data->items as $item) {
            if (!empty($item->detail) && isset($item->amount)) {
                $itemStmt->execute([$debt_id, trim($item->detail), floatval($item->amount)]);
            }
        }
    }

    $total = 0;
    foreach ($data->items as $item) $total += floatval($item->amount);
    sendLineToAllAdmins($db, "💳 ปิดหนี้แล้ว!\n━━━━━━━━━━━━━\nCase #{$case_id}\nจำนวน: " . count($data->items) . " รายการ\nรวม: " . number_format($total, 2) . " บาท");

    // Update document_steps debt_close_status
    $updateDoc = $db->prepare("UPDATE document_steps SET debt_close_status = 'done' WHERE case_id = ?");
    $updateDoc->execute([$case_id]);

    $db->commit();

    echo json_encode([
        "success" => true,
        "message" => "บันทึกการปิดหนี้สำเร็จ!",
        "debt_id" => $debt_id
    ]);

} catch (PDOException $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>