<?php
// api/kpi/history.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$date = isset($_GET['date']) ? $_GET['date'] : '';
$result = isset($_GET['result']) ? $_GET['result'] : '';

$conditions = [];
$params = [];

if($date) {
    $conditions[] = "DATE(kc.created_at) = ?";
    $params[] = $date;
}

if($result) {
    $conditions[] = "kc.result = ?";
    $params[] = $result;
}

$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

try {
    $query = "SELECT kc.*, cs.id as case_id, c.name as customer_name, u.name as checker_name
              FROM kpi_checks kc
              JOIN cases cs ON kc.case_id = cs.id
              JOIN customers c ON cs.customer_id = c.id
              LEFT JOIN users u ON kc.checker_id = u.id
              {$whereClause}
              ORDER BY kc.created_at DESC
              LIMIT 50";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
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