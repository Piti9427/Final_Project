<?php
// รองรับทั้ง local และ Railway
$servername = getenv('MYSQL_HOST') ?: 'localhost';
$username = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: '';
$dbname = getenv('MYSQL_DATABASE') ?: 'newcompany';
$port = getenv('MYSQL_PORT') ?: 3306;

$conn = mysqli_connect($servername, $username, $password, $dbname, $port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
