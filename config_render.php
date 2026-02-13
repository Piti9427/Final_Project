<?php
// Render MySQL/MariaDB config - รองรับทั้ง MYSQL_* และ DB_* env vars
$servername = getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: 'localhost';
$username   = getenv('MYSQL_USER') ?: getenv('DB_USER') ?: 'root';
$password   = getenv('MYSQL_PASSWORD') ?: getenv('DB_PASSWORD') ?: '';
$dbname     = getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'newcompany';
$port       = (int)(getenv('MYSQL_PORT') ?: getenv('DB_PORT') ?: 3306);

// เชื่อมต่อ MySQL/MariaDB
$conn = @mysqli_connect($servername, $username, $password, $dbname, $port);
if (!$conn) {
    $errorMsg = "Database connection failed: " . mysqli_connect_error();
    $errorMsg .= "\n\nConfig: Host=$servername, User=$username, DB=$dbname, Port=$port";
    error_log($errorMsg);
    die("Database connection failed. Please check your environment variables (MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE, MYSQL_PORT).");
}
?>
