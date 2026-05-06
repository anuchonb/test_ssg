<?php
// api/settings/backup_history.php
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
    $query = "SELECT bl.*, u.name as user_name 
              FROM backup_logs bl 
              LEFT JOIN users u ON bl.user_id = u.id 
              ORDER BY bl.created_at DESC 
              LIMIT 20";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // จัดรูปแบบขนาดไฟล์
    foreach ($data as &$row) {
        if ($row['file_size'] > 1048576) {
            $row['size_display'] = round($row['file_size'] / 1048576, 2) . ' MB';
        } elseif ($row['file_size'] > 1024) {
            $row['size_display'] = round($row['file_size'] / 1024, 2) . ' KB';
        } else {
            $row['size_display'] = $row['file_size'] . ' bytes';
        }
    }

    echo json_encode([
        "success" => true,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>