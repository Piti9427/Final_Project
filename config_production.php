<?php
// Production config for Railway.app
$servername = getenv('MYSQL_HOST') ?: getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'localhost';
$username = getenv('MYSQL_USER') ?: getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';
$database = getenv('MYSQL_DATABASE') ?: getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'newcompany';
$port = getenv('MYSQL_PORT') ?: getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '3306';

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Configure MySQL for PHP 8.x compatibility
$conn->query("SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
?>
