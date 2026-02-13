<!DOCTYPE html>
<?php
session_start();
include config_loader.php";
include "../users/checklogin.php";

$user_no = $_SESSION["user_no"] ?? null;
$user_login = $_SESSION["user_login"] ?? null;
$user_role = $_SESSION["role"] ?? "guest";

if (empty($user_no)) {
    die('<div class="alert alert-danger">ไม่พบข้อมูลผู้ใช้ กรุณาล็อกอินใหม่</div>');
}

$search = $_GET["search"] ?? "";

try {
    $db = new PDO("mysql:host=localhost;dbname=newcompany;charset=utf8", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
    } elseif ($module_no == "admin") {
        $stmt->execute([
            ':search' => "%$search%"
        ]);
    } else {
        $stmt->execute([
            ':user_login' => $user_login,
            ':search' => "%$search%"
        ]);
    }

    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">เกิดข้อผิดพลาด: ' . $e->getMessage() . '</div>';
    $applications = [];
}
?>




<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Dashboard</title>

  <!--  Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!--  Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <!--  DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  
  <!--  Theme & Plugins -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="../plugins/jqvmap/jqvmap.min.css">
  <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <?php
  include "../comp/preloader.php";
  ?>

  <!-- Navbar -->
  <?php
  include "../comp/navbar.php";
  ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php
   include "../comp/aside.php";
  ?>
  <div class="content-wrapper">
<section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>List of scholarship applicants</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
<div class="container-fluid">
    <!-- Status update alert -->
    <div id="statusAlert" class="alert alert-dismissible fade" role="alert">
        <span id="alertMessage"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
    <div class="card shadow-sm">
    <div class="card-body">
    <div class="table-responsive">
    <table id="scholarshipTable" class="table table-striped table-hover">
        <thead>
        <tr>
            <th>ลำดับ</th>
            <th>ทุนการศึกษา</th>
            <th>ชื่อ-นามสกุล</th>
            <th>คณะ</th>
            <th>สาขา</th>
            <th>ชั้นปี</th>
            <th>เกรดเฉลี่ย</th>
            <th>สถานะ</th>
            <th>คะแนนประเมิน</th> <!-- ✅ เพิ่มคอลัมน์ใหม่ -->
            <th>ดูข้อมูล</th>
            <th>ประเมิน</th> <!-- ✅ ให้ทุกคนเห็นเมนูนี้ -->
            
            <?php if(isset($_SESSION['user_login']) && (checkuser($_SESSION['user_login'], 'admin') == "yes" || checkuser($_SESSION['user_login'], 'teacher') == "yes")): ?>
                <th>จัดการสถานะ</th>
            <?php endif; ?>

            <?php if(isset($_SESSION['user_login']) && checkuser($_SESSION['user_login'], 'admin') == "yes"): ?>
                <th>ลบ</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
            <?php foreach($applications as $index => $app): ?>
                <tr id="app-row-<?= $app['id'] ?>">
                    <td><?= $index + 1 ?></td>
                    <td><?php echo htmlspecialchars($app['scholarship_title']); ?></td>
                    <td><?= htmlspecialchars($app['prefix_th'] . ' ' . $app['first_name_th'] . ' ' . $app['last_name_th']) ?></td>
                    <td><?= htmlspecialchars($app['faculty']) ?></td>
                    <td><?= htmlspecialchars($app['branch_no']) ?></td>
                    <td><?= htmlspecialchars($app['year_level']) ?></td>
                    <td><?= htmlspecialchars($app['gpa']) ?></td>
                    <td>
                        <span id="status-badge-<?= $app['id'] ?>" class="badge bg-<?php 
                            echo ($app['application_status'] == 'approved') ? 'success' : 
                                (($app['application_status'] == 'pending') ? 'warning' : 'danger'); ?>">
                            <?= ($app['application_status'] == 'approved') ? 'อนุมัติแล้ว' : 
                                (($app['application_status'] == 'pending') ? 'รออนุมัติ' : 'ไม่อนุมัติ') ?>
                        </span>
                    </td>
                    <td><span class="badge bg-primary" ><?= $app['total_score'] ?></span></td>
                    <td>
                        <a href="scholarship_form_details.php?scholarship_id=<?= $app['scholarship_id']; ?>&applicant_id=<?= $app['id']; ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> ดูรายละเอียด
                        </a>
                    </td>
                    <?php if(isset($_SESSION['user_login']) && (checkuser($_SESSION['user_login'], 'admin') == "yes" || checkuser($_SESSION['user_login'], 'teacher') == "yes")): ?>
                    <td>
                        <div class="btn-group">
                            <?php if($app['application_status'] == 'pending'): ?>
                                <button class="btn btn-success btn-sm approve-btn" onclick="updateStatus(<?= $app['id']; ?>, 'approved')">
                                    <i class="fas fa-check-circle"></i> อนุมัติ
                                </button>
                                <button class="btn btn-danger btn-sm reject-btn" onclick="updateStatus(<?= $app['id']; ?>, 'rejected')">
                                    <i class="fas fa-times-circle"></i> ไม่อนุมัติ
                                </button>
                            <?php elseif($app['application_status'] == 'approved'): ?>
                                <button class="btn btn-outline-success btn-sm" disabled>
                                    <i class="fas fa-check-circle"></i> อนุมัติแล้ว
                                </button>
                                <button class="btn btn-warning btn-sm reset-btn" onclick="updateStatus(<?= $app['id']; ?>, 'pending')">
                                    <i class="fas fa-undo"></i> รีเซ็ต
                                </button>
                            <?php elseif($app['application_status'] == 'rejected'): ?>
                                <button class="btn btn-outline-danger btn-sm" disabled>
                                    <i class="fas fa-times-circle"></i> ไม่อนุมัติแล้ว
                                </button>
                                <button class="btn btn-warning btn-sm reset-btn" onclick="updateStatus(<?= $app['id']; ?>, 'pending')">
                                    <i class="fas fa-undo"></i> รีเซ็ต
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                    <?php endif; ?>
                    <!-- ส่วนที่แสดงปุ่มจัดการสถานะการประเมิน -->
                    <td>
    <?php if ((isset($app['assessment_status']) && $app['assessment_status'] > 0) || (isset($app['total_score']) && $app['total_score'] > 0)): ?>
        <span class="badge bg-success"><i class="fas fa-check-circle"></i> ประเมินแล้ว</span>
    <?php elseif(isset($_SESSION['user_login']) && (checkuser($_SESSION['user_login'], 'admin') == "yes" || checkuser($_SESSION['user_login'], 'teacher') == "yes")): ?>
        <a href="assessment_form.php?scholarship_id=<?= urlencode($app['scholarship_id']); ?>&id=<?= urlencode($app['id']); ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-clipboard-list"></i> ทำแบบประเมิน
        </a>
    <?php else: ?> 
        <span class="badge bg-warning"><i class="fas fa-clock"></i> รอประเมิน</span>
    <?php endif; ?>
