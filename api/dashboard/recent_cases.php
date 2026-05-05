<?php
// api/dashboard/recent_cases.php
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;

try {
    $query = "
        SELECT cs.id, cs.status, cs.created_at, c.name as customer_name
        FROM cases cs
        JOIN customers c ON cs.customer_id = c.id
        ORDER BY cs.created_at DESC
        LIMIT :limit";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $data = $stmt->fetchAll();
    
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => $data
    ));
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(array("success" => false));
}
?>