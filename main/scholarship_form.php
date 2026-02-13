<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['user_login'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อนสมัครทุนการศึกษา'); window.location.href = '../users/login.php';</script>";
    exit();
}
// ส่วนที่เหลือของโค้ด scholarshipform.php
$userRole = isset($_SESSION["role"]) ? $_SESSION["role"] : "admin"; 
// เชื่อมต่อฐานข้อมูล
include "../config_loader.php";
// ตรวจสอบว่ามีพารามิเตอร์ id ถูกส่งมาหรือไม่
if (isset($_GET['id'])) {
    $scholarship_id = $_GET['id'];

    // ดึงข้อมูลทุนการศึกษาจากฐานข้อมูล
    $sql = "SELECT * FROM scholarships WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $scholarship_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $app = $result->fetch_assoc(); // กำหนดค่าให้ $app
    } else {
        echo "ไม่พบทุนการศึกษา";
        exit(); // หยุดการทำงานของสคริปต์
    }
} else {
    echo "ไม่พบทุนการศึกษา";
    exit();
}
?>
    <?php include "../users/checklogin.php"; ?>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <!-- Google Font -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sarabun:300,400,400i,700&display=swap">

<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">

<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/css/tempusdominus-bootstrap-4.min.css" integrity="sha512-PMjWzHVtwxdq7m7GIxBot5vdxUY+5aKP9wpKtvnNBZrVv1srI8tU6xvFMzG8crLNcMj/8Xl/WWmo/oAP/40p1g==" crossorigin="anonymous" />
<link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

<!-- Bootstrap Datepicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<!-- AdminLTE -->
<link rel="stylesheet" href="../dist/css/adminlte.min.css">

<!-- Plugins -->
<link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<link rel="stylesheet" href="../plugins/jqvmap/jqvmap.min.css">
<link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
<link rel="stylesheet" href="../plugins/iCheck/flat/blue.css">



  <style>
     .form-control[readonly] {
            background-color: #fff;
            cursor: default;
        }
        #calendar-icon {
            cursor: pointer;
        }
    .required-field::after {
      content: " *";
      color: red;
    }
    .input-group-text {
      cursor: pointer;
    }
    body {
      font-family: 'Sarabun', sans-serif;
    }
    .required-field::after {
      content: " *";
      color: red;
    }
    .photo-placeholder {
      width: 100px;
      height: 120px;
      border: 1px dashed #ccc;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 10px;
    }
    .section-header {
      background-color: #f8f9fa;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 4px;
    }
    .table-border {
      border: 1px solid #dee2e6;
    }
    .scholarship-history td, .scholarship-history th {
      border: 1px solid #dee2e6;
      padding: 8px;
    }
  </style>

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  

  <!-- Preloader -->
  <?php
  include "../comp/preloader.php";
  ?>

  <?php
  include "../comp/navbar.php";
  ?>

  <?php
   include "../comp/aside.php";
  ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>ใบสมัครทุนการศึกษา</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
  <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
  <div class="card">
  <div class="card-header bg-white bg-opacity-0 text-center p-4 flex flex-col items-center justify-center max-w-2xl mx-auto">
  <h3 class="card-title flex items-center justify-center">
    <i class="fas fa-graduation-cap mr-2"></i>
    ใบสมัครขอรับทุนการศึกษา คณะบริหารธุรกิจ มหาวิทยาลัยเทคโนโลยีราชมงคลธัญบุรี
  </h3>
</div>
    <div class="card-body">
    <form id="scholarshipForm" action="scholarship_submit.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmSubmission()">
    <input type="hidden" name="scholarship_id" value="<?php echo htmlspecialchars($scholarship_id, ENT_QUOTES, 'UTF-8'); ?>">
        <!-- Academic Year Section -->
        <div class="row mb-4">
  <!-- Logo Column -->
  <div class="col-md-3 text-center">
    <img src="../dist/img/image/logormutt.png" alt="RMUTT Logo" name="logo_photo" class="img-fluid" style="max-height: 150px;">
  </div>
  <!-- Academic Year Column -->
  <div class="col-md-6">
    <div class="form-group text-center">
      <label for="academic_year">ประจำปีการศึกษา</label>
        <?php
            // ดึงเดือนและปีปัจจุบัน
            $current_month = date('n'); // 1 = มกราคม, 12 = ธันวาคม
            $current_year = date('Y') + 543; // ปี พ.ศ.

            // กำหนดให้ปีการศึกษาเปลี่ยนในเดือน **กรกฎาคม (7)**
            if ($current_month >= 7) {
                $academic_year = $current_year;      // ตั้งแต่ ก.ค. เป็นต้นไป ใช้ปีปัจจุบัน
            } else {
                $academic_year = $current_year - 1;  // ก่อนเดือน ก.ค. ใช้ปีที่แล้ว
            }
        ?>
        <input type="text" class="form-control text-center" name="academic_year" value="<?php echo $academic_year; ?>" readonly>
        <p class="text-danger mt-2" style="font-size: 14px; font-weight: bold;">( กรุณากรอกข้อมูลให้ครบถ้วน หากข้อมูลบางส่วนไม่มีหรือไม่ตรงกับตัวท่าน สามารถเว้นว่างไว้ได้ )</p>

    </div>
  </div>
  <!-- Student Photo Column -->
  <div class="col-md-3 d-flex flex-column justify-content-center align-items-center">
    <div class="photo-placeholder text-center">
        <div class="form-group">
            <img class="product-image img-fluid" id="uploadPreview">
        </div>
    </div>
    <div class="form-group">
        <label for="exampleInputFile">File input</label>
        <div class="input-group">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="exampleInputFile" name="student_photo" onchange="PreviewImage();">
                <label class="custom-file-label" for="exampleInputFile">Choose file</label>                          
            </div>
        </div>                         
    </div>
</div>
</div>

        <!--Basic Information -->
        <div class="row">
          <div class="col-md-12">
            <h4 class="section-header">ข้อมูลผู้สมัครขอทุน</h4>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="required-field">คำนำหน้า (ภาษาไทย)</label>
                  <select class="form-control" name="prefix_th" >
                    <option value="">-- เลือก --</option>
                    <option value="นาย">นาย</option>
                    <option value="นาย">นางสาว</option>
                    <option value="นางสาว">นาง</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="required-field">ชื่อ (ภาษาไทย)</label>
                  <input type="text" class="form-control" name="first_name_th" required title="กรุณากรอกชื่อ">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="required-field">นามสกุล (ภาษาไทย)</label>
                  <input type="text" class="form-control" name="last_name_th" require>
                </div>
              </div>
            </div>
            <!-- English Name -->
            <div class="row">
            <div class="col-md-4">
  <div class="form-group">
    <label class="required-field">คำนำหน้า (ภาษาอังกฤษ)</label>
    <select class="form-control" name="prefix_en">
      <option value="">-- เลือก --</option>
      <option value="Mr.">Mr.</option>
      <option value="Ms.">Ms.</option>
      <option value="Mrs.">Mrs.</option>
    </select>
  </div>
</div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="required-field">ชื่อ (ภาษาอังกฤษตัวพิมพ์ใหญ่)</label>
                  <input type="text" class="form-control" name="first_name_en" >
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="required-field">นามสกุล (ภาษาอังกฤษตัวพิมพ์ใหญ่)</label>
                  <input type="text" class="form-control" name="last_name_en" >
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Educational Information -->
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="required-field">คณะ</label>
              <input type="text" class="form-control" name="faculty" value="บริหารธุรกิจ" readonly>
            </div>
          </div>
          <div class="col-md-4">
          <div class="form-group">
    <label for="branch" class="required-field">สาขาวิชา</label>
        <select onclick="showSuppliers(this.value)" class="form-select" name="user_no" id="user_no">
            <?php
              include "../config_loader.php";
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            if (!$conn) { die("Error ".mysqli_connect_error()); }
            
            // Modified query to exclude empty branch_no values
            $sql = "SELECT * FROM `authorize` WHERE branch_no IS NOT NULL AND branch_no != ''";
            $result = mysqli_query($conn, $sql);
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='".$row["user_no"]."'>".$row["branch_no"]."</option>";
            }
            ?>
        </select>
</div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="required-field">ชั้นปีที่</label>
              <select class="form-control" name="year_level" >
                <option value="">-- เลือก --</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
              </select>
            </div>
          </div>
        </div>
        <!--  -->
        <div class="row">
        <div class="col-md-6">
            <div class="form-group">
              <label class="required-field">รหัสประจำตัวนึกศึกษา</label>
              <input type="text" class="form-control" name="student_id" >
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="required-field">เกรดเฉลี่ย</label>
              <input type="number" step="0.01" class="form-control" name="gpa" min="0" max="4" >
            </div>
          </div>  
        </div>        
        <!--  -->
        <div class="row">
        <div class="col-md-4">
            <div class="form-group">
              <label class="required-field">สถานที่เกิด</label>
              <input type="text" class="form-control" name="birth_place" >
            </div>
          </div>
          <!-- ฟอร์มเลือกวันที่ -->
