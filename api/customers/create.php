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
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->name) &&
    !empty($data->phone)
) {
    try {
        // Check if customer exists
        $check_query = "SELECT id FROM customers WHERE phone = :phone LIMIT 1";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(":phone", $data->phone);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            // Update existing customer
            $row = $check_stmt->fetch();
            $customer_id = $row['id'];
            
            $query = "
            UPDATE customers SET 
                name = :name,
                facebook = :facebook,
                line_id = :line_id,
                page_name = :page_name,
                channel = :channel,
                grade = :grade,
                project_id = :project_id,
                price = :price,
                cashback = :cashback,
                living_type = :living_type,
                zone = :zone,
                company_name = :company_name,
                work_age_month = :work_age_month,
                welfare = :welfare,
                debt_status = :debt_status
            WHERE id = :id";
                
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $customer_id);
            
        } else {
            // Create new customer
            $customer_code = 'CUS' . date('Ymd') . rand(1000, 9999);
            
            $query = "
            INSERT INTO customers SET 
                customer_code = :customer_code,
                name = :name,
                phone = :phone,
                facebook = :facebook,
                line_id = :line_id,
                page_name = :page_name,
                channel = :channel,
                grade = :grade,
                project_id = :project_id,
                price = :price,
                cashback = :cashback,
                living_type = :living_type,
                zone = :zone,
                company_name = :company_name,
                work_age_month = :work_age_month,
                welfare = :welfare,
                debt_status = :debt_status,
                created_by = :created_by";
                
            $stmt = $db->prepare($query);
            $stmt->bindParam(":customer_code", $customer_code);
        }
        
        // Bind parameters
        $stmt->bindParam(":name", $data->name);
        $stmt->bindParam(":phone", $data->phone);
        $stmt->bindParam(":facebook", $data->facebook);
        $stmt->bindParam(":line_id", $data->line_id);
        $stmt->bindParam(":page_name", $data->page_name);
        $stmt->bindParam(":channel", $data->channel);
        $stmt->bindParam(":grade", $data->grade);
        $stmt->bindParam(":project_id", $data->project_id);
        $stmt->bindParam(":price", $data->price);
        $stmt->bindParam(":cashback", $data->cashback);
        $stmt->bindParam(":living_type", $data->living_type);
        $stmt->bindParam(":zone", $data->zone);
        $stmt->bindParam(":company_name", $data->company_name);
        $stmt->bindParam(":work_age_month", $data->work_age_month);
        $stmt->bindParam(":welfare", $data->welfare);
        $stmt->bindParam(":debt_status", $data->debt_status);
        $stmt->bindParam(":created_by", $data->created_by);
        
        if($stmt->execute()) {
            if(!isset($customer_id)) {
                $customer_id = $db->lastInsertId();
            }
            
            http_response_code(201);
            echo json_encode(array(
                "success" => true,
                "message" => "Customer saved successfully",
                "customer_id" => $customer_id
            ));
        } else {
            http_response_code(503);
            echo json_encode(array(
                "success" => false,
                "message" => "Unable to save customer"
            ));
        }
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(array(
            "success" => false,
            "message" => "Database error: " . $e->getMessage()
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Missing required fields"
    ));
}
?>