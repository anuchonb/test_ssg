<?php
// api/debt/get.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;

if (!$case_id) {
    echo json_encode(["success" => false, "message" => "กรุณาระบุ Case ID"]);
    exit();
}

try {
    // ดึง debt clearing
    $query = "SELECT * FROM debt_clearings WHERE case_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$case_id]);
    $debt = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($debt) {
        // ดึงรายการหนี้
        $itemQuery = "SELECT * FROM debt_items WHERE debt_id = ?";
        $itemStmt = $db->prepare($itemQuery);
        $itemStmt->execute([$debt['id']]);
        $debt['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        "success" => true,
        "data" => $debt ?: null
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>