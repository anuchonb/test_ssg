<?php
// api/inspection/save.php
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

// รับข้อมูลจาก FormData
$case_id = isset($_POST['case_id']) ? intval($_POST['case_id']) : 0;
$round = isset($_POST['round']) ? intval($_POST['round']) : 1;
$inspect_date = isset($_POST['inspect_date']) ? trim($_POST['inspect_date']) : date('Y-m-d H:i:s');
$status = isset($_POST['status']) ? trim($_POST['status']) : 'pass';
$note = isset($_POST['note']) ? trim($_POST['note']) : '';

if (!$case_id) {
    echo json_encode(["success" => false, "message" => "กรุณาระบุ Case ID"]);
    exit();
}

try {
    $db->beginTransaction();

    // บันทึกข้อมูลตรวจห้อง
    $query = "INSERT INTO inspections (case_id, round, inspect_date, status, note) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$case_id, $round, $inspect_date, $status, $note]);
    $inspection_id = $db->lastInsertId();

    // จัดการอัปโหลดรูปภาพ
    $uploaded_photos = [];
    
    if (isset($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
        $upload_dir = "../../uploads/inspections/{$case_id}/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $max_size = 5 * 1024 * 1024; // 5MB

        foreach ($_FILES['photos']['name'] as $key => $name) {
            $tmp_name = $_FILES['photos']['tmp_name'][$key];
            $file_type = $_FILES['photos']['type'][$key];
            $file_size = $_FILES['photos']['size'][$key];
            $error = $_FILES['photos']['error'][$key];

            if ($error === UPLOAD_ERR_OK) {
                // ตรวจสอบประเภทไฟล์
                if (!in_array($file_type, $allowed_types)) {
                    continue;
                }

                // ตรวจสอบขนาด
                if ($file_size > $max_size) {
                    continue;
                }

                // สร้างชื่อไฟล์
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $new_name = 'inspect_' . $round . '_' . time() . '_' . $key . '.' . $ext;
                $target_path = $upload_dir . $new_name;

                if (move_uploaded_file($tmp_name, $target_path)) {
                    $db_path = "uploads/inspections/{$case_id}/{$new_name}";
                    
                    // บันทึกในตาราง files
                    $fileQuery = "INSERT INTO files (case_id, file_path, file_type) VALUES (?, ?, ?)";
                    $fileStmt = $db->prepare($fileQuery);
                    $fileStmt->execute([$case_id, $db_path, 'inspection_photo']);
                    
                    $uploaded_photos[] = $db_path;
                }
            }
        }
    }

    // Log activity
    if (isset($_SESSION['user_id'])) {
        $action = "Inspection #{$round}: " . ($status === 'pass' ? 'ผ่าน' : 'ไม่ผ่าน');
        $logQuery = "INSERT INTO case_activities (case_id, action, user_id) VALUES (?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $logStmt->execute([$case_id, $action, $_SESSION['user_id']]);
    }

    $db->commit();

    echo json_encode([
        "success" => true,
        "message" => "บันทึกผลตรวจห้องครั้งที่ {$round} สำเร็จ!",
        "id" => $inspection_id,
        "photos_count" => count($uploaded_photos)
    ]);

} catch (PDOException $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>