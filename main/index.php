<?php
session_start();
include "../config_loader.php";
include "../users/checklogin.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Scholarship Management</title>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <?php
  // กำหนดค่าเริ่มต้นให้ $userRole เพื่อป้องกัน Warning
  $userRole = isset($_SESSION["role"]) ? $_SESSION["role"] : "admin"; 
  include "../comp/preloader.php";
  include "../comp/navbar.php";
  include "../comp/aside.php";

  // Secure SQL Query (ใช้ Prepared Statement)
  $search = isset($_GET["search"]) ? $_GET["search"] : "";
  $sql = "SELECT id, title, description, image_path, date_added FROM scholarships";
  if (!empty($search)) {
      $sql .= " WHERE title LIKE ?";
  }
  
  $stmt = $conn->prepare($sql);
  if (!empty($search)) {
      $searchParam = "%$search%";
      $stmt->bind_param("s", $searchParam);
  }
  $stmt->execute();
  $result = $stmt->get_result();

  ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
  <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Home</h1>
          </div>
          <div class="col-sm-6">
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Scholarships Management</h3>
            <form action="index.php" method="get" id="searchForm" class="float-right">
              <input type="text" id="searchInput" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by title">
              <button type="submit" id="searchButton" class="btn btn-sidebar">
                <i class="fas fa-search fa-fw"></i>
              </button>
            </form>
          </div>
          <div class="card-body">
            <?php
            // ตรวจสอบสิทธิ์ admin ก่อนแสดงปุ่ม Add Scholarship
            if(isset($_SESSION['user_login'])) {
              $checkuser = checkuser($_SESSION['user_login'], 'admin');
              if($checkuser == "yes") {
                echo '<a href="scholarship_add.php" class="btn btn-primary mb-3">
                        <i class="fas fa-plus"></i> เพิ่มทุนการศึกษา
                      </a>';
              }
            }
            ?>
          </div>
            <div class="row">
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $description = strip_tags($row["description"]);
        if (strlen($description) > 150) {
            $description = substr($description, 0, 150) . '...';
        }
        echo '
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <a href="viewinfo.php?id=' . $row["id"] . '">
                    <img src="../dist/img/' . htmlspecialchars($row["image_path"]) . '" class="card-img-top" alt="Scholarship Image" style="height: 160px; object-fit: cover;">
                </a>
                <div class="card-body">
                    <h6 class="card-title">' . htmlspecialchars($row["title"]) . '</h6><br>
                    <p class="small">' . htmlspecialchars($description) . '</p>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Added on ' . date("F j, Y", strtotime($row["date_added"])) . '</small>
                </div>';
        
        // ตรวจสอบว่าผู้ใช้เป็น admin หรือไม่โดยใช้ฟังก์ชัน checkuser()
        if(isset($_SESSION['user_login'])) {
            $checkuser = checkuser($_SESSION['user_login'], 'admin');
            if($checkuser == "yes") {
                echo '
                <div class="btn-group" role="group" aria-label="Action buttons">
                    <a href="scholarship_edit.php?id=' . $row["id"] . '" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> แก้ไข
                    </a>
                    <button class="btn btn-danger btn-sm delete-scholarship" data-id="' . $row["id"] . '">
                        <i class="fas fa-trash-alt"></i> ลบ
                    </button>
                </div>';
            }
        }

        echo '</div></div>';
    }
} else {
    echo '<p class="col-12 text-center">No scholarships found.</p>';
}
?>
</div>


          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include "../comp/footer.php"; ?>


<!-- Scripts -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="../dist/js/adminlte.js"></script>

<script>
$(document).ready(function () {
    $('#searchButton').on('click', function () {
        $('#searchForm').submit();
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('.delete-scholarship').on('click', function () {
        var scholarshipId = $(this).data('id'); // ดึงค่า ID
        
        // แสดง popup SweetAlert2
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่',
            text: "คุณจะไม่สามารถกู้คืนข้อมูลนี้ได้",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ถ้ากดยืนยัน → ส่ง AJAX ไปลบ
                $.ajax({
                    url: 'scholarship_delete.php',
                    type: 'POST',
                    data: { id: scholarshipId },
                    success: function (response) {
                        if (response === 'success') {
                            Swal.fire(
                                'ลบข้อมูลสำเร็จ!',
                                'ข้อมูลถูกลบเรียบร้อย',
                                'success'
                            ).then(() => {
                                location.reload(); // รีเฟรชหน้า
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'There was an issue deleting the scholarship.',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            'Error!',
                            'An error occurred while processing your request.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>

</body>
</html>
