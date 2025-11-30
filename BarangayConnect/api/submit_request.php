<?php
/**
 * API Endpoint: Submit certificate request
 * Method: POST
 * Accepts JSON: { residentID, cert_typeID, purpose, barangayID }
 * Returns: { success, message, data: { requestID, certificateURL } }
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $residentID = isset($input['residentID']) ? (int)$input['residentID'] : 0;
    $cert_typeID = isset($input['cert_typeID']) ? (int)$input['cert_typeID'] : 0;
    $purpose = isset($input['purpose']) ? trim($input['purpose']) : '';
    
    // Validation
    $errors = [];
    
    if ($residentID <= 0) {
        $errors[] = 'Please select a valid resident.';
    }
    
    if ($cert_typeID <= 0) {
        $errors[] = 'Please select a certificate type.';
    }
    
    if (empty($purpose)) {
        $errors[] = 'Purpose is required.';
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(' ', $errors)
        ]);
        exit;
    }
    
    // Verify resident exists
    $stmt = $pdo->prepare("SELECT residentID FROM residents WHERE residentID = :residentID");
    $stmt->bindParam(':residentID', $residentID, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Resident not found.'
        ]);
        exit;
    }
    
    // Verify certificate type exists
    $stmt = $pdo->prepare("SELECT cert_typeID FROM cert_type WHERE cert_typeID = :cert_typeID");
    $stmt->bindParam(':cert_typeID', $cert_typeID, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Certificate type not found.'
        ]);
        exit;
    }
    
    // Insert certificate request
    $stmt = $pdo->prepare("
        INSERT INTO cert_requests (residentID, cert_typeID, purpose, status, date_requested) 
        VALUES (:residentID, :cert_typeID, :purpose, 'Approved', NOW())
    ");
    
    $stmt->bindParam(':residentID', $residentID, PDO::PARAM_INT);
    $stmt->bindParam(':cert_typeID', $cert_typeID, PDO::PARAM_INT);
    $stmt->bindParam(':purpose', $purpose, PDO::PARAM_STR);
    
    $stmt->execute();
    
    $requestID = $pdo->lastInsertId();
    
    // Generate certificate URL
    $certificateURL = 'generate_certificate.php?requestID=' . $requestID;
    
    echo json_encode([
        'success' => true,
        'message' => 'Certificate request submitted successfully.',
        'data' => [
            'requestID' => $requestID,
            'certificateURL' => $certificateURL
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Error submitting request: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit certificate request. Please try again.'
    ]);
}