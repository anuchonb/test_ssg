<?php
// api/mortgage/save.php
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
    $mortgage_date = !empty($data->mortgage_date) ? trim($data->mortgage_date) : date('Y-m-d');
    $bank_name = !empty($data->bank_name) ? trim($data->bank_name) : '';
    $account_name = isset($data->account_name) ? trim($data->account_name) : '';
    $account_number = isset($data->account_number) ? trim($data->account_number) : '';
    $approved_amount = isset($data->approved_amount) ? floatval($data->approved_amount) : 0;

    $query = "INSERT INTO mortgages (case_id, mortgage_date, bank_name, account_name, account_number, approved_amount) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$case_id, $mortgage_date, $bank_name, $account_name, $account_number, $approved_amount]);

    echo json_encode([
        "success" => true,
        "message" => "บันทึกการจำนองสำเร็จ!",
        "id" => $db->lastInsertId()
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>