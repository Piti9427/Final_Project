<?php
/**
 * config.php
 * - Minimal .env loader (no extra libs)
 * - MySQL connection via Railway MySQL env vars (MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQLPORT)
 */

// --- Load .env (optional) ---
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        // Strip optional quotes
        $value = trim($value, " \"'\t\n\r\0\x0B");
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// --- Railway MySQL environment variables ---
$host = getenv('MYSQLHOST') ?: 'localhost';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$db   = getenv('MYSQLDATABASE') ?: 'final_project';
$port = getenv('MYSQLPORT') ?: '3306';

$conn = mysqli_connect($host, $user, $pass, $db, (int)$port);
if (!$conn) {
    $err = mysqli_connect_error();
    error_log("Database connection failed: {$err} | Host={$host} User={$user} DB={$db} Port={$port}");
    die('Database connection failed.');
}

mysqli_set_charset($conn, 'utf8mb4');
