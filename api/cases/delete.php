<?php
// api/cases/delete.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

// Only admin can delete
if($_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "ไม่มีสิทธิ์ลบ"]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->case_id)) {
    try {
        $db->beginTransaction();
        
        // Delete related records
        $tables = ['follow_logs', 'kpi_checks', 'pre_approvals', 'document_steps', 
                   'bank_submissions', 'approvals', 'debt_clearings', 'mortgages', 
                   'inspections', 'case_activities', 'files'];
        
        foreach($tables as $table) {
            $query = "DELETE FROM {$table} WHERE case_id = :case_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":case_id", $data->case_id);
            $stmt->execute();
        }
        
        // Delete case
        $query = "DELETE FROM cases WHERE id = :case_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":case_id", $data->case_id);
        $stmt->execute();
        
        $db->commit();
        
        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Case deleted"]);
        
    } catch(PDOException $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>