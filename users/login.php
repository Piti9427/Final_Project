<?php
session_start();
include "../config.php"; // เชื่อมต่อฐานข้อมูล
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | iTrace</title>

  <!-- CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

  <style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f4f6f9;
        margin: 0;
    }
    .login-container {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 400px;
    }
  </style>
</head>
<body>

<div class="login-container">
    <h3 class="text-center">Login</h3>

    <?php
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger text-center">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    ?>

    <form action="2login.php" method="post">
        <div class="mb-3">
            <label for="login" class="form-label">Username</label>
            <input type="text" name="login" id="login" class="form-control" placeholder="Enter your username" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <a href="../main/index.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
