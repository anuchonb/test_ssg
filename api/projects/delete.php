<?php
// api/projects/delete.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)) {
    try {
        // Check if has customers
        $check_query = "SELECT COUNT(*) as count FROM customers WHERE project_id = :id";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':id', $data->id);
        $check_stmt->execute();
        $count = $check_stmt->fetch()['count'];
        
        if($count > 0) {
            // Update customers to null project
            $update_query = "UPDATE customers SET project_id = NULL WHERE project_id = :id";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(':id', $data->id);
            $update_stmt->execute();
        }
        
        // Delete project
        $query = "DELETE FROM projects WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $data->id);
        $stmt->execute();
        
        http_response_code(200);
        echo json_encode(["success" => true, "message" => "ลบโครงการสำเร็จ"]);
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>