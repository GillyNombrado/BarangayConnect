<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'bcdb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT r.residentID, r.last_name, r.first_name, r.birthdate, r.gender, r.civil_status, r.address, b.barangayID, r.contact_no, r.email, r.date_registered FROM residents r INNER JOIN barangays b ON r.barangayID = b.barangayID ORDER BY residentID ASC";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Residents</title>
    <link rel="stylesheet" href="sidenav.css">
    <link rel="stylesheet" href="resident.css">
    <link rel="shortcut icon" href="img/Nagcarlan_Laguna_seal_logo.png" type="image/x-icon">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container">
    <!-- SIDE NAVIGATION BAR -->
    <header class="sidenav_head">
        <div class="sidenav_wrapper">
            <nav class="sidenav">
                <div class="sidenav_nav">
                    <!-- BRANDING  -->
                    <div class="branding">
                        <div class="logo">
                            <img src="img/Nagcarlan_Laguna_seal_logo.png" alt="Logo" srcset="">
                        </div>
                        <div class="branding_title">
                            <h4>BarangayConnect</h4>
                            <h5>Nagcarlan</h5>
                        </div>
                    </div>
                    <!-- NAVIGATION LIST -->
                    <ul class="navlist">
                        <li class="navlist_item"><a href="dashboard.php" class="navlink">Dashboard</a></li>
                        <li class="navlist_item"><a href="resident.php" class="navlink active">Residents</a></li>
                        <li class="navlist_item"><a href="manageuser.php" class="navlink">Manage Users</a></li>
                        <li class="navlist_item"><a href="cert.php" class="navlink">Certificate Request</a></li>
                        <li class="navlist_item"><a href="announce.php" class="navlink">Announcements</a></li>
                        <li class="navlist_item"><a href="report.php" class="navlink">Reports</a></li>
                        <li class="navlist_item"><a href="settings.php" class="navlink">System Settings</a></li>
                        <li class="navlist_item"><a href="login.html" class="navlink">Log out</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <section class="main_section">
        <div class="content">
            <!-- RESIDENT DATA  -->
            <div class="residentdata_container">
                <div class="residentdata_wrapper">
                    <div class="residentdata_title">
                        <h1>Resident Information</h1>
                    </div>
                    <div class="controls">
                        <a href="resident_add.php"><button class="addbtn"><i class="fas fa-plus"></i> Add Resident</button></a>
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="search" name="search" id="search" placeholder="Search Resident...">
                        </div>
                    </div>
                    
                    <!-- RESIDENT TABLE -->
                    <div class="table-container">
                        <table id="residentstable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Birth Date</th>
                                    <th>Gender</th>
                                    <th>Civil Status</th>
                                    <th>Address</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr id='row-{$row['residentID']}'>";
                                        echo "<td>{$row['residentID']}</td>";
                                        echo "<td>{$row['last_name']}</td>";
                                        echo "<td>{$row['first_name']}</td>";
                                        echo "<td>{$row['birthdate']}</td>";
                                        echo "<td>{$row['gender']}</td>";
                                        echo "<td>{$row['civil_status']}</td>";
                                        echo "<td>{$row['address']}</td>";
                                        echo "<td>{$row['contact_no']}</td>";
                                        echo "<td>{$row['email']}</td>";
                                        echo "<td>{$row['date_registered']}</td>";
                                        echo "<td class='actions'>
                                            <button class='action-btn edit-btn' onclick=\"window.location.href='resident_edit.php?id={$row['residentID']}';\"><i class='fas fa-edit'></i></button>
                                            <button class='action-btn delete-btn' onclick='deleteResident({$row['residentID']});'><i class='fas fa-trash'></i></button>
                                        </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='11'>No residents found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="resident.js"></script>
<script>
// Basic search functionality (client-side)
document.getElementById('search').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#residentstable tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>
</body>
</html>

<?php $conn->close(); ?>
