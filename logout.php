<?php
// logout.php (วางใน root directory)
session_start();

// กำหนด timezone
date_default_timezone_set('Asia/Bangkok');

// ตรวจสอบว่ามี session หรือไม่
$was_logged_in = isset($_SESSION['user_id']);

// ถ้ามี session ให้ทำลาย
if($was_logged_in) {
    // ลบ session variables
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
    
    // ลบ remember me cookies
    if(isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    if(isset($_COOKIE['remember_email'])) {
        setcookie('remember_email', '', time() - 3600, '/');
    }
    if(isset($_COOKIE['user_id'])) {
        setcookie('user_id', '', time() - 3600, '/');
    }
}

// Redirect ไปหน้า login พร้อมข้อความ
$message = $was_logged_in ? 'logout=success' : 'logout=already';
header("Location: index.php?{$message}");
exit();
?>