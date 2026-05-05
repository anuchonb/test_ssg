<?php
// api/projects/stats.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // ใช้เฉพาะ columns ที่มีจริง
    $query = "SELECT 
        COUNT(*) as total_projects,
        COALESCE(AVG(price), 0) as avg_price,
        COUNT(DISTINCT zone) as total_zones
        FROM projects";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats = $stmt->fetch();
    
    // นับลูกค้าที่สนใจโครงการ
    $customerQuery = "SELECT COUNT(*) as count FROM customers WHERE project_id IS NOT NULL";
    $customerStmt = $db->prepare($customerQuery);
    $customerStmt->execute();
    $total_customers = $customerStmt->fetch()['count'];
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "total_projects" => intval($stats['total_projects']),
        "total_customers" => intval($total_customers),
        "avg_price" => round(floatval($stats['avg_price']), 2),
        "total_zones" => intval($stats['total_zones'])
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>