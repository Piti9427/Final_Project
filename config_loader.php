<?php
// Auto-detect environment and load appropriate config
$environment = getenv('RAILWAY_ENVIRONMENT') ?: 'development';

if ($environment === 'production') {
    include 'config_production.php';
} else {
    include 'config.php';
}
?>
