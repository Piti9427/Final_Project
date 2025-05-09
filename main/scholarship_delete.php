<?php
include "../config.php"; // เชื่อมต่อฐานข้อมูล

if (isset($_POST['id'])) {
    $id = intval($_POST['id']); // ป้องกัน SQL Injection
    $sql = "DELETE FROM scholarships WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
        $stmt->close();
    } else {
        echo "error";
    }
    $conn->close();
}
?>