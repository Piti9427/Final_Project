<?php
$module = "admin";
include "../users/checkmodule.php"; // Added missing semicolon

include config_loader.php";

// Sanitize input
$user_no = $_GET["user_no"] ?? "";
$module_no = $_GET["module_no"] ?? "";
$branch_no = $_GET["branch_no"] ?? "";

// Database connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Use prepared statements to prevent SQL injection
$sql = "DELETE FROM authorize WHERE user_no = ? AND module_no = ? AND branch_no = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "sss", $user_no, $module_no, $branch_no); // Assuming all are strings
    if (mysqli_stmt_execute($stmt)) {
        header("Location: authorize_list.php");
        exit(); // Ensure script stops execution after redirect
    } else {
        echo "Error executing query: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "SQL Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
