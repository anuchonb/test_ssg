<?php
// api/approval/save.php
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
    echo json_encode(["success" => false, "message" => "กรุณาระบุ Case ID"]);
    exit();
}

try {
    $case_id = intval($data->case_id);
    $total_amount = isset($data->total_amount) ? floatval($data->total_amount) : 0;
    $room_amount = isset($data->room_amount) ? floatval($data->room_amount) : 0;
    $insurance_amount = isset($data->insurance_amount) ? floatval($data->insurance_amount) : 0;
    $furniture_amount = isset($data->furniture_amount) ? floatval($data->furniture_amount) : 0;
    $contract_date = !empty($data->contract_date) ? trim($data->contract_date) : null;
    $transfer_date = !empty($data->transfer_date) ? trim($data->transfer_date) : null;
    $note = isset($data->note) ? trim($data->note) : '';

    // ตรวจสอบว่ามีข้อมูลเดิมหรือไม่
    $checkQuery = "SELECT id FROM approvals WHERE case_id = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$case_id]);

    if ($checkStmt->rowCount() > 0) {
        // Update
        $query = "UPDATE approvals SET 
            total_amount = ?, room_amount = ?, insurance_amount = ?, 
            furniture_amount = ?, contract_date = ?, transfer_date = ?, note = ?
            WHERE case_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$total_amount, $room_amount, $insurance_amount, $furniture_amount, 
                        $contract_date, $transfer_date, $note, $case_id]);
    } else {
        // Insert
        $query = "INSERT INTO approvals 
            (case_id, total_amount, room_amount, insurance_amount, furniture_amount, contract_date, transfer_date, note) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$case_id, $total_amount, $room_amount, $insurance_amount, $furniture_amount, 
                        $contract_date, $transfer_date, $note]);
    }

    echo json_encode([
        "success" => true,
        "message" => "บันทึกผลอนุมัติสำเร็จ!"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>