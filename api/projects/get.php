<?php
// api/projects/get.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id) {
    try {
        // ใช้เฉพาะ columns ที่มีในตาราง
        $query = "SELECT id, name, price, zone, created_at 
                  FROM projects 
                  WHERE id = :id 
                  LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $project = $stmt->fetch();
            
            // ดึงจำนวนลูกค้าเพิ่มเติม
            $countQuery = "SELECT COUNT(*) as count FROM customers WHERE project_id = :id";
            $countStmt = $db->prepare($countQuery);
            $countStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $countStmt->execute();
            $project['customer_count'] = $countStmt->fetch()['count'];
            
            http_response_code(200);
            echo json_encode(["success" => true, "data" => $project]);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "ไม่พบโครงการ"]);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "กรุณาระบุ ID"]);
}
?>