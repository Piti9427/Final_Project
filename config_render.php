<?php
// Render PostgreSQL config (using PDO for PostgreSQL)
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'newcompany';
$port = getenv('DB_PORT') ?: '5432';

// PostgreSQL connection
try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$database", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("PostgreSQL connection failed: " . $e->getMessage());
}

// Create a mysqli-like interface for compatibility
class PostgresToMysqli {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function query($sql) {
        try {
            $result = $this->pdo->query($sql);
            return new PostgresResult($result);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function real_escape_string($value) {
        return $this->pdo->quote($value);
    }
    
    public function close() {
        $this->pdo = null;
    }
}

class PostgresResult {
    private $stmt;
    
    public function __construct($stmt) {
        $this->stmt = $stmt;
    }
    
    public function fetch_assoc() {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function fetch_all() {
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function num_rows() {
        return $this->stmt->rowCount();
    }
}

// Convert PDO to mysqli-like object
$conn = new PostgresToMysqli($conn);
?>
