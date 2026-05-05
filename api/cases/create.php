<?php
session_start();

if(!isset($_SESSION['logged_in'])) {
    http_response_code(401);
    echo json_encode(array("success" => false, "message" => "Unauthorized"));
    exit();
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->customer_id)) {
    try {
        $db->beginTransaction();
        
        // Create case
        $query = "INSERT INTO cases SET 
            customer_id = :customer_id,
            case_date = NOW(),
            status = 'ส่งเคส',
            follow_status = 'pending',
            follow_count = 0,
            owner_id = :owner_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":customer_id", $data->customer_id);
        $stmt->bindParam(":owner_id", $data->owner_id);
        $stmt->execute();
        
        $case_id = $db->lastInsertId();
        
        // Log activity
        $activity_query = "INSERT INTO case_activities SET 
            case_id = :case_id,
            action = 'Case Created',
            user_id = :user_id";
        
        $activity_stmt = $db->prepare($activity_query);
        $activity_stmt->bindParam(":case_id", $case_id);
        $activity_stmt->bindParam(":user_id", $data->owner_id);
        $activity_stmt->execute();
        
        $db->commit();
        
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Case created successfully",
            "case_id" => $case_id
        ));
        
    } catch(PDOException $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(array(
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Customer ID required"
    ));
}
?>