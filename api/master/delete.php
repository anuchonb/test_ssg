<?php
// api/master/delete.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "ไม่มีสิทธิ์"]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(empty($data->id)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "กรุณาระบุ ID ที่ต้องการลบ"]);
    exit();
}

try {
    // 1. ดึงข้อมูลก่อนลบเพื่อตรวจสอบ
    $get_query = "SELECT type, value FROM master_dropdowns WHERE id = :id";
    $get_stmt = $db->prepare($get_query);
    $get_stmt->execute([':id' => $data->id]);
    $item = $get_stmt->fetch();
    
    if(!$item) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลที่ต้องการลบ"]);
        exit();
    }

    // 2. ตรวจสอบว่ามีการใช้งานข้อมูลนี้ในตารางอื่นหรือไม่ (ตัวอย่าง)
    $is_used = false;
    switch($item['type']) {
        case 'channel':
            $check = $db->prepare("SELECT COUNT(*) FROM customers WHERE channel = :val");
            $check->execute([':val' => $item['value']]);
            $is_used = $check->fetchColumn() > 0;
            break;
        case 'zone':
            $check = $db->prepare("SELECT COUNT(*) FROM customers WHERE zone = :val");
            $check->execute([':val' => $item['value']]);
            $is_used = $check->fetchColumn() > 0;
            break;
        case 'bank':
            $check = $db->prepare("SELECT COUNT(*) FROM bank_submissions WHERE bank_name = :val");
            $check->execute([':val' => $item['value']]);
            $is_used = $check->fetchColumn() > 0;
            break;
    }

    if($is_used) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "ไม่สามารถลบ '{$item['value']}' ได้ เนื่องจากมีการใช้งานอยู่"
        ]);
        exit();
    }

    // 3. ลบข้อมูล
    $delete_query = "DELETE FROM master_dropdowns WHERE id = :id";
    $delete_stmt = $db->prepare($delete_query);
    $delete_stmt->execute([':id' => $data->id]);

    http_response_code(200);
    echo json_encode([
        "success" => true, 
        "message" => "ลบ '{$item['value']}' เรียบร้อยแล้ว"
    ]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
}
?>