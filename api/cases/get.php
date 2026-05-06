<?php
// api/cases/get.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    echo json_encode(["success" => false, "message" => "กรุณาระบุ ID"]);
    exit();
}

try {
    // ✅ JOIN ทุกตารางที่ต้องการ
    $query = "SELECT cs.*, 
              c.name as customer_name, 
              c.phone, 
              c.grade,
              c.customer_code,
              p.name as project_name,
              u.name as owner_name
              FROM cases cs
              JOIN customers c ON cs.customer_id = c.id
              LEFT JOIN projects p ON c.project_id = p.id
              LEFT JOIN users u ON cs.owner_id = u.id
              WHERE cs.id = ? LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        $case = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            "success" => true,
            "data" => $case
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "ไม่พบเคส #{$id}"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>