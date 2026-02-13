<!DOCTYPE html>
<?php
session_start();
include "../users/checklogin.php";
include config_loader.php"; // Include database connection

// Get scholarship ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch scholarship details from database
$sql = "SELECT id, title, description, image_path, date_added FROM scholarships WHERE id = ?"; // Include 'id' here
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$scholarship = $result->fetch_assoc();
$stmt->close();
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($scholarship['title']) ? htmlspecialchars($scholarship['title']) : 'Scholarship Details'; ?></title>

    <!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome -->
<link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- AdminLTE Theme Style (Should be loaded before plugins to avoid conflicts) -->
<link rel="stylesheet" href="../dist/css/adminlte.min.css">
<!-- Tempusdominus Bootstrap 4 (Date Picker) -->
<link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<!-- iCheck (Custom Checkboxes and Radio Buttons) -->
<link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<!-- JQVMap (Maps Plugin) -->
<link rel="stylesheet" href="../plugins/jqvmap/jqvmap.min.css">
<!-- overlayScrollbars (Custom Scrollbars) -->
<link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<!-- Daterange Picker -->
<link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
<!-- Summernote (Rich Text Editor) -->
<link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <?php include "../comp/preloader.php"; ?>

    <?php include "../comp/navbar.php"; ?>
    <?php include "../comp/aside.php"; ?>

    <div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>รายละเอียดทุนการศึกษา</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card text-center">
                        <div class="card-body">
                            <?php if ($scholarship): ?>
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <img src="../dist/img/<?php echo $scholarship['image_path']; ?>" class="img-fluid rounded mb-3" style="max-width: 100%; height: auto;" alt="Scholarship Image">
                                        <h3><?php echo htmlspecialchars($scholarship['title']); ?></h3>
                                        <p><?php echo nl2br(strip_tags($scholarship['description'], '<br>')); ?></p>
                                        <p><small class="text-muted">Added on <?php echo date("F j, Y", strtotime($scholarship['date_added'])); ?></small></p>
                                        <?php if(isset($_SESSION['user_login']) && checkuser($_SESSION['user_login'], 'user') == "yes"): ?>
    <a href="../main/scholarship_form.php?id=<?php echo $scholarship['id']; ?>" class="btn btn-primary">ส่งใบสมัครทุนการศึกษา</a>
<?php endif; ?>
                          
                                    </div>
                            <?php else: ?>
                                <p class="text-center">No scholarship details found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="col-sm-6">
                        <h4>ทุนการศึกษาอื่น ๆ</h4>
                    </div>
                    <div class="card">
                        <div class="card-body">
                        <div class="row row-cols-md-4 g-3" id="scholarships-list">
                          <?php
                          // Fetch 4 random scholarships for "Other scholarships" section
                          $sql = "SELECT * FROM scholarships ORDER BY RAND() LIMIT 4";
                          $result = $conn->query($sql);
                          if ($result->num_rows > 0) {
                              while ($row = $result->fetch_assoc()) {
                                  // ดึง description ออกมา และจัดการข้อความ
                                  $description = mb_convert_encoding($row["description"], 'UTF-8', 'auto');
                                  $description = htmlspecialchars(strip_tags($description));

                                  // กำหนดความยาวสูงสุดของ description
                                  $maxLength = 150; // กำหนดความยาวที่ต้องการ (150 ตัวอักษร)

                                  // ตัดข้อความที่ยาวเกิน
                                  if (strlen($description) > $maxLength) {
                                      $description = substr($description, 0, $maxLength) . '...'; // ตัดและเพิ่ม '...'
                                  }
                                  ?>
                                  <div class="col p-2">
                                      <div class="card h-100 shadow-sm">
                                          <a href="viewinfo.php?id=<?php echo $row['id']; ?>">
                                              <div style="height: 160px; overflow: hidden;">
                                                  <img src="../dist/img/<?php echo $row['image_path']; ?>" class="card-img-top w-100" alt="<?php echo htmlspecialchars($row['title']); ?>" style="object-fit: cover; height: 100%;">
                                              </div>
                                          </a>
                                          <div class="card-body py-2 px-3">
                                              <h6 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h6>
                                              <p class="card-text small"><?php echo $description; ?></p>
                                          </div>
                                          <div class="card-footer">
                                              <a href="viewinfo.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">ดูข้อมูลเพิ่มเติม</a>
                                          </div>
                                      </div>
                                  </div>
                                  <?php
                              }
                          }
                          ?>
                      </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!--  -->

    
    

    <?php include "../comp/footer.php"; ?>

</div>
<!-- Scripts -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="../dist/js/adminlte.js"></script>
<!--  Custom Search Script -->
<script>
$(document).ready(function () {

  function filterCards() {
    var value = $("#searchInput").val().toLowerCase();
    
    $(".card").each(function () {
      var title = $(this).find(".card-title").text().toLowerCase();
      
      if (title.includes(value)) {
        $(this).parent().show();
      } else {
        $(this).parent().hide();
      }
    });
  }

  // 🔍 Search Button Click
  $("#searchButton").on("click", function () {
    filterCards();
  });

  // 🔍 Pressing Enter Triggers Search
  $("#searchInput").on("keypress", function (event) {
    if (event.which === 13) { 
      event.preventDefault();
      filterCards();
    }
  });

});
</script>
</body>
</html>
<?php $conn->close(); ?>
