<?php
// Auto-detect environment and load appropriate config
// Render sets RENDER environment variable หรือ RENDER_SERVICE_NAME
$isRender = getenv('RENDER') || getenv('RENDER_SERVICE_NAME') || getenv('RENDER_SERVICE_ID');
$isRailway = getenv('RAILWAY_ENVIRONMENT');

if ($isRender) {
    // ใช้ config_render.php สำหรับ Render (PostgreSQL)
    include 'config_render.php';
} elseif ($isRailway) {
    // ใช้ config_production.php สำหรับ Railway (MySQL)
    include 'config_production.php';
} else {
    // Development หรือ local
    include 'config.php';
}
?>