<div class="col-sm-4">
    <div class="form-group">
    <label for="birthdate" class="form-label">วันเดือนปีเกิด <span class="text-danger">*</span></label>
    <div class="input-group">
    <input class="form-control" type="date" name="birth_date" value="<?php echo $currentDate;?>">
                </div>
    </div>
</div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="required-field">อายุ</label>
              <input type="number" class="form-control" name="age" >
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="required-field">ศาสนา</label>
              <select class="form-control" name="religion" >
                <option value="">-- เลือก --</option>
                <option value="พุทธ">พุทธ</option>
                <option value="คริสต์">คริสต์</option>
                <option value="อิสลาม">อิสลาม</option>
              </select>
            </div>
          </div>
        </div>
        <!-- Previous Education History -->
        <h4 class="section-header mt-4">ที่อยู่ตามทะเบียนบ้าน</h4>
        <div class="row">
  <div class="col-md-4">
    <div class="form-group">
      <label>บ้านเลขที่</label>
      <input type="text" class="form-control" name="permanent_house_no" required>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>หมู่ที่</label>
      <input type="text" class="form-control" name="permanent_moo">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>ถนน</label>
      <input type="text" class="form-control" name="permanent_road">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>ตำบล / แขวง</label>
      <input type="text" class="form-control" name="permanent_subdistrict">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>อำเภอ / เขต</label>
      <input type="text" class="form-control" name="permanent_district">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>จังหวัด</label>
      <input type="text" class="form-control" name="permanent_province">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>รหัสไปรษณีย์</label>
      <input type="text" class="form-control" name="permanent_postal_code">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>โทรศัพท์ (บ้าน)</label>
      <input type="text" class="form-control" name="permanent_phone">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>โทรศัพท์ (มือถือ)</label>
      <input type="text" class="form-control" name="permanent_mobile">
    </div>
  </div>
</div>
<!--  -->
<h4 class="section-header mt-4">ที่อยู่ปัจจุบัน (สามารถติดต่อได้ขณะกำลังศึกษา)</h4>
<div class="row">
  <div class="col-md-4">
    <div class="form-group">
      <label>ประเภทที่พัก</label>
      <select class="form-control" name="current_residence_type">
        <option value="">-- เลือก --</option>
        <option>หอพักนักศึกษา</option>
        <option>บ้าน</option>
        <option>อพาร์ตเมนต์</option>
        <option>บ้านเช่า</option>
        <option>หอพักเอกชน</option>
        <option>วัด</option>
      </select>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>อาคาร</label>
      <input type="text" class="form-control" name="current_building">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>หมายเลขห้องพัก</label>
      <input type="text" class="form-control" name="current_room_no">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>ที่อยู่เลขที่</label>
      <input type="text" class="form-control" name="current_house_no">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>หมู่ที่</label>
      <input type="text" class="form-control" name="current_moo">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>ถนน</label>
      <input type="text" class="form-control" name="current_road">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>ตำบล / แขวง</label>
      <input type="text" class="form-control" name="current_subdistrict">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>อำเภอ / เขต</label>
      <input type="text" class="form-control" name="current_district">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>จังหวัด</label>
      <input type="text" class="form-control" name="current_province">
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>รหัสไปรษณีย์</label>
      <input type="text" class="form-control" name="current_postal_code">
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>โทรศัพท์</label>
      <input type="text" class="form-control" name="current_phone">
    </div>
  </div>
</div>
        <!-- รายได้ -->
        <h4 class="section-header mt-4">รายได้</h4>
<div class="mt-8">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Regular Income -->
    <div class="space-y-6">
      <!-- Income from Parents -->
      <div class="row">
        <div class="col-md-6">
        <div class="form-group">
        <div class="flex items-center">
    <label class="font-medium">ได้รับเงินจากบิดา/มารดา :</label>
    <div class="flex gap-6 pt-2 pb-2 ">
    <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="parent_allowance_type" id="daily-parent" value="รายวัน">
    <label class="form-check-label" for="daily-parent">รายวัน</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="parent_allowance_type" id="weekly-parent" value="รายสัปดาห์">
    <label class="form-check-label" for="weekly-parent">รายสัปดาห์</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="parent_allowance_type" id="monthly-parent" value="รายเดือน">
    <label class="form-check-label" for="monthly-parent">รายเดือน</label>
  </div>
</div>
<div class="col-span-12 md:col-span-6">
        <input type="text" class="form-control" name="parent_allowance_amount" placeholder="จำนวนเงิน">
        </div>
</div>
      </div>
      </div>
      <!--  -->
      <div class="col-md-6">
          <div class="form-group">
        <div class="flex items-center">
    <label class="font-medium">ได้รับเงินจากผู้อุปการะนอกเหนือจากบิดามารดา :</label>
    <div class="flex gap-6 pt-2 pb-2">
    <div class="form-check form-check-inline">
    <input class="form-check-input" name="other_allowance_type" type="radio" id="daily-supporter" value="รายวัน">
    <label class="form-check-label" for="daily-supporter">รายวัน</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" name="other_allowance_type" type="radio" id="weekly-supporter" value="รายสัปดาห์">
    <label class="form-check-label" for="weekly-supporter">รายสัปดาห์</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" name="other_allowance_type" type="radio" id="monthly-supporter" value="รายเดือน">
    <label class="form-check-label" for="monthly-supporter">รายเดือน</label>
  </div>
</div>
<div class="col-span-12 md:col-span-6">
        <input type="text" class="form-control" name="other_allowance_amount" placeholder="จำนวนเงิน">
        </div>
</div>    
      </div>
      </div>
    <!-- รายได้จากทางอื่น -->
      <!-- การกู้ยืม -->
      <div class="col-md-6">
          <div class="form-group">
        <div class="flex items-center">
    <label class="font-medium">ได้รับเงินจากกองทุนเงินให้กู้ยืมเพื่อการศึกษา</label>
      <div class="col-span-12 md:col-span-6">
        <input type="text" class="form-control" name="loan_amount" placeholder="จำนวนเงินต่อเดือน">
      </div>
      </div>
      </div>
  </div>
      <!-- รายได้พิเศษ -->
      <div class="col-md-3">
    <div class="form-group">
      <label>มีรายได้พิเศษวันละ</label>
      <input type="text" class="form-control" name="extra_income_daily" placeholder="จำนวนเงิน">
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label y>โดยได้รับจาก</label>
      <input type="text" class="form-control" name="extra_income_source" placeholder=" (ระบุงาน / แหล่งที่ได้รับเงิน)">
    </div>
  </div>
  
  </div>
</div>
<!-- รายจ่าย -->
<h4 class="section-header mt-4">รายจ่าย</h4>
<div class="mt-8">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="space-y-6">
      <div class="row">
      <div class="col-md-6">
          <div class="form-group">
        <div class="flex items-center">
    <label class="font-medium">ค่าอาหาร</label>
      <div class="col-span-12 md:col-span-6">
        <input type="text" class="form-control" name="food_expense_daily" placeholder="บาทต่อวัน">
      </div>
      </div>
      </div>
  </div>
      <!--  -->
      <div class="col-md-6">
          <div class="form-group">
        <div class="flex items-center">
    <label class="font-medium">ค่าที่พัก</label>
      <div class="col-span-12 md:col-span-6">
        <input type="text" class="form-control" name="accommodation_expense" placeholder="บาทต่อเดือน">
      </div>
      </div>
      </div>
  </div>
      <!-- -->
      <div class="col-md-6">
          <div class="form-group">
        <div class="flex items-center">
    <label class="font-medium"> การเดินทางจากที่พักถึงมหาวิทยาลัยฯ โดย</label>
      <div class="col-span-12 md:col-span-6">
        <input type="text" class="form-control" name="transportation_method" placeholder="เดินทางยังไง">
      </div>
      </div>
      </div>
  </div>
  <!--  -->
  <div class="col-md-6">
          <div class="form-group">
        <div class="flex items-center">
    <label class="font-medium"> ค่าใช้จ่ายในการเดินทางระหว่างที่พักถึงสถานที่เรียน (ถ้ามี)</label>
      <div class="col-span-12 md:col-span-6">
        <input type="text" class="form-control" name="transportation_expense_daily" placeholder="บาทต่อวัน">
      </div>
      </div>
      </div>
  </div>
      <!--  -->
      <div class="col-md-6">
    <div class="form-group">
      <label> ค่าอุปกรณ์การเรียน / ตำราเรียน</label>
      <input type="text" class="form-control" name="education_supplies_expense" placeholder="บาทต่อเดือน">
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label y>ค่าใช้จ่ายอื่น ๆ</label>
      <input type="text" class="form-control" name="other_expense_detail" placeholder="ได้แก่" style="height: 40px;">    
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
    <label class="text-white text-opacity-10">---</label>
      <input type="text" class="form-control" name="other_expense_amount" placeholder="บาทต่อเดือน" style="height: 40px;">
    </div>
  </div>
  <div class="col-md-12">
          <div class="form-group">
        <div class="flex items-center">
    <label class="font-medium">ประมาณการค้าใช้จ่ายที่นักศึกษาคาดว่าจะเพียงพอสำหรับตนเอง</label>
      <div class="col-span-12 md:col-span-6">
        <input type="text" class="form-control" name="estimated_monthly_expense" placeholder="บาทต่อเดือน">
      </div>
      </div>
      </div>
  </div>
  </div>
