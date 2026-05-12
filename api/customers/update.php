<?php
// api/customers/update.php
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
        $query = "
        UPDATE customers SET 
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
            debt_status = :debt_status
        WHERE id = :id";
        
        $stmt = $db->prepare($query);
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
        $stmt->bindParam(":id", $data->id);
        
        if($stmt->execute()) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "อัพเดทข้อมูลลูกค้าสำเร็จ"
            ]);
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