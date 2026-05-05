<?php
// api/auth/logout.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// กำหนด timezone
date_default_timezone_set('Asia/Bangkok');

include_once '../../config/database.php';

// ตรวจสอบว่ามีการ login อยู่หรือไม่
if(isset($_SESSION['user_id'])) {
    try {
        // เชื่อมต่อฐานข้อมูล
        $database = new Database();
        $db = $database->getConnection();
        
        // บันทึก log การ logout
        $log_query = "INSERT INTO case_activities 
                      (case_id, action, user_id, created_at) 
                      VALUES (NULL, :action, :user_id, NOW())";
        
        $log_stmt = $db->prepare($log_query);
        $action = "User Logout: {$_SESSION['user_email']} (" . date('Y-m-d H:i:s') . ")";
        $log_stmt->bindParam(":action", $action);
        $log_stmt->bindParam(":user_id", $_SESSION['user_id'], PDO::PARAM_INT);
        $log_stmt->execute();
        
        // เก็บข้อมูลสำหรับ response
        $user_name = $_SESSION['user_name'];
        $user_role = $_SESSION['user_role'];
        
    } catch(PDOException $e) {
        // ถ้าเชื่อมต่อฐานข้อมูลไม่ได้ ก็ยังคง logout ได้
        error_log("Logout log error: " . $e->getMessage());
    }
    
    // ลบ session variables ทั้งหมด
    $_SESSION = array();
    
    // ลบ session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // ทำลาย session
    session_destroy();
    
    // ลบ remember me cookie (ถ้ามี)
    if(isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
        setcookie('remember_email', '', time() - 3600, '/');
    }
    
    // ตรวจสอบว่าเป็น AJAX request หรือไม่
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
       strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
        // Response สำหรับ AJAX
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "ออกจากระบบสำเร็จ",
            "redirect" => "../index.php"
        ]);
        
    } else {
        // Response สำหรับการเรียกปกติ
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "ออกจากระบบสำเร็จ กรุณารอสักครู่...",
            "redirect" => "../index.php"
        ]);
    }
    
} else {
    // ไม่ได้ login อยู่แล้ว
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
       strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "ไม่ได้เข้าใช้งานอยู่แล้ว",
            "redirect" => "../index.php"
        ]);
    } else {
        // Redirect ไปหน้า login โดยตรง
        header("Location: ../index.php");
        exit();
    }
}
?>