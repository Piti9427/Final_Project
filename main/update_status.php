<?php
// Include database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "newcompany";

// Get JSON data from POST request
$data = json_decode(file_get_contents('php://input'), true);

// Check if required data exists
if (isset($data['id']) && isset($data['status'])) {
    $id = $data['id'];
    $status = $data['status'];
    
    try {
    include '../config_loader.php';
    
    try {
        // Connect to database if not already connected by config_loader
        if (!isset($db) && !isset($conn)) {
             try {
                $dsn = "mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8";
                $db = new PDO($dsn, $username, $password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
             } catch (PDOException $e) {
                // Try using $conn if $db failed or wasn't set, might be mysqli from config
                if (isset($conn) && $conn instanceof PDO) {
                    $db = $conn;
                } else {
                    throw $e;
                }
             }
        } elseif (isset($conn) && $conn instanceof PDO) {
             $db = $conn;
        }
        
        // Make sure to use the correct field name from your database schema
        // If your field is named 'status' in the database:
        $stmt = $db->prepare("UPDATE scholarship_applications SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $success = $stmt->execute();
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
}
?>