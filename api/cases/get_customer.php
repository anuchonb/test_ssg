<?php
// api/cases/get_customer.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;

if($case_id) {
    try {
        $query = "SELECT c.*, p.name as project_name
                  FROM cases cs
                  JOIN customers c ON cs.customer_id = c.id
                  LEFT JOIN projects p ON c.project_id = p.id
                  WHERE cs.id = :case_id LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':case_id', $case_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode(["success" => true, "data" => $customer], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลลูกค้า"]);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "กรุณาระบุ Case ID"]);
}
?>