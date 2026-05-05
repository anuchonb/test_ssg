<?php
// api/kpi/list.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;

if($case_id) {
    try {
        $query = "SELECT k.*, u.name as checker_name
                  FROM kpi_checks k
                  LEFT JOIN users u ON k.checker_id = u.id
                  WHERE k.case_id = :case_id
                  ORDER BY k.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':case_id', $case_id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            "success" => true,
            "data" => $data
        ], JSON_UNESCAPED_UNICODE);
        
    } catch(PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "กรุณาระบุ Case ID"
    ]);
}
?>