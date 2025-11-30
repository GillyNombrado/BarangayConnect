<?php
session_start();
// if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'admin') {
//     header("Location: login.html");
//     exit();
// }

$conn = new mysqli("localhost", "root", "", "bcdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$errors = [];

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

    if (empty($last_name) || empty($first_name) || empty($birthdate) || empty($gender) || empty($civil_status) || empty($address) || empty($barangayID) || empty($email)) {
        $errors[] = "Please fill in all required fields.";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif (!empty($contact_no) && !is_numeric($contact_no)) {
        $errors[] = "Contact number must be numeric.";
    } else {
        $stmt = $conn->prepare("INSERT INTO residents (last_name, first_name, birthdate, gender, civil_status, address, barangayID, contact_no, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssiss", $last_name, $first_name, $birthdate, $gender, $civil_status, $address, $barangayID, $contact_no, $email);
        
        if ($stmt->execute()) {
            header("Location: resident.php"); 
            exit();
        } else {
            $errors[] = "Error adding resident: " . $stmt->error;
        }
        $stmt->close();
    }
}   
$conn->close();
?>

<!DOCTYPE html>
<html>
<head><title>Add Resident</title>
    <link rel="stylesheet" href="form.css">
    <link rel="shortcut icon" href="img/Nagcarlan_Laguna_seal_logo.png" type="image/x-icon">
</head>
<body>
<div class="container">  
    <form method="post">
        <h1>Resident Form</h1><br>
        <input type="text" name="last_name" id="last_name" placeholder="Last Name" required><br>
        <input type="text" name="first_name" id="first_name" placeholder="First Name" required><br>
        <label for="birthdate">Date of Birth</label>
        <input type="date" name="birthdate" id="birthdate" required><br>
        <select name="gender" id="gender" required>
            <option value="">Gender</option>
            <option value="Female">Female</option>
            <option value="Male">Male</option>
        </select>
        <select name="civil_status" id="civil_status" required>
            <option value="">Civil Status</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
            <option value="Widowed">Widowed</option>
            <option value="Separated">Separated</option>
        </select><br>
        <input type="text" name="address" id="address" placeholder="Address" required><br>
        <select name="barangayID" id="barangayID" required>
            <option value="">Barangay</option>
            <option value="7">Banago</option>
            <option value="6">Malaya</option>
            <option value="1">Poblacion I</option>
            <option value="2">Poblacion II</option>
            <option value="3">Poblacion III</option>
            <option value="4">Taytay</option>
            <option value="5">Yukos</option>
        </select><br>
        <input type="text" name="contact_no" id="contact_no" placeholder="Contact Number"><br>
        <input type="email" name="email" id="email" placeholder="Email" required><br>

        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <button class="addbtn" type="submit">Add</button>
        <a href="resident.php"><button type="button">Cancel</button></a>
    </form>
</div> 
</body>
</html>
