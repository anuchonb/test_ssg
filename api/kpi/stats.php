<?php
// api/kpi/stats.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // KPI ทั้งหมด
    $query = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN result = 'pass' THEN 1 ELSE 0 END) as pass,
        SUM(CASE WHEN result = 'fail' THEN 1 ELSE 0 END) as fail
        FROM kpi_checks";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // รอตรวจ
    $pendingQuery = "SELECT COUNT(*) as pending 
                     FROM cases 
                     WHERE id NOT IN (SELECT DISTINCT case_id FROM kpi_checks)
                     AND status NOT IN ('ยกเลิก', 'ไม่สนใจ')";
    $pendingStmt = $db->prepare($pendingQuery);
    $pendingStmt->execute();
    $pending = $pendingStmt->fetch(PDO::FETCH_ASSOC);

    // วันนี้
    $todayQuery = "SELECT COUNT(*) as today FROM kpi_checks WHERE DATE(created_at) = CURDATE()";
    $todayStmt = $db->prepare($todayQuery);
    $todayStmt->execute();
    $today = $todayStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "total" => (int)$stats['total'],
        "pass" => (int)$stats['pass'],
        "fail" => (int)$stats['fail'],
        "pending" => (int)$pending['pending'],
        "today" => (int)$today['today']
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>