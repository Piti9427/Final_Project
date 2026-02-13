<?php
// โหลดค่าจากไฟล์ .env ถ้ามี (ง่าย ๆ ไม่ต้องพึ่ง library เพิ่ม)
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        // ตรวจสอบว่ามี = sign ก่อนทำ destructuring เพื่อป้องกัน fatal error
        if (strpos($line, '=') === false) {
            // Skip malformed lines ที่ไม่มี = sign
            continue;
        }
        $parts = array_map('trim', explode('=', $line, 2));
        $key = $parts[0];
        $value = isset($parts[1]) ? $parts[1] : '';
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// รองรับทั้ง local, Docker, Railway, Render ฯลฯ ผ่าน env / .env
<?php
// ดึงค่าจาก Railway Environment Variables
$db_host = getenv('MYSQLHOST') ?: 'localhost';
$db_user = getenv('MYSQLUSER') ?: 'root';
$db_pass = getenv('MYSQLPASSWORD') ?: '';
$db_name = getenv('MYSQLDATABASE') ?: 'final_project';
$db_port = getenv('MYSQLPORT') ?: '3306';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

// เชื่อมต่อ database
$conn = @mysqli_connect($servername, $username, $password, $dbname, $port);
if (!$conn) {
    $errorMsg = "Database connection failed: " . mysqli_connect_error();
    $errorMsg .= "\n\nConfig: Host=$servername, User=$username, DB=$dbname, Port=$port";
    error_log($errorMsg);
    die("Database connection failed. Please check your environment variables (MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE, MYSQL_PORT).");
}
?>
