<?php
// api/customers/delete.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Admin + Admin Page ลบได้
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'admin_page'])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "คุณไม่มีสิทธิ์ในการลบ"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->id)) {
    echo json_encode(["success" => false, "message" => "กรุณาระบุ ID"]);
    exit();
}

$customer_id = intval($data->id);

try {
    $db->beginTransaction();

    // ✅ ดึง case_id ทั้งหมดของลูกค้านี้
    $stmt = $db->prepare("SELECT id FROM cases WHERE customer_id = ?");
    $stmt->execute([$customer_id]);
    $case_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // ✅ ลบข้อมูลที่เกี่ยวข้องกับเคส (ถ้ามี)
    if (!empty($case_ids)) {
        foreach ($case_ids as $case_id) {
            // ลบ debt_items ผ่าน debt_clearings (debt_items ไม่มี case_id)
            $debt_ids = $db->prepare("SELECT id FROM debt_clearings WHERE case_id = ?")->execute([$case_id])->fetchAll(PDO::FETCH_COLUMN);
            foreach ($debt_ids as $debt_id) {
                $db->prepare("DELETE FROM debt_items WHERE debt_id = ?")->execute([$debt_id]);
            }

            // ✅ ตารางที่มี case_id โดยตรง
            $tables = ['follow_logs', 'kpi_checks', 'pre_approvals', 'document_steps', 
                       'bank_submissions', 'approvals', 'debt_clearings', 
                       'mortgages', 'inspections', 'case_activities', 'files'];
            
            foreach ($tables as $table) {
                $db->prepare("DELETE FROM {$table} WHERE case_id = ?")->execute([$case_id]);
            }
        }
    }

    // ลบเคส
    $db->prepare("DELETE FROM cases WHERE customer_id = ?")->execute([$customer_id]);

    // ลบลูกค้า
    $db->prepare("DELETE FROM customers WHERE id = ?")->execute([$customer_id]);

    $db->commit();

    echo json_encode(["success" => true, "message" => "ลบข้อมูลสำเร็จ"]);

} catch (PDOException $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>