<?php
// api/customers/delete.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// เฉพาะ admin เท่านั้นที่ลบได้
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "คุณไม่มีสิทธิ์ในการลบ"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)) {
    try {
        $db->beginTransaction();
        
        // ตรวจสอบว่ามีเคสหรือไม่
        $check_query = "SELECT id FROM cases WHERE customer_id = :customer_id";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':customer_id', $data->id);
        $check_stmt->execute();
        $cases = $check_stmt->fetchAll();
        
        // ลบข้อมูลที่เกี่ยวข้องกับเคส
        foreach($cases as $case) {
            $tables = ['follow_logs', 'kpi_checks', 'pre_approvals', 'document_steps', 
                       'bank_submissions', 'approvals', 'debt_items', 'debt_clearings', 
                       'mortgages', 'inspections', 'case_activities', 'files'];
            
            foreach($tables as $table) {
                $delete_query = "DELETE FROM {$table} WHERE case_id = :case_id";
                $delete_stmt = $db->prepare($delete_query);
                $delete_stmt->bindParam(':case_id', $case['id']);
                $delete_stmt->execute();
            }
        }
        
        // ลบเคส
        $delete_cases = "DELETE FROM cases WHERE customer_id = :customer_id";
        $delete_cases_stmt = $db->prepare($delete_cases);
        $delete_cases_stmt->bindParam(':customer_id', $data->id);
        $delete_cases_stmt->execute();
        
        // ลบลูกค้า
        $delete_customer = "DELETE FROM customers WHERE id = :id";
        $delete_customer_stmt = $db->prepare($delete_customer);
        $delete_customer_stmt->bindParam(':id', $data->id);
        $delete_customer_stmt->execute();
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "ลบข้อมูลสำเร็จ"
        ]);
        
    } catch(PDOException $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "กรุณาระบุ ID"]);
}
?>