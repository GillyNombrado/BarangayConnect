<?php
session_start(); // Start session for logged-in user

// Create connection
$conn = new mysqli('localhost', 'root', '', 'bcdb');

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

// Get POST data
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

// Prepare and execute query (updated table name)
$stmt = $conn->prepare("SELECT userID, password, userType FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    // Verify password
    if (password_verify($password, $row['password'])) {
        // Successful login
        $_SESSION['userID'] = $row['userID'];
        $_SESSION['userType'] = $row['userType'];
        echo json_encode(['success' => true, 'userType' => $row['userType']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
}

// Close connections
$stmt->close();
$conn->close();
?>
