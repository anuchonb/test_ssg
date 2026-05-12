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
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$channel = isset($_GET['channel']) ? trim($_GET['channel']) : '';
$grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
$debt_status = isset($_GET['debt_status']) ? trim($_GET['debt_status']) : '';

$conditions = [];
$params = [];

if ($search) {
    $conditions[] = "(c.name LIKE ? OR c.phone LIKE ? OR c.customer_code LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($channel) { $conditions[] = "c.channel = ?"; $params[] = $channel; }
if ($grade) { $conditions[] = "c.grade = ?"; $params[] = $grade; }
if ($project_id) { $conditions[] = "c.project_id = ?"; $params[] = $project_id; }
if ($debt_status) { $conditions[] = "c.debt_status = ?"; $params[] = $debt_status; }

// Admin Page เห็นเฉพาะลูกค้าที่ตัวเองสร้าง
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin_page') {
    $conditions[] = "c.created_by = ?";
    $params[] = $_SESSION['user_id'];
}

$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

try {
    // COUNT
    $countQuery = "SELECT COUNT(*) as total FROM customers c {$whereClause}";
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute($params);
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    $total_pages = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;

    // ✅ SELECT - ใช้ Subquery ป้องกันลูกค้าซ้ำ
    $query = "
        SELECT c.*, p.name as project_name,
            (SELECT cs.id FROM cases cs WHERE cs.customer_id = c.id LIMIT 1) as case_id,
            (SELECT cs.status FROM cases cs WHERE cs.customer_id = c.id LIMIT 1) as case_status
        FROM customers c
        LEFT JOIN projects p ON c.project_id = p.id
            {$whereClause}
        ORDER BY c.created_at DESC 
        LIMIT {$offset}, {$per_page}";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>