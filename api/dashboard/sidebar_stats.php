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
    $pending_cases_query = "SELECT COUNT(*) as count FROM cases WHERE status = 'ส่งเคส'";
    if($_SESSION['user_role'] === 'admin_page') {
        $pending_cases_query .= " AND owner_id = :user_id";
    }
    
    $stmt = $db->prepare($pending_cases_query);
    if($_SESSION['user_role'] === 'admin_page') {
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
    }
    $stmt->execute();
    $pending_cases = $stmt->fetch()['count'];
    
    // นับ KPI ที่รอตรวจ
    $pending_kpi_query = "SELECT COUNT(*) as count 
                          FROM cases 
                          WHERE id NOT IN (SELECT DISTINCT case_id FROM kpi_checks)
                          AND status NOT IN ('ยกเลิก', 'ไม่สนใจ')";
    
    $stmt2 = $db->prepare($pending_kpi_query);
    $stmt2->execute();
    $pending_kpi = $stmt2->fetch()['count'];
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "pending_cases" => intval($pending_cases),
        "pending_kpi" => intval($pending_kpi)
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false]);
}
?>