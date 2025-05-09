<?php
session_start();
include "../users/checklogin.php";

// Database connection
include "../config.php"; // Adjust this to your actual database connection file

// Fetch scholarship data
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$stmt = $conn->prepare("SELECT title, description, image_path FROM scholarships WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($title, $description, $image_path);
$stmt->fetch();
$stmt->close();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    // Handle file upload
    $target_dir = "../dist/img/";
    if (isset($_FILES["scholarship_image"]) && $_FILES["scholarship_image"]["error"] == 0) {
        $filename = basename($_FILES["scholarship_image"]["name"]);
        $target_file = $target_dir . time() . "_" . $filename; // Add timestamp to make filename unique
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is an actual image or fake image
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
    
    // Update database
    if (empty($_SESSION['error'])) {
        $stmt = $conn->prepare("UPDATE scholarships SET title=?, description=?, image_path=? WHERE id=?");
        $stmt->bind_param("sssi", $title, $description, $image_path, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Scholarship updated successfully!";
            header("Location:index.php");
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
  <title>Edit Scholarship</title>

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
            <h1>Edit Scholarship</h1>
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
                
                <form action="scholarship_edit.php?id=<?php echo htmlspecialchars($id); ?>" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="title">Scholarship Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control summernote" id="description" name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label for="scholarship_image">Upload Image</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="scholarship_image" name="scholarship_image">
                        <label class="custom-file-label" for="scholarship_image">Choose file</label>
                      </div>
                    </div>
                    <?php if (!empty($image_path)) { ?>
                      <img src="../dist/img/<?php echo htmlspecialchars($image_path); ?>" alt="Current Image" class="img-fluid mt-2" style="max-height: 200px;">
                    <?php } ?>
                    <small class="text-muted">Recommended size: 1200×800 pixels. Max file size: 5MB.</small>
                  </div>
                  
                  <div class="form-group">
                  <button type="button" class="btn btn-primary" onclick="confirmSubmission()">บันทึก</button>
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
                timer: 1000
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
