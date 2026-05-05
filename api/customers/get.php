<?php
// api/customers/get.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$phone = isset($_GET['phone']) ? $_GET['phone'] : '';

if($id || $phone) {
    try {
        if($id) {
            $query = "SELECT * FROM customers WHERE id = :id LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        } else {
            $query = "SELECT * FROM customers WHERE phone = :phone LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':phone', $phone);
        }
        
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $customer = $stmt->fetch();
            
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "data" => $customer
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "ไม่พบข้อมูลลูกค้า"
            ]);
        }
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "กรุณาระบุ ID หรือเบอร์โทร"]);
}
?>