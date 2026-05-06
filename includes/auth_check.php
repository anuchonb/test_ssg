<?php
// includes/auth_check.php
if(!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in
function checkAuth() {
    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        // Check if it's an AJAX request
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'กรุณาเข้าสู่ระบบใหม่',
                'redirect' => '../index.php'
            ]);
            exit();
        }
        
        // Regular request
        header("Location: ../index.php?timeout=1");
        exit();
    }
    
    // Check session timeout (30 minutes)
    $timeout = 1800; // 30 minutes
    if(isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $timeout)) {
        session_destroy();
        
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Session หมดอายุ กรุณาเข้าสู่ระบบใหม่',
                'redirect' => '../index.php'
            ]);
            exit();
        }
        header("Location: ../index.php?timeout=1");
        exit();
    }
    
    // Update last activity time
    $_SESSION['login_time'] = time();
}

// Check specific role
function checkRole($allowed_roles) {
    if(!isset($_SESSION['user_role'])) {
        return false;
    }
    
    if(is_array($allowed_roles)) {
        return in_array($_SESSION['user_role'], $allowed_roles);
    }
    
    return $_SESSION['user_role'] === $allowed_roles;
}

// Require specific role or redirect
function requireRole($allowed_roles, $redirect = 'dashboard.php') {
    if(!checkRole($allowed_roles)) {
        header("Location: $redirect");
        exit();
    }
}

// Run auth check
checkAuth();
?>