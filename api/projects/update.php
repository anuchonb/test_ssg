<?php
// api/projects/update.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->name)) {
    try {
        // ใช้เฉพาะ columns ที่มีในตาราง
        $query = "UPDATE projects SET 
            name = :name,
            price = :price,
            zone = :zone
            WHERE id = :id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $data->name);
        $stmt->bindParam(':price', $data->price);
        $stmt->bindParam(':zone', $data->zone);
        $stmt->bindParam(':id', $data->id, PDO::PARAM_INT);
        
        if($stmt->execute()) {
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "อัพเดทโครงการสำเร็จ"]);
        }
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
}
?>