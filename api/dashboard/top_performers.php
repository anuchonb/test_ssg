<?php
// api/dashboard/top_performers.php
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "
        SELECT 
            u.name,
            COUNT(DISTINCT cs.id) as total_cases,
            SUM(CASE WHEN cs.status = 'อนุมัติ' THEN 1 ELSE 0 END) as approved_cases,
            ROUND(
                SUM(CASE WHEN cs.status = 'อนุมัติ' THEN 1 ELSE 0 END) / 
                COUNT(DISTINCT cs.id) * 100, 1
            ) as close_rate,
            COALESCE(
                ROUND(
                    SUM(CASE WHEN kc.result = 'pass' THEN 1 ELSE 0 END) / 
                    NULLIF(COUNT(DISTINCT kc.id), 0) * 100, 1
                ), 0
            ) as kpi_pass_rate
        FROM users u
        LEFT JOIN cases cs ON u.id = cs.owner_id
        LEFT JOIN kpi_checks kc ON cs.id = kc.case_id
        WHERE u.role = 'admin_page'
        GROUP BY u.id, u.name
        ORDER BY total_cases DESC, close_rate DESC
        LIMIT 10";
    
    $stmt = $db->prepare($query);
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