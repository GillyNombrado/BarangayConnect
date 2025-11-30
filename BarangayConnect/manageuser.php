<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'bcdb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_all = "SELECT userID, email, userType, created_at FROM users";
$result_all = $conn->query($sql_all);
if (!$result_all) {
    die("Query failed: " . $conn->error);
}

$sql_archived = "SELECT originalID, email, userType, archived_at FROM archived_users";  // Removed unused 'id'
$result_archived = $conn->query($sql_archived);
if (!$result_archived) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="sidenav.css">
    <link rel="stylesheet" href="manageuser.css">
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
                        <li class="navlist_item"><a href="resident.php" class="navlink">Residents</a></li>
                        <li class="navlist_item"><a href="manageuser.php" class="navlink active">Manage Users</a></li>
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
            <!-- MANAGE USERS  -->
            <div class="manageuser_container">
                <div class="manageuser_wrapper">
                    <div class="manageuser_title">
                        <h1>Manage Users</h1>
                    </div>
                    
                    <!-- CONTROLS (Add Button & Search) -->
                    <div class="controls">
                        <a href="manageuser_add.php"><button class="addbtn"><i class="fas fa-plus"></i> Add User</button></a>
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="search" name="search" id="search" placeholder="Search User...">
                        </div>
                    </div>

                    <!-- Table 1: Active Users -->
                    <div id="all" class="switch_content active">
                        <div class="table-container">
                            <table id="userstable">
                                <thead>
                                    <tr>
                                        <th>Account ID</th>
                                        <th>Email</th>
                                        <th>User Type</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_all->num_rows > 0) {
                                        while ($row = $result_all->fetch_assoc()) {
                                            echo "<tr id='row-{$row['userID']}'>";
                                            echo "<td>{$row['userID']}</td>";
                                            echo "<td>{$row['email']}</td>";
                                            echo "<td>{$row['userType']}</td>";
                                            echo "<td>{$row['created_at']}</td>";
                                            echo "<td class='actions'>
                                                <button class='action-btn edit-btn' onclick=\"window.location.href='manageuser_edit.php?id={$row['userID']}';\"><i class='fas fa-edit'></i> Update</button>
                                                <button class='action-btn delete-btn' onclick='archiveUser({$row['userID']});'><i class='fas fa-archive'></i> Archive</button>
                                                </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No user accounts found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Table 2: Archived Users -->
                    <div id="archived" class="switch_content">
                        <div class="table-container">
                            <table id="archivedtable">
                                <thead>
                                    <tr>
                                        <th>Account ID</th>
                                        <th>Email</th>
                                        <th>User Type</th>
                                        <th>Archived At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_archived->num_rows > 0) {
                                        while ($row = $result_archived->fetch_assoc()) {
                                            echo "<tr id='archived-row-{$row['originalID']}'>"; 
                                            echo "<td>{$row['originalID']}</td>";
                                            echo "<td>{$row['email']}</td>";
                                            echo "<td>{$row['userType']}</td>";
                                            echo "<td>{$row['archived_at']}</td>";
                                            echo "<td class='actions'>
                                                <button class='action-btn delete-btn' onclick='restoreUser({$row['originalID']});'><i class='fas fa-undo'></i> Restore</button>
                                            </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No archived user accounts found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                     <!-- Tab Buttons (Moved to top) -->
                    <div class="tab-buttons">
                        <button class="switch_button active" onclick="showTab('all', event)">Active</button>
                        <button class="switch_button" onclick="showTab('archived', event)">Archived</button>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
<script src="manageuser.js"></script>
</body>
</html>
<?php $conn->close(); ?>