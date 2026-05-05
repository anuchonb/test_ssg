<?php
// api/dashboard/executive_trend.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

try {
    $months = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
               'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    
    $labels = [];
    $cases_data = [];
    $approved_data = [];

    for ($m = 1; $m <= 12; $m++) {
        $labels[] = $months[$m];
        
        // Cases per month
        $caseStmt = $db->prepare("SELECT COUNT(*) FROM cases WHERE YEAR(created_at)=? AND MONTH(created_at)=?");
        $caseStmt->execute([$year, $m]);
        $cases_data[] = (int)$caseStmt->fetchColumn();

        // Approved per month
        $approveStmt = $db->prepare("SELECT COUNT(*) FROM cases WHERE YEAR(created_at)=? AND MONTH(created_at)=? AND status='อนุมัติ'");
        $approveStmt->execute([$year, $m]);
        $approved_data[] = (int)$approveStmt->fetchColumn();
    }

    echo json_encode([
        "success" => true,
        "data" => [
            "labels" => $labels,
            "cases" => $cases_data,
            "approved" => $approved_data
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>