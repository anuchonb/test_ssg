<?php
// api/dashboard/executive_staff.php
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

    $query = "SELECT u.name, 
              COUNT(cs.id) as cases,
              SUM(CASE WHEN cs.status = 'อนุมัติ' THEN 1 ELSE 0 END) as approved,
              COALESCE(SUM(a.total_amount), 0) as value
              FROM users u 
              LEFT JOIN cases cs ON cs.owner_id = u.id {$where}
              LEFT JOIN approvals a ON a.case_id = cs.id
              WHERE u.role = 'admin_page'
              GROUP BY u.id, u.name 
              ORDER BY approved DESC, value DESC 
              LIMIT 5";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$row) {
        $row['cases'] = (int)$row['cases'];
        $row['approved'] = (int)$row['approved'];
        $row['value'] = (float)$row['value'];
    }

    echo json_encode([
        "success" => true,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false]);
}
?>