</td>



                    <?php if(isset($_SESSION['user_login']) && checkuser($_SESSION['user_login'], 'admin') == "yes"): ?>
                        <td>
                            <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete(<?= $app['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>

    </div>
</div>
</div>
  </div>
  </section>
  <!-- /.content -->

  <!-- Footer -->
  <?php include "../comp/footer.php"; ?>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="../dist/js/adminlte.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- ✅ เพิ่ม SweetAlert2 -->
<script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script>
    $(document).ready(function () {
        $('#scholarshipTable').DataTable();
        // Hide alert initially
        $('#statusAlert').hide();
    });
    
    function confirmDelete(id) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณจะไม่สามารถกู้คืนข้อมูลนี้ได้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'ลบข้อมูลสำเร็จ!',
                text: 'ข้อมูลถูกลบเรียบร้อย',
                icon: 'success',
                timer: 1000, // ✅ ค้างไว้ 2 วินาที
                showConfirmButton: false
            });

            setTimeout(() => {
                window.location.href = 'scholarship_list_delete.php?id=' + id;
            }, 1000); // ✅ รอ 2 วินาทีก่อนเปลี่ยนหน้า
        }
    });
}
    
    function updateStatus(appId, newStatus) {
        // Customize confirmation message based on status
        let confirmMessage = '';
        if (newStatus === 'approved') {
            confirmMessage = 'คุณต้องการอนุมัติใช่หรือไม่?';
        } else if (newStatus === 'rejected') {
            confirmMessage = 'คุณต้องการไม่อนุมัติใช่หรือไม่?';
        } else {
            confirmMessage = 'คุณต้องการรีเซ็ตสถานะใช่หรือไม่?';
        }
        
        if (confirm(confirmMessage)) {
            console.log(`Updating application ${appId} to status: ${newStatus}`);
            
            fetch('update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: appId, status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Response from server:", data);
                if (data.success) {
                    // Update the status badge
                    let badge = document.getElementById("status-badge-" + appId);
                    if (badge) {
                        // Remove all background classes
                        badge.classList.remove("bg-warning", "bg-danger", "bg-success");
                        
                        // Add appropriate background class and text
                        if (newStatus === 'approved') {
                            badge.classList.add("bg-success");
                            badge.innerText = "อนุมัติแล้ว";
                            // Show success alert
                            showAlert('success', 'อัปเดตสถานะเป็นอนุมัติแล้วเรียบร้อย');
                        } else if (newStatus === 'rejected') {
                            badge.classList.add("bg-danger");
                            badge.innerText = "ไม่อนุมัติ";
                            // Show danger alert
                            showAlert('danger', 'อัปเดตสถานะเป็นไม่อนุมัติแล้วเรียบร้อย');
                        } else {
                            badge.classList.add("bg-warning");
                            badge.innerText = "รออนุมัติ";
                            // Show warning alert
                            showAlert('warning', 'รีเซ็ตสถานะเป็นรออนุมัติแล้วเรียบร้อย');
                        }
                    }
                    
                    // Refresh the buttons in this row
                    refreshButtons(appId, newStatus);
                    
                } else {
                    alert(data.message || "เกิดข้อผิดพลาด");
                }
            })
            .catch(error => {
                console.error("Fetch Error:", error);
                alert("ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้");
            });
        }
    }
    
    function showAlert(type, message) {
        const alertElement = $('#statusAlert');
        
        // Remove all alert types
        alertElement.removeClass('alert-success alert-danger alert-warning');
        
        // Add the appropriate alert type
        alertElement.addClass(`alert-${type}`);
        
        // Set message
        $('#alertMessage').text(message);
        
        // Show the alert
        alertElement.addClass('show').show();
        
        // Auto hide alert after 3 seconds
        setTimeout(() => {
            alertElement.removeClass('show').hide();
        }, 3000);
    }
    
    function refreshButtons(appId, currentStatus) {
        const row = document.querySelector(`#app-row-${appId} td:nth-child(10) .btn-group`);
        
        if (row) {
            // Create new buttons HTML based on current status
            let buttonsHtml = '';
            
            if (currentStatus === 'approved') {
                buttonsHtml += `
                    <button class="btn btn-outline-success btn-sm" disabled>
                        <i class="fas fa-check-circle"></i> อนุมัติแล้ว
                    </button>
                  
                    <button class="btn btn-warning btn-sm reset-btn" onclick="updateStatus(${appId}, 'pending')">
                        <i class="fas fa-undo"></i> รีเซ็ต
                    </button>
                `;
            } else if (currentStatus === 'rejected') {
                buttonsHtml += `
                    
                    <button class="btn btn-outline-danger btn-sm" disabled>
                        <i class="fas fa-times-circle"></i> ไม่อนุมัติแล้ว
                    </button>
                    <button class="btn btn-warning btn-sm reset-btn" onclick="updateStatus(${appId}, 'pending')">
                        <i class="fas fa-undo"></i> รีเซ็ต
                    </button>
                `;
            } else { // pending
                buttonsHtml += `
                    <button class="btn btn-success btn-sm approve-btn" onclick="updateStatus(${appId}, 'approved')">
                        <i class="fas fa-check-circle"></i> อนุมัติ
                    </button>
                    <button class="btn btn-danger btn-sm reject-btn" onclick="updateStatus(${appId}, 'rejected')">
                        <i class="fas fa-times-circle"></i> ไม่อนุมัติ
                    </button>
                `;
            }
            
            // Update the buttons
            row.innerHTML = buttonsHtml;
        }
    }
</script>

<script>
    $(document).ready(function () {
    // ตรวจสอบว่ามี parameter assessment_done หรือไม่
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('assessment_done')) {
        Swal.fire({
            icon: 'success',
            title: 'บันทึกแบบประเมินเรียบร้อย!',
            text: 'สถานะการประเมินถูกอัปเดตเป็น "ประเมินไปแล้ว"',
            timer: 1000,
            showConfirmButton: false
        });

        // แก้ไข selector ให้ตรงกับโครงสร้าง HTML ที่มีอยู่
        const applicantId = urlParams.get('applicant_id');
        if (applicantId) {
            // หาแถวที่ตรงกับ applicant_id และอัปเดตปุ่มการประเมิน
            const assessmentButton = $(`#app-row-${applicantId} td:nth-child(11) a.btn-secondary`);
            if (assessmentButton.length) {
                // แทนที่ปุ่มด้วย badge "ประเมินไปแล้ว"
                assessmentButton.replaceWith('<span class="badge bg-success"><i class="fas fa-check-circle"></i> ประเมินไปแล้ว</span>');
            }
        }
    }
});
</script>
</body>
</html>