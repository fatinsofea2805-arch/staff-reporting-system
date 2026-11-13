<?php
// Set the default timezone to Asia/Kuala_Lumpur (Malaysia Time)
date_default_timezone_set('Asia/Kuala_Lumpur');

// Database connection (include your connection.php file)
include('connection.php');

// Function to get count of records for a given date and table
function getCountForToday($tableName, $dateColumn) {
    global $dbCon;
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) AS count FROM `$tableName` WHERE DATE(`$dateColumn`) = ?";
    $stmt = $dbCon->prepare($sql);
    if ($stmt) {
        db_bind($stmt, "s", $today);
        $stmt->execute();
        $result = new DatabaseRows($stmt);
        $count = $result->fetch_assoc()['count'];
        $result->free_result();
        $stmt->closeCursor();
        return $count;
    } else {
        error_log("Error preparing count statement: " . db_error($dbCon));
        return 0; // Return 0 if there's an error
    }
}

// Function to get total number of staff
function getTotalStaff() {
    global $dbCon;
    $sql = "SELECT COUNT(*) AS total FROM staff"; // Replace 'staff' with the actual table name for staff
    $result = db_query($dbCon, $sql);
    $totalStaff = $result->fetch_assoc()['total'];
    return $totalStaff;
}

// Function to get total number of holidays
function getTotalHolidays() {
    global $dbCon;
    $sql = "SELECT COUNT(*) AS total FROM holiday"; // Replace 'holiday' with the actual table name for holidays
    $result = db_query($dbCon, $sql);
    $totalHolidays = $result->fetch_assoc()['total'];
    return $totalHolidays;
}

// Start the session to track the date of the last reset
session_start();

// Check if today's date is different from the last reset date
$currentDate = date('Y-m-d');
if (!isset($_SESSION['last_reset_date']) || $_SESSION['last_reset_date'] !== $currentDate) {
    // Reset the counts if the date has changed (midnight reset)
    $_SESSION['lateness_count_today'] = 0;
    $_SESSION['emergency_leave_count_today'] = 0;
    $_SESSION['last_reset_date'] = $currentDate; // Update the last reset date
}

// Get counts for today
$latenessCountToday = getCountForToday('lateness', 'LATENESS_DATE');
$emergencyLeaveCountToday = getCountForToday('emergency', 'LEAVE_DATE'); // Changed table name here

// Store today's counts in session for persistence
$_SESSION['lateness_count_today'] = $latenessCountToday;
$_SESSION['emergency_leave_count_today'] = $emergencyLeaveCountToday;

// Get totals for staff and holidays
$totalStaff = getTotalStaff();
$totalHolidays = getTotalHolidays();

$dbCon = null;
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="adminPanel.css">
</head>

<body>
    <style>  
    .col-md-6 {
        margin-top:15px;
        
    padding-bottom: 40px; /* Adjust padding to create the desired space between the cards */
}

.card {
    margin-left: 20px;
    margin-bottom: 10px; /* Increase bottom margin for spacing */
    border: none;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow for a lifted effect */
    width: 100%;
    max-width: 500px;
    height: auto;
    border-radius: 15px; /* Rounded corners for a soft look */
    background: linear-gradient(135deg, #6e7dff, #6ec1e4); /* Gradient background */
    color: black; /* Black text for better contrast */
    outline: 4px solid  #7494ec; /* Add outline color */
    outline-offset: 4px; /* Space between the card and the outline */
    transition: transform 0.3s ease, box-shadow 0.3s ease, outline 0.3s ease; /* Smooth transition for hover effects */
}

.card:hover {
    transform: translateY(-5px); /* Slight lift effect on hover */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Enhanced shadow on hover */
    outline: 4px solid #0e2238; /* Change outline color on hover */
}

.card-body {
    padding: 30px;
    text-align: center;
}

.card-title {
    font-size: 1.4em;
    color: #333; /* Light text color for the title */
    font-weight: bold;
    margin-bottom: 15px;
}

.card-text {
    font-size: 2em;
    font-weight: bold;
    color: blue; /* Bright blue for the main count to grab attention */
}

.card-text span {
    font-size: 1.5em;
    color: black; /* Black color for the value */
}

.welcome-section 
        {

            font-size:20px;
            margin-bottom: 80px; /* Adds space between the welcome text and the cards */
            font-family: 'Sour Gummy', sans-serif;
            font-weight: bold; /* Makes the text bold */

        }


    </style>
    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="lni lni-grid-alt"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#">TimeTrack</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="adminPanel.php" class="sidebar-link">
                        <i class="fas fa-chart-line"></i> <!-- Chart icon for Dashboard -->
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="profileAdmin.php" class="sidebar-link">
                        <i class="lni lni-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <!-- Lateness Section -->
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#lateness" aria-expanded="false" aria-controls="lateness">
                        <i class="bx bx-file"></i> <!-- Using bx-file for report -->
                        <span>Report</span>
                    </a>
                    <ul id="lateness" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="adminLateness.php" class="sidebar-link">Lateness</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#leave" aria-expanded="false" aria-controls="leave">
                        <i class="lni lni-calendar"></i>
                        <span>Leave</span>
                    </a>
                    <ul id="leave" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                data-bs-target="#leave-categories" aria-expanded="false" aria-controls="leave-categories">
                                Leave Categories
                            </a>
                            <ul id="leave-categories" class="sidebar-dropdown list-unstyled collapse">
                                <li class="sidebar-item">
                                    <a href="adminEmergency.php" class="sidebar-link">Emergency Leave</a>
                                </li>
                            </ul>
                        </li>
                        <!-- Holiday Section -->
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                data-bs-target="#holiday-categories" aria-expanded="false" aria-controls="holiday-categories">
                                Holiday
                            </a>
                            <ul id="holiday-categories" class="sidebar-dropdown list-unstyled collapse">
                                <li class="sidebar-item">
                                    <a href="addHoliday.php" class="sidebar-link">Add Holiday</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="viewHoliday.php" class="sidebar-link">View Holidays</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="adminLogout.php" class="sidebar-link">
                    <i class="lni lni-exit"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <div class="main">
            <nav class="navbar navbar-expand-lg navbar-light bg-white px-4 py-3 rounded shadow">
                <div class="d-flex align-items-center ms-auto">
                    <form class="form-inline d-flex">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </form>
                </div>
            </nav>
            <main class="content px-3 py-4">
                <div class="container-fluid">
                    <div class="mb-3 text-center welcome-section">
                        <!-- Center-aligned Admin Dashboard heading -->
                        <h3 class="fw-bold fs-4 mb-3">Welcome Back Admin !</h3>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Lateness Reports Submitted Today</h5>
                                    <p class="card-text"><?php echo $latenessCountToday; ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Spacer div -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">E.Leave Requests Submitted Today</h5>
                                    <p class="card-text"><?php echo $emergencyLeaveCountToday; ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Additional cards -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Staff</h5>
                                    <p class="card-text"><?php echo $totalStaff; ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Spacer div -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Holidays</h5>
                                    <p class="card-text"><?php echo $totalHolidays; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="script.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const hamBurger = document.querySelector(".toggle-btn");
            if (hamBurger) {
                hamBurger.addEventListener("click", function () {
                    const sidebar = document.querySelector("#sidebar");
                    sidebar.classList.toggle("expand");
                });
            } else {
                console.error("Toggle button not found.");
            }
        });
    </script>

</body>

</html>
