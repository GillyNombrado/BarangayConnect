<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'bcdb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch All Announcements
$sql_all = "SELECT a.announcementID, b.barangayID, a.title, a.content, a.author, a.status, a.date_posted FROM announcements a INNER JOIN barangays b ON a.barangayID = b.barangayID";
$result_all = $conn->query($sql_all);
if (!$result_all) {
    die("Query failed: " . $conn->error);
}

// Fetch Archived Announcements
$sql_archived = "SELECT a.announcementID, b.barangayID, a.title, a.content, a.author, a.status, a.date_posted FROM announcements a INNER JOIN barangays b ON a.barangayID = b.barangayID WHERE a.status = 'Archived'";
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
    <title>Announcements</title>
    <link rel="stylesheet" href="sidenav.css">
    <link rel="stylesheet" href="announce.css">
    <link rel="shortcut icon" href="img/Nagcarlan_Laguna_seal_logo.png" type="image/x-icon">
    <!-- Font Awesome for Icons -->
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
                        <li class="navlist_item"><a href="manageuser.php" class="navlink">Manage Users</a></li>
                        <li class="navlist_item"><a href="cert.php" class="navlink">Certificate Request</a></li>
                        <li class="navlist_item"><a href="announce.php" class="navlink active">Announcements</a></li>
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
            <!-- ANNOUNCEMENTS CARD -->
            <div class="announce_container">
                <div class="announce_wrapper">
                    <div class="announce_title">
                        <h1>Announcements</h1>
                    </div>

                    <!-- CONTROLS: Buttons & Search -->
                    <div class="controls">
                        <div class="left-controls">
                            <a href="announce_add.php"><button class="addbtn"><i class="fas fa-plus"></i> Post Announcement</button></a>
                            <div class="tab-buttons">
                                <button class="switch_button active" onclick="showTab('all')">All</button>
                                <button class="switch_button" onclick="showTab('archived')">Archived</button>
                            </div>
                        </div>
                        
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="search" name="search" id="search" placeholder="Search Announcements...">
                        </div>
                    </div>

                    <!-- Table 1: All Announcements -->
                    <div id="all" class="switch_content active">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Brgy ID</th>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Author</th>
                                        <th>Status</th>
                                        <th>Date Posted</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_all->num_rows > 0) {
                                        while ($row = $result_all->fetch_assoc()) {
                                            echo "<tr id='row-{$row['announcementID']}'>";
                                            echo "<td>{$row['announcementID']}</td>";
                                            echo "<td>{$row['barangayID']}</td>";
                                            echo "<td>{$row['title']}</td>";
                                            echo "<td class='content-cell'>{$row['content']}</td>";
                                            echo "<td>{$row['author']}</td>";
                                            echo "<td>{$row['status']}</td>";
                                            echo "<td>{$row['date_posted']}</td>";
                                            echo "<td class='actions'>
                                                <button class='action-btn edit-btn' onclick=\"window.location.href='announce_edit.php?id={$row['announcementID']}';\"><i class='fas fa-edit'></i> Update</button>
                                                <button class='action-btn delete-btn' onclick=\"deleteAnnounce({$row['announcementID']});\"><i class='fas fa-trash'></i> Delete</button>
                                            </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='8'>No announcements found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Table 2: Archived Announcements -->
                    <div id="archived" class="switch_content">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Brgy ID</th>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Author</th>
                                        <th>Status</th>
                                        <th>Date Posted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_archived->num_rows > 0) {
                                        while ($row = $result_archived->fetch_assoc()) {
                                            echo "<tr id='row-{$row['announcementID']}'>";
                                            echo "<td>{$row['announcementID']}</td>";
                                            echo "<td>{$row['barangayID']}</td>";
                                            echo "<td>{$row['title']}</td>";
                                            echo "<td class='content-cell'>{$row['content']}</td>";
                                            echo "<td>{$row['author']}</td>";
                                            echo "<td>{$row['status']}</td>";
                                            echo "<td>{$row['date_posted']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>No archived announcements found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="announce.js"></script>
<script>
    // Tab Switching Logic
    function showTab(tabId) {
        // Hide all contents
        const contents = document.querySelectorAll('.switch_content');
        contents.forEach(content => content.classList.remove('active'));

        // Remove active class from buttons
        const buttons = document.querySelectorAll('.switch_button');
        buttons.forEach(button => button.classList.remove('active'));

        // Show target and activate button
        document.getElementById(tabId).classList.add('active');
        event.currentTarget.classList.add('active');

        // Re-apply search filter when switching tabs
        const searchTerm = document.getElementById('search').value;
        filterTable(searchTerm);
    }

    // Search Logic
    document.getElementById('search').addEventListener('input', function() {
        filterTable(this.value);
    });

    function filterTable(searchTerm) {
        const term = searchTerm.toLowerCase();
        // Only search inside the currently active tab
        const activeContainer = document.querySelector('.switch_content.active');
        if(!activeContainer) return;

        const rows = activeContainer.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    }
</script>
</body>
</html>
<?php $conn->close(); ?>