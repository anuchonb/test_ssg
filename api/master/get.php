<?php
// api/master/get.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-cache, must-revalidate");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$type = isset($_GET['type']) ? trim($_GET['type']) : '';

try {
    if(!empty($type)) {
        // ดึงข้อมูลตามประเภท
        $query = "SELECT id, type, value, is_active, created_at 
                  FROM master_dropdowns 
                  WHERE type = :type 
                  ORDER BY id ASC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->execute();
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ถ้าไม่มีข้อมูล ให้ส่ง array เปล่า
        if(empty($data)) {
            $data = [];
        }
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        
    } else {
        // ดึงประเภททั้งหมด (สำหรับ overview)
        $query = "SELECT type, 
                  COUNT(*) as total_count,
                  SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count
                  FROM master_dropdowns 
                  GROUP BY type 
                  ORDER BY type ASC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            "success" => true,
            "data" => $data
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch(PDOException $e) {
    error_log("Master Get Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>