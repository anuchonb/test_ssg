<?php
// api/users/delete.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// เฉพาะ admin เท่านั้นที่ลบ user ได้
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "คุณไม่มีสิทธิ์ในการลบผู้ใช้"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)) {
    
    // ไม่ให้ลบตัวเอง
    if($data->id == $_SESSION['user_id']) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "ไม่สามารถลบบัญชีของตัวเองได้"
        ]);
        exit();
    }
    
    // ตรวจสอบว่าเป็น admin คนสุดท้ายหรือไม่
    if($data->id != $_SESSION['user_id']) {
        $check_query = "SELECT role FROM users WHERE id = :id";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(":id", $data->id);
        $check_stmt->execute();
        $user = $check_stmt->fetch();
        
        if($user['role'] === 'admin') {
            $count_query = "SELECT COUNT(*) as count FROM users WHERE role = 'admin'";
            $count_stmt = $db->prepare($count_query);
            $count_stmt->execute();
            $admin_count = $count_stmt->fetch()['count'];
            
            if($admin_count <= 1) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "ไม่สามารถลบ admin คนสุดท้ายได้"
                ]);
                exit();
            }
        }
    }
    
    try {
        $db->beginTransaction();
        
        // ย้ายเคสไปให้ admin คนอื่น (หรือ admin ตัวเอง)
        $update_cases = "UPDATE cases SET owner_id = :new_owner_id WHERE owner_id = :old_owner_id";
        $update_stmt = $db->prepare($update_cases);
        $update_stmt->bindParam(":new_owner_id", $_SESSION['user_id']);
        $update_stmt->bindParam(":old_owner_id", $data->id);
        $update_stmt->execute();
        
        // ลบ user
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $data->id);
        $stmt->execute();
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "ลบผู้ใช้สำเร็จ"
        ]);
        
    } catch(PDOException $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
        ]);
    }
    
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณาระบุ ID"
    ]);
}
?>