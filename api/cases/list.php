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

// ✅ สร้าง WHERE conditions
$conditions = [];
$params = [];

if ($search) {
    $conditions[] = "(c.name LIKE ? OR c.phone LIKE ? OR cs.id LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($status) {
    $conditions[] = "cs.status = ?";
    $params[] = $status;
}

if ($grade) {
    $conditions[] = "c.grade = ?";
    $params[] = $grade;
}

if ($date_from) {
    $conditions[] = "DATE(cs.created_at) >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $conditions[] = "DATE(cs.created_at) <= ?";
    $params[] = $date_to;
}

// ✅ Admin Page เห็นเฉพาะเคสของตัวเอง
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin_page') {
    $conditions[] = "cs.owner_id = ?";
    $params[] = $_SESSION['user_id'];
}

$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

try {
    // ✅ วิธีแก้: สร้าง params สำหรับ COUNT โดยเฉพาะ (คัดลอก $params)
    $countParams = $params; // ✅ ใช้สำหรับ COUNT เท่านั้น

    $countQuery = "SELECT COUNT(*) as total 
                   FROM cases cs 
                   JOIN customers c ON cs.customer_id = c.id 
                   {$whereClause}";
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute($countParams); // ✅ ใช้ $countParams
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    $total_pages = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;

    // ✅ ใส่ LIMIT ใน SQL โดยตรง
    $query = "SELECT cs.*, 
              c.name as customer_name, 
              c.phone, 
              c.facebook,
              c.grade,
              c.customer_code,
              p.name as project_name
              FROM cases cs 
              JOIN customers c ON cs.customer_id = c.id 
              LEFT JOIN projects p ON c.project_id = p.id 
              {$whereClause}
              ORDER BY cs.created_at DESC 
              LIMIT {$offset}, {$per_page}"; // ✅ LIMIT ใน SQL

    $stmt = $db->prepare($query);
    $stmt->execute($params); // ✅ ใช้ $params เดิม (ไม่มี LIMIT)
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
    error_log("Cases List Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>