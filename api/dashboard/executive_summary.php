<?php
// api/dashboard/executive_summary.php
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

    // Status summary
    $statusQuery = "SELECT COALESCE(cs.status, 'ไม่ระบุ') as status, COUNT(*) as count 
                    FROM cases cs {$where}
                    GROUP BY cs.status";
    $statusStmt = $db->prepare($statusQuery);
    $statusStmt->execute($params);
    $status_summary = [];
    while ($row = $statusStmt->fetch(PDO::FETCH_ASSOC)) {
        $status_summary[$row['status']] = (int)$row['count'];
    }

    // Total approved value
    $valueQuery = "SELECT COALESCE(SUM(a.total_amount), 0) 
                   FROM approvals a 
                   JOIN cases cs ON a.case_id = cs.id {$where}";
    $valueStmt = $db->prepare($valueQuery);
    $valueStmt->execute($params);
    $total_value = (float)$valueStmt->fetchColumn();

    // Average per case
    $caseCount = (int)$db->prepare("SELECT COUNT(*) FROM cases cs {$where}")->execute($params)->fetchColumn();
    $avg_per_case = $caseCount > 0 ? round($total_value / $caseCount, 2) : 0;

    // Average per staff
    $staffCount = (int)$db->prepare("SELECT COUNT(DISTINCT owner_id) FROM cases cs {$where}")->execute($params)->fetchColumn();
    $avg_per_staff = $staffCount > 0 ? round($total_value / $staffCount, 2) : 0;

    echo json_encode([
        "success" => true,
        "status_summary" => $status_summary,
        "total_value" => $total_value,
        "avg_per_case" => $avg_per_case,
        "avg_per_staff" => $avg_per_staff
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false]);
}
?>