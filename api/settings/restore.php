<?php
// api/settings/restore.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/database.php';

// ✅ เฉพาะ admin เท่านั้น
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "ไม่มีสิทธิ์ในการกู้คืนข้อมูล"
    ]);
    exit();
}

// ✅ ตรวจสอบว่ามีไฟล์อัปโหลด
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณาเลือกไฟล์สำรองข้อมูล (.sql)"
    ]);
    exit();
}

$file = $_FILES['file'];

// ✅ ตรวจสอบนามสกุลไฟล์
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'sql' && $ext !== 'zip') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "กรุณาเลือกไฟล์ .sql เท่านั้น"
    ]);
    exit();
}

// ✅ ตรวจสอบขนาดไฟล์ (สูงสุด 50MB)
$maxSize = 50 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ไฟล์มีขนาดเกิน 50MB"
    ]);
    exit();
}

// ✅ อ่านไฟล์
$sqlContent = file_get_contents($file['tmp_name']);

if (empty($sqlContent)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ไฟล์ว่างเปล่า"
    ]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

try {
    // ✅ เริ่ม transaction
    $db->beginTransaction();

    // ✅ สำรองข้อมูลก่อนกู้คืน (auto backup)
    $backupFile = '../../uploads/backups/auto_backup_before_restore_' . date('Y-m-d_H-i-s') . '.sql';
    $backupDir = dirname($backupFile);
    if (!file_exists($backupDir)) {
        mkdir($backupDir, 0777, true);
    }
    
    // สร้าง backup ด่วน
    createQuickBackup($db, $backupFile);

    // ✅ แยก SQL เป็นคำสั่งๆ
    $queries = parseSQL($sqlContent);

    $successCount = 0;
    $errorCount = 0;
    $errors = [];

    // ✅ รันทีละคำสั่ง
    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query)) continue;
        
        // ข้ามคำสั่งที่ไม่จำเป็น
        if (preg_match('/^(SET|USE|START TRANSACTION|COMMIT|\/\*|--)/i', $query)) {
            continue;
        }

        try {
            $db->exec($query);
            $successCount++;
        } catch (PDOException $e) {
            $errorCount++;
            $errors[] = [
                'query' => substr($query, 0, 100) . '...',
                'error' => $e->getMessage()
            ];
        }
    }

    // ✅ Commit transaction
    $db->commit();

    // ✅ Log activity
    $logQuery = "INSERT INTO case_activities (case_id, action, user_id) VALUES (NULL, ?, ?)";
    $logStmt = $db->prepare($logQuery);
    $action = "Database restored from: " . $file['name'] . " (Success: {$successCount}, Errors: {$errorCount})";
    $logStmt->execute([$action, $_SESSION['user_id']]);

    echo json_encode([
        "success" => true,
        "message" => "กู้คืนข้อมูลสำเร็จ!",
        "data" => [
            "filename" => $file['name'],
            "total_queries" => $successCount + $errorCount,
            "success_count" => $successCount,
            "error_count" => $errorCount,
            "backup_before" => basename($backupFile),
            "errors" => $errorCount > 0 ? array_slice($errors, 0, 5) : []
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "การกู้คืนล้มเหลว: " . $e->getMessage()
    ]);
}

/**
 * แยก SQL content เป็น array ของ queries
 */
function parseSQL($sql) {
    // ลบ comments
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    $sql = preg_replace('/--.*$/m', '', $sql);
    
    // แยกด้วย semicolon
    $queries = explode(';', $sql);
    
    // ลบช่องว่าง
    $queries = array_map('trim', $queries);
    $queries = array_filter($queries);
    
    return $queries;
}

/**
 * สร้าง backup ด่วนก่อน restore
 */
function createQuickBackup($db, $filename) {
    $output = "-- Quick Backup before Restore\n";
    $output .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
    
    $tables = [];
    $stmt = $db->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    foreach ($tables as $table) {
        $createStmt = $db->query("SHOW CREATE TABLE `{$table}`");
        $createRow = $createStmt->fetch(PDO::FETCH_NUM);
        $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $output .= $createRow[1] . ";\n\n";
        
        $dataStmt = $db->query("SELECT * FROM `{$table}`");
        $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($rows)) {
            $columns = array_keys($rows[0]);
            $columnList = '`' . implode('`, `', $columns) . '`';
            
            foreach ($rows as $row) {
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = $db->quote($value);
                    }
                }
                $output .= "INSERT INTO `{$table}` ({$columnList}) VALUES (" . implode(', ', $values) . ");\n";
            }
            $output .= "\n";
        }
    }
    
    file_put_contents($filename, $output);
}
?>