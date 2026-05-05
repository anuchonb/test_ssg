<?php
// api/auth/session.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if(isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => true,
        "user_id" => $_SESSION['user_id'],
        "user_name" => $_SESSION['user_name'],
        "user_role" => $_SESSION['user_role']
    ]);
} else {
    echo json_encode(["success" => false]);
}
?>