<?php
session_start();

$conn = new mysqli("localhost", "root", "", "bcdb");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

header('Content-Type: application/json');

// --- ACTION: ARCHIVE USER ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['archive_id'])) {
    $id = intval($_POST['archive_id']);
    
    $stmt = $conn->prepare("SELECT userID, residentID, email, password, userType, created_at FROM users WHERE userID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $insertStmt = $conn->prepare("INSERT INTO archived_users (originalID, residentID, email, password, userType, archived_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $insertStmt->bind_param("iisss", $user['userID'], $user['residentID'], $user['email'], $user['password'], $user['userType']);
        
        if ($insertStmt->execute()) {
            $deleteStmt = $conn->prepare("DELETE FROM users WHERE userID = ?");
            $deleteStmt->bind_param("i", $id);
            $deleteStmt->execute();
            $deleteStmt->close();
            
            echo json_encode(['success' => true, 'message' => 'User archived successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Archive failed: ' . $insertStmt->error]);
        }
        $insertStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
    $stmt->close();

// --- ACTION: RESTORE USER ---
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restore_id'])) {
    $id = intval($_POST['restore_id']); // This is the ID in archived_users table

    // 1. Fetch from archived_users
    $stmt = $conn->prepare("SELECT originalID, residentID, email, password, userType FROM archived_users WHERE originalID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $archivedUser = $result->fetch_assoc();
        $insertStmt = $conn->prepare("INSERT INTO users (userID, residentID, email, password, userType, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $insertStmt->bind_param("iisss", $archivedUser['originalID'], $archivedUser['residentID'], $archivedUser['email'], $archivedUser['password'], $archivedUser['userType']);

        if ($insertStmt->execute()) {
            $deleteStmt = $conn->prepare("DELETE FROM archived_users WHERE originalID = ?");
            $deleteStmt->bind_param("i", $id);
            $deleteStmt->execute();
            $deleteStmt->close();

            echo json_encode(['success' => true, 'message' => 'User restored successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Restore failed: ' . $insertStmt->error]);
        }
        $insertStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Archived user not found']);
    }
    $stmt->close();

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>