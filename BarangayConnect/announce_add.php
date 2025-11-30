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
    $barangayID = trim($_POST['barangayID']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author = trim($_POST['author']);

    if (empty($barangayID) || empty($title) || empty($content) || empty($author)) {
        $errors[] = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO announcements (barangayID, title, content, author) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $barangayID, $title, $content, $author);
        
        if ($stmt->execute()) {
            header("Location: announce.php"); 
            exit();
        } else {
            $errors[] = "Error adding announcement: " . $stmt->error;
        }
        $stmt->close();
    }
}   
$conn->close();
?>

<!DOCTYPE html>
<html>
<head><title>Add Announcement</title>
    <link rel="stylesheet" href="form.css">
    <link rel="shortcut icon" href="img/Nagcarlan_Laguna_seal_logo.png" type="image/x-icon">
</head>
<body>
<div class="container">  
    <form method="post">
        <h1>Announcement Form</h1><br>
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
        <input type="text" name="title" id="title" placeholder="Title" required><br>
        <input type="text" name="content" id="content" placeholder="Content" required><br>
        <input type="text" name="author" id="author" placeholder="Author" required><br>

        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <button class="addbtn" type="submit">Post</button>
        <a href="annouonce.php"><button type="button">Cancel</button></a>
    </form>
</div> 
</body>
</html>