</div>
</div>
</div>

<!--  -->
<h4 class="section-header mt-4">สภาพความเป็นอยู่ของผู้ขอทุน</h4>
<div class="mt-8">
  <div class="row g-3">
    <!-- Regular Income -->
    <!-- Income from Parents -->
    <div class="col-md-12">
      <div class="form-group">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="living_conditions_grantees" id="live-with-parents" value="อยู่กับบิดามารดา">
          <label class="form-check-label" for="live-with-parents">อยู่กับบิดามารดา</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="living_conditions_grantees" id="live-with-father" value="อยู่กับบิดา">
          <label class="form-check-label" for="live-with-father">อยู่กับบิดา</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="living_conditions_grantees" id="live-with-mother" value="อยู่กับมารดา">
          <label class="form-check-label" for="live-with-mother">อยู่กับมารดา</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="living_conditions_grantees" id="live-with-supporter" value="อยู่กับผู้อุปการะ">
          <label class="form-check-label" for="live-with-supporter">อยู่กับผู้อุปการะ</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="living_conditions_grantees" id="dorm-temple" value="อยู่หอพัก / วัด">
          <label class="form-check-label" for="dorm-temple">อยู่หอพัก / วัด</label>
        </div>
        <input type="text" class="form-control form-control-sm d-inline-block ms-2" name="relationship_benefactors" style="width: 200px;" placeholder="ความเกี่ยวข้องกับผู้อุปการะ [ถ้าเลือก]">
      </div>
    </div>

    <!-- ข้อมูลเพิ่มเติม -->
    <label class="">ถ้าเลือกตัวเลือก หอพัก / วัด กรุณากรอกข้อมูลด้านล่างนี้</label>
    <div class="col-12 col-md-6">
      <div class="form-group">
        <label>ชื่อ</label>
        <input type="text" class="form-control" name="dormitorytemple" placeholder="">
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="form-group">
        <label>ห้อง</label>
        <input type="text" class="form-control" name="dormitorytemple_room" placeholder="">
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="form-group">
        <label>สถานที่ติดต่อได้</label>
        <input type="text" class="form-control" name="dormitorytemple_contact" placeholder="">
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="form-group">
        <label>เบอร์โทรศัพท์</label>
        <input type="text" class="form-control" name="dormitorytemple_phone" placeholder="">
      </div>
    </div>
  </div>
</div>

<!--เริ่ม ค่าใช้จ่ายด้านที่พัก -->
<h4 class="section-header mt-4">ค่าใช้จ่ายด้านที่พัก</h4>
<div class="col-md-12">
    <div class="mt-8">
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <div class="d-flex flex-column gap-3">
                <!-- ตัวเลือกค่าที่พัก -->
                <div class="d-flex align-items-center gap-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="expense_select" value="ไม่เสียค่าที่พัก">
                        <label class="form-check-label">ไม่เสียค่าที่พัก</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="expense_select" value="ค่าหอพัก / ค่าเช่าบ้าน">
                        <label class="form-check-label">ค่าหอพัก / ค่าเช่าบ้าน</label>
                    </div>
                    <input type="text" class="form-control text-center" name="dormitoryhouse_fee" value="" style="max-width: 80px;" >
                    <span>บาท/เดือน</span>
                    <span>ประเภทการจ่าย:</span>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_type" value="จ่ายคนเดียว">
                        <label class="form-check-label">จ่ายคนเดียว</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_type" value="ร่วมกับผู้อื่น">
                        <label class="form-check-label">ร่วมกับผู้อื่น</label>
                    </div>
                </div>

                <!-- ตัวเลือกประเภทการจ่าย -->
                    

                <!-- ตัวเลือกทุนกู้ยืม -->
                <div class="d-flex align-items-center gap-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="scholarship_status" value="ได้รับทุนกู้ยืมรัฐบาล (กยศ.) (ปีล่าสุด) ปีการศึกษา">
                        <label class="form-check-label">ได้รับทุนกู้ยืมรัฐบาล (กยศ.) (ปีล่าสุด) ปีการศึกษา</label>
                    </div>
                    <input type="text" class="form-control text-center" name="scholarship_amount" value="" style="max-width: 100px;" >
                    <span>บาท / ปี</span>
                    <span>ค่าเทอม</span>
                    <input type="text" class="form-control text-center" name="scholarship_term_amount" value="" style="max-width: 100px;" >
                    <span>ค่าครองชีพรายเดือน</span>
                    <input type="text" class="form-control text-center" name="scholarship_cost_living" value="" style="max-width: 100px;" >
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="scholarship_status" value="ไม่ได้กู้ยืม">
                        <label class="form-check-label">ไม่ได้กู้ยืม</label>
                    </div>                 
                </div>
                
            </div>
        </div>
    </div>
</div>
<!--  -->            

