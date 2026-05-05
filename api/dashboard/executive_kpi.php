<?php
// api/dashboard/executive_kpi.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
$year  = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

try {
    // ============ HELPER ============
    function fetchOne($db, $sql, $params = []) {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    // ============ BUILD WHERE ============
    $conditions = [];
    $params = [];

    if ($year) {
        $conditions[] = "YEAR(cs.created_at) = ?";
        $params[] = $year;
    }
    if ($month) {
        $conditions[] = "MONTH(cs.created_at) = ?";
        $params[] = $month;
    }

    // ✅ สร้าง WHERE แบบปลอดภัย
    $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    // ============ TOTAL CASES ============
    $total_cases = (int) fetchOne($db, "SELECT COUNT(*) FROM cases cs {$where}", $params);

    // ============ APPROVED CASES ============
    // ✅ ใส่เงื่อนไข status ใน WHERE เดียวกัน
    $approved_conditions = $conditions;
    $approved_conditions[] = "cs.status = 'อนุมัติ'";
    $approved_where = "WHERE " . implode(" AND ", $approved_conditions);
    $approved_cases = (int) fetchOne($db, "SELECT COUNT(*) FROM cases cs {$approved_where}", $params);

    // ============ TOTAL VALUE ============
    $total_value = (float) fetchOne($db, "SELECT COALESCE(SUM(a.total_amount), 0) FROM approvals a JOIN cases cs ON a.case_id = cs.id {$where}", $params);

    // ============ TOTAL CUSTOMERS ============
    $total_customers = (int) fetchOne($db, "SELECT COUNT(*) FROM customers");

    // ============ GROWTH ============
    $growth = 0;
    if ($month) {
        $prevMonth = $month > 1 ? $month - 1 : 12;
        $prevYear  = $month > 1 ? $year : $year - 1;
        $prevTotal = (int) fetchOne($db, "SELECT COUNT(*) FROM cases WHERE YEAR(created_at)=? AND MONTH(created_at)=?", [$prevYear, $prevMonth]);
        if ($prevTotal > 0) {
            $growth = round((($total_cases - $prevTotal) / $prevTotal) * 100, 1);
        }
    }

    // ============ RATES ============
    $approval_rate  = $total_cases > 0 ? round(($approved_cases / $total_cases) * 100, 1) : 0;
    $conversion_rate = $total_customers > 0 ? round(($approved_cases / $total_customers) * 100, 1) : 0;

    // ============ RESPONSE ============
    echo json_encode([
        "success"          => true,
        "total_cases"      => $total_cases,
        "approved_cases"   => $approved_cases,
        "total_value"      => $total_value,
        "approval_rate"    => $approval_rate,
        "conversion_rate"  => $conversion_rate,
        "growth"           => $growth,
        "total_customers"  => $total_customers
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>