<!DOCTYPE html>
<?php
session_start();
include "../users/checklogin.php";
include config_loader.php";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // รับค่า scholarship_id และ applicant_id จาก URL
    $scholarship_id = filter_input(INPUT_GET, 'scholarship_id', FILTER_VALIDATE_INT);
    $applicant_id = filter_input(INPUT_GET, 'applicant_id', FILTER_VALIDATE_INT);
    
    if (!$scholarship_id || $scholarship_id <= 0 || !$applicant_id || $applicant_id <= 0) {
        echo "กรุณาระบุ ID ที่ถูกต้อง";
        exit();
    }
    
    // ดึงข้อมูลของผู้สมัคร และดึง branch_no จาก authorize โดย JOIN ผ่าน user_no
    $sql = "SELECT sa.*, COALESCE(auth.branch_no, 'ไม่มีข้อมูล') AS branch_no
            FROM scholarship_applications sa
            LEFT JOIN authorize auth ON sa.user_no = auth.user_no
            WHERE sa.scholarship_id = :scholarship_id AND sa.id = :applicant_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':scholarship_id', $scholarship_id, PDO::PARAM_INT);
    $stmt->bindParam(':applicant_id', $applicant_id, PDO::PARAM_INT);
    $stmt->execute();
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$application) {
        echo "ไม่พบข้อมูลผู้สมัครที่ต้องการ";
        exit();
    }
    
} catch (PDOException $e) {
    error_log($e->getMessage(), 3, '/path/to/error.log');
    echo "เกิดข้อผิดพลาด กรุณาลองใหม่ภายหลัง";
}


?>

<html lang="en">
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายละเอียดใบสมัครทุนการศึกษา</title>
    
    <!-- นำเข้า jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- นำเข้า Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- นำเข้า Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/th.min.js"></script>

<!-- นำเข้า Bootstrap Datepicker และ Tempus Dominus (Datetime Picker) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- นำเข้า DataTables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- นำเข้า AdminLTE -->
<script src="../dist/js/adminlte.min.js"></script>

<!-- นำเข้า Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<!-- นำเข้า Bootstrap Datepicker และ Tempus Dominus CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />

<!-- นำเข้า Font Awesome, Ionicons และ Google Fonts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sarabun:300,400,400i,700&display=swap">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

<!-- นำเข้า AdminLTE CSS -->
<link rel="stylesheet" href="../dist/css/adminlte.min.css">

<!-- นำเข้า Plugins CSS -->
<link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<link rel="stylesheet" href="../plugins/jqvmap/jqvmap.min.css">
<link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
<link rel="stylesheet" href="../plugins/iCheck/flat/blue.css">



    <style>
        .form-check-input:disabled {
        opacity: 1 !important;  /* ทำให้ความโปร่งใสเป็น 100% */
        background-color: #fff !important;  /* พื้นหลังสีขาว */
        border-color: #0d6efd !important;  /* สีขอบเหมือนตอนปกติ */
    }
    .form-check-input:disabled:checked {
        background-color: #0d6efd !important;  /* สีฟ้าสำหรับ radio ที่ถูกเลือก */
        border-color: #0d6efd !important;
    }
    .form-check-label {
        opacity: 1 !important;  /* ทำให้ความโปร่งใสของข้อความเป็น 100% */
        color: #000 !important;  /* สีข้อความดำปกติ */
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

    <?php include "../comp/preloader.php"; ?>
    <?php include "../comp/navbar.php"; ?>
    <?php include "../comp/aside.php"; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>รายละเอียดใบสมัครทุนการศึกษา</h1>
                    </div>
                </div>
            </div>
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
    <form id="scholarshipForm" action="scholarship_pdf.php" method="GET">
        <!-- Academic Year Section -->
        <div class="row mb-4">
            <!-- Logo Column -->
            <div class="col-md-3 text-center">
                <img src="../dist/img/image/logormutt.png" alt="RMUTT Logo" class="img-fluid" style="max-height: 150px;">
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
                </div>
            </div>
            <!-- Student Photo Column -->
            <div class="col-md-3 d-flex justify-content-center">
                <div class="photo-placeholder text-center">
                    <?php if (isset($application) && $application && isset($application['student_photo']) && $application['student_photo']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($application['student_photo']); ?>" 
                            class="img-fluid" alt="Student Photo">
                    <?php else: ?>
                        <div class="photo-placeholder">
                            <!-- คุณสามารถเพิ่มข้อความหรือรูปภาพ placeholder ที่นี่ -->
                            ไม่มีรูปภาพ
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!--Basic Information -->
        <div class="row">
          <div class="col-md-12">
            <h4 class="section-header">ข้อมูลผู้สมัครขอทุน</h4>
            <?php if (isset($application) && is_array($application)): ?>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="required-field">คำนำหน้า (ภาษาไทย)</label>
                <div class="detail-value border rounded p-2 bg-light mb-2">
                    <?php echo !empty($application['prefix_th']) ? htmlspecialchars($application['prefix_th']) : '-'; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label class="required-field">ชื่อ (ภาษาไทย)</label>
                <div class="detail-value border rounded p-2 bg-light mb-2">
                    <?php echo !empty($application['first_name_th']) ? htmlspecialchars($application['first_name_th']) : '-'; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label class="required-field">นามสกุล (ภาษาไทย)</label>
                <div class="detail-value border rounded p-2 bg-light mb-2">
                    <?php echo !empty($application['last_name_th']) ? htmlspecialchars($application['last_name_th']) : '-'; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add more rows and form fields here -->



            <!-- English Name -->
            <div class="row">
            <div class="col-md-4">
            <div class="form-group">
    <label class="required-field">คำนำหน้า (ภาษาอังกฤษ)</label>
    <div class="detail-value border rounded p-2 bg-light">
    <?php echo !empty($application['prefix_en']) ? htmlspecialchars($application['prefix_en']) : '-'; ?>
                </div>
</div>
</div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="required-field">ชื่อ (ภาษาอังกฤษตัวพิมพ์ใหญ่)</label>
                  <div class="detail-value border rounded p-2 bg-light">
                  <?php echo !empty($application['first_name_en']) ? htmlspecialchars($application['first_name_en']) : '-'; ?>
                </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="required-field">นามสกุล (ภาษาอังกฤษตัวพิมพ์ใหญ่)</label>
                  <div class="detail-value border rounded p-2 bg-light">
                  <?php echo !empty($application['last_name_en']) ? htmlspecialchars($application['last_name_en']) : '-'; ?>
                </div>
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
              <div class="detail-value border rounded p-2 bg-light">
              <?php echo !empty($application['faculty']) ? htmlspecialchars($application['faculty']) : '-'; ?>
            </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="required-field">สาขาวิชา</label>
              <div class="detail-value border rounded p-2 bg-light">
    <?php echo !empty($application['branch_no']) ? htmlspecialchars($application['branch_no']) : '-'; ?>
</div>

            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="required-field">ชั้นปีที่</label>
              <div class="detail-value border rounded p-2 bg-light">
              <?php echo !empty($application['year_level']) ? htmlspecialchars($application['year_level']) : '-'; ?>
                                </div>
            </div>
          </div>
        </div>
        <!--  -->
        <div class="row">
        <div class="col-md-6">
            <div class="form-group">
              <label class="required-field">รหัสประจำตัวนึกศึกษา</label>
              <div class="detail-value border rounded p-2 bg-light">
              <?php echo !empty($application['student_id']) ? htmlspecialchars($application['student_id']) : '-'; ?>
                                </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="required-field">เกรดเฉลี่ย</label>
              <div class="detail-value border rounded p-2 bg-light">
              <?php echo !empty($application['gpa']) ? htmlspecialchars($application['gpa']) : '-'; ?>
                                </div>
            </div>
          </div>  
        </div>        
        <!--  -->
        <div class="row">
        <div class="col-md-4">
            <div class="form-group">
              <label class="required-field">สถานที่เกิด</label>
              <div class="detail-value border rounded p-2 bg-light">
              <?php echo !empty($application['birth_place']) ? htmlspecialchars($application['birth_place']) : '-'; ?>
                                </div>
            </div>
          </div>
          <div class="col-sm-4">
                <div class="form-group">
                <label class="required-field">วันเดือนปีเกิด</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['birth_date']) ? htmlspecialchars($application['birth_date']) : '-'; ?>
                                </div>
                </div>
            </div>
            
          <div class="col-md-2">
            <div class="form-group">
              <label class="required-field">อายุ</label>
              <div class="detail-value border rounded p-2 bg-light">
              <?php echo !empty($application['age']) ? htmlspecialchars($application['age']) : '-'; ?>
                                </div>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="required-field">ศาสนา</label>
              <div class="detail-value border rounded p-2 bg-light">
              <?php echo !empty($application['religion']) ? htmlspecialchars($application['religion']) : '-'; ?>
                                </div>
            </div>
          </div>
        </div>
        <!-- Previous Education History -->
        <h4 class="section-header mt-4">ที่อยู่ตามทะเบียนบ้าน</h4>
        <div class="row">
  <div class="col-md-4">
    <div class="form-group">
      <label>บ้านเลขที่</label>
      <div class="detail-value border rounded p-2 bg-light">
    <?php echo !empty($application['permanent_house_no']) ? htmlspecialchars($application['permanent_house_no']) : '-'; ?>
</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>หมู่ที่</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['permanent_moo']) ? htmlspecialchars($application['permanent_moo']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>ถนน</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['permanent_road']) ? htmlspecialchars($application['permanent_road']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>ตำบล / แขวง</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['permanent_subdistrict']) ? htmlspecialchars($application['permanent_subdistrict']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>อำเภอ / เขต</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['permanent_district']) ? htmlspecialchars($application['permanent_district']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>จังหวัด</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['permanent_province']) ? htmlspecialchars($application['permanent_province']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>รหัสไปรษณีย์</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['permanent_postal_code']) ? htmlspecialchars($application['permanent_postal_code']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>โทรศัพท์ (บ้าน)</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['permanent_phone']) ? htmlspecialchars($application['permanent_phone']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>โทรศัพท์ (มือถือ)</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['permanent_mobile']) ? htmlspecialchars($application['permanent_mobile']) : '-'; ?>
                                </div>
    </div>
  </div>
