<?php
// api/customers/list.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build conditions
$conditions = [];
$params = [];

if($search) {
    $conditions[] = "(c.name LIKE :search OR c.phone LIKE :search2 OR c.customer_code LIKE :search3)";
    $params[':search'] = "%{$search}%";
    $params[':search2'] = "%{$search}%";
    $params[':search3'] = "%{$search}%";
}

if(!empty($_GET['channel'])) {
    $conditions[] = "c.channel = :channel";
    $params[':channel'] = $_GET['channel'];
}

if(!empty($_GET['grade'])) {
    $conditions[] = "c.grade = :grade";
    $params[':grade'] = $_GET['grade'];
}

if(!empty($_GET['project_id'])) {
    $conditions[] = "c.project_id = :project_id";
    $params[':project_id'] = $_GET['project_id'];
}

if(!empty($_GET['debt_status'])) {
    $conditions[] = "c.debt_status = :debt_status";
    $params[':debt_status'] = $_GET['debt_status'];
}

$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

try {
    // Count
    $countQuery = "SELECT COUNT(*) as total FROM customers c {$whereClause}";
    $countStmt = $db->prepare($countQuery);
    foreach($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $total = $countStmt->fetch()['total'];
    
    $total_pages = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;
    
    // Get data with case info
    $query = "
        SELECT c.*, p.name as project_name, cs.id as case_id, cs.status as case_status
        FROM customers c
        LEFT JOIN projects p ON c.project_id = p.id
        LEFT JOIN cases cs ON c.id = cs.customer_id
            {$whereClause}
        ORDER BY c.created_at DESC
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