<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'bcdb');
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$message = ""; 
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $residentID = trim($_POST['residentID']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);
    $userType = trim($_POST['userType']);
    $barangayID = trim($_POST['barangayID']);

    if (empty($email) || empty($password) || empty($cpassword) || empty($userType) || empty($barangayID)) {
        $errors[] = "Fill all required fields.";
    } elseif (!empty($residentID) && !is_numeric($residentID)) {
        $errors[] = "Resident ID must be a number.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email.";
    } elseif ($password !== $cpassword) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    } else {
        // Check duplicate email
        $stmt = $conn->prepare("SELECT userID FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email exists.";
        } else {
            // Check if residentID exists
            if (!empty($residentID)) {
                $stmt_check = $conn->prepare("SELECT residentID FROM residents WHERE residentID = ?");
                $stmt_check->bind_param("i", $residentID);
                $stmt_check->execute();
                $stmt_check->store_result();
                if ($stmt_check->num_rows == 0) {
                    $errors[] = "Resident ID does not exist.";
                }
                $stmt_check->close();
            }
            if (empty($errors)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (residentID, email, password, userType, barangayID, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("isssi", $residentID, $email, $hashed_password, $userType, $barangayID);
                if ($stmt->execute()) {
                    header("Location: manageuser.php");
                    exit();
                } else {
                    $errors[] = "Add failed.";
                }
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
    <link rel="stylesheet" href="form.css">
    <link rel="shortcut icon" href="img/Nagcarlan_Laguna_seal_logo.png" type="image/x-icon">
</head>
<body>
<div class="container">
    <form method="post">
        <h1>Add User</h1>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="password" name="cpassword" placeholder="Confirm Password" required><br>
        <select name="userType" required>
            <option value="">User Type</option>
            <option value="Resident">Resident</option>
            <option value="Admin">Admin</option>
        </select>
        <select name="barangayID" required>
            <option value="">Barangay</option>
            <option value="7">Banago</option>
            <option value="6">Malaya</option>
            <option value="1">Poblacion I</option>
            <option value="2">Poblacion II</option>
            <option value="3">Poblacion III</option>
            <option value="4">Taytay</option>
            <option value="5">Yukos</option>
        </select><br>
        <input type="number" name="residentID" placeholder="Resident ID (Optional)"> <br>
        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <br>
        <button class="addbtn" type="submit">Add</button>
        <a href="manageuser.php"><button type="button">Cancel</button></a>
    </form>
</div>
</body>
</html>
