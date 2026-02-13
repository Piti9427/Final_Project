<?php
session_start();
include config_loader.php"; // เชื่อมต่อฐานข้อมูล

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // เปิด Debug Mode

try {
    // ตรวจสอบค่าที่ส่งมา
    if (!isset($_POST["scholarship_id"]) || empty($_POST["scholarship_id"])) {
        throw new Exception("ไม่พบรหัสทุนการศึกษา");
    }
    if (!isset($_POST["applications_id"]) || empty($_POST["applications_id"])) {
        throw new Exception("ไม่พบรหัสใบสมัคร");
    }

    // เชื่อมต่อฐานข้อมูล
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Log ข้อมูลที่ส่งมาเพื่อ Debug
    error_log("POST data: " . print_r($_POST, true));

    // ✅ ใช้ trim() แทน intval() สำหรับ VARCHAR
    $scholarship_id = trim($_POST['scholarship_id']);
    $applications_id = intval($_POST['applications_id']); // ✅ ตรงกับฐานข้อมูล

    // ดึงค่าคะแนนจากฟอร์ม
    $income_score = isset($_POST['income_score']) ? intval($_POST['income_score']) : 0;
    $expense_score = isset($_POST['expense_score']) ? intval($_POST['expense_score']) : 0;
    $loan_score = isset($_POST['loan_score']) ? intval($_POST['loan_score']) : 0;
    $scholarship_score = isset($_POST['scholarship_score']) ? intval($_POST['scholarship_score']) : 0;
    $guardian_score = isset($_POST['guardian_score']) ? intval($_POST['guardian_score']) : 0;
    $guardian_count_score = isset($_POST['guardian_count_score']) ? intval($_POST['guardian_count_score']) : 0;

    // รับค่าข้อความ
    $reason = !empty($_POST['reason']) ? trim($_POST['reason']) : NULL;
    $total_score = isset($_POST['total_score']) ? intval($_POST['total_score']) : 0;
    $fund_type = !empty($_POST['fund_type']) ? trim($_POST['fund_type']) : NULL;
    $fund_value = !empty($_POST['fund_value']) ? trim($_POST['fund_value']) : NULL;
    $fund_reason = !empty($_POST['fund_reason']) ? trim($_POST['fund_reason']) : NULL;
    $reject_reason = !empty($_POST['reject_reason']) ? trim($_POST['reject_reason']) : NULL;
    $committee_note = !empty($_POST['committee_note']) ? trim($_POST['committee_note']) : NULL;

    // ✅ ปรับฟิลด์ให้ตรงกับฐานข้อมูล
    $sql = "INSERT INTO assessments (
                scholarship_id, applications_id, income_score, expense_score, loan_score, 
                scholarship_score, guardian_score, guardian_count_score, reason, total_score,
                fund_type, fund_value, fund_reason, reject_reason, committee_note
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("SQL Prepare Error: " . $conn->error);
    }

    // Bind ค่าลง SQL
    $stmt->bind_param("siiiiiiiiiisiss",    
        $scholarship_id, $applications_id,
        $income_score, $expense_score, $loan_score,
        $scholarship_score, $guardian_score, $guardian_count_score,
        $reason, $total_score, $fund_type, 
        $fund_value, $fund_reason, $reject_reason, $committee_note
    );

    // Execute SQL
    if (!$stmt->execute()) {
        error_log("Execute Error: " . $stmt->error);
        throw new Exception("Execute Error: " . $stmt->error);
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
    $conn->close();

    // ส่งข้อความสำเร็จกลับไป
    $_SESSION['success'] = "บันทึกการประเมินสำเร็จ!";
    header("Location: scholarship_list.php");
    exit();

} catch (Exception $e) {
    error_log("Error in save_assessment.php: " . $e->getMessage());
    $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    header("Location: assessment_form.php?scholarship_id=" . $_POST['scholarship_id'] . "&id=" . $_POST['applications_id']);
    exit();
}
?>
