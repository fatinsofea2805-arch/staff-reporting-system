<?php
// Include the database connection
include('connection.php'); // Ensure this file defines the $dbCon variable

// Set the timezone to Asia/Kuala_Lumpur
date_default_timezone_set('Asia/Kuala_Lumpur');

// Check if the 'id' parameter is provided in the URL
if (isset($_GET['id'])) {
    $HOLIDAY_ID = htmlspecialchars(trim($_GET['id']), ENT_QUOTES, 'UTF-8');

    // Fetch holiday details from the database using the provided ID
    $sql = "SELECT * FROM holiday WHERE HOLIDAY_ID = ?";
    $stmt = $dbCon->prepare($sql);
    if ($stmt === false) {
        die('SQLite prepare failed: ' . db_error($dbCon));
    }

    db_bind($stmt, "s", $HOLIDAY_ID);
    $stmt->execute();
    $result = new DatabaseRows($stmt);

    // Check if the holiday exists
    if ($result->num_rows > 0) {
        $holiday = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('Holiday not found.');
                window.location.href='viewHoliday.php';
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('Invalid holiday ID.');
            window.location.href='viewHoliday.php';
          </script>";
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $HOLIDAY_NAME = htmlspecialchars(trim($_POST['holiday_name']), ENT_QUOTES, 'UTF-8');
    $HOLIDAY_START = htmlspecialchars(trim($_POST['holiday_start']), ENT_QUOTES, 'UTF-8');
    $HOLIDAY_END = htmlspecialchars(trim($_POST['holiday_end']), ENT_QUOTES, 'UTF-8');
    $HOLIDAY_DURATION = (int)$_POST['holiday_duration'];
    $HOLIDAY_TIME = isset($_POST['holiday_time']) && !empty($_POST['holiday_time']) 
        ? htmlspecialchars(trim($_POST['holiday_time']), ENT_QUOTES, 'UTF-8') 
        : date('Y-m-d H:i:s');

    // Validate inputs
    if (empty($HOLIDAY_NAME) || empty($HOLIDAY_START) || empty($HOLIDAY_END) || $HOLIDAY_DURATION <= 0) {
        echo "<script>
                alert('Please fill in all required fields correctly.');
              </script>";
    } elseif (!strtotime($HOLIDAY_START) || !strtotime($HOLIDAY_END)) { // Validate date format
        echo "<script>
                alert('Invalid date format. Please enter valid dates.');
              </script>";
    } elseif (strtotime($HOLIDAY_START) > strtotime($HOLIDAY_END)) { // Check date range
        echo "<script>
                alert('Start date cannot be later than the end date.');
              </script>";
    } else {
        // Escape inputs for SQL query




        $sql = "SELECT * FROM Emergency ORDER BY LEAVE_TIME DESC"; // Most recent leave first

        // Prepare the SQL query to update holiday details
        $stmt = $dbCon->prepare("UPDATE holiday 
             SET HOLIDAY_NAME = ?, START_DATE = ?, END_DATE = ?, HOLIDAY_DURATION = ?, HOLIDAY_TIME = ? 
             WHERE HOLIDAY_ID = ?");
        if ($stmt === false) {
            die('SQLite prepare failed: ' . db_error($dbCon));
        }

        // Bind parameters
        db_bind($stmt, "sssiss", $HOLIDAY_NAME, $HOLIDAY_START, $HOLIDAY_END, $HOLIDAY_DURATION, $HOLIDAY_TIME, $HOLIDAY_ID);

       // Execute and handle the result
if ($stmt->execute()) {
    echo "<script>
            let userChoice = confirm('Holiday updated successfully! Click OK to continue or Cancel to stay on this page.');
            if (userChoice) {
                window.location.href = 'viewHoliday.php';
            }
          </script>";
} else {
    echo "<script>
            alert('Failed to update holiday: " . db_error($stmt) . "');
          </script>";
}


        // Close the statement
        $stmt->closeCursor();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Holiday</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="adminPanel.css">
</head>

<body>
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
                    <a href="viewHoliday" class="sidebar-link">View Holidays</a>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h1 class="fw-bold fs-4 mb-0">Edit Holiday</h1>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="holiday_name" class="form-label">Holiday Name</label>
                                <input type="text" class="form-control" id="holiday_name" name="holiday_name" 
                                       value="<?= htmlspecialchars($holiday['HOLIDAY_NAME']) ?>" required>
                            </div>

                            <!-- Holiday Start Date -->
                            <div class="mb-3">
                                <label for="holiday_start" class="form-label">Start Date:</label>
                                <input type="date" id="holiday_start" name="holiday_start" class="form-control" 
                                       value="<?= htmlspecialchars($holiday['START_DATE']) ?>" required onchange="calculateHolidayDays()">
                            </div>

                            <!-- Holiday End Date -->
                            <div class="mb-3">
                                <label for="holiday_end" class="form-label">End Date:</label>
                                <input type="date" id="holiday_end" name="holiday_end" class="form-control" 
                                       value="<?= htmlspecialchars($holiday['END_DATE']) ?>" required onchange="calculateHolidayDays()">
                            </div>

                            <!-- Duration Calculation -->
                            <div class="mb-3">
                                <label for="holiday_duration" class="form-label">Holiday Duration (Days):</label>
                                <input type="text" id="holiday_duration" name="holiday_duration" class="form-control" 
                                       value="<?= htmlspecialchars($holiday['HOLIDAY_DURATION']) ?>" readonly>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success me-2">Submit</button>
                                <button type="button" class="btn btn-danger" onclick="resetFormAndRedirect()">Cancel</button>
                            </div>
                        </form>
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

    function resetFormAndRedirect() {
        // Reset the form
        const form = document.querySelector('form'); // Ensure the form tag exists
        if (form) {
            form.reset(); // Clears all the fields in the form
        }

        // Redirect to the holiday list page
        window.location.href = 'viewHoliday.php';
    }

    function calculateHolidayDays() {
        const startDate = document.getElementById('holiday_start').value;
        const endDate = document.getElementById('holiday_end').value;

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);

            const timeDiff = end - start;
            const daysDiff = timeDiff / (1000 * 3600 * 24); 

            if (daysDiff >= 0) {
                document.getElementById('holiday_duration').value = daysDiff + 1; 
            } else {
                alert("End date cannot be before start date.");
                document.getElementById('holiday_duration').value = '';
            }
        }
    }



        </script>

</body>

</html>