<!--ประวัติการรับทุนการศึกษา  -->
            <h4 class="section-header mt-4">ประวัติการรับทุนการศึกษา</h4>
            <div class="container-fluid bg-white rounded">                
        <div class="mb-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="historycholarship_status" id="receivedScholarship" value="เคยได้รับทุนการศึกษา">
                <label class="form-check-label" for="receivedScholarship">เคยได้รับทุนการศึกษา</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="historycholarship_status" id="notReceivedScholarship" value="ไม่เคยได้รับทุนการศึกษา">
                <label class="form-check-label" for="notReceivedScholarship">ไม่เคยได้รับทุนการศึกษา</label>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered w-100">
                <thead class="table-light">
                    <tr>
                        <th>ระดับการศึกษา</th>
                        <th>ชื่อทุนการศึกษา/หน่วยงาน/ผู้ให้ทุน</th>
                        <th>จำนวนเงิน (บาท)</th>
                        <th>ประเภททุนการศึกษา</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>มัธยมปลาย</td>
                        <td><input type="text" class="form-control" name="senior_high_school"></td>
                        <td><input type="number" class="form-control" name="senior_high_school_amount"></td>
                        <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="landstatus" id="continous1" value="ต่อเนื่อง">
                                <label class="form-check-label" for="continous1">ต่อเนื่อง</label>
                            </div>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus" id="annual1" value="เฉพาะปี">
                                <label class="form-check-label" for="annual1">เฉพาะปี</label>
                            </div>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus" id="nonBinding1" value="ไม่ผูกพัน">
                                <label class="form-check-label" for="nonBinding1">ไม่ผูกพัน</label>
                            </div>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus" id="binding1" value="ผูกพัน">
                                <label class="form-check-label" for="binding1">ผูกพัน</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>อุดมศึกษาปีที่ 1</td>
                        <td><input type="text" class="form-control" name="one_years"></td>
                        <td><input type="number" class="form-control" name="one_years_amount"></td>
                        <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus1" id="continous2" value="ต่อเนื่อง">
                            <label class="form-check-label" for="continous2">ต่อเนื่อง</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus1" id="annual2" value="เฉพาะปี">
                            <label class="form-check-label" for="annual2">เฉพาะปี</label>
                          </div>
                          <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="landstatus1" id="nonBinding2" value="ไม่ผูกพัน">
                            <label class="form-check-label" for="nonBinding2">ไม่ผูกพัน</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus1" id="binding2" value="ผูกพัน">
                            <label class="form-check-label" for="binding2">ผูกพัน</label>
                          </div>
                        </td>
                    </tr>
                    <tr>
                        <td>อุดมศึกษาปีที่ 2</td>
                        <td><input type="text" class="form-control" name="two_years"></td>
                        <td><input type="number" class="form-control" name="two_years_amount"></td>
                        <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus2" id="continous3" value="ต่อเนื่อง">
                            <label class="form-check-label" for="continous3">ต่อเนื่อง</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus2" id="annual3" value="เฉพาะปี">
                            <label class="form-check-label" for="annual3">เฉพาะปี</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus2" id="nonBinding3" value="ไม่ผูกพัน">
                            <label class="form-check-label" for="nonBinding3">ไม่ผูกพัน</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus2" id="binding3" value="ผูกพัน">
                            <label class="form-check-label" for="binding3">ผูกพัน</label>
                          </div>
                        </td>
                    </tr>
                    <tr>
                        <td>อุดมศึกษาปีที่ 3</td>
                        <td><input type="text" class="form-control" name="three_years"></td>
                        <td><input type="number" class="form-control" name="three_years_amount"></td>
                        <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus3" id="continous4" value="ต่อเนื่อง">
                            <label class="form-check-label" for="continous4">ต่อเนื่อง</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus3" id="annual4" value="เฉพาะปี">
                            <label class="form-check-label" for="annual4">เฉพาะปี</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus3" id="nonBinding4" value="ไม่ผูกพัน">
                            <label class="form-check-label" for="nonBinding4">ไม่ผูกพัน</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus3" id="binding4" value="ผูกพัน">
                            <label class="form-check-label" for="binding4">ผูกพัน</label>
                          </div>
                        </td>
                    </tr>
                    <tr>
                        <td>อุดมศึกษาปีที่ 4</td>
                        <td><input type="text" class="form-control" name="four_years"></td>
                        <td><input type="number" class="form-control" name="four_years_amount"></td>
                        <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus4" id="continous5" value="ต่อเนื่อง">
                            <label class="form-check-label" for="continous5">ต่อเนื่อง</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="landstatus4" id="annual5" value="เฉพาะปี">
                            <label class="form-check-label" for="annual5">เฉพาะปี</label>
                          </div>
                          <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="landstatus4" id="nonBinding5" value="ไม่ผูกพัน">
                            <label class="form-check-label" for="nonBinding5">ไม่ผูกพัน</label>
                          </div>
                          <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="landstatus4" id="binding5" value="ผูกพัน">
                            <label class="form-check-label" for="binding5">ผูกพัน</label>
                          </div>
                        </td>
                    </tr>
                    <tr>
    <td>อุดมศึกษาปีที่ 5</td>
    <td><input type="text" class="form-control" name="five_years"></td>
    <td><input type="number" class="form-control" name="five_years_amount"></td>
    <td>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="landstatus5" id="continous6" value="ต่อเนื่อง">
            <label class="form-check-label" for="continous6">ต่อเนื่อง</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="landstatus5" id="annual6" value="เฉพาะปี">
            <label class="form-check-label" for="annual6">เฉพาะปี</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="landstatus5" id="nonBinding6" value="ไม่ผูกพัน">
            <label class="form-check-label" for="nonBinding6">ไม่ผูกพัน</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="landstatus5" id="binding6" value="ผูกพัน">
            <label class="form-check-label" for="binding6">ผูกพัน</label>
        </div>
    </td>
</tr>
<tr>
    <td>อุดมศึกษาปีที่ 6</td>
    <td><input type="text" class="form-control" name="six_years"></td>
    <td><input type="number" class="form-control" name="six_years_amount"></td>
    <td>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="landstatus6" id="continous7" value="ต่อเนื่อง">
            <label class="form-check-label" for="continous7">ต่อเนื่อง</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="landstatus6" id="annual7" value="เฉพาะปี">
            <label class="form-check-label" for="annual7">เฉพาะปี</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="landstatus6" id="nonBinding7" value="ไม่ผูกพัน">
            <label class="form-check-label" for="nonBinding7">ไม่ผูกพัน</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="landstatus6" id="binding7" value="ผูกพัน">
            <label class="form-check-label" for="binding7">ผูกพัน</label>
        </div>
    </td>
</tr>

                </tbody>
            </table>
        </div>
    </div>  
            <!-- ประวัติการศึกษาโดยย่อ -->
            <h4 class="section-header mt-4">ประวัติการศึกษาโดยย่อ</h4>            
  <div class="card-body" style="border: none;">
    <!-- ประถมศึกษา -->
    <div class="row mb-3">
      <div class="col-md-8">
        <div class="form-group">
          <label for="primary-school" class="form-label">ประถมศึกษา จากโรงเรียน</label>
          <input type="text" class="form-control" id="primary-school" name="primary_school">
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="primary-province" class="form-label">จังหวัด</label>
          <input type="text" class="form-control" id="primary-province" name="primary_province">
        </div>
      </div>
    </div>
    <!-- มัธยมศึกษาตอนต้น -->
    <div class="row mb-3">
      <div class="col-md-8">
        <div class="form-group">
          <label for="middle-school" class="form-label">มัธยมศึกษาตอนต้น จากโรงเรียน</label>
          <input type="text" class="form-control" id="middle-school" name="middle_school">
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="middle-province" class="form-label">จังหวัด</label>
          <input type="text" class="form-control" id="middle-province" name="middle_province">
        </div>
      </div>
    </div>
    <!-- มัธยมศึกษาตอนปลาย -->
    <div class="row">
      <div class="col-md-8">
        <div class="form-group">
          <label for="high-school" class="form-label">มัธยมศึกษาตอนปลาย จากโรงเรียน</label>
          <input type="text" class="form-control" id="high-school" name="high_school">
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="high-province" class="form-label">จังหวัด</label>
          <input type="text" class="form-control" id="high-province" name="high_province">
        </div>
      </div>
    </div>
  </div>
  <!-- จบ ประวัติการศึกษาโดยย่อ -->
  <!-- เรื้ม ข้อมูลของครอบครัวและผู้อุปการะ -->
  <h4 class="section-header mt-4">ข้อมูลของครอบครัวและผู้อุปการะ</h4>            
    <form>
    <div class="card-body" style="border: none;">
    <div class="card-header ">ข้อมูลบิดา</div>
    <div class="mt-4 mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">ชื่อ-นามสกุล บิดา</label>
                    <input type="text" class="form-control" name="father_fullname">
                </div>
                <div class="col-md-3">
                    <label class="form-label">อายุ</label>
                    <input type="number" class="form-control" name="father_age">
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">สถานะ</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="father_status" id="fatherAlive" value="มีชีวิต">
                        <label class="form-check-label" for="fatherAlive">มีชีวิต</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="father_status" id="fatherDeceased" value="ถึงแก่กรรม">
                        <label class="form-check-label" for="fatherDeceased">ถึงแก่กรรม</label>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">ที่อยู่ บ้านเลขที่</label>
                    <input type="text" class="form-control" name="father_house">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ตรอก / ซอย</label>
                    <input type="text" class="form-control" name="father_alley">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">หมู่ที่</label>
                    <input type="text" class="form-control" name="father_moo">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ถนน</label>
                    <input type="text" class="form-control" name="father_road">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ตำบล / แขวง</label>
                    <input type="text" class="form-control" name="father_subdistrict">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">อำเภอ / เขต</label>
                    <input type="text" class="form-control" name="father_district">
                </div>
                <div class="col-md-4">
                    <label class="form-label">จังหวัด</label>
                    <input type="text" class="form-control" name="father_province">
                </div>
                <div class="col-md-4">
                    <label class="form-label">รหัสไปรษณีย์</label>
                    <input type="text" class="form-control" name="father_post_code">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์บ้าน</label>
                    <input type="text" class="form-control" name="father_house_no">
                </div>
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์มือถือ</label>
                    <input type="text" class="form-control" name="father_phone">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">อาชีพบิดา</label>
                    <input type="text" class="form-control" name="father_occupation">
                </div>
                <div class="col-md-6">
                    <label class="form-label">รายได้ต่อเดือน (บาท)</label>
                    <input type="number" class="form-control" name="father_income">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">ตำแหน่ง / ยศ</label>
                    <input type="text" class="form-control" name="father_rank">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ลักษณะงาน</label>
                    <input type="text" class="form-control" name="father_job_description">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">สถานที่ทำงานของบิดา</label>
                    <input type="text" class="form-control" name="father_workplace">
                </div>
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์</label>
                    <input type="text" class="form-control" name="father_telephone">
                </div>
            </div>
    </div>
        </form>
    <!-- มารดา -->
    <div class="card-header">ข้อมูลมารดา</div>
    <div class="mt-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">ชื่อ-นามสกุล มารดาดา</label>
                    <input type="text" class="form-control" name="mother_fullname">
                </div>
                <div class="col-md-3">
                    <label class="form-label">อายุ</label>
                    <input type="number" class="form-control" name="mother_age">
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">สถานะ</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mother_status" id="motherAlive" value="มีชีวิต">
                        <label class="form-check-label" for="motherAlive">มีชีวิต</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mother_status" id="motherDeceased" value="ถึงแก่กรรม">
                        <label class="form-check-label" for="motherDeceased">ถึงแก่กรรม</label>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">ที่อยู่ บ้านเลขที่</label>
                    <input type="text" class="form-control" name="mother_house">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ตรอก / ซอย</label>
                    <input type="text" class="form-control" name="mother_ally">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">หมู่ที่</label>
                    <input type="text" class="form-control" name="mother_moo">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ถนน</label>
                    <input type="text" class="form-control" name="mother_road">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ตำบล / แขวง</label>
                    <input type="text" class="form-control" name="mother_subdistrict">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">อำเภอ / เขต</label>
                    <input type="text" class="form-control" name="mother_district">
                </div>
                <div class="col-md-4">
                    <label class="form-label">จังหวัด</label>
                    <input type="text" class="form-control" name="mother_province">
                </div>
                <div class="col-md-4">
                    <label class="form-label">รหัสไปรษณีย์</label>
                    <input type="text" class="form-control" name="mother_postcode">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์บ้าน</label>
                    <input type="text" class="form-control" name="mother_house_no">
                </div>
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์มือถือ</label>
                    <input type="text" class="form-control" name="mother_phone">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">อาชีพมารดา</label>
                    <input type="text" class="form-control" name="mother_occupation">
                </div>
                <div class="col-md-6">
                    <label class="form-label">รายได้ต่อเดือน (บาท)</label>
                    <input type="number" class="form-control" name="mother_income">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">ตำแหน่ง / ยศ</label>
                    <input type="text" class="form-control" name="mother_rank">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ลักษณะงาน</label>
                    <input type="text" class="form-control" name="mother_job_description">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">สถานที่ทำงานของมารดา</label>
                    <input type="text" class="form-control" name="mother_workplace">
                </div>
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์</label>
                    <input type="text" class="form-control" name="mother_telephone">
                </div>
            </div>
    </div>
  </div>
    <!-- จบ ข้อมูลครอบครัว -->
    <!--เริ่ม สภาพความเป็นอยู่ผู้ขอทุน -->
        <h4 class="section-header mt-4">สถานภาพครอบครัว</h4>
        <div class="mt-3">
            <div class="px-0">
                <div class="row mb-4">
                    <div class="col-md-12">
                    <div class="d-flex flex-wrap gap-4">
    <div class="form-check">
        <input class="form-check-input" type="radio" name="familystatus" id="livingTogether" value="บิดามารดาอยู่ด้วยกัน"> 
        <label class="form-check-label" for="livingTogether">บิดามารดาอยู่ด้วยกัน</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="familystatus" id="fatherDeceased" value="บิดาถึงแก่กรรม"> 
        <label class="form-check-label" for="fatherDeceased">บิดาถึงแก่กรรม</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="familystatus" id="motherDeceased" value="มารดาถึงแก่กรรม"> 
        <label class="form-check-label" for="motherDeceased">มารดาถึงแก่กรรม</label>
    </div>
