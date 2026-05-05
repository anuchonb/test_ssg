<?php
// api/dashboard/recent_activities.php
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

try {
    $query = "
        SELECT ca.action, ca.created_at, u.name as user_name
        FROM case_activities ca
        LEFT JOIN users u ON ca.user_id = u.id
        WHERE ca.case_id IS NOT NULL OR ca.action LIKE '%Login%'
        ORDER BY ca.created_at DESC
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