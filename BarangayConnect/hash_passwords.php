<?php
// hash_passwords.php - Standalone script to hash existing unhashed passwords in the database
// WARNING: Backup your database before running this script!
// Run this file once via browser, then delete it for security.

echo "<h1>Password Hashing Script</h1>";

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Your DB password
$dbname = "bcdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<p>Connected to database successfully.</p>";

// Query to find potentially unhashed passwords (those not starting with '$')
$sql = "SELECT userID, password FROM users WHERE password NOT LIKE '$%'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<p>Found " . $result->num_rows . " users with potentially unhashed passwords. Starting hashing...</p>";
    
    $updated_count = 0;
    while ($row = $result->fetch_assoc()) {
        $id = $row['userID'];
        $plain_password = $row['password'];
        
        // Hash the password
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
        
        // Update the database
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE userID = ?");
        $update_stmt->bind_param("si", $hashed_password, $id);
        
        if ($update_stmt->execute()) {
            echo "<p>Hashed password for user ID: $id</p>";
            $updated_count++;
        } else {
            echo "<p>Error hashing for user ID $id: " . $update_stmt->error . "</p>";
        }
        $update_stmt->close();
    }
    
    echo "<p><strong>Hashing complete! Updated $updated_count passwords.</strong></p>";
} else {
    echo "<p>No unhashed passwords found. All passwords may already be hashed.</p>";
}

$result->close();
$conn->close();

echo "<p>Script finished. Remember to delete this file after use.</p>";
?>
