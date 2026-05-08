<?php
// api/line/login.php
session_start();
header("Access-Control-Allow-Origin: *");

include_once '../../config/database.php';

// ✅ ดึงค่า Channel ID + Callback URL จาก DB
function getLineSetting($key) {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare("SELECT value FROM master_dropdowns WHERE type='system_settings' AND value LIKE ?");
    $stmt->execute([$key . ':%']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $parts = explode(':', $row['value'], 2);
        return $parts[1] ?? '';
    }
    return '';
}

$channel_id = getLineSetting('line_channel_id');
$callback_url = getLineSetting('line_callback_url');

if (empty($channel_id) || empty($callback_url)) {
    die("LINE Login ยังไม่ได้ตั้งค่า Channel ID หรือ Callback URL");
}

$state = bin2hex(random_bytes(16));
$_SESSION['line_state'] = $state;

$url = "https://access.line.me/oauth2/v2.1/authorize?" . http_build_query([
    'response_type' => 'code',
    'client_id' => $channel_id,
    'redirect_uri' => $callback_url,
    'state' => $state,
    'scope' => 'profile openid',
    'bot_prompt' => 'normal'
]);

header("Location: {$url}");
exit();
?>