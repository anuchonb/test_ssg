<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT VERSION() as version";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "mysql_version" => $result['version']
    ]);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false]);
}
?>