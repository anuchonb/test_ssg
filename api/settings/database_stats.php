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
    $dbname = 'test_ssg_db';
    $query = "SELECT 
        TABLE_NAME as name,
        COALESCE(TABLE_ROWS, 0) as `rows`,
        ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024, 2) as size_kb
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA = :dbname
        ORDER BY TABLE_NAME";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':dbname' => $dbname]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format size
    foreach($data as &$table) {
        if($table['size_kb'] > 1024) {
            $table['size'] = round($table['size_kb'] / 1024, 2) . ' MB';
        } else {
            $table['size'] = $table['size_kb'] . ' KB';
        }
    }
    
    http_response_code(200);
    echo json_encode(["success" => true, "data" => $data]);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false]);
}
?>