</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <div class="form-check">
                            <input class="form-check-input" type="radio" name="familystatus" id="divorced" value="บิดามารดาหย่าร้างกัน"> 
                                <label class="form-check-label" for="divorced">
                                    บิดามารดาหย่าร้างกัน
                                </label>
                            </div>
                            
                        </div>
                        <div class="flex-grow-1">
                                <input type="text" class="form-control" name="benefactor" placeholder="ผู้อุปการะนักศึกษา">
                            </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <div class="form-check">
                            <input class="form-check-input" type="radio" name="familystatus" id="separated" value="บิดามารดาแยกกันอยู่"> 
                                <label class="form-check-label" for="separated">
                                    บิดามารดาแยกกันอยู่
                                </label>
                            </div>
                            
                        </div>
                        <div class="flex-grow-1">
                                <input type="text" class="form-control" name="living_with" placeholder="นักศึกษาอาศัยอยู่กับ">
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">อื่นๆ</label>
                        <input type="text" class="form-control" name="other_familystatus" placeholder="โปรดระบุ">
                    </div>
                </div>
            </div>
        </div>
<!--จบ สภาพความเป็นอยู่ผู้ขอทุน  -->
        <h4 class="section-header mt-4">ที่ดินและที่อยู่อาศัยของบิดามารดา</h4>
        <div class="mt-3">
            <div class="px-0">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="parents_landstatus" id="ownLand" value="มีที่ดินสำหรับประกอบอาชีพเป็นของตนเอง">
                                <label class="form-check-label" for="ownLand">
                                    มีที่ดินสำหรับประกอบอาชีพเป็นของตนเอง
                                </label>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" class="form-control" name="ownfarm" style="width: 120px;">
                                <span>ไร่</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="parents_landstatus" id="rentLand" value="เช่าที่ดินผู้อื่น">
                                <label class="form-check-label" for="rentLand">
                                    เช่าที่ดินผู้อื่น
                                </label>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" class="form-control" name="otherfarm" style="width: 120px;">
                                <span>ไร่</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span>ค่าเช่าเดือนละ</span>
                                <input type="number" class="form-control" name="monthly_rent_land" style="width: 120px;">
                                <span>บาท</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span>หรือปีละ</span>
                                <input type="number" class="form-control" name="peryear_rent_land" style="width: 120px;">
                                <span>บาท</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="parents_landstatus" id="liveWithOthers" value="อาศัยผู้อื่น">
                                <label class="form-check-label" for="liveWithOthers">
                                    อาศัยผู้อื่น
                                </label>
                            </div>
                            <div class="flex-grow-1">
                                <input type="text" class="form-control" name="liveothers_land" placeholder="ระบุ">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="parents_landstatus" id="rentHouse" value="เช่าบ้านอยู่">
                                <label class="form-check-label" for="rentHouse">
                                    เช่าบ้านอยู่
                                </label>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span>ค่าเช่าเดือนละ</span>
                                <input type="number" class="form-control" name="renthouse_monthly_land" style="width: 120px;">
                                <span>บาท</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span>หรือปีละ</span>
                                <input type="number" class="form-control" name="renthouse_peryear_land" style="width: 120px;">
                                <span>บาท</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!--  -->
        <h4 class="section-header mt-4">ผู้อุปการะอื่นนอกจากบิดา/มารดา</h4>
        <div class="mt-3">
            <div class="px-0">
                <!-- Guardian Existence -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="hasGuardian" id="hasGuardianYes" value="มี">
                                <label class="form-check-label" for="hasGuardianYes">มี</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="hasGuardian" id="hasGuardianNo" value="ไม่มี">
                                <label class="form-check-label" for="hasGuardianNo">ไม่มี</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Guardian Personal Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">ชื่อ – นามสกุล ผู้อุปการะ</label>
                        <input type="text" class="form-control" name="guardian_fullname">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">อายุ (ปี)</label>
                        <input type="number" class="form-control" name="guardian_age">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">มีความเกี่ยวข้องเป็น</label>
                        <input type="text" class="form-control" name="guardian_relevant">
                    </div>
                </div>
                <!-- Address -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">บ้านเลขที่</label>
                        <input type="text" class="form-control" name="guardian_house">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ตรอก/ซอย</label>
                        <input type="text" class="form-control" name="guardian_ally">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">หมู่ที่</label>
                        <input type="text" class="form-control" name="guardian_moo">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ถนน</label>
                        <input type="text" class="form-control" name="guardian_road">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">ตำบล/แขวง</label>
                        <input type="text" class="form-control" name="guardian_subdistrict">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">อำเภอ/เขต</label>
                        <input type="text" class="form-control" name="guardian_district">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">จังหวัด</label>
                        <input type="text" class="form-control" name="guardian_province">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">รหัสไปรษณีย์</label>
                        <input type="text" class="form-control" name="guardian_postcode">
                    </div>
                </div>
                <!-- Contact Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">โทรศัพท์บ้าน</label>
                        <input type="tel" class="form-control" name="guardian_house_no">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">โทรศัพท์มือถือ</label>
                        <input type="tel" class="form-control" name="guardian_phone">
                    </div>
                </div>
                <!-- Marital Status and Children -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">สถานภาพ</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="guardian_status" id="single" value="โสด">
                                <label class="form-check-label" for="single">โสด</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="guardian_status" id="married" value="สมรส">
                                <label class="form-check-label" for="married">สมรส</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">มีบุตร (คน)</label>
                        <input type="number" class="form-control" name="guardian_children">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">กำลังศึกษา (คน)</label>
                        <input type="number" class="form-control" name="guardian_children_studying">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ประกอบอาชีพ (คน)</label>
                        <input type="number" class="form-control" name="guardian_children_occupation">
                    </div>
                </div>
                <!-- Occupation Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">อาชีพผู้อุปการะ</label>
                        <input type="text" class="form-control" name="guardian_occupation">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">รายได้เดือนละ (บาท)</label>
                        <input type="number" class="form-control" name="guardian_monthly_income">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">ตำแหน่ง/ยศ</label>
                        <input type="text" class="form-control" name="guardian_rank">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ลักษณะงาน</label>
                        <input type="text" class="form-control" name="guardian_job_description">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">สถานที่ทำงานของผู้อุปการะ</label>
                        <input type="text" class="form-control" name="guardian_workplace">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">โทรศัพท์</label>
                        <input type="tel" class="form-control" name="guardian_telephone">
                    </div>
                </div>
            </div>
        </div>
