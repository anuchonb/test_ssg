<?php
// includes/line_notify.php
function sendLineToCaseOwner($db, $case_id, $message) {
    // ดึง LINE User ID ของเจ้าของเคส
    $stmt = $db->prepare("
        SELECT u.line_user_id, u.notify_enabled 
        FROM cases cs 
        JOIN users u ON cs.owner_id = u.id 
        WHERE cs.id = ? AND u.line_user_id IS NOT NULL AND u.notify_enabled = 1
    ");
    $stmt->execute([$case_id]);
    $user = $stmt->fetch();
    
    if ($user && $user['line_user_id']) {
        sendPushMessage($user['line_user_id'], $message);
    }
}

function sendLineToAllAdmins($db, $message) {
    $stmt = $db->query("SELECT line_user_id FROM users WHERE role IN ('admin','support') AND line_user_id IS NOT NULL AND notify_enabled = 1");
    while ($user = $stmt->fetch()) {
        sendPushMessage($user['line_user_id'], $message);
    }
}

function sendPushMessage($user_id, $message) {
    $token = getSetting('line_channel_token');
    if (empty($token)) return false;
    
    $ch = curl_init('https://api.line.me/v2/bot/message/push');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'to' => $user_id,
        'messages' => [['type' => 'text', 'text' => $message]]
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function getSetting($key) {
    global $db;
    $stmt = $db->prepare("SELECT value FROM master_dropdowns WHERE type='system_settings' AND value LIKE ?");
    $stmt->execute([$key . ':%']);
    $row = $stmt->fetchColumn();
    return $row ? explode(':', $row, 2)[1] : '';
}
?>