<?php
// api/cases/get.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id) {
    try {
        $query = "SELECT cs.*, 
                  c.name as customer_name, c.phone,
                  u.name as owner_name
                  FROM cases cs
                  JOIN customers c ON cs.customer_id = c.id
                  LEFT JOIN users u ON cs.owner_id = u.id
                  WHERE cs.id = :id LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $case = $stmt->fetch(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode(["success" => true, "data" => $case], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "ไม่พบเคส"]);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "กรุณาระบุ ID"]);
}
?>