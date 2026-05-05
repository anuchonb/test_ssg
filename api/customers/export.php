<?php
// api/customers/export.php
session_start();
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=customers_export_" . date('Y-m-d') . ".csv");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "\xEF\xBB\xBF";

$headers = ['รหัส', 'ชื่อ', 'เบอร์โทร', 'Facebook', 'Line', 'ช่องทาง', 'เกรด', 'โครงการ', 'ราคา', 'วันที่เพิ่ม'];
$output = fopen('php://output', 'w');
fputcsv($output, $headers);

$query = "SELECT c.*, p.name as project_name 
          FROM customers c
          LEFT JOIN projects p ON c.project_id = p.id
          ORDER BY c.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute();

while($row = $stmt->fetch()) {
    fputcsv($output, [
        $row['customer_code'],
        $row['name'],
        $row['phone'],
        $row['facebook'],
        $row['line_id'],
        $row['channel'],
        $row['grade'],
        $row['project_name'],
        $row['price'],
        $row['created_at']
    ]);
}

fclose($output);
?>