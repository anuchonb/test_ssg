<?php
// api/logs/stats.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

$whereClause = "";
$params = [];

if($date_from) {
    $whereClause .= " AND DATE(created_at) >= :date_from";
    $params[':date_from'] = $date_from;
}
if($date_to) {
    $whereClause .= " AND DATE(created_at) <= :date_to";
    $params[':date_to'] = $date_to;
}

try {
    $query = "SELECT 
        COUNT(*) as total_logs,
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_logs,
        COUNT(DISTINCT user_id) as active_users,
        SUM(CASE WHEN action LIKE '%Error%' OR action LIKE '%ผิดพลาด%' THEN 1 ELSE 0 END) as errors
        FROM case_activities 
        WHERE 1=1 {$whereClause}";
    
    $stmt = $db->prepare($query);
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $stats = $stmt->fetch();
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "total_logs" => intval($stats['total_logs']),
        "today_logs" => intval($stats['today_logs']),
        "active_users" => intval($stats['active_users']),
        "errors" => intval($stats['errors'])
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false]);
}
?>