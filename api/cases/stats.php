<?php
// api/cases/stats.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT 
        COUNT(*) as all_cases,
        SUM(CASE WHEN status = 'ส่งเคส' THEN 1 ELSE 0 END) as submitted,
        SUM(CASE WHEN status = 'กำลังติดตาม' THEN 1 ELSE 0 END) as following,
        SUM(CASE WHEN status = 'อนุมัติ' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'ยกเลิก' THEN 1 ELSE 0 END) as cancelled,
        SUM(CASE WHEN status = 'ไม่สนใจ' THEN 1 ELSE 0 END) as not_interested
        FROM cases";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "all" => (int)$stats['all_cases'],
        "submitted" => (int)$stats['submitted'],
        "following" => (int)$stats['following'],
        "approved" => (int)$stats['approved'],
        "cancelled" => (int)$stats['cancelled'],
        "not_interested" => (int)$stats['not_interested']
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>