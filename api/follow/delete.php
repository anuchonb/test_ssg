<?php
// api/follow/delete.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$raw = file_get_contents("php://input");
$data = json_decode($raw);

if (!$data || empty($data->id)) {
    echo json_encode(["success" => false, "message" => "กรุณาระบุ ID"]);
    exit();
}

$id = intval($data->id);

try {
    // หา case_id ก่อนลบ
    $getQuery = "SELECT case_id FROM follow_logs WHERE id = ?";
    $getStmt = $db->prepare($getQuery);
    $getStmt->execute([$id]);
    $follow = $getStmt->fetch(PDO::FETCH_ASSOC);

    // ลบ
    $query = "DELETE FROM follow_logs WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);

    // อัปเดต follow_count
    if ($follow) {
        $updateQuery = "UPDATE cases SET follow_count = (SELECT COUNT(*) FROM follow_logs WHERE case_id = ?) WHERE id = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute([$follow['case_id'], $follow['case_id']]);
    }

    echo json_encode(["success" => true, "message" => "ลบสำเร็จ!"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>