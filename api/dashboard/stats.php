<?php
// api/dashboard/stats.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // สร้าง WHERE clause ตาม role
    $whereClause = "";
    $params = [];
    
    // แสดงสถิติเฉพาะของตัวเอง (ยกเว้น admin)
    if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin_page', 'kpi', 'support'])) {
        $whereClause = "WHERE cs.owner_id = ?";
        $params = [$_SESSION['user_id']];
    }
    
    // สถิติหลัก
    $query = "SELECT 
        (SELECT COUNT(*) FROM cases {$whereClause}) as total_cases,
        (SELECT COUNT(*) FROM cases WHERE status = 'อนุมัติ' {$whereClause}) as approved_cases,
        (SELECT COUNT(*) FROM cases WHERE status = 'กำลังติดตาม' {$whereClause}) as following_cases,
        (SELECT COUNT(*) FROM cases WHERE status IN ('ยกเลิก', 'ไม่สนใจ') {$whereClause}) as cancelled_cases,
        (SELECT COUNT(*) FROM cases WHERE DATE(created_at) = CURDATE()) as today_cases,
        (SELECT COUNT(*) FROM customers) as total_customers";
    
    $stmt = $db->prepare($query);
    if(!empty($params)) {
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
    }
    $stmt->execute();
    $stats = $stmt->fetch();
    
    // สถิติเพิ่มเติม
    $query2 = "SELECT 
        (SELECT COUNT(*) FROM bank_submissions) as bank_submitted,
        (SELECT COUNT(*) FROM kpi_checks WHERE result = 'pass') as kpi_pass,
        (SELECT COUNT(*) FROM kpi_checks WHERE result = 'fail') as kpi_fail,
        (SELECT COUNT(*) FROM document_steps WHERE debt_close_status = 'not_done') as pending_debt,
        (SELECT COUNT(*) FROM follow_logs WHERE DATE(created_at) = CURDATE()) as follow_today";
    
    $stmt2 = $db->prepare($query2);
    $stmt2->execute();
    $stats2 = $stmt2->fetch();
    
    // รวมข้อมูล
    $data = array_merge($stats, $stats2);
    
    // คำนวณอัตราการอนุมัติ
    $data['total_cases'] = intval($data['total_cases']);
    $data['approved_cases'] = intval($data['approved_cases']);
    $data['following_cases'] = intval($data['following_cases']);
    $data['cancelled_cases'] = intval($data['cancelled_cases']);
    $data['today_cases'] = intval($data['today_cases']);
    $data['total_customers'] = intval($data['total_customers']);
    $data['bank_submitted'] = intval($data['bank_submitted']);
    $data['kpi_pass'] = intval($data['kpi_pass']);
    $data['kpi_fail'] = intval($data['kpi_fail']);
    $data['pending_debt'] = intval($data['pending_debt']);
    $data['follow_today'] = intval($data['follow_today']);
    
    $data['approval_rate'] = $data['total_cases'] > 0 
        ? round(($data['approved_cases'] / $data['total_cases']) * 100, 1) 
        : 0;
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    error_log("Dashboard Stats Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error"
    ]);
}
?>