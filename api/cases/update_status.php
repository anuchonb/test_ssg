<?php
// api/cases/update_status.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->case_id) && !empty($data->status)) {
    try {
        $query = "UPDATE cases SET 
            status = :status,
            cancel_reason = :cancel_reason
            WHERE id = :case_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":status", $data->status);
        $stmt->bindParam(":cancel_reason", $data->cancel_reason);
        $stmt->bindParam(":case_id", $data->case_id);
        $stmt->execute();
        
        // Log activity
        $activity_query = "INSERT INTO case_activities SET 
            case_id = :case_id,
            action = :action,
            user_id = :user_id";
        
        $activity_stmt = $db->prepare($activity_query);
        $activity_stmt->bindParam(":case_id", $data->case_id);
        $action = "Status changed to: {$data->status}";
        $activity_stmt->bindParam(":action", $action);
        $activity_stmt->bindParam(":user_id", $_SESSION['user_id']);
        $activity_stmt->execute();
        
        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Status updated"]);
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>