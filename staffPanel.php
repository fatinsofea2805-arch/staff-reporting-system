<?php
// Start the session
session_start();

// Set the timezone if it's not set
date_default_timezone_set('Asia/Kuala_Lumpur'); // Change to your time zone if needed

// Include the database connection
include('connection.php');

// Validate session and STAFF_ID
if (!isset($_SESSION['STAFF_ID'])) {
    header("Location: staff_login.php?error=Session expired. Please log in again.");
    exit();
}

// Retrieve STAFF_ID from the session
$STAFF_ID = $_SESSION['STAFF_ID'];

// Validate STAFF_ID format
if (empty($STAFF_ID) || !preg_match('/^[A-Za-z0-9]{8}$/', $STAFF_ID)) {
    header("Location: staff_login.php?error=Invalid STAFF_ID retrieved from session. Please log in again.");
    exit();
}

// Validate the database connection
if (!$dbCon) {
    die("Database connection failed. Please try again later.");
}

// Fetch the staff name from the database
$staff_query = $dbCon->prepare("SELECT STAFF_NAME FROM staff WHERE STAFF_ID = ?");
db_bind($staff_query, "s", $STAFF_ID);
$staff_query->execute();
[$STAFF_NAME] = $staff_query->fetch(PDO::FETCH_NUM) ?: [null];
$staff_query->closeCursor();

// Check if the staff name was retrieved
if (empty($STAFF_NAME)) {
    // Handle the case where the staff name is not found
    $STAFF_NAME = "Unknown Staff";
}

// Get the current year and date
$current_year = date('Y');
$current_date = date('Y-m-d');

// Check if today's submission count is already stored in session
if (!isset($_SESSION['today_submission_date']) || $_SESSION['today_submission_date'] !== $current_date) {
    // Reset the submission counts if the date has changed (midnight reset)
    $_SESSION['today_submission_date'] = $current_date; // Update to today's date
    $_SESSION['today_leave_count'] = 0; // Reset the leave count
    $_SESSION['today_lateness_count'] = 0; // Reset the lateness count
}

// Query to calculate total emergency leave submissions for the current year
$leave_query = $dbCon->prepare("SELECT COUNT(*) FROM emergency WHERE STAFF_ID = ? AND CAST(strftime('%Y', LEAVE_DATE) AS INTEGER) = ?");
db_bind($leave_query, "si", $STAFF_ID, $current_year);
$leave_query->execute();
[$leave_count] = $leave_query->fetch(PDO::FETCH_NUM) ?: [null];
$leave_query->closeCursor();

// Query to calculate total lateness submissions for the current year
$lateness_query = $dbCon->prepare("SELECT COUNT(*) FROM Lateness WHERE STAFF_ID = ? AND CAST(strftime('%Y', LATENESS_DATE) AS INTEGER) = ?");
db_bind($lateness_query, "si", $STAFF_ID, $current_year);
$lateness_query->execute();
[$lateness_count] = $lateness_query->fetch(PDO::FETCH_NUM) ?: [null];
$lateness_query->closeCursor();

// Query to fetch the count of upcoming holidays (holidays that have not passed yet)
$holiday_query = $dbCon->prepare("SELECT COUNT(*) FROM HOLIDAY WHERE START_DATE > ? AND CAST(strftime('%Y', START_DATE) AS INTEGER) = ?");
db_bind($holiday_query, "si", $current_date, $current_year);
$holiday_query->execute();
[$upcoming_holiday_count] = $holiday_query->fetch(PDO::FETCH_NUM) ?: [null];
$holiday_query->closeCursor();

// Query to calculate total emergency leave submissions for today
$today_leave_query = $dbCon->prepare("SELECT COUNT(*) FROM emergency WHERE STAFF_ID = ? AND DATE(LEAVE_DATE) = ?");
db_bind($today_leave_query, "ss", $STAFF_ID, $current_date);
$today_leave_query->execute();
[$today_leave_count] = $today_leave_query->fetch(PDO::FETCH_NUM) ?: [null];
$today_leave_query->closeCursor();

// Query to calculate total lateness submissions for today
$today_lateness_query = $dbCon->prepare("SELECT COUNT(*) FROM Lateness WHERE STAFF_ID = ? AND DATE(LATENESS_DATE) = ?");
db_bind($today_lateness_query, "ss", $STAFF_ID, $current_date);
$today_lateness_query->execute();
[$today_lateness_count] = $today_lateness_query->fetch(PDO::FETCH_NUM) ?: [null];
$today_lateness_query->closeCursor();

// Update session counts for today's submissions
$_SESSION['today_leave_count'] = $today_leave_count;
$_SESSION['today_lateness_count'] = $today_lateness_count;

// Calculate the total applications submitted today (lateness + emergency leave)
$total_today_submissions = $_SESSION['today_leave_count'] + $_SESSION['today_lateness_count'];

// Close the database connection
$dbCon = null;
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Panel</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />

    <!-- Add Font Awesome (if not already included) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="staffPanel.css">
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
    outline: 4px solid #7494ec; /* Add outline color */
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
    <a href="staffPanel.php" class="sidebar-link">
        <i class="fas fa-chart-line"></i> <!-- Chart icon for Dashboard -->
        <span>Dashboard</span>
    </a>
</li>

                <li class="sidebar-item">
                    <a href="profileStaff.php" class="sidebar-link">
                        <i class="lni lni-user"></i>
                        <span>Profile</span>
                    </a>
                </li>

                <!-- Lateness Section -->
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#lateness" aria-expanded="false" aria-controls="lateness">
                        <i class="bx bx-file"></i>
                        <span>Report</span>
                    </a>
                    <ul id="lateness" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="lateness.php" class="sidebar-link">Lateness</a>
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
                                data-bs-target="#leave-types" aria-expanded="false" aria-controls="leave-types">
                                Leave Categories
                            </a>
                            <ul id="leave-types" class="sidebar-dropdown list-unstyled collapse">
                                <li class="sidebar-item">
                                    <a href="emergency.php" class="sidebar-link">Emergency Leave</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="staffHoliday.php" class="sidebar-link">Holiday</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#myLog" aria-expanded="false" aria-controls="myLog">
                        <i class="bx bx-history"></i>
                        <span>My Log</span>
                    </a>
                    <ul id="myLog" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="viewEmergency.php" class="sidebar-link">Emergency Log</a>
                        </li>
                        <li class="sidebar-item">
                            <a href="viewLateness.php" class="sidebar-link">Lateness Log</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="homepage.html" class="sidebar-link">
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
        <!-- Welcome Section -->
        <div class="mb-3 text-center welcome-section">
    <p class="mb-0">Welcome, <?php echo htmlspecialchars($STAFF_NAME); ?>!</p>
    <p>Here's a quick summary of your dashboard.</p>
</div>

        <div class="row">
            <!-- Remaining Leave Submissions -->
            <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">Total Lateness Submissions</div>
                                <div class="card-text"><?php echo $lateness_count; ?> <span></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">Total Emergency Leave Submissions</div>
                                <div class="card-text"><?php echo $leave_count; ?> <span></span></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="card-title">Total Applications Submitted Today</div>
            <div class="card-text"><?php echo $total_today_submissions; ?> <span></span></div>
        </div>
    </div>
</div>

        <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                            <div class="card-title">Upcoming Holiday</div>
                            <div class="card-text"><?php echo $upcoming_holiday_count; ?> <span></span></div>

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
