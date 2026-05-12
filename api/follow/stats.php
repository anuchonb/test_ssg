<?php
// api/follow/stats.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;

if($case_id) {
    $query = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status IN ('interested', 'high_interest') THEN 1 ELSE 0 END) as interested,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status IN ('cancelled', 'not_interested', 'not_qualified') THEN 1 ELSE 0 END) as rejected
    FROM follow_logs 
    WHERE case_id = :case_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':case_id', $case_id, PDO::PARAM_INT);
    $stmt->execute();
    $stats = $stmt->fetch();
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "total" => intval($stats['total']),
        "interested" => intval($stats['interested']),
        "pending" => intval($stats['pending']),
        "rejected" => intval($stats['rejected'])
    ]);
}
?>