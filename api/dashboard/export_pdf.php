<?php
// api/dashboard/export_pdf.php
session_start();
header("Access-Control-Allow-Origin: *");

include_once '../../config/database.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("ไม่มีสิทธิ์");
}

$database = new Database();
$db = $database->getConnection();

$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
$year  = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$months_th = [
    '', 'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน',
    'กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'
];
$month_name = $month ? $months_th[$month] : 'ทั้งปี';
$year_th    = $year + 543;

// ============ HELPER FUNCTION ============
function fetchOne($db, $sql, $params = []) {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

function fetchAll($db, $sql, $params = []) {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

try {
    if ($month) {
        // ✅ มีเดือน
        $total_cases    = fetchOne($db, "SELECT COUNT(*) FROM cases WHERE YEAR(created_at)=? AND MONTH(created_at)=?", [$year, $month]);
        $approved_cases = fetchOne($db, "SELECT COUNT(*) FROM cases WHERE YEAR(created_at)=? AND MONTH(created_at)=? AND status='อนุมัติ'", [$year, $month]);
        $total_value    = fetchOne($db, "SELECT COALESCE(SUM(a.total_amount),0) FROM approvals a JOIN cases cs ON a.case_id=cs.id WHERE YEAR(cs.created_at)=? AND MONTH(cs.created_at)=?", [$year, $month]);
        $projects       = fetchAll($db, "SELECT p.name, COUNT(cs.id) as count FROM projects p LEFT JOIN customers c ON c.project_id=p.id LEFT JOIN cases cs ON cs.customer_id=c.id WHERE YEAR(cs.created_at)=? AND MONTH(cs.created_at)=? GROUP BY p.id,p.name ORDER BY count DESC LIMIT 5", [$year, $month]);
        $staff          = fetchAll($db, "SELECT u.name, COUNT(cs.id) as cases, SUM(CASE WHEN cs.status='อนุมัติ' THEN 1 ELSE 0 END) as approved, COALESCE(SUM(a.total_amount),0) as value FROM users u LEFT JOIN cases cs ON cs.owner_id=u.id AND YEAR(cs.created_at)=? AND MONTH(cs.created_at)=? LEFT JOIN approvals a ON a.case_id=cs.id WHERE u.role='admin_page' GROUP BY u.id,u.name ORDER BY approved DESC LIMIT 5", [$year, $month]);
        $statuses       = fetchAll($db, "SELECT COALESCE(status,'ไม่ระบุ') as status, COUNT(*) as count FROM cases WHERE YEAR(created_at)=? AND MONTH(created_at)=? GROUP BY status ORDER BY count DESC", [$year, $month]);

    } elseif ($year) {
        // ✅ เฉพาะปี
        $total_cases    = fetchOne($db, "SELECT COUNT(*) FROM cases WHERE YEAR(created_at)=?", [$year]);
        $approved_cases = fetchOne($db, "SELECT COUNT(*) FROM cases WHERE YEAR(created_at)=? AND status='อนุมัติ'", [$year]);
        $total_value    = fetchOne($db, "SELECT COALESCE(SUM(a.total_amount),0) FROM approvals a JOIN cases cs ON a.case_id=cs.id WHERE YEAR(cs.created_at)=?", [$year]);
        $projects       = fetchAll($db, "SELECT p.name, COUNT(cs.id) as count FROM projects p LEFT JOIN customers c ON c.project_id=p.id LEFT JOIN cases cs ON cs.customer_id=c.id WHERE YEAR(cs.created_at)=? GROUP BY p.id,p.name ORDER BY count DESC LIMIT 5", [$year]);
        $staff          = fetchAll($db, "SELECT u.name, COUNT(cs.id) as cases, SUM(CASE WHEN cs.status='อนุมัติ' THEN 1 ELSE 0 END) as approved, COALESCE(SUM(a.total_amount),0) as value FROM users u LEFT JOIN cases cs ON cs.owner_id=u.id AND YEAR(cs.created_at)=? LEFT JOIN approvals a ON a.case_id=cs.id WHERE u.role='admin_page' GROUP BY u.id,u.name ORDER BY approved DESC LIMIT 5", [$year]);
        $statuses       = fetchAll($db, "SELECT COALESCE(status,'ไม่ระบุ') as status, COUNT(*) as count FROM cases WHERE YEAR(created_at)=? GROUP BY status ORDER BY count DESC", [$year]);

    } else {
        // ✅ ทั้งหมด
        $total_cases    = fetchOne($db, "SELECT COUNT(*) FROM cases");
        $approved_cases = fetchOne($db, "SELECT COUNT(*) FROM cases WHERE status='อนุมัติ'");
        $total_value    = fetchOne($db, "SELECT COALESCE(SUM(a.total_amount),0) FROM approvals a JOIN cases cs ON a.case_id=cs.id");
        $projects       = fetchAll($db, "SELECT p.name, COUNT(cs.id) as count FROM projects p LEFT JOIN customers c ON c.project_id=p.id LEFT JOIN cases cs ON cs.customer_id=c.id GROUP BY p.id,p.name ORDER BY count DESC LIMIT 5");
        $staff          = fetchAll($db, "SELECT u.name, COUNT(cs.id) as cases, SUM(CASE WHEN cs.status='อนุมัติ' THEN 1 ELSE 0 END) as approved, COALESCE(SUM(a.total_amount),0) as value FROM users u LEFT JOIN cases cs ON cs.owner_id=u.id LEFT JOIN approvals a ON a.case_id=cs.id WHERE u.role='admin_page' GROUP BY u.id,u.name ORDER BY approved DESC LIMIT 5");
        $statuses       = fetchAll($db, "SELECT COALESCE(status,'ไม่ระบุ') as status, COUNT(*) as count FROM cases GROUP BY status ORDER BY count DESC");
    }

    $total_cases    = (int)$total_cases;
    $approved_cases = (int)$approved_cases;
    $total_value    = (float)$total_value;

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// HTML Output
ob_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Executive Report - <?php echo $month_name . ' ' . $year_th; ?></title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Sarabun',sans-serif;font-size:14px;color:#333;padding:30px}
        .header{text-align:center;margin-bottom:20px;border-bottom:3px solid #667eea;padding-bottom:15px}
        .header h1{color:#667eea;font-size:24px;margin-bottom:5px}
        .header p{color:#666;font-size:14px}
        .kpi-row{display:flex;gap:10px;margin-bottom:25px}
        .kpi-card{flex:1;padding:15px;border-radius:8px;text-align:center;color:white}
        .kpi-card.purple{background:linear-gradient(135deg,#667eea,#764ba2)}
        .kpi-card.green{background:linear-gradient(135deg,#1cc88a,#13855c)}
        .kpi-card.blue{background:linear-gradient(135deg,#36b9cc,#258391)}
        .kpi-card.orange{background:linear-gradient(135deg,#f6c23e,#dda20a);color:#333}
        .kpi-card h2{font-size:26px;margin-bottom:5px}
        .kpi-card p{font-size:12px;opacity:.9}
        .section{margin-bottom:25px}
        .section h2{font-size:16px;color:#667eea;border-bottom:2px solid #e3e6f0;padding-bottom:8px;margin-bottom:12px}
        table{width:100%;border-collapse:collapse}
        table th{background:#667eea;color:white;padding:8px 12px;text-align:left;font-size:13px}
        table td{padding:8px 12px;border-bottom:1px solid #e3e6f0;font-size:13px}
        tbody tr:nth-child(even){background:#f8f9fc}
        .text-right{text-align:right}
        .text-center{text-align:center}
        .footer{text-align:center;margin-top:30px;padding-top:15px;border-top:1px solid #e3e6f0;color:#999;font-size:12px}
        .btn-print{position:fixed;top:20px;right:20px;background:#667eea;color:white;border:none;padding:10px 20px;border-radius:5px;cursor:pointer;font-size:14px;z-index:9999}
        .btn-print:hover{background:#764ba2}
        @media print{body{padding:0}.btn-print{display:none}}
    </style>
</head>
<body>

    <button class="btn-print" onclick="window.print()">🖨️ พิมพ์รายงาน / Save PDF</button>

    <div class="header">
        <h1>📊 รายงาน Executive Summary</h1>
        <p>ประจำเดือน <?php echo $month_name . ' ' . $year_th; ?> | วันที่: <?php echo date('d/m/') . $year_th; ?></p>
    </div>

    <div class="kpi-row">
        <div class="kpi-card purple"><h2><?php echo number_format($total_cases); ?></h2><p>เคสทั้งหมด</p></div>
        <div class="kpi-card green"><h2><?php echo number_format($approved_cases); ?></h2><p>อนุมัติแล้ว</p></div>
        <div class="kpi-card blue"><h2><?php echo number_format($total_value); ?> บาท</h2><p>มูลค่ารวม</p></div>
        <div class="kpi-card orange"><h2><?php echo $total_cases > 0 ? round($approved_cases/$total_cases*100,1) : 0; ?>%</h2><p>อัตราการอนุมัติ</p></div>
    </div>

    <div class="section">
        <h2>📋 สรุปสถานะเคส</h2>
        <table>
            <thead><tr><th>สถานะ</th><th class="text-center">จำนวน</th><th class="text-center">สัดส่วน</th></tr></thead>
            <tbody>
                <?php foreach($statuses as $s): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s['status']); ?></td>
                    <td class="text-center"><?php echo number_format($s['count']); ?></td>
                    <td class="text-center"><?php echo $total_cases > 0 ? round($s['count']/$total_cases*100,1).'%' : '0%'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>🏆 Top 5 โครงการ</h2>
        <table>
            <thead><tr><th>#</th><th>โครงการ</th><th class="text-center">จำนวนเคส</th></tr></thead>
            <tbody>
                <?php if(empty($projects)): ?>
                    <tr><td colspan="3" class="text-center">ไม่มีข้อมูล</td></tr>
                <?php else: ?>
                    <?php foreach($projects as $i=>$p): ?>
                    <tr><td><?php echo $i+1; ?></td><td><?php echo htmlspecialchars($p['name']?:'ไม่ระบุ'); ?></td><td class="text-center"><?php echo number_format($p['count']); ?></td></tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>👥 Top 5 พนักงาน</h2>
        <table>
            <thead><tr><th>#</th><th>พนักงาน</th><th class="text-center">เคส</th><th class="text-center">อนุมัติ</th><th class="text-right">มูลค่า</th></tr></thead>
            <tbody>
                <?php if(empty($staff)): ?>
                    <tr><td colspan="5" class="text-center">ไม่มีข้อมูล</td></tr>
                <?php else: ?>
                    <?php foreach($staff as $i=>$s): ?>
                    <tr><td><?php echo $i+1; ?></td><td><?php echo htmlspecialchars($s['name']); ?></td><td class="text-center"><?php echo number_format($s['cases']); ?></td><td class="text-center"><?php echo number_format($s['approved']); ?></td><td class="text-right"><?php echo number_format($s['value']); ?> บาท</td></tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>รายงานจากระบบ CRM Condo v1.0 | © <?php echo $year_th; ?> | พิมพ์เมื่อ: <?php echo date('d/m/Y H:i:s'); ?></p>
    </div>
</body>
</html>
<?php
echo ob_get_clean();
?>