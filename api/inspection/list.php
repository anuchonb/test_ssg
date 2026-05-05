<?php
// api/inspection/list.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;

if (!$case_id) {
    echo json_encode(["success" => false, "message" => "กรุณาระบุ Case ID"]);
    exit();
}

try {
    // ดึงข้อมูลตรวจห้อง
    $query = "SELECT * FROM inspections WHERE case_id = ? ORDER BY round ASC";
    $stmt = $db->prepare($query);
    $stmt->execute([$case_id]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ดึงรูปภาพสำหรับแต่ละ inspection
    foreach ($data as &$item) {
        // หารูปภาพที่เกี่ยวข้อง (ใช้ file_type = 'inspection_photo')
        $photoQuery = "SELECT file_path FROM files WHERE case_id = ? AND file_type = 'inspection_photo' ORDER BY id ASC";
        $photoStmt = $db->prepare($photoQuery);
        $photoStmt->execute([$case_id]);
        $photos = $photoStmt->fetchAll(PDO::FETCH_COLUMN);
        
        $item['photos'] = $photos ?: [];
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