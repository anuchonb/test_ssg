<?php
// api/dashboard/executive_channels.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

try {
    $where = "";
    $params = [];
    if ($year) { $where .= " AND YEAR(cs.created_at) = ?"; $params[] = $year; }
    if ($month) { $where .= " AND MONTH(cs.created_at) = ?"; $params[] = $month; }

    $query = "SELECT COALESCE(c.channel, 'ไม่ระบุ') as channel, COUNT(cs.id) as count 
              FROM customers c 
              LEFT JOIN cases cs ON cs.customer_id = c.id {$where}
              GROUP BY c.channel 
              ORDER BY count DESC 
              LIMIT 5";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $counts = [];
    foreach ($results as $row) {
        if ($row['count'] > 0) {
            $labels[] = $row['channel'];
            $counts[] = (int)$row['count'];
        }
    }

    echo json_encode([
        "success" => true,
        "data" => [
            "labels" => $labels,
            "counts" => $counts
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false]);
}
?>