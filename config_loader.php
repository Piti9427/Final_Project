<?php
// Auto-detect environment and load appropriate config
$environment = getenv('RAILWAY_ENVIRONMENT') ?: getenv('RENDER') ?: 'development';

if ($environment === 'production') {
    include 'config_production.php';
} elseif ($environment === 'render') {
    include 'config_render.php';
} else {
    include 'config.php';
}
?>
