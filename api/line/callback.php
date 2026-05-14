<?php
// api/line/callback.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=UTF-8");

include_once '../../config/database.php';

// ✅ ดึงค่าจาก DB
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
$channel_secret = getLineSetting('line_channel_secret');

$state = $_GET['state'] ?? '';
if (empty($state) || $state !== ($_SESSION['line_state'] ?? '')) {
    die("Invalid state");
}

$code = $_GET['code'] ?? '';
if (empty($code)) die("No authorization code");

try {
    $token_url = 'https://api.line.me/oauth2/v2.1/token';
    $post_data = [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => getLineSetting('line_callback_url'),
        'client_id' => $channel_id,
        'client_secret' => $channel_secret
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $token_data = json_decode($response, true);
    $access_token = $token_data['access_token'];

    $profile_url = 'https://api.line.me/v2/profile';
    $ch = curl_init($profile_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$access_token}"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $profile_response = curl_exec($ch);
    curl_close($ch);

    $profile = json_decode($profile_response, true);

    if (isset($profile['userId']) && isset($_SESSION['user_id'])) {
        $database = new Database();
        $db = $database->getConnection();

        $stmt = $db->prepare("UPDATE users SET line_user_id=?, line_display_name=?, line_connected_at=NOW() WHERE id=?");
        $stmt->execute([$profile['userId'], $profile['displayName'] ?? '', $_SESSION['user_id']]);

        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:sans-serif;text-align:center;padding:50px;}h2{color:#06C755;}p{font-size:18px;}</style></head><body>';
        echo '<h2>✅ เชื่อมต่อ LINE สำเร็จ!</h2>';
        echo '<p>ชื่อ: ' . htmlspecialchars($profile['displayName'] ?? '-') . '</p>';
        echo '<p>หน้าต่างนี้จะปิดอัตโนมัติ...</p>';
        echo '<script>setTimeout(function(){ window.close(); }, 2000);</script>';
        echo '</body></html>';
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>