<?php
// api/kpi/pending.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT cs.id as case_id, cs.created_at, 
              c.name as customer_name, c.phone,
              u.name as owner_name
              FROM cases cs
              JOIN customers c ON cs.customer_id = c.id
              LEFT JOIN users u ON cs.owner_id = u.id
              WHERE cs.id NOT IN (SELECT DISTINCT case_id FROM kpi_checks)
              AND cs.status NOT IN ('ยกเลิก', 'ไม่สนใจ')
              ORDER BY cs.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "success" => true,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>