<?php
/**
 * API Endpoint: Get most common purposes for a certificate type
 * Parameters: cert_typeID
 * Returns JSON list of top 5 most used purposes
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../db.php';

try {
    $certTypeID = isset($_GET['cert_typeID']) ? (int)$_GET['cert_typeID'] : null;
    
    if (!$certTypeID) {
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
        exit;
    }
    
    $stmt = $pdo->prepare("
        SELECT 
            purpose, 
            COUNT(*) as usage_count 
        FROM cert_requests 
        WHERE cert_typeID = :cert_typeID 
            AND purpose IS NOT NULL 
            AND purpose != ''
        GROUP BY purpose 
        ORDER BY usage_count DESC 
        LIMIT 5
    ");
    
    $stmt->bindParam(':cert_typeID', $certTypeID, PDO::PARAM_INT);
    $stmt->execute();
    
    $purposes = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $purposes
    ]);
    
} catch (PDOException $e) {
    error_log("Error fetching common purposes: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch common purposes.'
    ]);
}