</div>
<!--  -->
<h4 class="section-header mt-4">ที่อยู่ปัจจุบัน (สามารถติดต่อได้ขณะกำลังศึกษา)</h4>
<div class="row">
  <div class="col-md-4">
    <div class="form-group">
      <label>ประเภทที่พัก</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['current_residence_type']) ? htmlspecialchars($application['current_residence_type']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>อาคาร</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['current_building']) ? htmlspecialchars($application['current_building']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>หมายเลขห้องพัก</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['current_room_no']) ? htmlspecialchars($application['current_room_no']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>ที่อยู่เลขที่</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['current_house_no']) ? htmlspecialchars($application['current_house_no']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>หมู่ที่</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['current_moo']) ? htmlspecialchars($application['current_moo']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>ถนน</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['current_road']) ? htmlspecialchars($application['current_road']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>ตำบล / แขวง</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['current_subdistrict']) ? htmlspecialchars($application['current_subdistrict']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>อำเภอ / เขต</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['current_district']) ? htmlspecialchars($application['current_district']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>จังหวัด</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['current_province']) ? htmlspecialchars($application['current_province']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>รหัสไปรษณีย์</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['current_postal_code']) ? htmlspecialchars($application['current_postal_code']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>โทรศัพท์</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['current_phone']) ? htmlspecialchars($application['current_phone']) : '-'; ?>
                                </div>
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
    <div class="flex gap-6 pt-2 pb-2">
    <?php 
        $selectedAllowanceType = !empty($application['parent_allowance_type']) ? $application['parent_allowance_type'] : ''; 
        $options = ['รายวัน', 'รายสัปดาห์', 'รายเดือน']; // ตัวเลือกทั้งหมด
    ?>

    <?php foreach ($options as $option): ?>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="parent_allowance_type" id="allowance_<?php echo $option; ?>" value="<?php echo $option; ?>" 
                <?php echo ($selectedAllowanceType === $option) ? 'checked disabled' : 'disabled'; ?>>
            <label class="form-check-label" for="allowance_<?php echo $option; ?>"><?php echo $option; ?></label>
        </div>
    <?php endforeach; ?>
</div>

    <div class="col-span-12 md:col-span-6">
    <div class="detail-value border rounded p-2 bg-light">
    <?php echo !empty($application['parent_allowance_amount']) ? htmlspecialchars($application['parent_allowance_amount']) : '-'; ?>
                                    </div>
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
                        <?php 
                            $selectedAllowanceType = !empty($application['other_allowance_type']) ? $application['other_allowance_type'] : ''; 
                            $options = ['รายวัน', 'รายสัปดาห์', 'รายเดือน']; // ตัวเลือกทั้งหมด
                        ?>
                        <?php foreach ($options as $option): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="other_allowance_type" id="allowance_<?php echo $option; ?>" value="<?php echo $option; ?>" 
                                    <?php echo ($selectedAllowanceType === $option) ? 'checked disabled' : 'disabled'; ?>>
                                <label class="form-check-label" for="allowance_<?php echo $option; ?>"><?php echo $option; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <div class="detail-value border rounded p-2 bg-light">
                            <?php echo !empty($application['other_allowance_amount']) ? htmlspecialchars($application['other_allowance_amount']) : '-'; ?>
                        </div>
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
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['loan_amount']) ? htmlspecialchars($application['loan_amount']) : '-'; ?>
                                </div>
      </div>
      </div>
      </div>
  </div>
      <!-- รายได้พิเศษ -->
      <div class="col-md-3">
    <div class="form-group">
      <label>มีรายได้พิเศษวันละ</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['extra_income_daily']) ? htmlspecialchars($application['extra_income_daily']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label y>โดยได้รับจาก</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['extra_income_source']) ? htmlspecialchars($application['extra_income_source']) : '-'; ?>
                                </div>
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
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['food_expense_daily']) ? htmlspecialchars($application['food_expense_daily']) : '-'; ?>
                                </div>
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
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['accommodation_expense']) ? htmlspecialchars($application['accommodation_expense']) : '-'; ?>
                                </div>
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
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['transportation_method']) ? htmlspecialchars($application['transportation_method']) : '-'; ?>
                                </div>
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
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['transportation_expense_daily']) ? htmlspecialchars($application['transportation_expense_daily']) : '-'; ?>
                                </div>
      </div>
      </div>
      </div>
  </div>
      <!--  -->
      <div class="col-md-6">
    <div class="form-group">
      <label> ค่าอุปกรณ์การเรียน / ตำราเรียน</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['education_supplies_expense']) ? htmlspecialchars($application['education_supplies_expense']) : '-'; ?>
                                </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label y>ค่าใช้จ่ายอื่น ๆ</label>
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['other_expense_detail']) ? htmlspecialchars($application['other_expense_detail']) : '-'; ?>
                                </div>    
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
    <label class="text-white text-opacity-10">---</label>
    <div class="detail-value border rounded p-2 bg-light">
    <?php echo !empty($application['other_expense_amount']) ? htmlspecialchars($application['other_expense_amount']) : '-'; ?>
                                </div> 
    </div>
  </div>
  <div class="col-md-12">
          <div class="form-group">
        <div class="flex items-center">
    <label class="font-medium">ประมาณการค้าใช้จ่ายที่นักศึกษาคาดว่าจะเพียงพอสำหรับตนเอง</label>
      <div class="col-span-12 md:col-span-6">
      <div class="detail-value border rounded p-2 bg-light">
      <?php echo !empty($application['estimated_monthly_expense']) ? htmlspecialchars($application['estimated_monthly_expense']) : '-'; ?>
                                </div> 
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
<div class="col-md-12">
<div class="mt-8">
    <div class="row g-3">
        <!-- แสดงเงื่อนไขการอยู่อาศัย -->
        <div class="col-12">
            <div class="form-group">
                <div class="d-flex gap-3 flex-wrap align-items-center">
                    <?php 
                        $selectedLivingCondition = !empty($application['living_conditions_grantees']) ? $application['living_conditions_grantees'] : ''; 
                        $relationshipBenefactors = !empty($application['relationship_benefactors']) ? htmlspecialchars($application['relationship_benefactors']) : ''; 
                        $options = ['อยู่กับบิดามารดา', 'อยู่กับบิดา', 'อยู่กับมารดา', 'อยู่กับผู้อุปการะ', 'อยู่หอพัก / วัด']; 
                    ?>

                    <?php foreach ($options as $option): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="living_conditions_grantees" id="living_<?php echo $option; ?>" value="<?php echo $option; ?>"
                                <?php echo ($selectedLivingCondition === $option) ? 'checked disabled' : 'disabled'; ?>>
                            <label class="form-check-label" for="living_<?php echo $option; ?>">
                                <?php echo htmlspecialchars($option); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>

                    <!-- แสดงช่อง "ความสัมพันธ์กับผู้อุปการะ" ทันที -->
                    <input type="text" class="form-control" value="<?php echo $relationshipBenefactors; ?>" disabled style="max-width: 200px;" placeholder="ความสัมพันธ์กับผู้อุปการะ">
                </div>
            </div>
        </div>

        <!-- แสดงข้อมูลเพิ่มเติมเมื่ออยู่หอพัก/วัด -->
        <label class="">ถ้าเลือกตัวเลือก หอพัก / วัด กรุณากรอกข้อมูลด้านล่างนี้</label>
            <div class="col-12">
                <div class="row g-2">
                    <!-- ชื่อ -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label">ชื่อ</label>
                            <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['dormitorytemple']) ? htmlspecialchars($application['dormitorytemple']) : '-'; ?>
                            </div>
                        </div>
                    </div>
            
                    <!-- ห้อง & สถานที่ติดต่อ -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label">ห้อง</label>
                            <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['dormitorytemple_room']) ? htmlspecialchars($application['dormitorytemple_room']) : '-'; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label">สถานที่ติดต่อได้</label>
                            <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['dormitorytemple_contact']) ? htmlspecialchars($application['dormitorytemple_contact']) : '-'; ?>
                            </div>
                        </div>
                    </div>

                    <!-- เบอร์โทรศัพท์ -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label">เบอร์โทรศัพท์</label>
                            <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['dormitorytemple_phone']) ? htmlspecialchars($application['dormitorytemple_phone']) : '-'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
