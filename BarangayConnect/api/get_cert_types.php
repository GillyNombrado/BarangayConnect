<?php
/**
 * API Endpoint: Get all certificate types
 * Returns JSON list of certificate types ordered by name
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../db.php';

try {
    $stmt = $pdo->prepare("SELECT cert_typeID, cert_types FROM cert_type ORDER BY cert_types");
    $stmt->execute();
    
    $certTypes = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $certTypes
    ]);
    
} catch (PDOException $e) {
    error_log("Error fetching certificate types: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch certificate types.'
    ]);
}