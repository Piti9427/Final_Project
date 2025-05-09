<?php
session_start();
include "../users/checklogin.php";

// Get scholarship ID from URL
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid scholarship ID";
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Include database connection
include "../config/connect.php"; // Adjust this to your actual database connection file

// Fetch scholarship details to get image path
$stmt = $conn->prepare("SELECT image_path FROM scholarships WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $scholarship = $result->fetch_assoc();
    
    // Delete the image file if it exists
    if(!empty($scholarship['image_path'])) {
        $image_path = "../dist/img/" . $scholarship['image_path'];
        if(file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete the scholarship record
    $delete_stmt = $conn->prepare("DELETE FROM scholarships WHERE id = ?");
    $delete_stmt->bind_param("i", $id);
    
    if($delete_stmt->execute()) {
        $_SESSION['success'] = "Scholarship deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting scholarship: " . $conn->error;
    }
    
    $delete_stmt->close();
} else {
    $_SESSION['error'] = "Scholarship not found";
}

$stmt->close();
$conn->close();

header("Location: index.php");
exit();
?>