</div>
<!--จบ สภาพความเป็นอยู่ผู้ขอทุน  -->

<!--เริ่ม ค่าใช้จ่ายด้านที่พัก -->
<h4 class="section-header mt-4">ค่าใช้จ่ายด้านที่พัก</h4>
<div class="col-md-12">
    <div class="mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?php 
                            $selectedExpense = !empty($application['expense_select']) ? $application['expense_select'] : ''; 
                            $dormitoryFee = !empty($application['dormitoryhouse_fee']) ? htmlspecialchars($application['dormitoryhouse_fee']) : '0';
                            $paymentType = !empty($application['payment_type']) ? htmlspecialchars($application['payment_type']) : '';
                            $expenseOptions = ['ไม่เสียค่าที่พัก', 'ค่าหอพัก / ค่าเช่าบ้าน'];
                            $paymentOptions = ['จ่ายคนเดียว', 'ร่วมกับผู้อื่น'];
                        ?>

                        <!-- ค่าที่พัก -->
                        <div class="d-flex gap-3 flex-wrap align-items-center">
                            <?php foreach ($expenseOptions as $option): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" disabled 
                                        <?php echo ($selectedExpense === $option) ? 'checked' : ''; ?>>
                                    <label class="form-check-label">
                                        <?php echo htmlspecialchars($option); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                            <div class="d-flex align-items-center ms-3">
                                <input type="text" class="form-control text-center" 
                                    value="<?php echo $dormitoryFee; ?>" 
                                    disabled style="max-width: 80px;">
                                <span class="ms-2">บาท/เดือน</span>
                            </div>
                            <div class="d-flex gap-3 align-items-center ms-3">
                            <span>ประเภทการจ่าย:</span>
                            <?php foreach ($paymentOptions as $option): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" disabled 
                                        <?php echo ($paymentType === $option) ? 'checked' : ''; ?>>
                                    <label class="form-check-label">
                                        <?php echo htmlspecialchars($option); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        </div>                        
                    </div>
                </div>

<!--จบ ค่าใช้จ่ายด้านที่พัก -->


                <!-- ทุนกู้ยืม -->
                <div class="col-md-12">
    <div class="form-group">
        <?php 
            $scholarshipStatus = !empty($application['scholarship_status']) ? $application['scholarship_status'] : ''; 
            $scholarshipAmount = !empty($application['scholarship_amount']) ? htmlspecialchars($application['scholarship_amount']) : '';
            $scholarshipTermAmount = !empty($application['scholarship_term_amount']) ? htmlspecialchars($application['scholarship_term_amount']) : '';
            $scholarshipCostLiving = !empty($application['scholarship_cost_living']) ? htmlspecialchars($application['scholarship_cost_living']) : '';
            $scholarshipOptions = ['ได้รับทุนกู้ยืมรัฐบาล (กยศ.) (ปีล่าสุด) ปีการศึกษา', 'ไม่ได้กู้ยืม'];
        ?>

        <div class="d-flex gap-3 flex-wrap align-items-center">
            <?php foreach ($scholarshipOptions as $option): ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio" disabled 
                        <?php echo ($scholarshipStatus === $option) ? 'checked' : ''; ?>>
                    <label class="form-check-label">
                        <?php echo htmlspecialchars($option); ?>
                    </label>
                </div>
                
                <?php if ($option === 'ได้รับทุนกู้ยืมรัฐบาล (กยศ.) (ปีล่าสุด) ปีการศึกษา'): ?>
                    <div class="d-flex align-items-center">
                        <input type="text" class="form-control text-center" 
                            value="<?php echo $scholarshipAmount; ?>" 
                            disabled style="max-width: 120px;">
                        <span class="ms-2">บาท / ปี</span>
                    </div>
                    
                    <div class="d-flex align-items-center ms-3">
                        <span>ค่าเทอม</span>
                        <input type="text" class="form-control text-center ms-2" 
                            value="<?php echo $scholarshipTermAmount; ?>" 
                            disabled style="max-width: 120px;">
                    </div>
                    
                    <div class="d-flex align-items-center ms-3">
                        <span>ค่าครองชีพรายเดือน</span>
                        <input type="text" class="form-control text-center ms-2" 
                            value="<?php echo $scholarshipCostLiving; ?>" 
                            disabled style="max-width: 120px;">
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>


            </div>
        </div>
    </div>
</div>


<!--ประวัติการรับทุนการศึกษา  -->              
            <h4 class="section-header">ประวัติการรับทุนการศึกษา</h4>
            <div class="container-fluid bg-white rounded"> 
            <div class="mb-3">
    <div class="d-flex gap-3">
        <?php 
            $selectedStatus = !empty($application['historycholarship_status']) ? $application['historycholarship_status'] : ''; 
            $scholarshipOptions = ['เคยได้รับทุนการศึกษา', 'ไม่เคยได้รับทุนการศึกษา']; // ตัวเลือกที่ต้องแสดงทั้งหมด
        ?>

        <?php foreach ($scholarshipOptions as $option): ?>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="historycholarship_status" 
                    value="<?php echo $option; ?>" 
                    <?php echo ($selectedStatus === $option) ? 'checked' : ''; ?> 
                    disabled>
                <label class="form-check-label">
                    <?php echo htmlspecialchars($option); ?>
                </label>
            </div>
        <?php endforeach; ?>
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
                        <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['senior_high_school']) ? htmlspecialchars($application['senior_high_school']) : '-'; ?>
                                </div></td>
                        <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['senior_high_school_amount']) ? htmlspecialchars($application['senior_high_school_amount']) : '-'; ?>
                                </div></td>
                                <td>
            <div class="form-check form-check-inline">
                <?php 
                    $selectedLandStatus = !empty($application['landstatus']) ? $application['landstatus'] : ''; 
                    $landOptions = ['ต่อเนื่อง', 'เฉพาะปี', 'ไม่ผูกพัน', 'ผูกพัน']; // ตัวเลือกทั้งหมด
                ?>

                <?php foreach ($landOptions as $option): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="landstatus" value="<?php echo $option; ?>"
                            <?php echo ($selectedLandStatus === $option) ? 'checked' : ''; ?> disabled>
                        <label class="form-check-label"><?php echo $option; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>


</td>
                    </tr>
                    <tr>
                        <td>อุดมศึกษาปีที่ 1</td>
                        <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['one_years']) ? htmlspecialchars($application['one_years']) : '-'; ?>
                                </div></td>
                                <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['one_years_amount']) ? htmlspecialchars($application['one_years_amount']) : '-'; ?>
                                </div></td>
                                
                                <td>
                                <div class="form-check form-check-inline">
    <?php 
        $selectedLandStatus = !empty($application['landstatus1']) ? $application['landstatus1'] : ''; 
        $landOptions = ['ต่อเนื่อง', 'เฉพาะปี', 'ไม่ผูกพัน', 'ผูกพัน']; // ตัวเลือกทั้งหมด
    ?>

    <?php foreach ($landOptions as $option): ?>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="landstatus1" value="<?php echo $option; ?>"
                <?php echo ($selectedLandStatus === $option) ? 'checked' : ''; ?> disabled>
            <label class="form-check-label"><?php echo $option; ?></label>
        </div>
    <?php endforeach; ?>
</div>


</td>
                    </tr>
                    <tr>
                        <td>อุดมศึกษาปีที่ 2</td>
                        <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['two_years']) ? htmlspecialchars($application['two_years']) : '-'; ?>
                                </div></td>
                                <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['two_years_amount']) ? htmlspecialchars($application['two_years_amount']) : '-'; ?>
                                </div></td>
                                <td>
                                <div class="form-check form-check-inline">
                <?php 
                    $selectedLandStatus = !empty($application['landstatus2']) ? $application['landstatus2'] : ''; 
                    $landOptions = ['ต่อเนื่อง', 'เฉพาะปี', 'ไม่ผูกพัน', 'ผูกพัน']; // ตัวเลือกทั้งหมด
                ?>

                <?php foreach ($landOptions as $option): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="landstatus2" value="<?php echo $option; ?>"
                            <?php echo ($selectedLandStatus === $option) ? 'checked' : ''; ?> disabled>
                        <label class="form-check-label"><?php echo $option; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

