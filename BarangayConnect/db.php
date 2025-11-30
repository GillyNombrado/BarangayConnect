<?php
/**
 * Database Connection using PDO
 * 
 * IMPORTANT: Update these credentials for your environment
 * For production, use environment variables or a secure config file
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'bcdb');
define('DB_USER', 'root');
define('DB_PASS', ''); // Update with your MySQL password
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // Log error securely - don't expose DB details to users
    error_log("Database Connection Error: " . $e->getMessage());
    
    // Return generic error to user
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed. Please contact the administrator.'
    ]));
}