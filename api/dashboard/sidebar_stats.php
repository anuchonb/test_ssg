<?php
// api/dashboard/sidebar_stats.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // นับเคสที่รอติดตาม
    $pendingCasesQuery = "SELECT COUNT(*) as count FROM cases WHERE status = 'ส่งเคส'";
    
    // Admin Page เห็นเฉพาะของตัวเอง
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin_page') {
        $pendingCasesQuery .= " AND owner_id = ?";
        $stmt = $db->prepare($pendingCasesQuery);
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        $stmt = $db->query($pendingCasesQuery);
    }
    $pending_cases = (int)$stmt->fetchColumn();

    // นับ KPI ที่รอตรวจ
    $pendingKpiQuery = "SELECT COUNT(*) as count 
                        FROM cases 
                        WHERE id NOT IN (SELECT DISTINCT case_id FROM kpi_checks)
                        AND status NOT IN ('ยกเลิก', 'ไม่สนใจ')";
    $pendingKpiStmt = $db->query($pendingKpiQuery);
    $pending_kpi = (int)$pendingKpiStmt->fetchColumn();

    echo json_encode([
        "success" => true,
        "pending_cases" => $pending_cases,
        "pending_kpi" => $pending_kpi
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>