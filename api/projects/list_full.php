<?php
// api/projects/list_full.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$zone = isset($_GET['zone']) ? $_GET['zone'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$conditions = [];
$params = [];

if($search) {
    $conditions[] = "(p.name LIKE :search OR p.zone LIKE :search2)";
    $params[':search'] = "%{$search}%";
    $params[':search2'] = "%{$search}%";
}

if($zone) {
    $conditions[] = "p.zone = :zone";
    $params[':zone'] = $zone;
}

$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// Sort order - ใช้ columns ที่มีอยู่จริง
$orderClause = "ORDER BY p.created_at DESC";
switch($sort) {
    case 'oldest': $orderClause = "ORDER BY p.created_at ASC"; break;
    case 'name_asc': $orderClause = "ORDER BY p.name ASC"; break;
    case 'name_desc': $orderClause = "ORDER BY p.name DESC"; break;
    case 'price_asc': $orderClause = "ORDER BY p.price ASC"; break;
    case 'price_desc': $orderClause = "ORDER BY p.price DESC"; break;
}

try {
    // Count
    $countQuery = "SELECT COUNT(*) as total FROM projects p {$whereClause}";
    $countStmt = $db->prepare($countQuery);
    foreach($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $total = $countStmt->fetch()['total'];
    
    $total_pages = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;
    
    // ใช้เฉพาะ columns ที่มีในตาราง projects จริงๆ
    $query = "SELECT p.id, p.name, p.price, p.zone, p.created_at,
              (SELECT COUNT(*) FROM customers WHERE project_id = p.id) as customer_count,
              (SELECT COUNT(*) FROM cases cs 
               JOIN customers c ON cs.customer_id = c.id 
               WHERE c.project_id = p.id) as case_count
              FROM projects p
              {$whereClause}
              {$orderClause}
              LIMIT :offset, :per_page";
    
    $stmt = $db->prepare($query);
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
    $stmt->execute();
    
    $data = $stmt->fetchAll();
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $data,
        "pagination" => [
            "total" => intval($total),
            "total_pages" => $total_pages,
            "current_page" => $page,
            "per_page" => $per_page,
            "has_next" => $page < $total_pages,
            "has_prev" => $page > 1
        ]
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>