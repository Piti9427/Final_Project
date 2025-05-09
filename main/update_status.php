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
        // Connect to database
        $db = new PDO("mysql:host=localhost;dbname=newcompany;charset=utf8", "root", "");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
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