<?php
// api/projects/create.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->name)) {
    try {
        // ใช้เฉพาะ columns ที่มีในตาราง projects
        $query = "INSERT INTO projects SET 
            name = :name,
            price = :price,
            zone = :zone,
            created_at = NOW()";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $data->name);
        $stmt->bindParam(':price', $data->price);
        $stmt->bindParam(':zone', $data->zone);
        
        if($stmt->execute()) {
            $project_id = $db->lastInsertId();
            
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "เพิ่มโครงการสำเร็จ",
                "project_id" => $project_id
            ]);
        }
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "กรุณากรอกชื่อโครงการ"]);
}
?>