<!--  -->
        <h4 class="section-header mt-4">ข้อมูลการศึกษาและอาชีพพี่น้องของผู้ขอทุน</h4>
        <div class="mt-3">
            <div class="px-0">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-center">
                            <span>ผู้ขอทุน มีพี่ – น้อง (รวมผู้ขอทุน) จำนวน</span>
                            <input type="number" class="form-control" name="sibling_amount" style="width: 80px">
                            <span>คน</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-center">
                            <span>และผู้ขอทุนเป็นบุตรคนที่</span>
                            <input type="number" class="form-control" name="sibling_child_amount" style="width: 80px">
                            <span>ของครอบครัว</span>
                        </div>
                    </div>
                </div>
                <div class="small mb-2">กรอกรายละเอียดพี่น้อง (เรียงตามลำดับมากไปน้อย) รวมทั้งผู้ขอทุนด้วย</div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 60px">คนที่</th>
                                <th>ชื่อ - สกุล</th>
                                <th style="width: 80px">อายุ</th>
                                <th colspan="2" class="text-center">การศึกษา</th>
                                <th>อาชีพ</th>
                                <th>รายได้  ต่อเดือน</th>
                                <th>สถานภาพ  สมรส</th>
                                <th>จำนวน  บุตร</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>สถานศึกษา</th>
                                <th>ระดับชั้น</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_fullname_one"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_age_one"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_education_one"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_grade_level_one"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_occupation_one"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_monthly_income_one"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_status_one"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_children_amount_one"></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_fullname_two"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_age_two"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_education_two"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_grade_level_two"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_occupation_two"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_monthly_income_two"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_status_two"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_children_amount_two"></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_fullname_three"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_age_three"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_education_three"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_grade_level_three"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_occupation_three"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_monthly_income_three"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_status_three"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_children_amount_three"></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_fullname_four"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_age_four"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_education_four"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_grade_level_four"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_occupation_four"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_monthly_income_four"></td>
                                <td><input type="text" class="form-control form-control-sm" name="sibling_status_four"></td>
                                <td><input type="number" class="form-control form-control-sm" name="sibling_children_amount_four"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <div class="px-0">
                <!-- Number of Children -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex gap-3 align-items-center">
                            <span>ขณะนี้มีบุตรที่อยู่ในความอุปการะของบิดา และ/หรือ มารดา จำนวน</span>
                            <input type="number" class="form-control" name="sibling_currently_children" style="width: 80px">
                            <span>คน</span>
                        </div>
                    </div>
                </div>
                <!-- Financial Problems -->
                <div class="row mb-3">
                    <div class="col-12">
                            <label class="form-label">ครอบครัวประสบปัญหาขาดแคลนเงินอย่างไร</label>
                            <textarea type="text" class="form-control" rows="3" name="sibling_financial_problems"></textarea>
                    </div>
                </div>
                <!-- Solutions -->
                <div class="row mb-3">
                    <div class="col-12">
                            <label class="form-label">และแก้ไขปัญหาโดยวิธีการใดเมื่อขาดเงิน</label>
                            <textarea type="text" class="form-control" rows="3" name="sibling_solutions"></textarea>
                    </div>
                </div>
                <!-- Scholarship Necessity -->
                <div class="row mb-3">
                    <div class="col-12">
                            <label class="form-label">ความจำเป็นที่ต้องขอรับทุนการศึกษา</label>
                            <textarea type="text" class="form-control" rows="3" name="sibling_scholarship_necessity"></textarea>
                    </div>
                </div>
                <!-- Health Problems -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">ประสบปัญหาอื่นๆ ปัญหาด้านสุขภาพ – โรคประจำตัว</label>
                        <div class="d-flex gap-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="healthIssue" id="noHealth" value="ไม่มี">
                                <label class="form-check-label" for="noHealth">ไม่มี</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="healthIssue" id="hasHealth" value="มี">
                                <label class="form-check-label" for="hasHealth">มี</label>
                            </div>
                        </div>
                        <input type="text" name="healthIssueDescription" class="form-control" placeholder="ระบุ">
                    </div>
                </div>
                <!-- Study Problems -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">ปัญหาด้านอื่นๆ ที่เป็นอุปสรรคต่อการเรียน</label>
                        <textarea class="form-control" name="studyProblems" rows="3"></textarea>
                    </div>
                </div>
                <!-- Family Problems -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">ปัญหาครอบครัว</label>
                        <textarea class="form-control" name="familyProblems" rows="3"></textarea>
                    </div>
                </div>
                <!-- Part-time Job -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">งานพิเศษที่ทำอยู่</label>
                        <input type="text" class="form-control" name="parttime_job">
                    </div>
                    <div class="col-3">
                        <label class="form-label">รายได้</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="parttime_income">
                            <span class="input-group-text">บาท/</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">&nbsp;</label> <select class="form-select" name="parttime_income_period">
                            <option value="">-- เลือก --</option>
                            <option value="วัน">วัน</option>
                            <option value="สัปดาห์">สัปดาห์</option>
                            <option value="เดือน">เดือน</option>
                            <option value="ปี">ปี</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
<!--  -->
<div class="row mt-4">
    <!-- ความสามารถพิเศษ -->
    <div class="col-12 mb-4">
        <div class="form-group">
            <label class="form-label">มีความสามารถพิเศษอะไรบ้าง ระบุ</label>
            <textarea class="form-control" name="special_abilities" rows="3"></textarea>
        </div>
    </div>
    <!-- กิจกรรมที่เคยทำในสถานศึกษา -->
    <div class="col-12">
        <h5 class="mb-3">กิจกรรมที่เคยทำในสถานศึกษา</h5>
        <div class="form-group mb-3">
            <div class="input-group">
                <span class="input-group-text">1.</span>
                <input type="text" class="form-control" name="special_activities">
            </div>
        </div>
        <div class="form-group mb-3">
            <div class="input-group">
                <span class="input-group-text">2.</span>
                <input type="text" class="form-control" name="special_activities1">
            </div>
        </div>
        <div class="form-group mb-3">
            <div class="input-group">
                <span class="input-group-text">3.</span>
                <input type="text" class="form-control" name="special_activities2">
            </div>
        </div>
    </div>
</div>
<!--  -->
<div class="row mt-4">
    <div class="col-12">
        <h5 class="mb-3">รางวัลทางด้านการศึกษาที่เคยได้รับ</h5>
        <div class="form-group mb-3">
            <div class="row">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text">1.</span>
                        <input type="text" class="form-control" name="awards">
                    </div>
                </div>
                <div class="col-md-3">
                <div class="input-group w-100">
                    <span class="input-group-text">ปี พ.ศ.</span>
                    <input type="text" class="form-control" name="awards_year">
                </div>
                </div>
            </div>
        </div>
        <div class="form-group mb-3">
            <div class="row">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text">2.</span>
                        <input type="text" class="form-control" name="awards1">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text">ปี พ.ศ.</span>
                        <input type="text" class="form-control" name="awards_year1">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->
