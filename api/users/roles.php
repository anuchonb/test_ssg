<?php
// api/users/roles.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$roles = [
    [
        "value" => "admin_page",
        "label" => "Admin Page",
        "description" => "กรอกข้อมูลลูกค้า ส่งเคส ติดตาม",
        "color" => "primary",
        "icon" => "fas fa-file-alt"
    ],
    [
        "value" => "kpi",
        "label" => "KPI",
        "description" => "ตรวจสอบคุณภาพการสนทนา",
        "color" => "warning",
        "icon" => "fas fa-check-circle"
    ],
    [
        "value" => "support",
        "label" => "Support",
        "description" => "ทำเอกสาร ส่งธนาคาร ปิดหนี้ จำนอง",
        "color" => "success",
        "icon" => "fas fa-headset"
    ],
    [
        "value" => "admin",
        "label" => "Admin",
        "description" => "ดูแลระบบทั้งหมด",
        "color" => "danger",
        "icon" => "fas fa-crown"
    ]
];

http_response_code(200);
echo json_encode([
    "success" => true,
    "data" => $roles
]);
?>