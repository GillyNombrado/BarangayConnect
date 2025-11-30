<?php
/**
 * API Endpoint: Search residents by name
 * Parameters: q (search query), barangayID (optional filter)
 * Returns JSON list of matching residents
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Include database connection
require_once __DIR__ . '/../db.php';

try {
    // Get and sanitize inputs
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    $barangayID = isset($_GET['barangayID']) && $_GET['barangayID'] !== '' ? intval($_GET['barangayID']) : null;
    
    // Require at least 2 characters for search
    if (strlen($query) < 2) {
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
        exit;
    }
    
    // Prepare search term
    $searchTerm = "%{$query}%";
    
    // Build query based on whether barangay filter is provided
    if ($barangayID > 0) {
        // With barangay filter
        $sql = "SELECT 
                    r.residentID, 
                    r.first_name, 
                    r.last_name, 
                    r.birthdate, 
                    r.address,
                    r.barangayID,
                    b.barangayName
                FROM residents r
                LEFT JOIN barangays b ON r.barangayID = b.barangayID
                WHERE (
                    CONCAT(r.first_name, ' ', r.last_name) LIKE ? 
                    OR r.first_name LIKE ? 
                    OR r.last_name LIKE ?
                )
                AND r.barangayID = ?
                ORDER BY r.first_name, r.last_name 
                LIMIT 15";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $barangayID]);
    } else {
        // Without barangay filter
        $sql = "SELECT 
                    r.residentID, 
                    r.first_name, 
                    r.last_name, 
                    r.birthdate, 
                    r.address,
                    r.barangayID,
                    b.barangayName
                FROM residents r
                LEFT JOIN barangays b ON r.barangayID = b.barangayID
                WHERE (
                    CONCAT(r.first_name, ' ', r.last_name) LIKE ? 
                    OR r.first_name LIKE ? 
                    OR r.last_name LIKE ?
                )
                ORDER BY r.first_name, r.last_name 
                LIMIT 15";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    }
    
    $residents = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $residents,
        'query' => $query,
        'barangayID' => $barangayID,
        'count' => count($residents)
    ]);
    
} catch (PDOException $e) {
    error_log("Error searching residents: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to search residents.',
        'error' => $e->getMessage(),
        'line' => $e->getLine()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred.',
        'error' => $e->getMessage()
    ]);
}