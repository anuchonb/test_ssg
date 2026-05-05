<?php
// api/logs/export.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=activity_logs_" . date('Y-m-d') . ".csv");
header("Pragma: no-cache");
header("Expires: 0");

include_once '../../config/database.php';

// ตรวจสอบสิทธิ์
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo "ไม่มีสิทธิ์";
    exit();
}

$database = new Database();
$db = $database->getConnection();

// รับพารามิเตอร์
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

// สร้าง WHERE clause
$conditions = [];
$params = [];

if ($search) {
    $conditions[] = "(ca.action LIKE ? OR u.name LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($type) {
    switch ($type) {
        case 'login':
            $conditions[] = "(ca.action LIKE '%Login%' OR ca.action LIKE '%Logout%' OR ca.action LIKE '%เข้าสู่ระบบ%' OR ca.action LIKE '%ออกจากระบบ%')";
            break;
        case 'case':
            $conditions[] = "(ca.action LIKE '%Case%' OR ca.action LIKE '%เคส%')";
            break;
        case 'customer':
            $conditions[] = "(ca.action LIKE '%Customer%' OR ca.action LIKE '%ลูกค้า%')";
            break;
        case 'kpi':
            $conditions[] = "ca.action LIKE '%KPI%'";
            break;
        case 'support':
            $conditions[] = "(ca.action LIKE '%Bank%' OR ca.action LIKE '%Document%' OR ca.action LIKE '%Debt%' OR ca.action LIKE '%Mortgage%' OR ca.action LIKE '%Inspection%')";
            break;
        case 'error':
            $conditions[] = "(ca.action LIKE '%Error%' OR ca.action LIKE '%ผิดพลาด%')";
            break;
    }
}

if ($user_id) {
    $conditions[] = "ca.user_id = ?";
    $params[] = $user_id;
}

if ($date_from) {
    $conditions[] = "DATE(ca.created_at) >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $conditions[] = "DATE(ca.created_at) <= ?";
    $params[] = $date_to;
}

$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

try {
    $query = "SELECT ca.id, ca.action, ca.created_at, 
              u.name as user_name, u.role as user_role,
              ca.case_id
              FROM case_activities ca 
              LEFT JOIN users u ON ca.user_id = u.id 
              {$whereClause}
              ORDER BY ca.created_at DESC
              LIMIT 10000";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // สร้าง CSV
    $output = fopen('php://output', 'w');

    // BOM สำหรับภาษาไทย (ให้ Excel อ่านภาษาไทยได้)
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Header row
    fputcsv($output, ['#', 'วันที่/เวลา', 'ผู้ใช้', 'Role', 'Case ID', 'กิจกรรม']);

    // Data rows
    $row_num = 1;
    foreach ($data as $row) {
        // แปลง Role เป็นภาษาไทย
        $role_labels = [
            'admin' => 'Admin',
            'admin_page' => 'Admin Page',
            'kpi' => 'KPI',
            'support' => 'Support'
        ];
        $role_label = isset($role_labels[$row['user_role']]) ? $role_labels[$row['user_role']] : ($row['user_role'] ?? '-');

        fputcsv($output, [
            $row_num,
            $row['created_at'],
            $row['user_name'] ?? 'System',
            $role_label,
            $row['case_id'] ? '#' . $row['case_id'] : '-',
            $row['action']
        ]);
        $row_num++;
    }

    fclose($output);

} catch (PDOException $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>