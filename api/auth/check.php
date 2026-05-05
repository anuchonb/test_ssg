<?php
// api/auth/check.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "authenticated" => true,
        "user" => array(
            "id" => $_SESSION['user_id'],
            "name" => $_SESSION['user_name'],
            "email" => $_SESSION['user_email'],
            "role" => $_SESSION['user_role']
        )
    ));
} else {
    http_response_code(401);
    echo json_encode(array(
        "success" => false,
        "authenticated" => false,
        "message" => "กรุณาเข้าสู่ระบบ"
    ));
}
?>