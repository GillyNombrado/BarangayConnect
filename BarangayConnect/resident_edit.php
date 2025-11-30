<?php
session_start();
// Uncomment: if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'admin') {
//     header("Location: login.html");
//     exit();
// }

$message = ""; 
$errors = [];

$conn = new mysqli("localhost", "root", "", "bcdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM residents WHERE residentID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    die("Resident not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $last_name = trim($_POST['last_name']);
    $first_name = trim($_POST['first_name']);
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $civil_status = $_POST['civil_status'];
    $address = trim($_POST['address']);
    $barangayID = trim($_POST['barangayID']);
    $contact_no = trim($_POST['contact_no']);
    $email = trim($_POST['email']);

    if (empty($last_name) || empty($first_name) || empty($birthdate) || empty($gender) || empty($civil_status) || empty($address) || empty($barangayID)) {
        $errors[] = "Please fill in all required fields.";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("UPDATE residents SET last_name = ?, first_name = ?, birthdate = ?, gender = ?, civil_status = ?, address = ?, barangayID = ?, contact_no = ?, email = ? WHERE residentID = ?");
        $stmt->bind_param("ssssssiss", $last_name, $first_name, $birthdate, $gender, $civil_status, $address, $barangayID, $contact_no, $email);
        
        if ($stmt->execute()) {
            header("Location: resident.php");
            exit();
        } else {
            $errors[] = "Error updating resident: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Resident</title>
    <link rel="stylesheet" href="form.css">
    <link rel="shortcut icon" href="img/Nagcarlan_Laguna_seal_logo.png" type="image/x-icon">
</head>
<body>
<div class="container">
    <form method="post">
        <h1>Edit Resident</h1><br>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($row['last_name']); ?>" required><br>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($row['first_name']); ?>" required><br>
        <label for="birthdate">Date of Birth:</label>
        <input type="date" name="birthdate" id="birthdate" value="<?php echo htmlspecialchars($row['birthdate']); ?>" required><br>
        <select name="gender" id="gender" required>
            <option value="">Gender</option>
            <option value="Female" <?php if ($row['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            <option value="Male" <?php if ($row['gender'] == 'Male') echo 'selected'; ?>>Male</option>
        </select>
        <select name="civil_status" id="civil_status" required>
            <option value="">Civil Status</option>
            <option value="Single" <?php if ($row['civil_status'] == 'Single') echo 'selected'; ?>>Single</option>
            <option value="Married" <?php if ($row['civil_status'] == 'Married') echo 'selected'; ?>>Married</option>
            <option value="Widowed" <?php if ($row['civil_status'] == 'Widowed') echo 'selected'; ?>>Widowed</option>
            <option value="Separated" <?php if ($row['civil_status'] == 'Separated') echo 'selected'; ?>>Separated</option>
        </select><br>
        <input type="text" name="address" value="<?php echo htmlspecialchars($row['address']); ?>" required><br>
        <select name="barangayID" id="barangayID" required>
            <option value="7" <?php if ($row['barangayID'] == '7') echo 'selected'; ?>>Banago</option>
            <option value="6" <?php if ($row['barangayID'] == '6') echo 'selected'; ?>>Malaya</option>
            <option value="1" <?php if ($row['barangayID'] == '1') echo 'selected'; ?>>Poblacion I</option>
            <option value="2" <?php if ($row['barangayID'] == '2') echo 'selected'; ?>>Poblacion II</option>
            <option value="3" <?php if ($row['barangayID'] == '3') echo 'selected'; ?>>Poblacion III</option>
            <option value="4" <?php if ($row['barangayID'] == '4') echo 'selected'; ?>>Taytay</option>
            <option value="5" <?php if ($row['barangayID'] == '5') echo 'selected'; ?>>Yukos</option>
        </select><br>     
        <input type="text" name="contact_no" id="contact_no" value="<?php echo htmlspecialchars($row['contact_no']); ?>"><br>   
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($row['email']); ?>"><br>
        
        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <br>        
        <button class="upbtn" type="submit">Update</button>
        <a href="resident.php"><button type="button">Cancel</button></a>
    </form>
</div>
</body>
</html>
