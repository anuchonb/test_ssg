<?php
// api/document/update.php
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
    $doc_status_1 = isset($data->doc_status_1) ? trim($data->doc_status_1) : '';
    $doc_status_2 = isset($data->doc_status_2) ? trim($data->doc_status_2) : '';
    $doc_status_3 = isset($data->doc_status_3) ? trim($data->doc_status_3) : '';
    $bank_name = isset($data->bank_name) ? trim($data->bank_name) : '';
    $bank_account = isset($data->bank_account) ? trim($data->bank_account) : '';
    $precheck_status = isset($data->precheck_status) ? trim($data->precheck_status) : '';
    $debt_close_status = isset($data->debt_close_status) ? trim($data->debt_close_status) : '';
    $note = isset($data->note) ? trim($data->note) : '';

    // ตรวจสอบว่ามีข้อมูลเดิมหรือไม่
    $checkQuery = "SELECT id FROM document_steps WHERE case_id = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$case_id]);

    if ($checkStmt->rowCount() > 0) {
        // Update
        $query = "UPDATE document_steps SET 
            doc_status_1 = ?, doc_status_2 = ?, doc_status_3 = ?,
            bank_name = ?, bank_account = ?,
            precheck_status = ?, debt_close_status = ?,
            note = ?
            WHERE case_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $doc_status_1, $doc_status_2, $doc_status_3,
            $bank_name, $bank_account,
            $precheck_status, $debt_close_status,
            $note, $case_id
        ]);
    } else {
        // Insert
        $query = "INSERT INTO document_steps 
            (case_id, doc_status_1, doc_status_2, doc_status_3, bank_name, bank_account, precheck_status, debt_close_status, note) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $case_id,
            $doc_status_1, $doc_status_2, $doc_status_3,
            $bank_name, $bank_account,
            $precheck_status, $debt_close_status,
            $note
        ]);
    }

    echo json_encode([
        "success" => true,
        "message" => "บันทึกสถานะเอกสารสำเร็จ!"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>