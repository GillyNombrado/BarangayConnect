<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'bcdb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Common SQL parts
$base_sql = "SELECT res.residentID, r.reportID, rt.report_typeID, r.message, r.response, r.status, r.date_reported 
             FROM reports r 
             INNER JOIN report_type rt ON r.report_typeID = rt.report_typeID  
             INNER JOIN residents res ON r.residentID = res.residentID";

// Query for all
$sql_all = "$base_sql ORDER BY r.date_reported DESC";
$result_all = $conn->query($sql_all);

// Query for pending
$sql_pending = "$base_sql WHERE r.status = 'Pending' ORDER BY r.date_reported DESC";
$result_pending = $conn->query($sql_pending);

// Query for approved
$sql_approved = "$base_sql WHERE r.status = 'Approved' ORDER BY r.date_reported DESC";
$result_approved = $conn->query($sql_approved);

// Query for denied
$sql_denied = "$base_sql WHERE r.status = 'Denied' ORDER BY r.date_reported DESC";
$result_denied = $conn->query($sql_denied);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="sidenav.css">
    <link rel="stylesheet" href="report.css">
    <link rel="shortcut icon" href="img/Nagcarlan_Laguna_seal_logo.png" type="image/x-icon">
    <!-- Font Awesome -->
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
                        <li class="navlist_item"><a href="announce.php" class="navlink">Announcements</a></li>
                        <li class="navlist_item"><a href="report.php" class="navlink active">Reports</a></li>
                        <li class="navlist_item"><a href="settings.php" class="navlink">System Settings</a></li>
                        <li class="navlist_item"><a href="login.html" class="navlink">Log out</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <section class="main_section">
        <div class="content">
            <!-- REPORTS CONTAINER -->
            <div class="report_container">
                <div class="report_wrapper">
                    <div class="report_title">
                        <h1>Reports</h1>
                    </div>
                    
                    <!-- CONTROLS: Tabs & Search -->
                    <div class="controls">
                        <div class="tab-buttons">
                            <button class="switch_button active" onclick="showTab('all')">All Reports</button>
                            <button class="switch_button" onclick="showTab('pending')">Pending</button>
                            <button class="switch_button" onclick="showTab('approved')">Approved</button>
                            <button class="switch_button" onclick="showTab('denied')">Denied</button>
                        </div>
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="search" name="search" id="search" placeholder="Search Reports...">
                        </div>
                    </div>

                    <!-- TABLE 1: ALL REPORTS -->
                     <div id="all" class="switch_content active">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Resident ID</th>
                                        <th>Report Type</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Date Reported</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_all && $result_all->num_rows > 0) {
                                        while ($row = $result_all->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$row['residentID']}</td>";
                                            echo "<td>{$row['report_typeID']}</td>";
                                            echo "<td class='message-cell'>{$row['message']}</td>";
                                            echo "<td>{$row['status']}</td>";
                                            echo "<td>{$row['date_reported']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No reports found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                     </div>
                    
                    <!-- TABLE 2: PENDING REPORTS -->
                    <div id="pending" class="switch_content">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Resident ID</th>
                                        <th>Report Type</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Date Reported</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_pending && $result_pending->num_rows > 0) {
                                        while ($row = $result_pending->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$row['residentID']}</td>";
                                            echo "<td>{$row['report_typeID']}</td>";
                                            echo "<td class='message-cell'>{$row['message']}</td>";
                                            echo "<td>{$row['status']}</td>";
                                            echo "<td>{$row['date_reported']}</td>";
                                            echo "<td class='actions'>
                                                <button class='action-btn edit-btn' onclick=\"window.location.href='manageuser_edit.php?id={$row['reportID']}';\"><i class='fas fa-check'></i> Accept</button>
                                                <button class='action-btn delete-btn' onclick=\"deleteUser({$row['reportID']});\"><i class='fas fa-times'></i> Deny</button>
                                            </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No pending reports found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABLE 3: APPROVED REPORTS -->
                    <div id="approved" class="switch_content">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Resident ID</th>
                                        <th>Report Type</th>
                                        <th>Message</th>
                                        <th>Response</th>
                                        <th>Status</th>
                                        <th>Date Reported</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_approved && $result_approved->num_rows > 0) {
                                        while ($row = $result_approved->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$row['residentID']}</td>";
                                            echo "<td>{$row['report_typeID']}</td>";
                                            echo "<td class='message-cell'>{$row['message']}</td>";
                                            echo "<td class='message-cell'>{$row['response']}</td>";
                                            echo "<td>{$row['status']}</td>";
                                            echo "<td>{$row['date_reported']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No approved reports found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABLE 4: DENIED REPORTS -->
                    <div id="denied" class="switch_content">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Resident ID</th>
                                        <th>Report Type</th>
                                        <th>Message</th>
                                        <th>Response</th>
                                        <th>Status</th>
                                        <th>Date Reported</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_denied && $result_denied->num_rows > 0) {
                                        while ($row = $result_denied->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$row['residentID']}</td>";
                                            echo "<td>{$row['report_typeID']}</td>";
                                            echo "<td class='message-cell'>{$row['message']}</td>";
                                            echo "<td class='message-cell'>{$row['response']}</td>";
                                            echo "<td>{$row['status']}</td>";
                                            echo "<td>{$row['date_reported']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No denied reports found.</td></tr>";
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

<script>
    // Tab Switching
    function showTab(tabId) {
        // Hide all contents
        const contents = document.querySelectorAll('.switch_content');
        contents.forEach(content => content.classList.remove('active'));

        // Reset buttons
        const buttons = document.querySelectorAll('.switch_button');
        buttons.forEach(button => button.classList.remove('active'));

        // Activate specific tab and button
        document.getElementById(tabId).classList.add('active');
        event.currentTarget.classList.add('active');

        // Re-apply search filter to the new tab
        const searchVal = document.getElementById('search').value;
        filterTable(searchVal);
    }

    // Search Logic
    document.getElementById('search').addEventListener('input', function() {
        filterTable(this.value);
    });

    function filterTable(filterText) {
        filterText = filterText.toLowerCase();
        // Get active tab table
        const activeContent = document.querySelector('.switch_content.active');
        if(!activeContent) return;

        const rows = activeContent.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filterText) ? '' : 'none';
        });
    }
</script>
</body>
</html>
<?php $conn->close(); ?>