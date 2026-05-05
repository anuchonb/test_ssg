<?php
// api/file/upload.php
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

$case_id = isset($_POST['case_id']) ? intval($_POST['case_id']) : 0;
$file_type = isset($_POST['file_type']) ? trim($_POST['file_type']) : 'document';

if (!$case_id || !isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณาระบุ Case ID และเลือกไฟล์"
    ]);
    exit();
}

$file = $_FILES['file'];

// ตรวจสอบนามสกุลไฟล์
$allowed_ext = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed_ext)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ประเภทไฟล์ไม่ถูกต้อง (อนุญาต: " . implode(', ', $allowed_ext) . ")"
    ]);
    exit();
}

// ตรวจสอบขนาดไฟล์ (5MB)
$max_size = 5 * 1024 * 1024;
if ($file['size'] > $max_size) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ไฟล์มีขนาดเกิน 5MB"
    ]);
    exit();
}

// สร้างโฟลเดอร์
$upload_dir = "../../uploads/cases/{$case_id}/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// สร้างชื่อไฟล์ใหม่
$new_filename = time() . '_' . uniqid() . '.' . $ext;
$target_path = $upload_dir . $new_filename;

if (move_uploaded_file($file['tmp_name'], $target_path)) {
    // บันทึกในฐานข้อมูล
    $db_path = "uploads/cases/{$case_id}/{$new_filename}";
    
    try {
        $query = "INSERT INTO files (case_id, file_path, file_type) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$case_id, $db_path, $file_type]);

        echo json_encode([
            "success" => true,
            "message" => "อัปโหลดไฟล์สำเร็จ!",
            "file_id" => $db->lastInsertId(),
            "file_path" => $db_path,
            "file_name" => $new_filename
        ]);
    } catch (PDOException $e) {
        // ลบไฟล์ถ้าบันทึก DB ไม่สำเร็จ
        unlink($target_path);
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "อัปโหลดไฟล์ไม่สำเร็จ"
    ]);
}
?>