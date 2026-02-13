<?php 
include "../main/session.php"; 
include "../users/checklogin.php";
include config_loader.php"; 

if(isset($_SESSION['user_login']))
{
    $checkuser = checkuser($_SESSION['user_login'], 'admin');
    if($checkuser == "no") {
        echo '<meta http-equiv="refresh" content="0;url=../main/index.php">';
        exit();
    }
} else {
    echo '<meta http-equiv="refresh" content="0;url=../main/index.php">';
    exit();
}

if (isset($_GET["user_no"])) {
    $user_no = $_GET["user_no"];

    // Connect to database
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Step 1: Delete related records in 'authorize' table
    $deleteAuthorize = "DELETE FROM authorize WHERE user_no = ?";
    $stmt1 = $conn->prepare($deleteAuthorize);
    $stmt1->bind_param("i", $user_no);
    $stmt1->execute();
    $stmt1->close();

    // Step 2: Delete user from 'users' table
    $deleteUser = "DELETE FROM users WHERE user_no = ?";
    $stmt2 = $conn->prepare($deleteUser);
    $stmt2->bind_param("i", $user_no);
    
    if ($stmt2->execute()) {
        header("Refresh:0;url=users_list.php"); // Redirect to user list after deletion
    } else {
        echo "Error: " . $stmt2->error;
    }
    
    $stmt2->close();
    $conn->close();
}
?>