<div class="row mt-4">
    <!-- จุดมุ่งหมายในอนาคต -->
    <div class="col-12 mb-4">
        <div class="form-group">
            <label class="form-label">จุดมุ่งหมายในอนาคตเมื่อจบการศึกษา</label>
            <textarea class="form-control" name="future_goals" rows="3"></textarea>
        </div>
    </div>
    <!-- บุคคลใกล้ชิดที่สามารถติดต่อได้ -->
    <div class="col-12 mb-4">
        <h5 class="mb-3">บุคคลใกล้ชิดที่สามารถติดต่อได้กรณีเร่งด่วน</h5>
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label">ชื่อ - สกุล</label>
                    <input type="text" class="form-control" name="emergency_contact_name">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">มีความเกี่ยวข้องเป็น</label>
                    <input type="text" class="form-control" name="emergency_contact_relevant">
                </div>
            </div>
        </div>
        <!-- ที่อยู่ -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">บ้านเลขที่</label>
                    <input type="text" class="form-control" name="emergency_contact_house">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">ตรอก/ซอย</label>
                    <input type="text" class="form-control" name="emergency_contact_ally">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">หมู่ที่</label>
                    <input type="text" class="form-control" name="emergency_contact_moo">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">ถนน</label>
                    <input type="text" class="form-control" name="emergency_contact_road">
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">ตำบล/แขวง</label>
                    <input type="text" class="form-control" name="emergency_contact_subdistrict">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">อำเภอ/เขต</label>
                    <input type="text" class="form-control" name="emergency_contact_district">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">จังหวัด</label>
                    <input type="text" class="form-control" name="emergency_contact_province">
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">รหัสไปรษณีย์</label>
                    <input type="text" class="form-control" name="emergency_contact_postcode">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">โทรศัพท์บ้าน</label>
                    <input type="tel" class="form-control" name="emergency_contact_house_no">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">โทรศัพท์มือถือ</label>
                    <input type="tel" class="form-control" name="emergency_contact_phone">
                </div>
            </div>
        </div>
    </div>
    <!-- จำนวนเงินทุน -->
    <div class="col-12 mb-4">
        <h5 class="mb-3">จำนวนเงินทุนที่ต้องการ</h5>
        <div class="form-group mb-3">
            <label class="form-label">หากมหาวิทยาลัยพิจารณาให้ทุนการศึกษานักศึกษาเห็นว่าจำนวนเงินที่เหมาะสม คือ</label>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="scholarship_required" id="amount3000" value="3,000 บาท">
                    <label class="form-check-label" for="amount3000">3,000 บาท</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="scholarship_required" id="amount4000" value="4,000 บาท">
                    <label class="form-check-label" for="amount4000">4,000 บาท</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="scholarship_required" id="amount5000" value="5,000 บาท<">
                    <label class="form-check-label" for="amount5000">5,000 บาท</label>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">นักศึกษาจะนำเงินที่ได้รับไปใช้จ่ายเป็นค่าอะไรบ้าง (ระบุรายละเอียด)</label>
            <textarea class="form-control" name="scholarship_amount_description" rows="5"></textarea>
        </div>
    </div>
</div>
<!--  -->
<div class="row mt-4">
    <!-- คำรับรอง -->
    <div class="col-12 mb-4">
        <div class="form-group mb-4">
            <div class="border p-3 mb-3 bg-light">
                <p class="mb-0">ข้าพเจ้าขอรับรองว่า ข้อความที่ข้าพเจ้าให้ไว้ เป็นความจริงทุกประการ หากปรากฏว่าข้อมูลไม่เป็นความจริง ข้าพเจ้ายินยอมให้ มหาวิทยาลัยเทคโนโลยีราชมงคลธัญบุรี ตัดสิทธิ์การรับทุนการศึกษาตลอดสภาพการนักศึกษา และจะพิจารณาโทษทางวินัยนักศึกษา รวมทั้งยินยอมคืนเงินทุนการศึกษาในส่วนที่ข้าพเจ้าได้รับไปแล้วให้แก่มหาวิทยาลัยทันที</p>
                <p class="mt-3 mb-0">ทั้งนี้ หากข้าพเจ้าได้รับการพิจารณาให้ได้รับทุนการศึกษา ข้าพเจ้ายินดีที่จะให้ความร่วมมือในการทำกิจกรรมตามเงื่อนไข ที่มหาวิทยาลัยกำหนด</p>
            </div>
        </div>
        <!-- ลายเซ็น -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">ลงชื่อผู้สมัครขอรับทุน</label>
                    <input type="text" class="form-control" name="signature_scholarship" placeholder="ลายเซ็นอิเล็กทรอนิกส์">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">ชื่อ-นามสกุล (ตัวบรรจง)</label>
                    <input type="text" class="form-control" name="signature_name" placeholder="ชื่อ-นามสกุล">
                </div>
            </div>
        </div>
        <!-- วันที่ -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">วันที่</label>
                    <select class="form-control" name="signature_date">
                        <option value="">-- เลือกวัน --</option>
                        <?php
                        for ($i = 1; $i <= 31; $i++) {
                            echo "<option value=\"$i\">$i</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">เดือน</label>
                    <select class="form-control" name="signature_month">
                        <option value="">-- เลือกเดือน --</option>
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            echo "<option value=\"$i\">$i</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">พ.ศ.</label>
                    <input type="number" class="form-control" name="signature_year" placeholder="พ.ศ." value="<?php echo date('Y') + 543; ?>" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->
<div class="row mt-4">
    <div class="col-12">
        <h5 class="mb-3">บรรยายประวัติ สภาพครอบครัว และเหตุผลความจำเป็นในการรับทุน</h5>
        <div class="form-group">
            <label class="form-label text-danger mb-2">(ระบุให้ละเอียดชัดเจน และพิมพ์ให้ถูกต้อง)</label>
            <textarea 
                class="form-control"
                name="describe_scholarship" 
                rows="25">
            </textarea>
        </div>
    </div>
</div>
<!--  -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">แผนที่แสดงที่อยู่ของผู้ปกครอง และแสดงสถานที่ / จุดที่ตั้งสำคัญๆ เพื่อให้สามารถเดินทางได้โดยสะดวก</h5>
                    </div>
                    <div class="card-body">
            <!-- อัปโหลดแผนที่ -->
            <div class="mb-3">
                <label for="fileUpload1" class="form-label">อัพโหลดแผนที่หรือเอกสาร</label>
                <input type="file" class="form-control" name="fileUpload1[]" id="fileUpload1" accept="image/*,.pdf,.doc,.docx" multiple required>
                <div id="fileList1" class="list-group mt-2">
                    <!-- Preview ไฟล์จะแสดงใน JavaScript -->
                </div>
            </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="landmarks" class="form-label">จุดสังเกตที่สำคัญ</label>
                                    <textarea class="form-control" name="landmarks" id="landmarks" rows="3" placeholder="ระบุจุดสังเกตที่สำคัญ"></textarea>
                                </div>
                             </div>
                             <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="directions" class="form-label">คำอธิบายเส้นทาง</label>
                                    <textarea class="form-control" name="directions" id="directions" rows="3" placeholder="อธิบายเส้นทางการเดินทาง"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!--  -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">สำเนาบัตรประจำตัวนักศึกษา / V-Card 1 ฉบับ</h5>
                    </div>
                    <div class="card-body">
            <!-- อัปโหลดแผนที่ -->
            <div class="mb-3">
                <input type="file" class="form-control" name="fileUpload2[]" id="fileUpload2" accept="image/*,.pdf,.doc,.docx" multiple required>
                <div id="fileList2" class="list-group mt-2">
                    <!-- Preview ไฟล์จะแสดงใน JavaScript -->
                </div>
            </div>
                    </div>
                </div>
            </div>
        </div>
<!--  -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">ใบแสดงผลการศึกษาเฉลี่ยสะสม (ให้นักศึกษาพิมพ์จากเว็บไซต์ของมหาวิทยาลัย)</h5>
                    </div>
                    <div class="card-body">
            <!-- อัปโหลดแผนที่ -->
            <div class="mb-3">
                <input type="file" class="form-control" name="fileUpload3[]" id="fileUpload3" accept="image/*,.pdf,.doc,.docx" multiple required>
                <div id="fileList3" class="list-group mt-2">
                    <!-- Preview ไฟล์จะแสดงใน JavaScript -->
                </div>
            </div>
                    </div>
                </div>
            </div>
        </div>
<!--  -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">สำเนาหน้าสมุดบัญชีเงินธนาคารของผู้ขอรับทุนการศึกษา</h5>
                    </div>
                    <div class="card-body">
            <!-- อัปโหลดแผนที่ -->
            <div class="mb-3">
                <input type="file" class="form-control" name="fileUpload4[]" id="fileUpload4" accept="image/*,.pdf,.doc,.docx" multiple required>
                <div id="fileList4" class="list-group mt-2">
                    <!-- Preview ไฟล์จะแสดงใน JavaScript -->
                </div>
            </div>
                    </div>
                </div>
            </div>
        </div>        
<!-- Submit Button -->
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <input type="hidden" name="scholarship_id" value="<?php echo isset($app['id']) ? htmlspecialchars($app['id'], ENT_QUOTES, 'UTF-8') : 'ไม่พบ ID'; ?>">
                    <input type="hidden" name="user_login" value="<?php echo htmlspecialchars($_SESSION['user_login'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="button" class="btn btn-primary" onclick="confirmSubmission()">ส่งใบสมัครทุน</button>
                </form>
            </div>
        </div>
  <!-- </form> -->
  </section>
    </div>
  </div>
  <!-- /.content-wrapper

<?php
  include "../comp/footer.php";
  ?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery UI -->
<script src="../plugins/jquery-ui/jquery-ui.min.js"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/th.min.js"></script>

<!-- Bootstrap Datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>

<!-- Tempus Dominus (Datetime Picker) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>

<!-- Sparkline -->
<script src="../plugins/sparklines/sparkline.js"></script>

<!-- JQVMap -->
<script src="../plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../plugins/jqvmap/maps/jquery.vmap.usa.js"></script>

<!-- jQuery Knob Chart -->
<script src="../plugins/jquery-knob/jquery.knob.min.js"></script>

<!-- Date Range Picker -->
<script src="../plugins/daterangepicker/daterangepicker.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Summernote -->
<script src="../plugins/summernote/summernote-bs4.min.js"></script>

<!-- overlayScrollbars -->
<script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

<!-- bs-custom-file-input -->
<script src="../plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

<!-- AdminLTE -->
<script src="../dist/js/adminlte.min.js"></script>

<!-- AdminLTE demo & dashboard scripts -->
<script src="../dist/js/demo.js"></script>
<script src="../dist/js/pages/dashboard.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- ✅ เพิ่ม SweetAlert2 -->
<!-- ปุ่มกดส่งใบสมัคร -->
<script>
function confirmSubmission() {
    var scholarshipIdInput = document.querySelector('input[name="scholarship_id"]');
    var userLoginInput = document.querySelector('input[name="user_login"]');
    var scholarshipId = scholarshipIdInput ? scholarshipIdInput.value.trim() : null;
    var userLogin = userLoginInput ? userLoginInput.value.trim() : null;

    if (!scholarshipId || scholarshipId === 'ไม่พบ ID') {
        Swal.fire({
            icon: 'error',
            title: 'ข้อผิดพลาด',
            text: 'กรุณาระบุ ID ทุนที่ถูกต้อง'
        });
        return false;
    }

    Swal.fire({
        title: 'ยืนยันการสมัครทุน?',
        text: "คุณต้องการส่งใบสมัครหรือไม่? ข้อมูลจะไม่สามารถแก้ไขได้หลังจากส่ง",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ส่งเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'กำลังดำเนินการ...',
                text: 'ระบบกำลังส่งใบสมัครของคุณ',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 2000
            });

            setTimeout(() => {
                Swal.fire({
                    title: 'ส่งใบสมัครสำเร็จ!',
                    text: 'ระบบได้บันทึกข้อมูลของคุณแล้ว',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    document.getElementById('scholarshipForm').submit(); // ✅ ส่งฟอร์มจริง
                });
            }, 1000);
        }
    });
}
</script>




