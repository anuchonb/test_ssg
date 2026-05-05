<?php
// api/follow/get.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id) {
    $query = "SELECT * FROM follow_logs WHERE id = :id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $data = $stmt->fetch();
        http_response_code(200);
        echo json_encode(["success" => true, "data" => $data]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false]);
    }
}
?>