</td>
                    </tr>
                    <tr>
                        <td>อุดมศึกษาปีที่ 3</td>
                        <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['three_years']) ? htmlspecialchars($application['three_years']) : '-'; ?>
                                </div></td>
                                <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['three_years_amount']) ? htmlspecialchars($application['three_years_amount']) : '-'; ?>
                                </div></td>
                                <td>
                                <div class="form-check form-check-inline">
                <?php 
                    $selectedLandStatus = !empty($application['landstatus3']) ? $application['landstatus3'] : ''; 
                    $landOptions = ['ต่อเนื่อง', 'เฉพาะปี', 'ไม่ผูกพัน', 'ผูกพัน']; // ตัวเลือกทั้งหมด
                ?>

                <?php foreach ($landOptions as $option): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="landstatus3" value="<?php echo $option; ?>"
                            <?php echo ($selectedLandStatus === $option) ? 'checked' : ''; ?> disabled>
                        <label class="form-check-label"><?php echo $option; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

</td>
                    </tr>
                    <tr>
                        <td>อุดมศึกษาปีที่ 4</td>
                        <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['four_years']) ? htmlspecialchars($application['four_years']) : '-'; ?>
                                </div></td>
                                <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['four_years_amount']) ? htmlspecialchars($application['four_years_amount']) : '-'; ?>
                                </div></td>
                                <td>
                                <div class="form-check form-check-inline">
                <?php 
                    $selectedLandStatus = !empty($application['landstatus4']) ? $application['landstatus4'] : ''; 
                    $landOptions = ['ต่อเนื่อง', 'เฉพาะปี', 'ไม่ผูกพัน', 'ผูกพัน']; // ตัวเลือกทั้งหมด
                ?>

                <?php foreach ($landOptions as $option): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="landstatus4" value="<?php echo $option; ?>"
                            <?php echo ($selectedLandStatus === $option) ? 'checked' : ''; ?> disabled>
                        <label class="form-check-label"><?php echo $option; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

</td>
                    </tr>
                    <tr>
                        <td>อุดมศึกษาปีที่ 5</td>
                        <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['five_years']) ? htmlspecialchars($application['five_years']) : '-'; ?>
                                </div></td>
                                <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['five_years_amount']) ? htmlspecialchars($application['five_years_amount']) : '-'; ?>
                                </div></td>
                                <td>
                                <div class="form-check form-check-inline">
                <?php 
                    $selectedLandStatus = !empty($application['landstatus5']) ? $application['landstatus5'] : ''; 
                    $landOptions = ['ต่อเนื่อง', 'เฉพาะปี', 'ไม่ผูกพัน', 'ผูกพัน']; // ตัวเลือกทั้งหมด
                ?>

                <?php foreach ($landOptions as $option): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="landstatus5" value="<?php echo $option; ?>"
                            <?php echo ($selectedLandStatus === $option) ? 'checked' : ''; ?> disabled>
                        <label class="form-check-label"><?php echo $option; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

</td>
                    </tr>
                    <tr>
                        <td>อุดมศึกษาปีที่ 6</td>
                        <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['six_years']) ? htmlspecialchars($application['six_years']) : '-'; ?>
                                </div></td>
                                <td><div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['six_years_amount']) ? htmlspecialchars($application['six_years_amount']) : '-'; ?>
                                </div></td>
                                <td>
                                <div class="form-check form-check-inline">
                <?php 
                    $selectedLandStatus = !empty($application['landstatus6']) ? $application['landstatus6'] : ''; 
                    $landOptions = ['ต่อเนื่อง', 'เฉพาะปี', 'ไม่ผูกพัน', 'ผูกพัน']; // ตัวเลือกทั้งหมด
                ?>

                <?php foreach ($landOptions as $option): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="landstatus6" value="<?php echo $option; ?>"
                            <?php echo ($selectedLandStatus === $option) ? 'checked' : ''; ?> disabled>
                        <label class="form-check-label"><?php echo $option; ?></label>
                    </div>
                <?php endforeach; ?>
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
          <div class="detail-value border rounded p-2 bg-light">
          <?php echo !empty($application['primary_school']) ? htmlspecialchars($application['primary_school']) : '-'; ?>
                                </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="primary-province" class="form-label">จังหวัด</label>
          <div class="detail-value border rounded p-2 bg-light">
          <?php echo !empty($application['primary_province']) ? htmlspecialchars($application['primary_province']) : '-'; ?>
                                </div>
        </div>
      </div>
    </div>
    <!-- มัธยมศึกษาตอนต้น -->
    <div class="row mb-3">
      <div class="col-md-8">
        <div class="form-group">
          <label for="middle-school" class="form-label">มัธยมศึกษาตอนต้น จากโรงเรียน</label>
          <div class="detail-value border rounded p-2 bg-light">
          <?php echo !empty($application['middle_school']) ? htmlspecialchars($application['middle_school']) : '-'; ?>
                                </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="middle-province" class="form-label">จังหวัด</label>
          <div class="detail-value border rounded p-2 bg-light">
          <?php echo !empty($application['middle_province']) ? htmlspecialchars($application['middle_province']) : '-'; ?>
                                </div>
        </div>
      </div>
    </div>
    <!-- มัธยมศึกษาตอนปลาย -->
    <div class="row">
      <div class="col-md-8">
        <div class="form-group">
          <label for="high-school" class="form-label">มัธยมศึกษาตอนปลาย จากโรงเรียน</label>
          <div class="detail-value border rounded p-2 bg-light">
          <?php echo !empty($application['high_school']) ? htmlspecialchars($application['high_school']) : '-'; ?>
                                </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="high-province" class="form-label">จังหวัด</label>
          <div class="detail-value border rounded p-2 bg-light">
          <?php echo !empty($application['high_province']) ? htmlspecialchars($application['high_province']) : '-'; ?>
                                </div>
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
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_fullname']) ? htmlspecialchars($application['father_fullname']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">อายุ</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_age']) ? htmlspecialchars($application['father_age']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">สถานะ</label>
                    <div class="form-check form-check-inline">
                        <?php 
                            $selectedFatherStatus = !empty($application['father_status']) ? $application['father_status'] : ''; 
                            $options = ['มีชีวิต', 'ถึงแก่กรรม']; // ตัวเลือกทั้งหมด
                        ?>

                        <?php foreach ($options as $option): ?>
                            <input class="form-check-input" type="radio" name="father_status" id="father_status_<?php echo $option; ?>" value="<?php echo $option; ?>" 
                                <?php echo ($selectedFatherStatus === $option) ? 'checked disabled' : 'disabled'; ?>>
                            <label class="form-check-label" for="father_status_<?php echo $option; ?>"><?php echo $option; ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">ที่อยู่ บ้านเลขที่</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_house']) ? htmlspecialchars($application['father_house']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ตรอก / ซอย</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_alley']) ? htmlspecialchars($application['father_alley']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">หมู่ที่</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_moo']) ? htmlspecialchars($application['father_moo']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ถนน</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_road']) ? htmlspecialchars($application['father_road']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ตำบล / แขวง</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_subdistrict']) ? htmlspecialchars($application['father_subdistrict']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">อำเภอ / เขต</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_district']) ? htmlspecialchars($application['father_district']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">จังหวัด</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_province']) ? htmlspecialchars($application['father_province']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">รหัสไปรษณีย์</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_post_code']) ? htmlspecialchars($application['father_post_code']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์บ้าน</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_house_no']) ? htmlspecialchars($application['father_house_no']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์มือถือ</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_phone']) ? htmlspecialchars($application['father_phone']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">อาชีพบิดา</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_occupation']) ? htmlspecialchars($application['father_occupation']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">รายได้ต่อเดือน (บาท)</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_income']) ? htmlspecialchars($application['father_income']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">ตำแหน่ง / ยศ</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_rank']) ? htmlspecialchars($application['father_rank']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ลักษณะงาน</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_job_description']) ? htmlspecialchars($application['father_job_description']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">สถานที่ทำงานของบิดา</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_workplace']) ? htmlspecialchars($application['father_workplace']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['father_telephone']) ? htmlspecialchars($application['father_telephone']) : '-'; ?>
                                </div>
                </div>
            </div>
    </div>
        </form>
    <!--จบ บิดา  -->
    <!-- มารดา -->
    <form>
    <div class="card-header">ข้อมูลมารดา</div>
    <div class="mt-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">ชื่อ-นามสกุล มารดา</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_fullname']) ? htmlspecialchars($application['mother_fullname']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">อายุ</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_age']) ? htmlspecialchars($application['mother_age']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">สถานะ</label>
                    <div class="form-check form-check-inline" ">
                        <?php 
                            $selectedMotherStatus = !empty($application['mother_status']) ? $application['mother_status'] : ''; 
                            $options = ['มีชีวิต', 'ถึงแก่กรรม']; // ตัวเลือกทั้งหมด
                        ?>

                        <?php foreach ($options as $option): ?>
                            <input class="form-check-input" type="radio" name="mother_status" id="mother_status_<?php echo $option; ?>" value="<?php echo $option; ?>" 
                                <?php echo ($selectedMotherStatus === $option) ? 'checked disabled' : 'disabled'; ?>>
                            <label class="form-check-label" for="mother_status_<?php echo $option; ?>"><?php echo $option; ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">ที่อยู่ บ้านเลขที่</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_house']) ? htmlspecialchars($application['mother_house']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ตรอก / ซอย</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_ally']) ? htmlspecialchars($application['mother_ally']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">หมู่ที่</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_moo']) ? htmlspecialchars($application['mother_moo']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ถนน</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_road']) ? htmlspecialchars($application['mother_road']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ตำบล / แขวง</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_subdistrict']) ? htmlspecialchars($application['mother_subdistrict']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">อำเภอ / เขต</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_district']) ? htmlspecialchars($application['mother_district']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">จังหวัด</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_province']) ? htmlspecialchars($application['mother_province']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">รหัสไปรษณีย์</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_postcode']) ? htmlspecialchars($application['mother_postcode']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์บ้าน</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_house_no']) ? htmlspecialchars($application['mother_house_no']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์มือถือ</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_phone']) ? htmlspecialchars($application['mother_phone']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">อาชีพมารดา</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_occupation']) ? htmlspecialchars($application['mother_occupation']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">รายได้ต่อเดือน (บาท)</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_income']) ? htmlspecialchars($application['mother_income']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">ตำแหน่ง / ยศ</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_rank']) ? htmlspecialchars($application['mother_rank']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ลักษณะงาน</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_job_description']) ? htmlspecialchars($application['mother_job_description']) : '-'; ?>
                                </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">สถานที่ทำงานของมารดา</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_workplace']) ? htmlspecialchars($application['mother_workplace']) : '-'; ?>
                                </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์</label>
                    <div class="detail-value border rounded p-2 bg-light">
                    <?php echo !empty($application['mother_telephone']) ? htmlspecialchars($application['mother_telephone']) : '-'; ?>
                                </div>
                </div>
            </div>
    </div>
        </form>
  </div>
    <!-- จบ ข้อมูลครอบครัว -->
    <!--เริ่ม สภาพความเป็นอยู่ผู้ขอทุน -->
        <h4 class="section-header mt-4">สถานภาพครอบครัว</h4>
        <div class="mt-3">
    <div class="row">
        <div class="col-md-12">
            <?php 
                $selectedFamilyStatus = !empty($application['familystatus']) ? $application['familystatus'] : ''; 
                $familyOptions = [
                    'บิดามารดาอยู่ด้วยกัน', 
                    'บิดาถึงแก่กรรม', 
                    'มารดาถึงแก่กรรม'
                ]; 
            ?>

            <!-- ตัวเลือกทั่วไป -->
            <div class="d-flex flex-wrap gap-4">
                <?php foreach ($familyOptions as $option): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="familystatus" 
                               value="<?php echo $option; ?>" 
                               <?php echo ($selectedFamilyStatus === $option) ? 'checked' : ''; ?> disabled>
                        <label class="form-check-label"><?php echo $option; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ตัวเลือกที่มีช่องกรอกข้อมูล -->
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="familystatus" 
                               value="บิดามารดาหย่าร้างกัน" 
                               <?php echo ($selectedFamilyStatus === 'บิดามารดาหย่าร้างกัน') ? 'checked' : ''; ?> disabled>
                        <label class="form-check-label">บิดามารดาหย่าร้างกัน</label> 
                    </div>
                    <input type="text" class="form-control ms-2" style="width: 100%;"
                               value="<?php echo !empty($application['benefactor']) ? htmlspecialchars($application['benefactor']) : ''; ?>"
                               placeholder=""
                               <?php echo ($selectedFamilyStatus === 'บิดามารดาหย่าร้างกัน') ? '' : 'disabled'; ?>>
                </div>

                <div class="col-md-12">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="familystatus"
                               value="บิดามารดาแยกกันอยู่" 
                               <?php echo ($selectedFamilyStatus === 'บิดามารดาแยกกันอยู่') ? 'checked' : ''; ?> disabled>
                        <label class="form-check-label">บิดามารดาแยกกันอยู่</label>
                    </div>
                    <input type="text" class="form-control ms-2" style="width: 100%;"
                               value="<?php echo !empty($application['living_with']) ? htmlspecialchars($application['living_with']) : ''; ?>"
                               placeholder=""
                               <?php echo ($selectedFamilyStatus === 'บิดามารดาแยกกันอยู่') ? '' : 'disabled'; ?>>
                </div>
            </div>

            <!-- ตัวเลือกอื่นๆ -->
            <div class="row mt-2">
                <div class="col-md-12">
                    <label class="form-label">อื่นๆ</label>
                    <input type="text" class="form-control"
                           value="<?php echo !empty($application['other_familystatus']) ? htmlspecialchars($application['other_familystatus']) : ''; ?>" 
                           disabled>
                </div>
            </div>
        </div>
    </div>
