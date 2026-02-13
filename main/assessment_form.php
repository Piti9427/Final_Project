<?php
session_start();
include "../users/checklogin.php";
include "../config_loader.php"; 
include "../comp/aside.php";

// ✅ รับค่า scholarship_id จาก URL
$scholarship_id = filter_input(INPUT_GET, 'scholarship_id', FILTER_VALIDATE_INT);

if (!$scholarship_id || $scholarship_id <= 0) {
    echo "<p class='text-danger'>⚠️ scholarship_id ไม่ถูกต้อง</p>";
    exit();
}

try {
    // ✅ เชื่อมต่อฐานข้อมูล (ใช้ connection จาก config_loader.php)
    $sql = "SELECT id AS application_primary_id, 
                   parent_allowance_amount, other_allowance_amount, 
                   loan_amount, extra_income_daily, food_expense_daily, accommodation_expense, 
                   transportation_expense_daily, other_expense_amount, 
                   scholarship_amount, scholarship_term_amount, scholarship_cost_living, 
                   historycholarship_status, living_conditions_grantees, guardian_monthly_income, 
                   landstatus, landstatus1, landstatus2, landstatus3, landstatus4, landstatus5, landstatus6,
                   sibling_currently_children, describe_scholarship
            FROM scholarship_applications
            WHERE scholarship_id = ?
            ORDER BY id DESC
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $scholarship_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // ✅ กำหนดค่าที่ต้องการ
        $application_primary_id = $row['application_primary_id'] ?? 0;
        $parent_allowance = floatval($row['parent_allowance_amount'] ?? 0);
        $other_allowance = floatval($row['other_allowance_amount'] ?? 0);
        $loan_amount = floatval($row['loan_amount'] ?? 0);
        $extra_income_daily = floatval($row['extra_income_daily'] ?? 0);
        $food_expense_daily = floatval($row['food_expense_daily'] ?? 0);
        $accommodation_expense = floatval($row['accommodation_expense'] ?? 0);
        $transportation_expense_daily = floatval($row['transportation_expense_daily'] ?? 0);
        $other_expense_amount = floatval($row['other_expense_amount'] ?? 0);
        $scholarship_amount = floatval($row['scholarship_amount'] ?? 0);
        $scholarship_term_amount = floatval($row['scholarship_term_amount'] ?? 0);
        $scholarship_cost_living = floatval($row['scholarship_cost_living'] ?? 0);
        $historycholarship_status = trim($row['historycholarship_status'] ?? '');

        // ✅ รวมค่าจากทุก landstatus
        // ✅ ตรวจสอบค่าทุก landstatus และใช้ค่าที่มีล่าสุด
        $landStatuses = array_map(fn($val) => trim($val ?? ''), [
            $row['landstatus'] ?? '',
            $row['landstatus1'] ?? '',
            $row['landstatus2'] ?? '',
            $row['landstatus3'] ?? '',
            $row['landstatus4'] ?? '',
            $row['landstatus5'] ?? '',
            $row['landstatus6'] ?? ''
        ]);

        $landStatuses = array_filter($landStatuses, fn($val) => $val !== ''); // ✅ เอาค่าที่ไม่ใช่ช่องว่าง
        $selectedLandStatus = !empty($landStatuses) ? end($landStatuses) : ''; // ✅ เอาค่าล่าสุด
        
        // ✅ ตรวจสอบค่า landstatus ที่ถูกต้อง
        var_dump($selectedLandStatus); 
        
        // ✅ ใช้ค่า historycholarship_status ถ้ามีข้อมูล
        $selectedStatus = (!empty($historycholarship_status)) ? $historycholarship_status : '';

    } else {
        echo "<p class='text-danger'>⚠️ ไม่พบข้อมูลสำหรับ scholarship_id: $scholarship_id</p>";
        $application_primary_id = 0;
        $selectedStatus = ''; // ✅ ถ้าไม่มีข้อมูล ให้เป็นค่าว่าง
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo "<p class='text-danger'>❌ เกิดข้อผิดพลาด กรุณาลองใหม่ภายหลัง</p>";
    $selectedStatus = ''; // ✅ ป้องกันการเกิด error ถ้า SQL ล้มเหลว
}

