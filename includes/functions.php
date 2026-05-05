<?php
// includes/functions.php

/**
 * Format number to Thai Baht
 */
function formatBaht($number) {
    if(!$number && $number !== 0) return '-';
    return number_format($number, 2) . ' บาท';
}

/**
 * Format date to Thai format
 */
function thaiDate($date, $format = 'full') {
    if(!$date) return '-';
    
    $timestamp = strtotime($date);
    $thai_months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม',
        4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
        7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน',
        10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    
    $day = date('j', $timestamp);
    $month = $thai_months[date('n', $timestamp)];
    $year = date('Y', $timestamp) + 543; // Convert to Buddhist year
    $time = date('H:i', $timestamp);
    
    switch($format) {
        case 'short':
            return "$day $month $year";
        case 'datetime':
            return "$day $month $year $time น.";
        default:
            return "$day $month $year";
    }
}

/**
 * Generate customer code
 */
function generateCustomerCode() {
    return 'CUS' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Generate case number
 */
function generateCaseNumber() {
    return 'CASE-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}

/**
 * Get status badge HTML
 */
function getStatusBadge($status) {
    $badges = [
        'ส่งเคส' => '<span class="badge bg-primary">ส่งเคส</span>',
        'กำลังติดตาม' => '<span class="badge bg-warning text-dark">กำลังติดตาม</span>',
        'อนุมัติ' => '<span class="badge bg-success">อนุมัติ</span>',
        'ยกเลิก' => '<span class="badge bg-danger">ยกเลิก</span>',
        'ไม่สนใจ' => '<span class="badge bg-secondary">ไม่สนใจ</span>',
        'วงเงินไม่ถึง' => '<span class="badge bg-info">วงเงินไม่ถึง</span>'
    ];
    
    return isset($badges[$status]) ? $badges[$status] : '<span class="badge bg-light text-dark">' . $status . '</span>';
}

/**
 * Calculate age from birth date
 */
function calculateAge($birthDate) {
    $birth = new DateTime($birthDate);
    $now = new DateTime();
    $interval = $birth->diff($now);
    return $interval->y;
}

/**
 * Calculate work experience in months
 */
function calculateWorkExperience($startDate) {
    $start = new DateTime($startDate);
    $now = new DateTime();
    $interval = $start->diff($now);
    return ($interval->y * 12) + $interval->m;
}

/**
 * Validate Thai phone number
 */
function validatePhone($phone) {
    // Remove non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check length (9-10 digits for Thai mobile)
    if(strlen($phone) < 9 || strlen($phone) > 10) {
        return false;
    }
    
    // Check if starts with 0
    if(substr($phone, 0, 1) !== '0') {
        return false;
    }
    
    return true;
}

/**
 * Mask sensitive data
 */
function maskPhone($phone) {
    if(strlen($phone) >= 9) {
        return substr($phone, 0, 3) . 'XXX' . substr($phone, -3);
    }
    return $phone;
}

/**
 * Get user permissions
 */
function getUserPermissions($role) {
    $permissions = [
        'admin' => ['all'],
        'admin_page' => ['customer_create', 'customer_edit', 'case_create', 'follow'],
        'kpi' => ['kpi_check', 'kpi_view'],
        'support' => ['document_manage', 'bank_submit', 'debt_manage', 'mortgage_manage', 'inspection']
    ];
    
    return isset($permissions[$role]) ? $permissions[$role] : [];
}

/**
 * Check if user has permission
 */
function hasPermission($permission) {
    if(!isset($_SESSION['user_role'])) {
        return false;
    }
    
    $permissions = getUserPermissions($_SESSION['user_role']);
    
    return in_array('all', $permissions) || in_array($permission, $permissions);
}

/**
 * Log activity
 */
function logActivity($db, $case_id, $action, $user_id) {
    try {
        $query = "INSERT INTO case_activities SET 
            case_id = :case_id,
            action = :action,
            user_id = :user_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":case_id", $case_id);
        $stmt->bindParam(":action", $action);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Upload file
 */
function uploadFile($file, $case_id, $type = 'document') {
    $target_dir = "../uploads/{$type}/{$case_id}/";
    
    // Create directory if not exists
    if(!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
    
    if(!in_array($file_extension, $allowed_extensions)) {
        return ['success' => false, 'message' => 'ประเภทไฟล์ไม่ถูกต้อง'];
    }
    
    if($file['size'] > 5000000) { // 5MB
        return ['success' => false, 'message' => 'ไฟล์ขนาดเกิน 5MB'];
    }
    
    $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if(move_uploaded_file($file['tmp_name'], $target_file)) {
        return [
            'success' => true,
            'file_path' => "uploads/{$type}/{$case_id}/{$new_filename}",
            'file_name' => $new_filename
        ];
    }
    
    return ['success' => false, 'message' => 'อัปโหลดไฟล์ไม่สำเร็จ'];
}

/**
 * Send LINE notification
 */
function sendLineNotify($message, $token) {
    $url = 'https://notify-api.line.me/api/notify';
    
    $headers = [
        'Authorization: Bearer ' . $token
    ];
    
    $post_data = [
        'message' => $message
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $http_code === 200;
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ip = '';
    
    if(isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif(isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return $ip;
}

/**
 * Pagination helper
 */
function getPagination($total, $page = 1, $per_page = 20) {
    $total_pages = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;
    
    return [
        'total' => $total,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'per_page' => $per_page,
        'offset' => $offset,
        'has_next' => $page < $total_pages,
        'has_prev' => $page > 1
    ];
}

/**
 * Escape HTML
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Debug function
 */
function debug($data, $die = true) {
    echo '<pre style="background: #f4f4f4; padding: 10px; border-radius: 5px; margin: 10px 0;">';
    print_r($data);
    echo '</pre>';
    
    if($die) {
        die();
    }
}

/**
 * Convert number to Thai text
 */
function numberToThaiText($number) {
    $thai_numbers = ['', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'];
    $thai_units = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];
    
    if($number == 0) return 'ศูนย์';
    
    $text = '';
    $str_number = strrev((string)$number);
    
    for($i = 0; $i < strlen($str_number); $i++) {
        $digit = intval($str_number[$i]);
        
        if($digit != 0) {
            if($i % 6 == 0 && $i > 0) {
                $text = 'ล้าน' . $text;
            }
            
            if($i % 6 == 1 && $digit == 1) {
                $text = 'สิบ' . $text;
            } elseif($i % 6 == 1 && $digit == 2) {
                $text = 'ยี่สิบ' . $text;
            } elseif($digit == 1 && $i % 6 == 0 && strlen($str_number) > 1) {
                $text = 'เอ็ด' . $text;
            } else {
                $text = $thai_numbers[$digit] . $thai_units[$i % 6] . $text;
            }
        }
    }
    
    return $text;
}
?>