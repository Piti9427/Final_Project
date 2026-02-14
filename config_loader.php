<?php
// config_loader.php — always delegate to config.php
// config.php already handles Railway (MYSQLHOST), Render (MYSQL_HOST), and local (.env / defaults)
include __DIR__ . '/config.php';
?>
