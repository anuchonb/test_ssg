<?php
// api/file/delete.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$raw = file_get_contents("php://input");
$data = json_decode($raw);

if (!$data || empty($data->id)) {
    echo json_encode([
        "success" => false,
        "message" => "กรุณาระบุ File ID"
    ]);
    exit();
}

$id = intval($data->id);

try {
    // ดึง path ก่อนลบ
    $getQuery = "SELECT file_path FROM files WHERE id = ?";
    $getStmt = $db->prepare($getQuery);
    $getStmt->execute([$id]);
    $file = $getStmt->fetch(PDO::FETCH_ASSOC);

    // ลบจากฐานข้อมูล
    $query = "DELETE FROM files WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);

    // ลบไฟล์จริง
    if ($file && !empty($file['file_path'])) {
        $physical_path = "../../" . $file['file_path'];
        if (file_exists($physical_path)) {
            unlink($physical_path);
        }
    }

    echo json_encode([
        "success" => true,
        "message" => "ลบไฟล์สำเร็จ!"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>