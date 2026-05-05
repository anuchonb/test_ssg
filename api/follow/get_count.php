<?php
// api/follow/get_count.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;

if ($case_id) {
    $query = "SELECT COUNT(*) as count FROM follow_logs WHERE case_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$case_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["count" => (int)$result['count']]);
} else {
    echo json_encode(["count" => 0]);
}
?>