<?php
// api/logs/list.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 25;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

$conditions = [];
$params = [];

if($search) {
    $conditions[] = "(ca.action LIKE :search OR u.name LIKE :search2)";
    $params[':search'] = "%{$search}%";
    $params[':search2'] = "%{$search}%";
}

if($type) {
    switch($type) {
        case 'login':
            $conditions[] = "(ca.action LIKE '%Login%' OR ca.action LIKE '%Logout%' OR ca.action LIKE '%เข้าสู่ระบบ%' OR ca.action LIKE '%ออกจากระบบ%')";
            break;
        case 'case':
            $conditions[] = "(ca.action LIKE '%Case%' OR ca.action LIKE '%เคส%')";
            break;
        case 'customer':
            $conditions[] = "(ca.action LIKE '%Customer%' OR ca.action LIKE '%ลูกค้า%')";
            break;
        case 'kpi':
            $conditions[] = "ca.action LIKE '%KPI%'";
            break;
        case 'support':
            $conditions[] = "(ca.action LIKE '%Bank%' OR ca.action LIKE '%Document%' OR ca.action LIKE '%Debt%' OR ca.action LIKE '%Mortgage%' OR ca.action LIKE '%Inspection%')";
            break;
    }
}

if($user_id) {
    $conditions[] = "ca.user_id = :user_id";
    $params[':user_id'] = $user_id;
}

if($date_from) {
    $conditions[] = "DATE(ca.created_at) >= :date_from";
    $params[':date_from'] = $date_from;
}

if($date_to) {
    $conditions[] = "DATE(ca.created_at) <= :date_to";
    $params[':date_to'] = $date_to;
}

$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

try {
    // Count
    $countQuery = "SELECT COUNT(*) as total FROM case_activities ca LEFT JOIN users u ON ca.user_id = u.id {$whereClause}";
    $countStmt = $db->prepare($countQuery);
    foreach($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $total = $countStmt->fetch()['total'];
    
    $total_pages = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;
    
    // Get data
    $query = "SELECT ca.*, u.name as user_name, u.role as user_role
              FROM case_activities ca 
              LEFT JOIN users u ON ca.user_id = u.id 
              {$whereClause}
              ORDER BY ca.created_at DESC 
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