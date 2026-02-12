<?php
// Database initialization for Railway.app
include 'config_production.php';

// Read SQL file
$sql = file_get_contents('newcompany.sql');

// Remove comments and clean up SQL
$sql = preg_replace('/--.*$/m', '', $sql);
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

// Split into individual queries
$queries = preg_split('/;\s*\n/', $sql);

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if (!$conn->query($query)) {
            echo "Error: " . $conn->error . "\n";
            echo "Query: " . $query . "\n";
        } else {
            echo "Query executed successfully\n";
        }
    }
}

echo "Database initialization completed!\n";
?>