// ✅ คำนวณผลรวม (💡 เอากลับมาให้แล้ว!)
$total_income = array_sum([$parent_allowance, $other_allowance, $loan_amount, $extra_income_daily]);
$total_spend = array_sum([$food_expense_daily, $accommodation_expense, $transportation_expense_daily, $other_expense_amount]);

// ✅ ตัวเลือกที่ต้องแสดง
$scholarshipOptions = ['ต่อเนื่อง', 'เฉพาะปี', 'ไม่ผูกพัน', 'ผูกพัน']; 

// ✅ ตรวจสอบว่า `$selectedStatus` อยู่ใน `$scholarshipOptions` หรือไม่
if (!in_array($selectedStatus, $scholarshipOptions, true)) {
    $selectedStatus = ''; // ตั้งค่าเป็นค่าว่างถ้าไม่ตรงกับตัวเลือก
}
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
  <style>
    body {
      font-family: 'Sarabun', sans-serif;
    }
    .form-header {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    .submit-button {
      margin-top: 20px;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
<?php
    include "../comp/navbar.php";
?>


  <!-- Content Wrapper -->
  <div class="content-wrapper">
  <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Assessment</h1>
          </div>
          <div class="col-sm-6">
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header ">
          <form id="assessmentForm" action="save_assessment.php" method="POST" onsubmit="return confirmSubmission(event)">
          <div class="mb-4">
                    <div class="table-responsive">
                    <table class="table table-bordered">
                <thead>
                    <tr class="text-center">
                        <th style="width: 5%">ข้อ</th>
                        <th style="width: 45%">รายละเอียดข้อคำถาม</th>
                        <th style="width: 10%">คะแนน</th>
                        <th style="width: 40%">เกณฑ์พิจารณา</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <!-- ข้อ 1 -->
                    <tr>
                        <td rowspan="6" width="5%" class="text-center">1</td>
                        <td rowspan="6" width="45%">
                            <div>รายรับของนักศึกษาผู้ขอทุน (ประมาณการทั้งเดือน)</div>
                            <div class="mt-2">
                                ระบุ - เงินค่าครองชีพจากบิดา/มารดา/ผู้อุปการะ
                                <div class="mt-1">
                                    <div class="input-group">
                                        <span class="input-group-text">จำนวน</span>
                                            <div class="detail-value border rounded p-2 bg-light"><?= number_format($total_income) ?></div>
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td width="5%" class="text-center">5</td>
                        <td width="45%">รายรับรวมน้อยกว่าหรือเท่ากับ 5,000 บาท</td>
                    </tr>
                    <tr>
                        <td class="text-center">4</td>
                        <td>รายรับรวมอยู่ระหว่าง 5,001 - 10,000 บาท</td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td>รายรับรวมอยู่ระหว่าง 10,001 - 15,000 บาท</td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td>รายรับรวมอยู่ระหว่าง 15,001 - 20,000 บาท</td>
                    </tr>
                    <tr>
                        <td class="text-center">1</td>
                        <td>รายรับรวมมากกว่า 20,000 บาทขึ้นไป</td>
                    </tr>
                    <tr>
                        <td class="text-center">
                        <select class="form-control" name="income_score" id="income_score" required>
                            <option value="" selected disabled>เลือก</option>
                            <option value="5">5 คะแนน</option>
                            <option value="4">4 คะแนน</option>
                            <option value="3">3 คะแนน</option>
                            <option value="2">2 คะแนน</option>
                            <option value="1">1 คะแนน</option>
                        </select>
                        </td>
                        <td></td>
                    </tr>


                    <!-- ข้อ 2 -->
                    <tr>
                    <td rowspan="6" width="5%" class="text-center">2</td>
                    <td rowspan="6" width="45%">
                            <div>รายจ่ายของผู้ขอทุน (ประมาณการทั้งเดือน)</div>
                            <div class="mt-2">
                                <div class="row mb-2">
                                    <div class="col-4">ระบุ ค่าอาหาร</div>
                                    <div class="col-8">
                                        <div class="input-group">
                                            <div class="detail-value border rounded p-2 bg-light"><?= number_format($food_expense_daily) ?></div>
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4">ค่าที่พัก</div>
                                    <div class="col-8">
                                        <div class="input-group">
                                        <div class="detail-value border rounded p-2 bg-light"><?= number_format($accommodation_expense) ?></div>
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4">ค่าเดินทาง</div>
                                    <div class="col-8">
                                        <div class="input-group">
                                        
                                        <div class="detail-value border rounded p-2 bg-light"><?= number_format($transportation_expense_daily) ?></div>
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4">ค่าใช้จ่ายอื่นๆ</div>
                                    <div class="col-8">
                                        <div class="input-group">
                                        
                                        <div class="detail-value border rounded p-2 bg-light"><?= number_format($other_expense_amount) ?></div>
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-4">รวมรายจ่ายทั้งเดือน</div>
                                    <div class="col-8">
                                        <div class="input-group">
                                        <div class="detail-value border rounded p-2 bg-light"><?= number_format($total_spend) ?></div>
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td width="5%" class="text-center">5</td>
                        <td width="45%">รายจ่ายรวมทั้งเดือน มีมากกว่ารายรับรวมทั้งเดือน หรือบางครั้งต้องพึ่งพาเงินจากบุคคลอื่น</td>
                    </tr>
                    <tr>
                        <td class="text-center">4</class=>
                        <td>รายจ่ายรวมทั้งเดือน เท่ากับรับรวมทั้งเดือนต้องใช้เงินให้หมดเดือน</td>
                    </tr>
                    <tr>
                        <td class="text-center">3</class=>
                        <td>หักรายจ่ายรวมทั้งเดือน แล้วเหลือเงินไม่เกิน 500 บาทต่อเดือน</td>
                    </tr>
                    <tr>
                        <td class="text-center"">2</class=>
                        <td>หักรายจ่ายรวมทั้งเดือน แล้วเหลือเงิน 1,000 – 2,000 บาท</td>
                    </tr>
                    <tr>
                        <td class="text-center">1</class=>
                        <td>หักรายจ่ายรวมทั้งเดือน เหลือเงินมากกว่า 2,000 บาท</td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <select class="form-control" name="expense_score" id="expense_score" required>
                                <option value="" selected disabled>เลือก</option>
                                <option value="5">5 คะแนน</option>
                                <option value="4">4 คะแนน</option>
                                <option value="3">3 คะแนน</option>
                                <option value="2">2 คะแนน</option>
                                <option value="1">1 คะแนน</option>
                            </select>
                        </td>
                        
                    </tr>




                    <!-- ข้อ 3 -->
                    <tr>
                        <td rowspan="4" width="5%" class="text-center">3</td>
                        <td rowspan="4" width="45%">
                            <div>การกู้เงินจากกองทุนกู้ยืมเพื่อการศึกษา <b>กยศ หรือ กรอ.</b> ปีการศึกษาล่าสุดของนักศึกษาผู้ขอทุน ได้รับเงินกู้เป็นจำนวนเงินทั้งหมด</div>
                            <div class="mt-2">
                                <div class="input-group mb-2">
                                  <div class="detail-value border rounded p-2 bg-light"><?= number_format($scholarship_amount) ?></div>
                                    <span class="input-group-text">บาท</span>
                                </div>
                                <div>แบ่งเป็น</div>
                                <div class="row mb-2 mt-1">
                                    <div class="col-4">1.ค่าเทอม</div>
                                    <div class="col-8">
                                        <div class="input-group">
                                          <div class="detail-value border rounded p-2 bg-light"><?= number_format($scholarship_term_amount) ?></div>
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4">2.ค่าครองชีพรายเดือน</div>
                                    <div class="col-8">
                                        <div class="input-group">
                                          <div class="detail-value border rounded p-2 bg-light"><?= number_format($scholarship_cost_living) ?></div>
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">5</td>
                        <td >ไม่ได้กู้เงินจากกองทุนเพื่อการศึกษา (กยศ หรือ กรอ.)</td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td >ได้รับเงินกู้ยืมจากกองทุนเพื่อการศึกษา (กยศ หรือ กรอ.) เพียงบางส่วน เช่น <i>กู้เฉพาะค่าเทอมอย่างเดียว / กู้ค่าครองชีพรายเดือนอย่างเดียว</i></td>
                    </tr>
                    <tr>
                        <td class="text-center">1</td>
                        <td >ได้รับเงินกู้ยืมจากกองทุนเพื่อการศึกษา (กยศ หรือ กรอ.) <i>เต็มจำนวน เช่น กู้ทั้งค่าเทอมและค่าครองชีพ</i></td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <select class="form-control" name="loan_score" id="loan_score" required>
                                <option value="" selected disabled>เลือก</option>
                                <option value="5">5 คะแนน</option>
                                <option value="3">3 คะแนน</option>
                                <option value="1">1 คะแนน</option>
                            </select>
                        </td>
                    </tr>


                    <!-- ข้อ 4 -->
                    <tr>
                    <td rowspan="4" width="5%" class="text-center">4</td>
                    <td rowspan="4" width="45%">
                            <div>การรับทุนการศึกษาในปีการศึกษาที่ผ่านมานักศึกษาได้รับจากหน่วยงานใด</div>
                            <div class="mt-2">
                                <!-- <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="" id="faculty">
                                    <label class="form-check-label" for="faculty">คณะฯ</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="" id="university">
                                    <label class="form-check-label" for="university">มหาวิทยาลัยฯ</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="" id="external">
                                    <label class="form-check-label" for="external">หน่วยงานภายนอก</label>
                                </div> -->
                                <div class="d-flex gap-3">
    <?php 
        // ✅ กำหนดค่าให้ตรงกับค่าจากฐานข้อมูล
        $selectedStatus = isset($historycholarship_status) ? trim($historycholarship_status) : ''; 

        // ✅ ตัวเลือกที่ต้องแสดง
        $scholarshipOptions = ['เคยได้รับทุนการศึกษา', 'ไม่เคยได้รับทุนการศึกษา']; 
    ?>

    <?php foreach ($scholarshipOptions as $option): ?>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="historycholarship_status" 
                value="<?= htmlspecialchars($option) ?>" 
                <?= ($selectedStatus === trim($option)) ? 'checked' : ''; ?> 
                disabled>
            <label class="form-check-label">
                <?= htmlspecialchars($option); ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>
    
                            <div class="mt-2">
                                <div class="input-group mb-2">
                                    <!-- <input type="text" class="form-control"> -->
                                </div>
                            </div>    
                            </div>
                            <div class="mt-2">
                                <div class="input-group mb-2">
                                    <!-- <span class="input-group-text">- จำนวนเงินที่ได้รับ</span>
                                    <input type="number" class="form-control" placeholder="">
                                    <span class="input-group-text">บาท</span> -->
                                </div>
                            </div>
                            <div class="mt-2">
                            <div class="form-check form-check-inline">
    <?php 
        $landOptions = ['ต่อเนื่อง', 'เฉพาะปี', 'ไม่ผูกพัน', 'ผูกพัน']; // ตัวเลือกทั้งหมด
    ?>

    <?php foreach ($landOptions as $option): ?>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="landstatus" value="<?= htmlspecialchars($option) ?>"
                <?= (trim($selectedLandStatus) === trim($option)) ? 'checked' : ''; ?> disabled>
            <label class="form-check-label"><?= htmlspecialchars($option); ?></label>
        </div>
    <?php endforeach; ?>
</div>




                                </div>
                            </div>
                        </td>
                        <td class="text-center">5</td>
                        <td >ไม่เคยได้รับทุนการศึกษา</td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td >ได้รับทุนการศึกษาในรอบปีที่ผ่านมา</td>
                    </tr>
                    <tr>
                        <td class="text-center">1</td>
                        <td >ได้รับทุนการศึกษาแบบต่อเนื่อง</td>
                    </tr>
                    <tr></tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-center">
                            <select class="form-control" name="scholarship_score" id="scholarship_score" required>
                                <option value="" selected disabled>เลือก</option>
                                <option value="5">5 คะแนน</option>
                                <option value="3">3 คะแนน</option>
                                <option value="1">1 คะแนน</option>
                            </select>
                        </td>
                        
                    </tr>


                    <!-- ข้อ 5 -->
                    <tr>
                        <td rowspan="3" class="text-center">5</td>
                        <td rowspan="3">
                            <div>ผู้อุปการะที่ส่งเสียเลี้ยงดู</div>
                            <div class="mt-2">
    <div class="form-check">
        <?php 
            // ✅ ตัวเลือกที่ทำให้เลือก "ไม่มีผู้อุปการะ"
            $livingOptions = ["อยู่กับบิดามารดา", "อยู่กับบิดา", "อยู่กับมารดา", "อยู่หอพัก / วัด"];
            
            // ✅ ค่าที่ดึงจากฐานข้อมูล
            $selectedLivingCondition = trim($row['living_conditions_grantees'] ?? '');
            $guardianIncome = isset($row['guardian_monthly_income']) ? floatval($row['guardian_monthly_income']) : '';

            // ✅ ตรวจสอบค่าที่ดึงมา
            $isNoSupporterChecked = in_array($selectedLivingCondition, $livingOptions, true);
            $isHasSupporterChecked = ($selectedLivingCondition === "อยู่กับผู้อุปการะ");
        ?>

        <!-- ✅ ช่อง "ไม่มีผู้อุปการะ" -->
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="no-supporter" value="ไม่มีผู้อุปการะ"
                <?= $isNoSupporterChecked ? 'checked' : ''; ?> disabled>
            <label class="form-check-label" for="no-supporter">ไม่มีผู้อุปการะ</label>
        </div>
    </div>

    <!-- ✅ ช่อง "มีผู้อุปการะและมีรายได้ต่อเดือน" -->
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="has-supporter" value="มีผู้อุปการะ"
            <?= $isHasSupporterChecked ? 'checked' : ''; ?> disabled>
        <label class="form-check-label" for="has-supporter">มีผู้อุปการะและมีรายได้ต่อเดือน</label>

        <!-- ✅ ช่องกรอกรายได้ (ใช้ guardian_monthly_income) -->
        <div class="input-group mt-1" style="max-width: 250px;">
            <input type="number" class="form-control" placeholder="บาท"
                value="<?= $isHasSupporterChecked ? htmlspecialchars($guardianIncome) : ''; ?>" disabled>
            <span class="input-group-text">บาท</span>
        </div>
    </div>
</div>



                        </td>
                        <td class="text-center">5</td>
                        <td >ไม่มีผู้อุปการะ</td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td >มีผู้อุปการะ และมีรายได้ต่อเดือนไม่เกิน 15,000 บาท</td>
                    </tr>
                    <tr>
                        <td class="text-center">1</td>
                        <td >มีผู้อุปการะ แต่มีรายได้ต่อเดือนเกิน 15,000 บาท</td>
                    </tr>
                    <tr>
                    <td colspan="2"></td> <!-- รวม 2 ช่องเพื่อป้องกันเส้นขอบ -->
                    <td class="text-center">
                        <select class="form-control" name="guardian_score" id="guardian_score" required>
                            <option value="" selected disabled>เลือก</option>
                            <option value="5">5 คะแนน</option>
                            <option value="3">3 คะแนน</option>
                            <option value="1">1 คะแนน</option>
                        </select>
                    </td>
                </tr>


                    <!-- ข้อ 6 -->
                    <tr>
                        <td rowspan="5" class="text-center">6</td>
                        <td rowspan="5">
                            <div class="d-flex align-items-center">
                                <div class="me-2">จำนวนคนที่ผู้อุปการะเลี้ยงดูจำนวน</div>
                                <div class="input-group" style="max-width: 150px;">
                                <input type="number" class="form-control" name="sibling_currently_children" value="<?= htmlspecialchars($row['sibling_currently_children'] ?? '') ?>" readonly>
                                    <span class="input-group-text">คน</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">5</td>
                        <td >5 คนขึ้นไป หรือ ไม่มีผู้อุปการะ</td>
                    </tr>
                    <tr>
                        <td class="text-center">4</td>
                        <td >4 คน</td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td >3 คน</td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td >2 คน</td>
                    </tr>
                    <tr>
                        <td class="text-center">1</td>
                        <td >1 คน</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-center">
                            <select class="form-control" name="guardian_count_score" id="guardian_count_score" required>
                                <option value="" selected disabled>เลือก</option>
                                <option value="5">5 คะแนน</option>
                                <option value="4">4 คะแนน</option>
                                <option value="3">3 คะแนน</option>
                                <option value="2">2 คะแนน</option>
                                <option value="1">1 คะแนน</option>
                            </select>
                        </td>
                    </tr>


                    <!-- ข้อ 7 -->
                    <tr>
                        <td rowspan="5" class="text-center">7</td>
                        <td rowspan="5">
                            <div>เหตุผลและความจำเป็นในการขอทุน</div>
                            <div class="mt-2">
                                <textarea class="form-control" id="reason" name="reason" rows="4" required readonly><?= htmlspecialchars($row['describe_scholarship'] ?? '') ?></textarea>
                            </div>
                        </td>
                        <td class="text-center">5</td>
                        <td >พิจารณาจากดุลพินิจของคณะกรรมการผู้สัมภาษณ์</td>
                    </tr>
                    <tr>
                        <td class="text-center">4</td>
                        <td ></td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td ></td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td ></td>
                    </tr>
                    <tr>
                        <td class="text-center">1</td>
                        <td ></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-center">
                            <select class="form-control" name="reason_score" id="reason_score" required>
                                <option value="" selected disabled>เลือก</option>
                                <option value="5">5 คะแนน</option>
                                <option value="4">4 คะแนน</option>
                                <option value="3">3 คะแนน</option>
                                <option value="2">2 คะแนน</option>
                                <option value="1">1 คะแนน</option>
                            </select>
                        </td>
                    </tr>
                    <!--  -->
                </tbody>
            </table>
                </div>
                <!-- รวมคะแนน -->
            <div class="row mt-3 mb-4">
                <div class="col-8 text-end">
                    <strong>รวมคะแนนที่ได้</strong>
                </div>
                <div class="col-4">
                    <div class="input-group">
                    <input type="text" class="form-control" name="total_score" id="total_score" readonly>
                        <span class="input-group-text">คะแนน</span>
                    </div>
                </div>
            </div>

            <!-- สรุปผลการพิจารณา -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>สรุปผลการพิจารณาข้อคิดเห็นจากคณะกรรมการผู้สัมภาษณ์ทุน</strong>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>1.ให้ทุนการศึกษา โดยพิจารณาอยู่ในประเภทกลุ่มทุน (คณะกรรมการสามารถเลือกได้มากกว่า 1 ประเภท)</strong>
                        <div class="mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="type-1-1">
                                <label class="form-check-label" for="type-1-1">1.1 ทุนประเภทขาดแคลนทุนทรัพย์</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="type-1-2">
                                <label class="form-check-label" for="type-1-2">1.2 ทุนประเภทเรียนดี (นักศึกษามีผลการศึกษาเฉลี่ยสะสม ตั้งแต่ (GPAX) 3.5 ขึ้นไป)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="type-1-3">
                                <label class="form-check-label" for="type-1-3">1.3 ทุนประเภทนักศึกษาที่มีผลงานด้านกิจกรรมเด่น</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>2.มูลค่าทุนที่พิจารณา</strong>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" value="" id="amount-5000">
                                <label class="form-check-label" for="amount-5000">2.1 จำนวนเงิน 5,000 บาท</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" value="" id="amount-10000">
                                <label class="form-check-label" for="amount-10000">2.2 จำนวนเงิน 10,000 บาท</label>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="amount-more">
                                <label class="form-check-label" for="amount-more">2.3 จำนวนเงินมากกว่า 10,000 บาท ขึ้นไป เหตุผลประกอบเนื่องจาก</label>
                            </div>
                            <div class="mt-1">
                                <label for="fund_reason" class="form-label">โปรดระบุเหตุผล (กรณีเลือกมากกว่า 10,000 บาท)</label>
                                <input type="text" class="form-control" id="fund_reason" name="fund_reason">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>3.กรณีไม่พิจารณาให้ทุนเหตุผลประกอบเนื่องจาก</strong>
                        <div class="mt-1">
                            <textarea class="form-control" name="reject_reason" id="reject_reason" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <strong>บันทึกเพิ่มเติมของคณะกรรมการผู้สัมภาษณ์ทุน :</strong>
                        <textarea class="form-control" name="committee_note" id="committee_note" rows="2"></textarea>
                    </div>
                </div>
            </div>
      <div class="text-center submit-button">
      <input type="hidden" name="scholarship_id" value="<?php echo isset($_GET['scholarship_id']) ? htmlspecialchars($_GET['scholarship_id'], ENT_QUOTES, 'UTF-8') : ''; ?>">
      <input type="hidden" name="applications_id" value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8') : ''; ?>">


      <button type="submit" class="btn btn-primary">บันทึกการประเมิน</button>
                <button class="btn btn-secondary" type="reset">ล้างข้อมูล</button>
            </div>
    </form>
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
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- ✅ เพิ่ม SweetAlert2 -->  


<script>
function confirmSubmission(event) {
    var form = document.getElementById('assessmentForm');

    // ตรวจสอบ scholarship_id
    var scholarshipId = document.querySelector('input[name="scholarship_id"]').value.trim();
    if (!scholarshipId) {
        Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: 'กรุณาระบุ ID ทุนที่ถูกต้อง' });
        return false;
    }

    // ตรวจสอบคะแนนที่จำเป็นต้องเลือก
    var requiredSelects = ['income_score', 'expense_score', 'loan_score', 'scholarship_score', 'guardian_score', 'guardian_count_score'];
    for (var i = 0; i < requiredSelects.length; i++) {
        var select = document.getElementById(requiredSelects[i]);
        if (select && !select.value) {
            Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: 'กรุณาเลือกคะแนนให้ครบทุกช่อง' });
            return false;
        }
    }


    // ✅ Popup ยืนยันก่อนส่ง (ไม่มี Popup ยืนยันการสมัครทุนแล้ว)
    Swal.fire({
        title: 'ยืนยันการบันทึก',
        text: "คุณต้องการบันทึกข้อมูลใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, บันทึกเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'กำลังดำเนินการ...',
                text: 'ระบบกำลังบันทึกข้อมูลของคุณ',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 2000
            });

            setTimeout(() => {
                Swal.fire({
                    title: 'บันทึกสำเร็จ!',
                    text: 'ระบบได้บันทึกข้อมูลของคุณแล้ว',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    form.submit(); // ✅ ส่งฟอร์มจริง
                });
            }, 1000);
        }
    });

    return false;
}

</script>

  <!-- คำนวณคะแนนรวมอัตโนมัติ -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const scoreSelects = [
        document.getElementById('income_score'),
        document.getElementById('expense_score'),
        document.getElementById('loan_score'),
        document.getElementById('scholarship_score'),
        document.getElementById('guardian_score'),
        document.getElementById('guardian_count_score'),
        document.getElementById('reason_score')
      ];
      
      const totalScoreInput = document.getElementById('total_score');
      
      // คำนวณคะแนนรวมเมื่อมีการเปลี่ยนแปลงค่าในช่องคะแนน
      scoreSelects.forEach(select => {
        select.addEventListener('change', calculateTotalScore);
      });
      
      function calculateTotalScore() {
        let total = 0;
        scoreSelects.forEach(select => {
          if (select.value) {
            total += parseInt(select.value);
          }
        });
        
        totalScoreInput.value = total;
      }
    });
  </script>

<script>
$(document).ready(function () {
    $('#searchButton').on('click', function () {
        $('#searchForm').submit();
    });
});
</script>



</body>
</html>
