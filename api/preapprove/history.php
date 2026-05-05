<?php
// api/preapprove/history.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;

if (!$case_id) {
    echo json_encode([
        "success" => false,
        "message" => "กรุณาระบุ Case ID"
    ]);
    exit();
}

try {
    $query = "SELECT * FROM pre_approvals WHERE case_id = ? ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$case_id]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // เพิ่ม label สถานะ
    $status_labels = [
        'processing' => 'กำลังดำเนินการ',
        'approved' => 'อนุมัติ',
        'rejected' => 'ปฏิเสธ'
    ];

    foreach ($data as &$row) {
        $row['status_label'] = isset($status_labels[$row['status']]) ? $status_labels[$row['status']] : $row['status'];
    }

    echo json_encode([
        "success" => true,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>