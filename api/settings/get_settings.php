<?php
// api/settings/get_settings.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

// เฉพาะ admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "ไม่มีสิทธิ์"]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

try {
    $stmt = $db->prepare("SELECT value FROM master_dropdowns WHERE type = 'system_settings' AND is_active = 1");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $settings = [];
    foreach ($rows as $row) {
        $parts = explode(':', $row, 2);
        if (count($parts) == 2) {
            $settings[$parts[0]] = $parts[1];
        }
    }

    // ถ้าไม่มีข้อมูลให้ใช้ค่าเริ่มต้น
    if (empty($settings)) {
        $settings = [
            'system_name' => 'CRM Condo System',
            'company_name' => '',
            'language' => 'th',
            'timezone' => 'Asia/Bangkok',
            'items_per_page' => '25',
            'date_format' => 'd/m/Y',
            'currency_format' => 'thb',
            'enable_register' => '1',
            'enable_maintenance' => '0',
            'line_token' => '',
            'email_new_case' => '1',
            'email_kpi_fail' => '1',
            'email_approval' => '1',
            'line_new_case' => '0',
            'line_approval' => '1',
            'line_daily_report' => '0'
        ];
    }

    echo json_encode(["success" => true, "data" => $settings], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>