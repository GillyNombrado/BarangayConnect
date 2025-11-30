<?php
session_start();
// if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'admin') {
//     header("Location: login.html");
//     exit();
// }

$conn = new mysqli("localhost", "root", "", "bcdb");
if ($conn->connect_error) { die("Connection failed."); }

$message = ""; 
$errors = [];

// Fetch user data
$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM users WHERE userID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) { die("User not found."); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);
    $barangayID = trim($_POST['barangayID']);

    if (empty($email) || empty($password) || empty($cpassword) || empty($barangayID)) {
        $errors[] = "Fill all fields.";
    } elseif ($password !== $cpassword) {
        $errors[] = "Passwords do not match.";
    } else {
        // Check duplicate email (exclude current user)
        $stmt = $conn->prepare("SELECT userID FROM users WHERE email = ? AND userID != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET residentID = ?, email = ?, password = ?, barangayID = ? WHERE userID = ?");
            $stmt->bind_param("isssii", $residentID, $email, $hashed_password, $barangayID, $id);
            if ($stmt->execute()) {
                header("Location: manageuser.php");
                exit();
            } else {
                $errors[] = "Update failed.";
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
    <title>Edit User</title>
    <link rel="stylesheet" href="form.css">
    <link rel="shortcut icon" href="img/Nagcarlan_Laguna_seal_logo.png" type="image/x-icon">

</head>
<body>
<div class="container">
    <form method="post">
        <h1>Edit User</h1>
        <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="password" name="cpassword" placeholder="Confirm Password" required><br>
        <select name="barangayID" required>
            <option value="7" <?php if ($row['barangayID'] == '7') echo 'selected'; ?>>Banago</option>
            <option value="6" <?php if ($row['barangayID'] == '6') echo 'selected'; ?>>Malaya</option>
            <option value="1" <?php if ($row['barangayID'] == '1') echo 'selected'; ?>>Poblacion I</option>
            <option value="2" <?php if ($row['barangayID'] == '2') echo 'selected'; ?>>Poblacion II</option>
            <option value="3" <?php if ($row['barangayID'] == '3') echo 'selected'; ?>>Poblacion III</option>
            <option value="4" <?php if ($row['barangayID'] == '4') echo 'selected'; ?>>Taytay</option>
            <option value="5" <?php if ($row['barangayID'] == '5') echo 'selected'; ?>>Yukos</option>
        </select>
        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <br>    
        <button class="upbtn" type="submit">Update</button>
        <a href="manageuser.php"><button type="button">Cancel</button></a>
    </form>
</div>
</body>
</html>
