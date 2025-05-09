<?php
require_once __DIR__ . '/../vendor/autoload.php'; // โหลด mPDF
include "../config.php";    
include "../users/checklogin.php"; 

session_start();

$user_no = isset($_SESSION["user_no"]) ? $_SESSION["user_no"] : null;
$user_role = isset($_SESSION["role"]) ? $_SESSION["role"] : "guest";

if (!$user_no) {
    die('ไม่พบข้อมูลผู้ใช้ กรุณาล็อกอินใหม่');
}

// ค้นหาข้อมูลจากฐานข้อมูล
$search = isset($_GET["search"]) ? $_GET["search"] : "";

try {
    $db = new PDO("mysql:host=localhost;dbname=newcompany;charset=utf8", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ตรวจสอบ role ของ user
    $stmt = $db->prepare("SELECT module_no, branch_no FROM authorize WHERE user_no = :user_no");
    $stmt->execute([':user_no' => $user_no]);
    $authData = $stmt->fetch(PDO::FETCH_ASSOC);

    $module_no = $authData['module_no'] ?? null;
    $branch_no = $authData['branch_no'] ?? null;

    $sql = "SELECT 
                sa.id, sa.scholarship_id, sa.student_id, sa.prefix_th,
                COALESCE(sa.first_name_th, 'ไม่มีชื่อ') AS first_name_th,
                COALESCE(sa.last_name_th, 'ไม่มีนามสกุล') AS last_name_th,
                sa.faculty, COALESCE(auth.branch_no, 'ไม่มีสาขา') AS branch_no,
                sa.year_level, sa.gpa, sa.status AS application_status,
                s.title AS scholarship_title, u.user_login,
                (SELECT COUNT(*) FROM assessments WHERE assessments.applications_id = sa.id) AS assessment_status,
                COALESCE(SUM(a.total_score), 0) AS total_score
            FROM scholarship_applications sa
            LEFT JOIN scholarships s ON sa.scholarship_id = s.id
            LEFT JOIN authorize auth ON sa.user_no = auth.user_no
            LEFT JOIN users u ON sa.user_no = u.user_no
            LEFT JOIN assessments a ON sa.id = a.applications_id
            WHERE (sa.first_name_th LIKE :search OR sa.last_name_th LIKE :search OR s.title LIKE :search)";

    if ($module_no == "teacher") {
        $sql .= " AND (auth.branch_no = :branch_no OR auth.branch_no IS NULL)";
    } elseif ($module_no !== "admin") {
        $sql .= " AND sa.user_login = :user_login";
    }

    $sql .= " GROUP BY sa.id ORDER BY sa.id ASC";

    $stmt = $db->prepare($sql);
    
    if ($module_no == "teacher") {
        $stmt->execute([
            ':branch_no' => $branch_no,
            ':search' => "%$search%"
        ]);
    } else {
        $stmt->execute([
            ':search' => "%$search%"
        ]);
    }

    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // นับจำนวนผู้สมัครในแต่ละสาขา
    $branchCounts = [];
    foreach ($applications as $app) {
        $branch = $app['branch_no'] ?? "ไม่มีสาขา";
        if (!isset($branchCounts[$branch])) {
            $branchCounts[$branch] = 0;
        }
        $branchCounts[$branch]++;
    }

    } catch (PDOException $e) {
    die("เกิดข้อผิดพลาด: " . $e->getMessage());
    }

//สร้าง HTML สำหรับ PDF
$html = '<style>
    body { font-family: "sarabun"; }
    h2 { text-align: center; font-size: 22px; margin-bottom: 20px; }
    table { border-collapse: collapse; width: 100%; }
    th { background-color:rgb(51, 53, 54); color: white; padding: 8px; text-align: start; }
    td { padding: 8px; text-align: start; border: 1px solid #ddd; }
    tr:nth-child(even) { background-color: #f2f2f2; }
</style>';

$html .= '<h2>รายงานผู้สมัครทุนการศึกษา</h2>';
$html .= '<table border="1">
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ทุนการศึกษา</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>คณะ</th>
                    <th>สาขา</th>
                    <th>ชั้นปี</th>
                    <th>GPA</th>
                    <th>สถานะ</th>
                    <th>คะแนนประเมิน</th>
                    
                </tr>
            </thead>
            <tbody>';

foreach ($applications as $index => $app) {
    $status_text = ($app['application_status'] == 'approved') ? ' อนุมัติแล้ว' :
                   (($app['application_status'] == 'pending') ? ' รออนุมัติ' : ' ไม่อนุมัติ');

    $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . htmlspecialchars($app['scholarship_title']) . '</td>
                <td>' . htmlspecialchars($app['prefix_th'] . ' ' . $app['first_name_th'] . ' ' . $app['last_name_th']) . '</td>
                <td>' . htmlspecialchars($app['faculty']) . '</td>
                <td>' . htmlspecialchars($app['branch_no']) . '</td>
                <td>' . htmlspecialchars($app['year_level']) . '</td>
                <td>' . htmlspecialchars($app['gpa']) . '</td>
                <td>' . $status_text . '</td>
                <td>' . htmlspecialchars($app['total_score']) . '</td>
                
              </tr>';
}

$totalApplicants = count($applications);
$approvedCount = count(array_filter($applications, fn($app) => $app['application_status'] == 'approved'));
$pendingCount = count(array_filter($applications, fn($app) => $app['application_status'] == 'pending'));
$rejectedCount = $totalApplicants - ($approvedCount + $pendingCount);

$html .= '</tbody></table>';

// สรุปรายงา น
// $html .= '<h3 style="text-align:start; margin-top: 20px;"> สรุปรายงาน</h3>';
$html .= "<p style='text-align:start; margin-top: 20px;'>ผู้สมัครทั้งหมด: <b>$totalApplicants คน</b> | อนุมัติ: <b>$approvedCount คน</b> | รออนุมัติ: <b>$pendingCount คน</b> | ไม่อนุมัติ: <b>$rejectedCount คน</b></p>";
// สรุปจำนวนผู้สมัครในแต่ละสาขา
$html .= '<div>';
foreach ($branchCounts as $branch => $count) {
    $html .= "<p style='margin: 5px 0; text-align:left;'>สาขา <b>$branch</b>: <b>$count คน</b></p>";
}
$html .= '</div>';


$mpdf = new \Mpdf\Mpdf([
    'default_font_size' => 14, 
    'default_font' => 'dejavusans', // รองรับ Unicode และ Emoji ดีขึ้น
    'orientation' => 'L' // แนวนอน
]);

$mpdf->WriteHTML($html);
$mpdf->Output('Scholarship_Report.pdf', 'I');
?>