<?php
if(isset($_GET['id'])) {
    $id = intval($_GET['id']); // แปลงให้เป็นตัวเลขเพื่อความปลอดภัย
    try {
        $conn = new PDO("mysql:host=localhost;dbname=newcompany;charset=utf8", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ตรวจสอบว่า ID มีอยู่ในฐานข้อมูลจริงหรือไม่
        $checkStmt = $conn->prepare("SELECT * FROM scholarship_applications WHERE id = :id");
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            // ทำการลบข้อมูล
            $stmt = $conn->prepare("DELETE FROM scholarship_applications WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // ✅ ลบสำเร็จ → กลับไปยังหน้าเดิม
            header("Location: scholarship_list.php?status=deleted");
            exit();
        } else {
            // ❌ ไม่พบข้อมูล → กลับไปหน้าหลักพร้อม error message
            header("Location: scholarship_list.php?status=notfound");
            exit();
        }
    } catch (PDOException $e) {
        // ❌ กรณีเกิดข้อผิดพลาด
        header("Location: scholarship_list.php?status=error");
        exit();
    }
} else {
    // ❌ ไม่มี ID ที่ต้องการลบ
    header("Location: scholarship_list.php?status=noid");
    exit();
}
?>
