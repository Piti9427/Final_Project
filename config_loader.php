<?php
// Auto-detect environment and load appropriate config
// Render sets RENDER environment variable หรือ RENDER_SERVICE_NAME
$isRender = getenv('RENDER') || getenv('RENDER_SERVICE_NAME') || getenv('RENDER_SERVICE_ID');
$isRailway = getenv('RAILWAY_ENVIRONMENT');
$environment = getenv('RAILWAY_ENVIRONMENT') ?: 'development';

if ($isRender) {
    // ใช้ config_render.php สำหรับ Render (รองรับ MySQL/MariaDB)
    include 'config_render.php';
} elseif ($environment === 'production') {
    include 'config_production.php';
} else {
    // Development หรือ local
    include config_loader.php';
}
?>
