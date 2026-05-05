<?php
// api/dashboard/chart_cases.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

try {
    // สร้างข้อมูลทุกเดือน (1-12)
    $months_thai = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    
    $labels = [];
    $submitted = [];
    $approved = [];
    $cancelled = [];
    
    // ดึงข้อมูลจาก database
    $query = "SELECT 
                MONTH(created_at) as month,
                SUM(CASE WHEN status = 'ส่งเคส' THEN 1 ELSE 0 END) as submitted_count,
                SUM(CASE WHEN status = 'กำลังติดตาม' THEN 1 ELSE 0 END) as following_count,
                SUM(CASE WHEN status = 'อนุมัติ' THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN status IN ('ยกเลิก', 'ไม่สนใจ') THEN 1 ELSE 0 END) as cancelled_count,
                COUNT(*) as total_count
              FROM cases 
              WHERE YEAR(created_at) = :year
              GROUP BY MONTH(created_at)
              ORDER BY month ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll();
    
    // จัดรูปแบบข้อมูลเป็น key => value
    $data_by_month = [];
    foreach($results as $row) {
        $data_by_month[$row['month']] = $row;
    }
    
    // สร้างข้อมูลครบ 12 เดือน
    for($month = 1; $month <= 12; $month++) {
        $labels[] = $months_thai[$month];
        
        if(isset($data_by_month[$month])) {
            $submitted[] = intval($data_by_month[$month]['submitted_count']);
            $approved[] = intval($data_by_month[$month]['approved_count']);
            $cancelled[] = intval($data_by_month[$month]['cancelled_count']);
        } else {
            $submitted[] = 0;
            $approved[] = 0;
            $cancelled[] = 0;
        }
    }
    
    // ส่งข้อมูลกลับ
    $response = [
        "success" => true,
        "data" => [
            "labels" => $labels,
            "submitted" => $submitted,
            "approved" => $approved,
            "cancelled" => $cancelled,
            "year" => $year
        ]
    ];
    
    http_response_code(200);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    error_log("Chart Cases Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>