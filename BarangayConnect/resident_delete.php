<?php
session_start();

$conn = new mysqli("localhost", "root", "", "bcdb");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM residents WHERE residentID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Delete failed']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>
