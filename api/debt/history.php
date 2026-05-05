<?php
// api/debt/history.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;

if($case_id) {
    $query = "SELECT * FROM debt_clearings WHERE case_id = :case_id ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':case_id', $case_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll();
    
    http_response_code(200);
    echo json_encode(["success" => true, "data" => $data]);
}
?>