<?php
// api/users/list.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// ตรวจสอบการ login
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบ"]);
    exit();
}

try {
    // Get parameters
    $role = isset($_GET['role']) ? $_GET['role'] : '';
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // Build query conditions
    $conditions = [];
    $params = [];
    
    // กรองตาม role (ถ้าระบุ)
    if($role) {
        $conditions[] = "role = :role";
        $params[':role'] = $role;
    }
    
    // ค้นหา
    if($search) {
        $conditions[] = "(name LIKE :search OR email LIKE :search2)";
        $params[':search'] = "%{$search}%";
        $params[':search2'] = "%{$search}%";
    }
    
    // ถ้าเป็น admin_page จะเห็นเฉพาะตัวเอง
    if($_SESSION['user_role'] === 'admin_page') {
        $conditions[] = "id = :user_id";
        $params[':user_id'] = $_SESSION['user_id'];
    }
    
    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
    
    // Query
    $query = "SELECT id, name, email, role, created_at 
              FROM users 
              {$whereClause} 
              ORDER BY role ASC, name ASC";
    
    $stmt = $db->prepare($query);
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $users = $stmt->fetchAll();
    
    // เพิ่มข้อมูลเพิ่มเติม
    foreach($users as &$user) {
        // นับจำนวนเคสที่ดูแล
        $caseQuery = "SELECT COUNT(*) as count FROM cases WHERE owner_id = :user_id";
        $caseStmt = $db->prepare($caseQuery);
        $caseStmt->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
        $caseStmt->execute();
        $user['total_cases'] = intval($caseStmt->fetch()['count']);
        
        // Role label
        $roleLabels = [
            'admin' => 'ผู้ดูแลระบบ',
            'admin_page' => 'Admin Page',
            'kpi' => 'KPI',
            'support' => 'Support'
        ];
        $user['role_label'] = isset($roleLabels[$user['role']]) ? $roleLabels[$user['role']] : $user['role'];
        
        // Role color
        $roleColors = [
            'admin' => 'danger',
            'admin_page' => 'primary',
            'kpi' => 'warning',
            'support' => 'success'
        ];
        $user['role_color'] = isset($roleColors[$user['role']]) ? $roleColors[$user['role']] : 'secondary';
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $users,
        "total" => count($users)
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    error_log("Users List Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "เกิดข้อผิดพลาดในการดึงข้อมูล"
    ]);
}
?>