</div>
<!--จบ สภาพความเป็นอยู่ผู้ขอทุน  -->

        <h4 class="section-header mt-4">ที่ดินและที่อยู่อาศัยของบิดามารดา</h4>
        <div class="mt-3">
        <div class="px-0">
    <?php 
        $landOptions = [
            'มีที่ดินสำหรับประกอบอาชีพเป็นของตนเอง' => 'ownfarm',
            'เช่าที่ดินผู้อื่น' => 'otherfarm',
            'อาศัยผู้อื่น' => 'liveothers_land',
            'เช่าบ้านอยู่' => 'renthouse_monthly_land'
        ];
        $selectedLandStatus = !empty($application['parents_landstatus']) ? $application['parents_landstatus'] : '';
    ?>
    
    <?php foreach ($landOptions as $option => $inputKey): ?>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="parents_landstatus" disabled <?php echo ($selectedLandStatus == $option) ? 'checked' : ''; ?>>
                        <label class="form-check-label"> <?php echo htmlspecialchars($option); ?> </label>
                    </div>
                    
                    <?php if ($option != 'เช่าบ้านอยู่' && !empty($application[$inputKey])): ?>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" class="form-control" value="<?php echo htmlspecialchars($application[$inputKey]); ?>" style="width: 120px;" disabled>
                            <span>ไร่</span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($option == 'เช่าที่ดินผู้อื่น'): ?>
                        <?php if (!empty($application['monthly_rent_land'])): ?>
                            <div class="d-flex align-items-center gap-2">
                                <span>ค่าเช่าเดือนละ</span>
                                <input type="number" class="form-control" value="<?php echo htmlspecialchars($application['monthly_rent_land']); ?>" style="width: 120px;" disabled>
                                <span>บาท</span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($application['peryear_rent_land'])): ?>
                            <div class="d-flex align-items-center gap-2">
                                <span>หรือปีละ</span>
                                <input type="number" class="form-control" value="<?php echo htmlspecialchars($application['peryear_rent_land']); ?>" style="width: 120px;" disabled>
                                <span>บาท</span>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if ($option == 'เช่าบ้านอยู่'): ?>
                        <?php if (!empty($application['renthouse_monthly_land'])): ?>
                            <div class="d-flex align-items-center gap-2">
                                <span>ค่าเช่าเดือนละ</span>
                                <input type="number" class="form-control" value="<?php echo htmlspecialchars($application['renthouse_monthly_land']); ?>" style="width: 120px;" disabled>
                                <span>บาท</span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($application['renthouse_peryear_land'])): ?>
                            <div class="d-flex align-items-center gap-2">
                                <span>หรือปีละ</span>
                                <input type="number" class="form-control" value="<?php echo htmlspecialchars($application['renthouse_peryear_land']); ?>" style="width: 120px;" disabled>
                                <span>บาท</span>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if ($option == 'อาศัยผู้อื่น'): ?>
                        <div class="flex-grow-1">
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($application['liveothers_land'] ?? ''); ?>" placeholder="ระบุรายละเอียด" disabled>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</div>
<!--  -->
        <h4 class="section-header mt-4">ผู้อุปการะอื่นนอกจากบิดา/มารดา</h4>
        <div class="mt-3">
            <div class="px-0">
                <!-- Guardian Existence -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="col-12">
                        <div class="mb-3">
    <?php 
        $selectedGuardian = !empty($application['hasGuardian']) ? $application['hasGuardian'] : ''; 
        $guardianOptions = ['มี', 'ไม่มี'];
    ?>
    
    <div class="d-flex flex-row gap-4">
        <?php foreach ($guardianOptions as $option): ?>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="hasGuardian" id="guardian_<?php echo $option; ?>" 
                       value="<?php echo $option; ?>" 
                       <?php echo ($selectedGuardian == $option) ? 'checked' : ''; ?> 
                       disabled>
                <label class="form-check-label" for="guardian_<?php echo $option; ?>">
                    <?php echo $option; ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
