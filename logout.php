<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
$was_logged_in = isset($_SESSION['user_id']);
if($was_logged_in) {
    $_SESSION = array();
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
    session_destroy();
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
$message = $was_logged_in ? 'logout=success' : 'logout=already';
header("Location: index.php?{$message}");
exit();
?>