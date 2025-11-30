<?php
/**
 * API Endpoint: Get all barangays
 * Returns JSON list of barangays ordered by name
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../db.php';

try {
    $stmt = $pdo->prepare("SELECT barangayID, barangayName FROM barangays ORDER BY barangayName");
    $stmt->execute();
    
    $barangays = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $barangays
    ]);
    
} catch (PDOException $e) {
    error_log("Error fetching barangays: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch barangays.'
    ]);
}