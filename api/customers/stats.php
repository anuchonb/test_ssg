<?php
// api/customers/stats.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN grade = 'A+' THEN 1 ELSE 0 END) as grade_a_plus,
        SUM(CASE WHEN grade = 'A' THEN 1 ELSE 0 END) as grade_a,
        SUM(CASE WHEN grade = 'B' THEN 1 ELSE 0 END) as grade_b,
        SUM(CASE WHEN id IN (SELECT DISTINCT customer_id FROM cases) THEN 1 ELSE 0 END) as with_cases,
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today
        FROM customers";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats = $stmt->fetch();
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "total" => intval($stats['total']),
        "grade_a_plus" => intval($stats['grade_a_plus']),
        "grade_a" => intval($stats['grade_a']),
        "grade_b" => intval($stats['grade_b']),
        "with_cases" => intval($stats['with_cases']),
        "today" => intval($stats['today'])
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>