<?php
// api/projects/check_customers.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id) {
    $query = "SELECT 
        (SELECT COUNT(*) FROM customers WHERE project_id = :id) as customer_count,
        (SELECT COUNT(*) FROM cases cs JOIN customers c ON cs.customer_id = c.id WHERE c.project_id = :id2) as case_count";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':id2', $id);
    $stmt->execute();
    $result = $stmt->fetch();
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "customer_count" => intval($result['customer_count']),
        "case_count" => intval($result['case_count'])
    ]);
}
?>