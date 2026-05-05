<?php
// api/cases/list.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
$date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

// Build WHERE conditions
$conditions = [];
$params = [];

if($search) {
    $conditions[] = "(c.name LIKE :search1 OR c.phone LIKE :search2 OR cs.id LIKE :search3)";
    $params[':search1'] = "%{$search}%";
    $params[':search2'] = "%{$search}%";
    $params[':search3'] = "%{$search}%";
}

if($status) {
    $conditions[] = "cs.status = :status";
    $params[':status'] = $status;
}

if($grade) {
    $conditions[] = "c.grade = :grade";
    $params[':grade'] = $grade;
}

if($date_from) {
    $conditions[] = "DATE(cs.created_at) >= :date_from";
    $params[':date_from'] = $date_from;
}

if($date_to) {
    $conditions[] = "DATE(cs.created_at) <= :date_to";
    $params[':date_to'] = $date_to;
}

$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

try {
    // Count total
    $countQuery = "SELECT COUNT(*) as total 
                   FROM cases cs 
                   JOIN customers c ON cs.customer_id = c.id 
                   {$whereClause}";
    
    $countStmt = $db->prepare($countQuery);
    foreach($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $total_pages = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;
    
    // Get data
    $query = "SELECT cs.*, 
              c.name as customer_name, 
              c.phone, 
              c.grade,
              c.customer_code,
              p.name as project_name
              FROM cases cs 
              JOIN customers c ON cs.customer_id = c.id 
              LEFT JOIN projects p ON c.project_id = p.id 
              {$whereClause}
              ORDER BY cs.created_at DESC 
              LIMIT {$offset}, {$per_page}";
    
    $stmt = $db->prepare($query);
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $data,
        "pagination" => [
            "total" => (int)$total,
            "total_pages" => $total_pages,
            "current_page" => $page,
            "per_page" => $per_page,
            "has_next" => $page < $total_pages,
            "has_prev" => $page > 1
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    error_log("Cases List Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>