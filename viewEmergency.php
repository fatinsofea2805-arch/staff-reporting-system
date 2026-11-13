<?php
// Include the database connection
include('connection.php');

// Validate session and STAFF_ID
session_start();
if (!isset($_SESSION['STAFF_ID'])) {
    echo "Session expired. Please log in again.";
    exit();
}

// Retrieve STAFF_ID from the session
$STAFF_ID = $_SESSION['STAFF_ID'];

// Validate the database connection
if (!$dbCon) {
    die("Connection failed: " . 'SQLite connection unavailable');
}

// Get the search query if it exists
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Construct the SQL query to search for emergency leave records
$sql = "SELECT E.LEAVE_TIME, E.LEAVE_TYPE, E.LEAVE_DURATION, E.LEAVE_HOURS, E.OTHER_LEAVE_DETAILS, E.FILE_NAME, E.LEAVE_STATUS, E.LEAVE_ID
        FROM Emergency E
        WHERE E.STAFF_ID = ?";

// If there's a search query, add the search condition
if (!empty($searchQuery)) {
    $sql .= " AND (E.LEAVE_TYPE LIKE ? OR E.LEAVE_STATUS LIKE ? OR E.OTHER_LEAVE_DETAILS LIKE ? OR E.LEAVE_DURATION LIKE ? OR E.LEAVE_HOURS LIKE ?)";
}

// Order the records by LEAVE_TIME
$sql .= " ORDER BY E.LEAVE_TIME ASC";

// Prepare the statement
$stmt = $dbCon->prepare($sql);

// Bind the parameters
if (!empty($searchQuery)) {
    $searchTerm = "%" . $searchQuery . "%"; // Add wildcards for partial matching
    db_bind($stmt, "ssssss", $STAFF_ID, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm); // Bind parameters for the query
} else {
    db_bind($stmt, "s", $STAFF_ID); // Bind only STAFF_ID if no search term
}

// Execute the query
$stmt->execute();

// Get the result
$result = new DatabaseRows($stmt);

// Check for errors
if (!$result) {
    die("Error executing query: " . db_error($stmt));
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Leave Submission </title>
    <!-- Add Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="staffPanel.css">
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
                        <i class="bx bx-file"></i> <!-- Using bx-file for report -->
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
    <form class="form-inline d-flex" method="GET" action="viewEmergency.php">
                        <input class="form-control me-2" type="search" placeholder="Search" name="search" aria-label="Search">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </form>

    </div>
</nav>
<main class="content px-3 py-4">
    <div class="container-fluid">
        <!-- Add heading above the table -->
        <h3 class="mb-4 text-center">Emergency Leave Report List</h3> <!-- Center the heading -->
        <p class="text-muted text-center">Below is the list of emergency leave requests along with their details.</p> <!-- Center the text -->

        <!-- Container with shadow under the table -->
        <div class="table-container shadow-lg p-4 rounded" style="max-width: 1200px; margin: 0 auto;"> <!-- Center the table container -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered" style="width: 100%; text-align: center;"> <!-- Center the table content -->
                    <thead class="thead-dark">
                        <tr style="background-color: #000000; color: white;">
                            <th scope="col">#</th> <!-- Number column -->
                            <th scope="col">Time</th> <!-- Time column -->
                            <th scope="col">Type</th> <!-- Leave type column -->
                            <th scope="col">Duration</th> <!-- Duration column -->
                            <th scope="col">Hours</th> <!-- Hours column -->
                            <th scope="col">Other Details</th> <!-- Other leave details column -->
                            <th scope="col">File</th> <!-- File column -->
                            <th scope="col">Status</th> <!-- Status column -->
                            <th scope="col">Actions</th> <!-- Actions column -->
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Counter variable to display incremental numbers
                        $counter = 1;
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            // Format the LEAVE_TIME if necessary
                            $formattedLeaveTime = date('Y-m-d H:i:s', strtotime($row["LEAVE_TIME"]));

                            // Check if the leave duration is "Hourly Leave" or other leave types
                            $leaveDuration = htmlspecialchars($row["LEAVE_DURATION"], ENT_QUOTES, 'UTF-8');
                            $leaveHours = '-'; // Default to '-'

                            // If the leave duration is "Hourly Leave", show "Hourly Leave" in the Duration column and display leave hours in the Hours column
                            if (strtolower(trim($leaveDuration)) === "hourly leave") {
                                $leaveDuration = "Hourly Leave"; // Update to show "Hourly Leave"
                                $leaveHours = htmlspecialchars($row["LEAVE_HOURS"], ENT_QUOTES, 'UTF-8'); // Show leave hours for hourly leave
                            } elseif (strtolower(trim($leaveDuration)) !== "full day" && strtolower(trim($leaveDuration)) !== "half day") {
                                $leaveHours = htmlspecialchars($row["LEAVE_HOURS"], ENT_QUOTES, 'UTF-8'); // Show leave hours for other leave types
                            } else {
                                $leaveHours = '-'; // For full day or half day, no hours are shown
                            }
                    ?>
                        <tr>
                            <td><?php echo $counter++; ?></td> <!-- Display incremental number -->
                            <td><?php echo htmlspecialchars($formattedLeaveTime, ENT_QUOTES, 'UTF-8'); ?></td> <!-- Formatted Leave Time -->
                            <td><?php echo htmlspecialchars($row["LEAVE_TYPE"], ENT_QUOTES, 'UTF-8'); ?></td> <!-- Leave Type -->
                            <td><?php echo $leaveDuration; ?></td> <!-- Leave Duration (Hourly Leave or other types) -->
                            <td><?php echo $leaveHours; ?></td> <!-- Leave Hours (if available or '-') -->
                            <td><?php echo htmlspecialchars($row["OTHER_LEAVE_DETAILS"], ENT_QUOTES, 'UTF-8'); ?></td> <!-- Other Leave Details -->
                            <td>
    <?php 
    if (!empty($row["FILE_NAME"])) {
        // Sanitize the file name for safe output
        $fileName = htmlspecialchars($row["FILE_NAME"], ENT_QUOTES, 'UTF-8');
        // Adjust the file path to match your server's file storage location
        $filePath = "uploads/" . $fileName; 
        echo "<a href='" . $filePath . "' target='_blank' class='text-primary'>View File</a>";
    } else {
        echo "No file";
    }
    ?>
</td>
                            <td><?php echo htmlspecialchars($row["LEAVE_STATUS"], ENT_QUOTES, 'UTF-8'); ?></td> <!-- Leave Status -->
                            <td>
                                <!-- Action icons: Edit and Delete with space between them -->
                                <a href="editEmergency.php?id=<?php echo htmlspecialchars($row['LEAVE_ID'], ENT_QUOTES, 'UTF-8'); ?>" class="text-success" title="Edit" style="margin-right: 10px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="deleteEmergency.php?id=<?php echo $row['LEAVE_ID']; ?>" class="text-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this emergency leave record?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='10'>No records found.</td></tr>"; // Adjust column span
                    }
                    ?>
                    </tbody>
                </table>
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


       
        function editLateness(id) {
        alert(`Edit lateness report with ID: ${id}`);
        // Redirect to the edit page with the ID as a query parameter
        window.location.href = `editLateness.php?id=${id}`;
    }

    function deleteLateness(id) {
        if (confirm("Are you sure you want to delete this lateness record?")) {
            alert(`Deleted lateness record with ID: ${id}`);
            // Redirect to the delete page with the ID as a query parameter
            window.location.href = `deleteLateness.php?id=${id}`;
        }
    }
        </script>

</body>

</html>