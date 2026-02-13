<?php
// Render PostgreSQL config
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'newcompany';
$port = getenv('DB_PORT') ?: '5432';

// PostgreSQL connection
try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$database", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
