<?php
// api/master/save.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

// ตรวจสอบสิทธิ์
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "ไม่มีสิทธิ์ในการจัดการข้อมูล"]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// อ่านข้อมูล JSON
$raw_data = file_get_contents("php://input");
$data = json_decode($raw_data);

// ตรวจสอบข้อมูลที่จำเป็น
if(empty($data->type) || empty($data->value)) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "message" => "กรุณากรอกประเภทและค่าข้อมูลให้ครบถ้วน"
    ]);
    exit();
}

// ทำความสะอาดข้อมูล
$type = trim($data->type);
$value = trim($data->value);
$is_active = isset($data->is_active) ? intval($data->is_active) : 1;
$id = isset($data->id) ? intval($data->id) : 0;

try {
    // ตรวจสอบข้อมูลซ้ำ (ยกเว้นตัวเองถ้าเป็นการแก้ไข)
    $check_sql = "SELECT id FROM master_dropdowns WHERE type = :type AND value = :value";
    $check_params = [':type' => $type, ':value' => $value];
    
    if($id > 0) {
        $check_sql .= " AND id != :exclude_id";
        $check_params[':exclude_id'] = $id;
    }
    
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->execute($check_params);
    
    if($check_stmt->rowCount() > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            "success" => false,
            "message" => "ข้อมูล '$value' ในประเภท '$type' มีอยู่แล้วในระบบ"
        ]);
        exit();
    }

    // ---------- อัปเดตหรือเพิ่มข้อมูล ----------
    if($id > 0) {
        // ---- แก้ไขข้อมูล ----
        $sql = "UPDATE master_dropdowns SET 
                value = :value, 
                is_active = :is_active 
                WHERE id = :id AND type = :type";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':value' => $value,
            ':is_active' => $is_active,
            ':id' => $id,
            ':type' => $type
        ]);
        
        $message = "อัปเดตข้อมูล '$value' สำเร็จ";
        
    } else {
        // ---- เพิ่มข้อมูลใหม่ ----
        $sql = "INSERT INTO master_dropdowns (type, value, is_active) 
                VALUES (:type, :value, :is_active)";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':type' => $type,
            ':value' => $value,
            ':is_active' => $is_active
        ]);
        
        $id = $db->lastInsertId();
        $message = "เพิ่มข้อมูล '$value' สำเร็จ";
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => $message,
        "id" => $id
    ]);

} catch(PDOException $e) {
    error_log("Master Data Save Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "เกิดข้อผิดพลาดในระบบ: " . $e->getMessage()
    ]);
}
?>