<!-- ควบคุมการแสดงผลของทุนการศึกษา -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hasScholarship = document.getElementById('hasScholarship');
    const noScholarship = document.getElementById('noScholarship');
    const amountGroup = document.getElementById('amountGroup');
    const amountInput = document.querySelector('input[name="scholarship_amount"]');

    // แสดง amountGroup ตลอดเวลา
    amountGroup.style.display = 'flex';

    hasScholarship.addEventListener('change', function() {
        amountGroup.style.display = 'flex';
    });

    noScholarship.addEventListener('change', function() {
        amountGroup.style.display = 'flex';
    });

    amountInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, ''); // ให้พิมพ์ได้เฉพาะตัวเลข
    });
});
</script>

<!-- ควบคุมการแสดงผลค่าที่พัก -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const noRent = document.getElementById('no-rent');
    const payRent = document.getElementById('pay-rent');
    const feeInput = document.getElementById('fee-input-group');
    const paymentOptions = document.getElementById('payment-options');
    const dormitoryFee = document.getElementById('dormitoryFee'); 

    // แสดงทุกอย่างตั้งแต่ต้น
    feeInput.style.display = 'block';
    paymentOptions.style.display = 'block';
    selectedInfo.style.display = 'block';

    function updateDisplay() {
        if (payRent.checked && dormitoryFee && dormitoryFee.value) {
            selectedInfo.style.display = 'block';
            expenseDisplay.textContent = `${dormitoryFee.value} บาท/เดือน`;

            const selectedPaymentType = document.querySelector('input[name="payment_type"]:checked');
            paymentTypeDisplay.textContent = selectedPaymentType ? selectedPaymentType.value : '-';
        } else {
            selectedInfo.style.display = 'block'; // ให้แสดงไว้เสมอ
            expenseDisplay.textContent = '-';
            paymentTypeDisplay.textContent = '-';
        }
    }

    document.querySelectorAll('input[name="expense_select"]').forEach(radio => {
        radio.addEventListener('change', function() {
            feeInput.style.display = 'block'; 
            paymentOptions.style.display = 'block';

            if (this.id !== 'pay-rent' && dormitoryFee) {
                dormitoryFee.value = '';
            }
            updateDisplay();
        });
    });

    if (dormitoryFee) {
        dormitoryFee.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            updateDisplay();
        });
    }

    document.querySelectorAll('input[name="payment_type"]').forEach(input => {
        input.addEventListener('change', updateDisplay);
    });

    // เรียกให้แสดงผลตั้งแต่เริ่ม
    updateDisplay();
});
</script>

<!-- ฟังก์ชันอัปโหลดไฟล์และแสดงตัวอย่าง -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    function handleFileUpload(fileInputId, fileListId) {
        const fileUpload = document.getElementById(fileInputId);
        const fileList = document.getElementById(fileListId);

        fileUpload.addEventListener('change', function() {
            fileList.innerHTML = ''; 

            Array.from(this.files).forEach((file) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'list-group-item d-flex justify-content-between align-items-center';

                const fileInfo = document.createElement('div');
                fileInfo.innerHTML = `
                    <i class="bi bi-file-earmark me-2"></i>
                    <span>${file.name} <small class="text-muted ms-2">(${formatFileSize(file.size)})</small></span>
                `;

                fileItem.appendChild(fileInfo);
                fileList.appendChild(fileItem);

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'mt-2 img-thumbnail';
                        img.style.maxHeight = '200px';
                        fileItem.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    handleFileUpload('fileUpload1', 'fileList1');
    handleFileUpload('fileUpload2', 'fileList2');
    handleFileUpload('fileUpload3', 'fileList3');
    handleFileUpload('fileUpload4', 'fileList4');
});
</script>

<!-- ฟังก์ชันแสดงตัวอย่างรูปถ่ายขนาด 1 นิ้ว -->
<script>
function PreviewImage() {
    var oFReader = new FileReader();
    oFReader.readAsDataURL(document.getElementById("exampleInputFile").files[0]);

    oFReader.onload = function (oFREvent) {
        document.getElementById("uploadPreview").src = oFREvent.target.result;
    };
}

// อัปเดตชื่อไฟล์ที่เลือก
$('#exampleInputFile').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName);
});
</script>

<!-- ฟังก์ชันสำหรับอัปโหลดไฟล์ -->
<script>
function previewImage(input) {
    var fileName = input.files[0].name;
    var fileNameDisplayId = input.id + '_file_name';
    var previewId = input.id + '_preview';
    
    // อัพเดทชื่อไฟล์
    document.getElementById(fileNameDisplayId).textContent = 'เลือกไฟล์: ' + fileName;
    
    // แสดงรูปตัวอย่าง
    var preview = document.getElementById(previewId);
    var file = input.files[0];
    var reader = new FileReader();

    reader.onloadend = function () {
        preview.src = reader.result;
        preview.style.display = 'block';
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}
</script>
<script>
    const fileUpload = document.getElementById('fileUpload1');
    const fileList = document.getElementById('fileList1');

    fileUpload.addEventListener('change', function () {
        fileList.innerHTML = ''; // ล้างข้อมูลเดิม

        Array.from(this.files).forEach((file) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'list-group-item';

            const fileInfo = document.createElement('span');
            fileInfo.innerText = `${file.name}`;

            const previewImg = document.createElement('img');
            previewImg.style.maxWidth = "100px";
            previewImg.style.marginLeft = "10px";

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }

            fileItem.appendChild(fileInfo);
            fileItem.appendChild(previewImg);
            fileList.appendChild(fileItem);
        });
    });
</script>

</body>
</html>