</div>
                        </div>                    
                    </div>
                </div>
                <!-- Guardian Personal Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">ชื่อ – นามสกุล ผู้อุปการะ</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_fullname']) ? htmlspecialchars($application['guardian_fullname']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">อายุ (ปี)</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_age']) ? htmlspecialchars($application['guardian_age']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">มีความเกี่ยวข้องเป็น</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_relevant']) ? htmlspecialchars($application['guardian_relevant']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <!-- Address -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">บ้านเลขที่</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_house']) ? htmlspecialchars($application['guardian_house']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ตรอก/ซอย</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_ally']) ? htmlspecialchars($application['guardian_ally']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">หมู่ที่</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_moo']) ? htmlspecialchars($application['guardian_moo']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ถนน</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_road']) ? htmlspecialchars($application['guardian_road']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">ตำบล/แขวง</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_subdistrict']) ? htmlspecialchars($application['guardian_subdistrict']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">อำเภอ/เขต</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_district']) ? htmlspecialchars($application['guardian_district']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">จังหวัด</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_province']) ? htmlspecialchars($application['guardian_province']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">รหัสไปรษณีย์</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_postcode']) ? htmlspecialchars($application['guardian_postcode']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <!-- Contact Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">โทรศัพท์บ้าน</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_house_no']) ? htmlspecialchars($application['guardian_house_no']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">โทรศัพท์มือถือ</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_phone']) ? htmlspecialchars($application['guardian_phone']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <!-- Marital Status and Children -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">สถานภาพ</label>
                        <div class="d-flex gap-4">
    <?php 
        $selectedGuardianstatus = !empty($application['guardian_status']) ? $application['guardian_status'] : ''; 
        $guardianStatusOptions = ['โสด', 'สมรส'];
    ?>
    
    <?php foreach ($guardianStatusOptions as $option): ?>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="guardian_status" id="guardian_status_<?php echo $option; ?>" 
                   value="<?php echo $option; ?>" 
                   <?php echo ($selectedGuardianstatus == $option) ? 'checked' : ''; ?> 
                   disabled>
            <label class="form-check-label" for="guardian_status_<?php echo $option; ?>">
                <?php echo $option; ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">มีบุตร (คน)</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_children']) ? htmlspecialchars($application['guardian_children']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">กำลังศึกษา (คน)</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_children_studying']) ? htmlspecialchars($application['guardian_children_studying']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ประกอบอาชีพ (คน)</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_children_occupation']) ? htmlspecialchars($application['guardian_children_occupation']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <!-- Occupation Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">อาชีพผู้อุปการะ</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_occupation']) ? htmlspecialchars($application['guardian_occupation']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">รายได้เดือนละ (บาท)</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_monthly_income']) ? htmlspecialchars($application['guardian_monthly_income']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">ตำแหน่ง/ยศ</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_rank']) ? htmlspecialchars($application['guardian_rank']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ลักษณะงาน</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_job_description']) ? htmlspecialchars($application['guardian_job_description']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">สถานที่ทำงานของผู้อุปการะ</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_workplace']) ? htmlspecialchars($application['guardian_workplace']) : '-'; ?>
                            </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">โทรศัพท์</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['guardian_telephone']) ? htmlspecialchars($application['guardian_telephone']) : '-'; ?>
                            </div>
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
                            <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['sibling_amount']) ? htmlspecialchars($application['sibling_amount']) : '-'; ?>
                            </div>
                            <span>คน</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-center">
                            <span>และผู้ขอทุนเป็นบุตรคนที่</span>
                            <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['sibling_child_amount']) ? htmlspecialchars($application['sibling_child_amount']) : '-'; ?>
                            </div>
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
                                <th>รายได้ ต่อเดือน</th>
                                <th>สถานภาพ สมรส</th>
                                <th>จำนวน บุตร</th>
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
                                <td><?php echo !empty($application['sibling_fullname_one']) ? htmlspecialchars($application['sibling_fullname_one']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_age_one']) ? htmlspecialchars($application['sibling_age_one']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_education_one']) ? htmlspecialchars($application['sibling_education_one']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_grade_level_one']) ? htmlspecialchars($application['sibling_grade_level_one']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_occupation_one']) ? htmlspecialchars($application['sibling_occupation_one']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_monthly_income_one']) ? htmlspecialchars($application['sibling_monthly_income_one']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_status_one']) ? htmlspecialchars($application['sibling_status_one']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_children_amount_one']) ? htmlspecialchars($application['sibling_children_amount_one']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><?php echo !empty($application['sibling_fullname_two']) ? htmlspecialchars($application['sibling_fullname_two']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_age_two']) ? htmlspecialchars($application['sibling_age_two']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_education_two']) ? htmlspecialchars($application['sibling_education_two']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_grade_level_two']) ? htmlspecialchars($application['sibling_grade_level_two']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_occupation_two']) ? htmlspecialchars($application['sibling_occupation_two']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_monthly_income_two']) ? htmlspecialchars($application['sibling_monthly_income_two']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_status_two']) ? htmlspecialchars($application['sibling_status_two']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_children_amount_two']) ? htmlspecialchars($application['sibling_children_amount_two']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><?php echo !empty($application['sibling_fullname_three']) ? htmlspecialchars($application['sibling_fullname_three']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_age_three']) ? htmlspecialchars($application['sibling_age_three']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_education_three']) ? htmlspecialchars($application['sibling_education_three']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_grade_level_three']) ? htmlspecialchars($application['sibling_grade_level_three']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_occupation_three']) ? htmlspecialchars($application['sibling_occupation_three']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_monthly_income_three']) ? htmlspecialchars($application['sibling_monthly_income_three']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_status_three']) ? htmlspecialchars($application['sibling_status_three']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_children_amount_three']) ? htmlspecialchars($application['sibling_children_amount_three']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><?php echo !empty($application['sibling_fullname_four']) ? htmlspecialchars($application['sibling_fullname_four']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_age_four']) ? htmlspecialchars($application['sibling_age_four']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_education_four']) ? htmlspecialchars($application['sibling_education_four']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_grade_level_four']) ? htmlspecialchars($application['sibling_grade_level_four']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_occupation_four']) ? htmlspecialchars($application['sibling_occupation_four']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_monthly_income_four']) ? htmlspecialchars($application['sibling_monthly_income_four']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_status_four']) ? htmlspecialchars($application['sibling_status_four']) : '-'; ?></td>
                                <td><?php echo !empty($application['sibling_children_amount_four']) ? htmlspecialchars($application['sibling_children_amount_four']) : '-'; ?></td>
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
                            <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['sibling_currently_children']) ? htmlspecialchars($application['sibling_currently_children']) : '-'; ?>
                            </div>
                            <span>คน</span>
                        </div>
                    </div>
                </div>
                <!-- Financial Problems -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">ครอบครัวประสบปัญหาขาดแคลนเงินอย่างไร</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['sibling_financial_problems']) ? htmlspecialchars($application['sibling_financial_problems']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <!-- Solutions -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">และแก้ไขปัญหาโดยวิธีการใดเมื่อขาดเงิน</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['sibling_solutions']) ? htmlspecialchars($application['sibling_solutions']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <!-- Scholarship Necessity -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">ความจำเป็นที่ต้องขอรับทุนการศึกษา</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['sibling_scholarship_necessity']) ? htmlspecialchars($application['sibling_scholarship_necessity']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <!-- Health Problems -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">ประสบปัญหาอื่นๆ ปัญหาด้านสุขภาพ – โรคประจำตัว</label>
                        <div class="d-flex gap-4">
    <?php 
        $selectedhealthIssue = !empty($application['healthIssue']) ? $application['healthIssue'] : ''; 
        $healthIssueOptions = ['มี', 'ไม่มี'];
    ?>
    
    <?php foreach ($healthIssueOptions as $option): ?>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="healthIssue" id="healthIssue_<?php echo $option; ?>" 
                   value="<?php echo $option; ?>" 
                   <?php echo ($selectedhealthIssue == $option) ? 'checked' : ''; ?> 
                   disabled>
            <label class="form-check-label" for="healthIssue_<?php echo $option; ?>">
                <?php echo $option; ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>
                        <div class="detail-value border rounded p-2 bg-light">
                            <?php echo !empty($application['healthIssueDescription']) ? htmlspecialchars($application['healthIssueDescription']) : '-'; ?>
                        </div>    
                    </div>
                </div>
                <!-- Study Problems -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">ปัญหาด้านอื่นๆ ที่เป็นอุปสรรคต่อการเรียน</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['studyProblems']) ? htmlspecialchars($application['studyProblems']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <!-- Family Problems -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">ปัญหาครอบครัว</label>
                        <div class="detail-value border rounded p-2 bg-light">
                                <?php echo !empty($application['familyProblems']) ? htmlspecialchars($application['familyProblems']) : '-'; ?>
                            </div>
                    </div>
                </div>
                <!-- Part-time Job -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">งานพิเศษที่ทำอยู่</label>
                        <div class="detail-value border rounded p-2 bg-light">
                            <?php echo !empty($application['parttime_job']) ? htmlspecialchars($application['parttime_job']) : '-'; ?>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">รายได้</label>
                        <div class="input-group" style="display: flex; align-items: center;">
    <div class="border rounded p-2 bg-light flex-grow-1" style="min-width: 100px;">
        <?php echo !empty($application['parttime_income']) ? htmlspecialchars($application['parttime_income']) : '-'; ?>
    </div>
    <span class="input-group-text" style="white-space: nowrap;">บาท</span>
</div>

                    </div>
                    <div class="col-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="detail-value border rounded p-2 bg-light">
                            <?php echo !empty($application['parttime_income_period']) ? htmlspecialchars($application['parttime_income_period']) : '-'; ?>
                        </div>
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
            <div class="detail-value border rounded p-2 bg-light">
                            <?php echo !empty($application['special_abilities']) ? htmlspecialchars($application['special_abilities']) : '-'; ?>
                        </div>
        </div>
    </div>
    <!-- กิจกรรมที่เคยทำในสถานศึกษา -->
    <div class="col-12">
        <h5 class="mb-3">กิจกรรมที่เคยทำในสถานศึกษา</h5>
        <div class="form-group mb-3">
        <div class="input-group" style="display: flex; align-items: center;">
    <span class="input-group-text" style="white-space: nowrap;">1.</span>
    <div class="border rounded p-2 bg-light flex-grow-1" style="min-width: 100px;">
        <?php echo !empty($application['special_activities']) ? htmlspecialchars($application['special_activities']) : '-'; ?>
    </div>
</div>
        </div>
        <div class="form-group mb-3">
        <div class="input-group" style="display: flex; align-items: center;">
    <span class="input-group-text" style="white-space: nowrap;">2.</span>
    <div class="border rounded p-2 bg-light flex-grow-1" style="min-width: 100px;">
        <?php echo !empty($application['special_activities1']) ? htmlspecialchars($application['special_activities1']) : '-'; ?>
    </div>
</div>
        </div>
        <div class="form-group mb-3">
        <div class="input-group" style="display: flex; align-items: center;">
    <span class="input-group-text" style="white-space: nowrap;">3.</span>
    <div class="border rounded p-2 bg-light flex-grow-1" style="min-width: 100px;">
        <?php echo !empty($application['special_activities2']) ? htmlspecialchars($application['special_activities2']) : '-'; ?>
    </div>
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
                <div class="input-group" style="display: flex; align-items: center;">
    <span class="input-group-text" style="white-space: nowrap;">1</span>
    <div class="border rounded p-2 bg-light flex-grow-1" style="min-width: 100px;">
        <?php echo !empty($application['awards']) ? htmlspecialchars($application['awards']) : '-'; ?>
    </div>
</div>
                </div>
                <div class="col-md-3">
                <div class="input-group" style="display: flex; align-items: center;">
    <span class="input-group-text" style="white-space: nowrap;">ปี พ.ศ.</span>
    <div class="border rounded p-2 bg-light flex-grow-1" style="min-width: 100px;">
        <?php echo !empty($application['awards_year']) ? htmlspecialchars($application['awards_year']) : '-'; ?>
    </div>
</div>

                </div>
            </div>
        </div>
        <div class="form-group mb-3">
            <div class="row">
                <div class="col-md-9">
                <div class="input-group" style="display: flex; align-items: center;">
    <span class="input-group-text" style="white-space: nowrap;">2</span>
    <div class="border rounded p-2 bg-light flex-grow-1" style="min-width: 100px;">
        <?php echo !empty($application['awards1']) ? htmlspecialchars($application['awards1']) : '-'; ?>
    </div>
</div>
                </div>
                <div class="col-md-3">
                <div class="input-group" style="display: flex; align-items: center;">
    <span class="input-group-text" style="white-space: nowrap;">ปี พ.ศ.</span>
    <div class="border rounded p-2 bg-light flex-grow-1" style="min-width: 100px;">
        <?php echo !empty($application['awards_year1']) ? htmlspecialchars($application['awards_year1']) : '-'; ?>
    </div>
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
            <div class="detail-value border rounded p-2 bg-light">
                <?php echo !empty($application['future_goals']) ? htmlspecialchars($application['future_goals']) : '-'; ?>
            </div>
        </div>
    </div>
    <!-- บุคคลใกล้ชิดที่สามารถติดต่อได้ -->
    <div class="col-12 mb-4">
        <h5 class="mb-3">บุคคลใกล้ชิดที่สามารถติดต่อได้กรณีเร่งด่วน</h5>
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label">ชื่อ - สกุล</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_name']) ? htmlspecialchars($application['emergency_contact_name']) : '-'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">มีความเกี่ยวข้องเป็น</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_relevant']) ? htmlspecialchars($application['emergency_contact_relevant']) : '-'; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- ที่อยู่ -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">บ้านเลขที่</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_house']) ? htmlspecialchars($application['emergency_contact_house']) : '-'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">ตรอก/ซอย</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_ally']) ? htmlspecialchars($application['emergency_contact_ally']) : '-'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">หมู่ที่</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_moo']) ? htmlspecialchars($application['emergency_contact_moo']) : '-'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">ถนน</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_road']) ? htmlspecialchars($application['emergency_contact_road']) : '-'; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">ตำบล/แขวง</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_subdistrict']) ? htmlspecialchars($application['emergency_contact_subdistrict']) : '-'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">อำเภอ/เขต</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_district']) ? htmlspecialchars($application['emergency_contact_district']) : '-'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">จังหวัด</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_province']) ? htmlspecialchars($application['emergency_contact_province']) : '-'; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">รหัสไปรษณีย์</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_postcode']) ? htmlspecialchars($application['emergency_contact_postcode']) : '-'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">โทรศัพท์บ้าน</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_house_no']) ? htmlspecialchars($application['emergency_contact_house_no']) : '-'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">โทรศัพท์มือถือ</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['emergency_contact_phone']) ? htmlspecialchars($application['emergency_contact_phone']) : '-'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- จำนวนเงินทุน -->
    <div class="col-12 mb-4">
        <h5 class="mb-3">จำนวนเงินทุนที่ต้องการ</h5>
        <div class="form-group mb-3">
            <label class="form-label">หากมหาวิทยาลัยพิจารณาให้ทุนการศึกษานักศึกษาเห็นว่าจำนวนเงินที่เหมาะสม คือ</label>
            <div class="form-check form-check-inline">
            <?php 
                // ค่าที่ถูกเลือกจากฐานข้อมูล
                $selectedscholarshiprequired = !empty($application['scholarship_required']) ? $application['scholarship_required'] : ''; 
                
                // ตัวเลือกทั้งหมด
                $scholarshipOptions = ['3,000 บาท', '4,000 บาท', '5,000 บาท'];
            ?>

            <?php foreach ($scholarshipOptions as $option): ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="scholarship" id="scholarship_<?php echo $option; ?>" 
                        value="<?php echo $option; ?>" 
                        <?php echo ($selectedscholarshiprequired == $option) ? 'checked' : ''; ?> 
                        disabled>
                    <label class="form-check-label" for="scholarship_<?php echo $option; ?>">
                        <?php echo $option; ?>
                    </label>
                </div>
            <?php endforeach; ?>
                    </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">นักศึกษาจะนำเงินที่ได้รับไปใช้จ่ายเป็นค่าอะไรบ้าง (ระบุรายละเอียด)</label>
            <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['scholarship_amount_description']) ? htmlspecialchars($application['scholarship_amount_description']) : '-'; ?>
                    </div>
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
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['signature_scholarship']) ? htmlspecialchars($application['signature_scholarship']) : '-'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">ชื่อ-นามสกุล (ตัวบรรจง)</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['signature_name']) ? htmlspecialchars($application['signature_name']) : '-'; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- วันที่ -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">วันที่</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['signature_date']) ? htmlspecialchars($application['signature_date']) : '-'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">เดือน</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['signature_month']) ? htmlspecialchars($application['signature_month']) : '-'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">พ.ศ.</label>
                    <div class="detail-value border rounded p-2 bg-light">
                        <?php echo !empty($application['signature_year']) ? htmlspecialchars($application['signature_year']) : '-'; ?>
                    </div>
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
            <textarea 
                class="form-control border rounded" 
                rows="25" 
                readonly><?php echo !empty($application['describe_scholarship']) ? htmlspecialchars($application['describe_scholarship']) : '-'; ?>
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
                            <form>
                                <div class="mb-4">
                                <!-- Preview Area -->
                                    <div class="border rounded p-3 bg-light" style="min-height: 200px;">
                                        <div id="fileUpload1" class="d-flex align-items-center justify-content-between">
                                            <?php if (isset($application['fileUpload1']) && !empty($application['fileUpload1'])): ?>
                                                <!-- ชื่อไฟล์อยู่ฝั่งซ้ายและกึ่งกลาง -->
                                                <div class="text-start pe-3 d-flex align-items-center" style="flex-grow: 1;">
                                                    <div class="mt-2 text-muted">
                                                        <strong>ชื่อไฟล์:</strong>
                                                        <?php echo htmlspecialchars($application['fileUpload1']); ?>
                                                    </div>
                                                </div>
                                                <!-- รูปภาพอยู่ฝั่งขวา -->
                                                <div class="text-end">
                                                    <img src="uploads/<?php echo htmlspecialchars($application['fileUpload1']); ?>" 
                                                        alt="Preview" 
                                                        class="img-fluid img-thumbnail" 
                                                        style="max-height: 300px; width: auto; object-fit: contain; border-radius: 5px;">
                                                </div>
                                            <?php else: ?>
                                                <!-- กรณีไม่มีไฟล์ -->
                                                <div class="alert alert-danger w-100 text-center">ไม่มีไฟล์</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="landmarks" class="form-label">จุดสังเกตที่สำคัญ</label>
                                        <textarea class="form-control" id="landmarks" rows="3" placeholder="ระบุจุดสังเกตที่สำคัญ" readonly>
                                           <?php echo !empty($application['landmarks']) ? htmlspecialchars($application['landmarks']) : '-'; ?>
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="directions" class="form-label">คำอธิบายเส้นทาง</label>
                                        <textarea class="form-control" id="directions" rows="3" placeholder="อธิบายเส้นทางการเดินทาง" readonly>
                                            <?php echo !empty($application['directions']) ? htmlspecialchars($application['directions']) : '-'; ?>
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
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
                        <form>
                            <div class="mb-4">
                                <!-- Preview Area -->
                                <div class="border rounded p-3 bg-light" style="min-height: 200px;">
                                    <div id="fileUpload2" class="d-flex align-items-center justify-content-between">
                                        <?php if (isset($application['fileUpload2']) && !empty($application['fileUpload2'])): ?>
                                            <!-- ชื่อไฟล์อยู่ฝั่งซ้ายและกึ่งกลาง -->
                                            <div class="text-start pe-3 d-flex align-items-center" style="flex-grow: 1;">
                                                <div class="mt-2 text-muted">
                                                    <strong>ชื่อไฟล์:</strong>
                                                    <?php echo htmlspecialchars($application['fileUpload2']); ?>
                                                </div>
                                            </div>

                                            <!-- รูปภาพอยู่ฝั่งขวา -->
                                            <div class="text-end">
                                                <img src="uploads/<?php echo htmlspecialchars($application['fileUpload2']); ?>" 
                                                    alt="Preview" 
                                                    class="img-fluid img-thumbnail" 
                                                    style="max-height: 300px; width: auto; object-fit: contain; border-radius: 5px;">
                                            </div>
                                        <?php else: ?>
                                            <!-- กรณีไม่มีไฟล์ -->
                                            <div class="alert w-100 text-center align-item-center">ไม่มีไฟล์</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
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
                        <form>
                            <div class="mb-4">
                                <!-- Preview Area -->
                                <div class="border rounded p-3 bg-light" style="min-height: 200px;">
                                    <div id="fileUpload3" class="d-flex align-items-center justify-content-between">
                                        <?php if (isset($application['fileUpload3']) && !empty($application['fileUpload3'])): ?>
                                            <!-- ชื่อไฟล์อยู่ฝั่งซ้ายและกึ่งกลาง -->
                                            <div class="text-start pe-3 d-flex align-items-center" style="flex-grow: 1;">
                                                <div class="mt-2 text-muted">
                                                    <strong>ชื่อไฟล์:</strong>
                                                    <?php echo htmlspecialchars($application['fileUpload3']); ?>
                                                </div>
                                            </div>

                                            <!-- รูปภาพอยู่ฝั่งขวา -->
                                            <div class="text-end">
                                                <img src="uploads/<?php echo htmlspecialchars($application['fileUpload3']); ?>" 
                                                    alt="Preview" 
                                                    class="img-fluid img-thumbnail" 
                                                    style="max-height: 300px; width: auto; object-fit: contain; border-radius: 5px;">
                                            </div>
                                        <?php else: ?>
                                            <!-- กรณีไม่มีไฟล์ -->
                                            <div class="alert w-100 text-center align-item-center">ไม่มีไฟล์</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
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
                        <form>
                            <div class="mb-4">
                                <!-- Preview Area -->
                                <div class="border rounded p-3 bg-light" style="min-height: 200px;">
                                    <div id="fileUpload4" class="d-flex align-items-center justify-content-between">
                                        <?php if (isset($application['fileUpload4']) && !empty($application['fileUpload4'])): ?>
                                            <!-- ชื่อไฟล์อยู่ฝั่งซ้ายและกึ่งกลาง -->
                                            <div class="text-start pe-3 d-flex align-items-center" style="flex-grow: 1;">
                                                <div class="mt-2 text-muted">
                                                    <strong>ชื่อไฟล์:</strong>
                                                    <?php echo htmlspecialchars($application['fileUpload4']); ?>
                                                </div>
                                            </div>

                                            <!-- รูปภาพอยู่ฝั่งขวา -->
                                            <div class="text-end">
                                                <img src="uploads/<?php echo htmlspecialchars($application['fileUpload4']); ?>" 
                                                    alt="Preview" 
                                                    class="img-fluid img-thumbnail" 
                                                    style="max-height: 300px; width: auto; object-fit: contain; border-radius: 5px;">
                                            </div>
                                        <?php else: ?>
                                            <!-- กรณีไม่มีไฟล์ -->
                                            <div class="alert w-100 text-center align-item-center">ไม่มีไฟล์</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-center">
            <input type="hidden" name="scholarship_id" value="<?php echo htmlspecialchars($application['scholarship_id']); ?>">
    <input type="hidden" name="applicant_id" value="<?php echo htmlspecialchars($application['id']); ?>">
    <button type="submit" class="btn btn-primary">Print</button>
    <button type="button" class="btn btn-primary" onclick="openPDF1()">pdf1</button></form>
            </div>
        </div>
  </section>
    </div>
    <!-- <?php else: ?>
    <div class="alert alert-danger">
        ไม่พบข้อมูลผู้สมัคร
    </div>
