<?php
// api/cases/export.php
session_start();
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=cases_export_" . date('Y-m-d') . ".csv");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// BOM for Thai Excel
echo "\xEF\xBB\xBF";

// Headers
$headers = ['Case ID', 'ลูกค้า', 'เบอร์โทร', 'โครงการ', 'สถานะ', 'เจ้าของ', 'วันที่สร้าง'];
$output = fopen('php://output', 'w');
fputcsv($output, $headers);

$query = "SELECT cs.id, c.name, c.phone, p.name as project_name, 
          cs.status, u.name as owner_name, cs.created_at
          FROM cases cs
          JOIN customers c ON cs.customer_id = c.id
          LEFT JOIN projects p ON c.project_id = p.id
          LEFT JOIN users u ON cs.owner_id = u.id
          ORDER BY cs.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute();

while($row = $stmt->fetch()) {
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['phone'],
        $row['project_name'],
        $row['status'],
        $row['owner_name'],
        $row['created_at']
    ]);
}

fclose($output);
?>