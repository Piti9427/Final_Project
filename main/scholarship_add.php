<?php
session_start();
include "../users/checklogin.php";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    include config_loader.php"; // Adjust this to your actual database connection file
    
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date_added = date("Y-m-d H:i:s"); // Current date and time
    
    // Handle file upload
    $target_dir = "../dist/img/";
    $image_path = "";
    
    if (isset($_FILES["scholarship_image"]) && $_FILES["scholarship_image"]["error"] == 0) {
        $filename = basename($_FILES["scholarship_image"]["name"]);
        $target_file = $target_dir . time() . "_" . $filename; // Add timestamp to make filename unique
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["scholarship_image"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $_SESSION['error'] = "File is not an image.";
            $uploadOk = 0;
        }
        
        // Check file size (5MB max)
        if ($_FILES["scholarship_image"]["size"] > 5000000) {
            $_SESSION['error'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        // If everything is ok, try to upload file
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["scholarship_image"]["tmp_name"], $target_file)) {
                $image_path = pathinfo($target_file, PATHINFO_BASENAME);
            } else {
                $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            }
        }
    }
    
    // Insert into database
    if (empty($_SESSION['error'])) {
      $user_no = $_SESSION['user_no']; // ดึง user_no จาก session

      $stmt = $conn->prepare("INSERT INTO scholarships (title, description, image_path, date_added, user_no) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssi", $title, $description, $image_path, $date_added, $user_no);

      
      if ($stmt->execute()) {
          // ดึงค่า scholarship_id ที่เพิ่งสร้างขึ้น
          $scholarship_id = $conn->insert_id;

          // Update row with scholarship_id (ไม่จำเป็น)
          $update_stmt = $conn->prepare("UPDATE scholarships SET scholarship_id = ? WHERE id = ?");
          $update_stmt->bind_param("ii", $scholarship_id, $scholarship_id);
          $update_stmt->execute();
          $update_stmt->close();

          $_SESSION['success'] = "Scholarship added successfully! ID: " . $scholarship_id;
          header("Location: index.php");
          exit();
      } else {
          $_SESSION['error'] = "Error: " . $stmt->error;
      }
      
      $stmt->close();
  }
  
  $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add New Scholarship</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <?php include "../comp/preloader.php"; ?>

  <!-- Navbar -->
  <?php include "../comp/navbar.php"; ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php include "../comp/aside.php"; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add New Scholarship</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <!-- /.card-header -->
              <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                  <div class="alert alert-danger">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                  </div>
                <?php endif; ?>
                
                <form action="scholarship_add.php" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="title">Scholarship Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control summernote" id="description" name="description" rows="4"></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label for="scholarship_image">Upload Image</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="scholarship_image" name="scholarship_image" required>
                        <label class="custom-file-label" for="scholarship_image">เลือกไฟล์</label>
                      </div>
                    </div>
                    <small class="text-muted">Recommended size: 1200×800 pixels. Max file size: 5MB.</small>
                  </div>
                  
                  <div class="form-group">
                  <button type="button" class="btn btn-primary" onclick="confirmSubmission()">บันทึกทุนการศึกษา</button>
                    <a href="index.php" class="btn btn-default">ยกเลิก</a>
                  </div>
                </form>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <?php include "../comp/footer.php"; ?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- Summernote -->
<script src="../plugins/summernote/summernote-bs4.min.js"></script>
<!-- bs-custom-file-input -->
<script src="../plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmSubmission() {
    Swal.fire({
        title: 'ยืนยันการบันทึก?',
        text: "คุณต้องการบันทึกข้อมูลทุนการศึกษานี้หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, บันทึกเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'กำลังบันทึก...',
                text: 'ระบบกำลังบันทึกข้อมูลของคุณ',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 2000
            });

            setTimeout(() => {
                Swal.fire({
                    title: 'บันทึกสำเร็จ!',
                    text: 'ระบบได้บันทึกข้อมูลทุนการศึกษาแล้ว',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    document.querySelector('form').submit(); // ✅ ส่งฟอร์มจริง
                });
            }, 1000);
        }
    });
}
</script>

<script>
$(function () {
  // Summernote
  $('.summernote').summernote({
    height: 300,
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'underline', 'clear']],
      ['color', ['color']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['table', ['table']],
      ['insert', ['link']],
      ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });
  
  // File input
  bsCustomFileInput.init();
});
</script>
</body>
</html>