<?php endif; ?> -->
    <?php include "../comp/footer.php"; ?>
</div>
<!-- โหลด Moment.js ก่อน -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<!-- โหลด Tempus Dominus Bootstrap 4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/js/tempusdominus-bootstrap-4.min.js" crossorigin="anonymous"></script>

<!-- โหลด jQuery ก่อน -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- โหลด jQuery UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<!-- โหลด Tempus Dominus (ถ้าจำเป็น) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/js/tempusdominus-bootstrap-4.min.js" crossorigin="anonymous"></script>

<!-- จากนั้นใช้ .datepicker() -->
<script>
    $(document).ready(function() {
        $("#your-datepicker").datepicker();
    });
</script>

    <!-- Script สำหรับใช้งาน Datepicker -->
    <script>
        $(document).ready(function(){
            $('#datepicker').datepicker({
                format: 'mm/dd/yyyy',
                startDate: '-3d'
            });
        });
    </script>
    <!--  -->

    <!-- เปิด mpdf -->
    <script>
function confirmSubmission() {
    var scholarshipIdInput = document.querySelector('input[name="scholarship_id"]');
    var applicantIdInput = document.querySelector('input[name="applicant_id"]');

    var scholarshipId = scholarshipIdInput ? scholarshipIdInput.value.trim() : null;
    var applicantId = applicantIdInput ? applicantIdInput.value.trim() : null;

    if (!scholarshipId || scholarshipId === 'ไม่พบ ID') {
        alert('กรุณาระบุ scholarship_id ที่ถูกต้อง');
        return false;
    }

    if (!applicantId || applicantId === 'ไม่พบ ID') {
        alert('กรุณาระบุ applicant_id ที่ถูกต้อง');
        return false;
    }

    // ส่งฟอร์ม
    document.getElementById('scholarshipForm').submit();
}
</script>

<!-- ทดลอง pdf1 -->
<script>
function openPDF1() {
    var scholarshipId = document.querySelector('input[name="scholarship_id"]').value.trim();
    var applicantId = document.querySelector('input[name="applicant_id"]').value.trim();
    
    if (!scholarshipId || !applicantId) {
        alert('กรุณาระบุ scholarship_id และ applicant_id');
        return;
    }

    window.location.href = 'scholarship_pdf1.php?scholarship_id=' + encodeURIComponent(scholarshipId) + '&applicant_id=' + encodeURIComponent(applicantId);
}
</script>
</body>
</html>