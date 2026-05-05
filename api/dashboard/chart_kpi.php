<?php
// api/dashboard/chart_kpi.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // นับ KPI ใน 30 วันล่าสุด
    $query = "
        SELECT 
            COUNT(*) as total_checks,
            SUM(CASE WHEN result = 'pass' THEN 1 ELSE 0 END) as pass_count,
            SUM(CASE WHEN result = 'fail' THEN 1 ELSE 0 END) as fail_count
        FROM kpi_checks 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $kpi_result = $stmt->fetch();
    
    // นับเคสที่ยังไม่ถูกตรวจ KPI
    $pending_query = "
        SELECT COUNT(*) as pending_count 
        FROM cases cs
        WHERE cs.id NOT IN (
            SELECT DISTINCT case_id FROM kpi_checks
        )
        AND cs.status NOT IN ('ยกเลิก', 'ไม่สนใจ')";
    
    $pending_stmt = $db->prepare($pending_query);
    $pending_stmt->execute();
    $pending_result = $pending_stmt->fetch();
    
    $pass = intval($kpi_result['pass_count']);
    $fail = intval($kpi_result['fail_count']);
    $pending = intval($pending_result['pending_count']);
    $total = $pass + $fail + $pending;
    
    // คำนวณเปอร์เซ็นต์
    $pass_percent = $total > 0 ? round(($pass / $total) * 100, 1) : 0;
    $fail_percent = $total > 0 ? round(($fail / $total) * 100, 1) : 0;
    $pending_percent = $total > 0 ? round(($pending / $total) * 100, 1) : 0;
    
    $response = [
        "success" => true,
        "data" => [
            "pass" => $pass,
            "fail" => $fail,
            "pending" => $pending,
            "total" => $total,
            "pass_percent" => $pass_percent,
            "fail_percent" => $fail_percent,
            "pending_percent" => $pending_percent,
            "labels" => ["ผ่าน", "ไม่ผ่าน", "รอตรวจ"],
            "values" => [$pass, $fail, $pending],
            "colors" => [
                "rgba(75, 192, 192, 0.7)",
                "rgba(255, 99, 132, 0.7)",
                "rgba(255, 206, 86, 0.7)"
            ],
            "borderColors" => [
                "rgba(75, 192, 192, 1)",
                "rgba(255, 99, 132, 1)",
                "rgba(255, 206, 86, 1)"
            ]
        ]
    ];
    
    http_response_code(200);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    error_log("Chart KPI Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>