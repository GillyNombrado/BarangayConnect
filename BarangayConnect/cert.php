<?php
session_start();

$conn = new mysqli("localhost", "root", "", "bcdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query for all requests
$sql_all = "SELECT c.requestID, ct.cert_typeID, c.purpose, c.status, c.date_requested FROM cert_requests c INNER JOIN cert_type ct ON c.cert_typeID = ct.cert_typeID";
$result_all = $conn->query($sql_all);

// Query for pending requests
$sql_pending = "SELECT c.requestID, ct.cert_typeID, c.purpose, c.status, c.date_requested FROM cert_requests c INNER JOIN cert_type ct ON c.cert_typeID = ct.cert_typeID WHERE c.status = 'Pending'";
$result_pending = $conn->query($sql_pending);

// Query for approved requests
$sql_approved = "SELECT c.requestID, ct.cert_typeID, c.purpose, c.status, c.date_requested FROM cert_requests c INNER JOIN cert_type ct ON c.cert_typeID = ct.cert_typeID WHERE c.status = 'Approved'";
$result_approved = $conn->query($sql_approved);

// Query for denied requests
$sql_denied = "SELECT c.requestID, ct.cert_typeID, c.purpose, c.status, c.date_requested FROM cert_requests c INNER JOIN cert_type ct ON c.cert_typeID = ct.cert_typeID WHERE c.status = 'Denied'";
$result_denied = $conn->query($sql_denied);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Requests</title>
    <link rel="stylesheet" href="sidenav.css">
    <link rel="stylesheet" href="cert.css">
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
                        <li class="navlist_item"><a href="cert.php" class="navlink active">Certificate Request</a></li>
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
            <!-- CERTIFICATE REQUESTS  -->
            <div class="cert_container">
                <div class="cert_wrapper">
                    <div class="cert_title">
                        <h1>Certificate Requests</h1>
                    </div>
                    
                    <!-- CONTROLS: TABS & SEARCH -->
                    <div class="controls">
                        <div class="tab-buttons">
                            <button class="switch_button active" onclick="showTab('all')">All Requests</button>
                            <button class="switch_button" onclick="showTab('pending')">Pending</button>
                            <button class="switch_button" onclick="showTab('approved')">Approved</button>
                            <button class="switch_button" onclick="showTab('denied')">Denied</button>
                        </div>
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="search" name="search" id="search" placeholder="Search Requests...">
                        </div>
                    </div>
                    
                    <!-- Table 1: All Requests -->
                    <div id="all" class="switch_content active">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Request Info</th>
                                        <th>Cert Type ID</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                        <th>Date Requested</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_all && $result_all->num_rows > 0) {
                                        while ($row = $result_all->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$row['requestID']}</td>";
                                            echo "<td>{$row['cert_typeID']}</td>";
                                            echo "<td>{$row['purpose']}</td>";
                                            echo "<td>{$row['status']}</td>";
                                            echo "<td>{$row['date_requested']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No requests found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Table 2: Pending Requests -->
                    <div id="pending" class="switch_content">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Request Info</th>
                                        <th>Cert Type ID</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                        <th>Date Requested</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_pending && $result_pending->num_rows > 0) {
                                        while ($row = $result_pending->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$row['requestID']}</td>";
                                            echo "<td>{$row['cert_typeID']}</td>";
                                            echo "<td>{$row['purpose']}</td>";
                                            echo "<td>{$row['status']}</td>";
                                            echo "<td>{$row['date_requested']}</td>";
                                            echo "<td class='actions'>
                                                <button class='action-btn edit-btn' onclick=\"window.location.href='accept.php?id={$row['requestID']}';\"><i class='fas fa-check'></i> Accept</button>
                                                <button class='action-btn delete-btn' onclick=\"deleteUser({$row['requestID']});\"><i class='fas fa-times'></i> Deny</button>
                                                </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No pending requests found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Table 3: Approved Requests -->
                    <div id="approved" class="switch_content">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Request Info</th>
                                        <th>Cert Type ID</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                        <th>Date Requested</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_approved && $result_approved->num_rows > 0) {
                                        while ($row = $result_approved->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$row['requestID']}</td>";
                                            echo "<td>{$row['cert_typeID']}</td>";
                                            echo "<td>{$row['purpose']}</td>";
                                            echo "<td>{$row['status']}</td>";
                                            echo "<td>{$row['date_requested']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No approved requests found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Table 4: Denied Requests -->
                    <div id="denied" class="switch_content">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Cert Type ID</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                        <th>Date Requested</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_denied && $result_denied->num_rows > 0) {
                                        while ($row = $result_denied->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$row['requestID']}</td>";
                                            echo "<td>{$row['cert_typeID']}</td>";
                                            echo "<td>{$row['purpose']}</td>";
                                            echo "<td>{$row['status']}</td>";
                                            echo "<td>{$row['date_requested']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No denied requests found.</td></tr>";
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
    // Tab Switching Logic
    function showTab(tabId) {
        const contents = document.querySelectorAll('.switch_content');
        contents.forEach(content => content.classList.remove('active'));

        const buttons = document.querySelectorAll('.switch_button');
        buttons.forEach(button => button.classList.remove('active'));

        document.getElementById(tabId).classList.add('active');
        event.currentTarget.classList.add('active');
        
        // Re-trigger search on tab switch to filter the newly visible table
        const searchVal = document.getElementById('search').value;
        filterTable(searchVal);
    }

    // Search Functionality
    document.getElementById('search').addEventListener('input', function() {
        filterTable(this.value);
    });

    function filterTable(filterText) {
        filterText = filterText.toLowerCase();
        // Get the active table container
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