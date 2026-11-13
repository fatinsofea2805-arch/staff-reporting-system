<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Emergency</title>
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
    <form class="form-inline d-flex" method="GET" action="adminEmergency.php">
                        <input class="form-control me-2" type="search" placeholder="Search" name="search" aria-label="Search">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </form>

    </div>
</nav>   
<?php
include('connection.php');

if (!$dbCon) {
    die("Connection failed: " . 'SQLite connection unavailable');
}

$searchTerm = isset($_GET['search']) ? db_escape($dbCon, $_GET['search']) : '';
// Format the search term if it's a valid hour string (H format)
if (!empty($searchTerm) && strtotime($searchTerm)) {
    $formattedSearchTerm = date('H', strtotime($searchTerm)); // Format the search term to H (hour only)
} else {
    $formattedSearchTerm = $searchTerm;
}
$sql = "SELECT E.LEAVE_TIME, E.LEAVE_TYPE, E.LEAVE_DURATION, E.LEAVE_HOURS, E.OTHER_LEAVE_DETAILS, E.FILE_NAME, E.LEAVE_STATUS, E.LEAVE_ID, S.STAFF_NAME
        FROM Emergency E
        JOIN Staff S ON E.STAFF_ID = S.STAFF_ID ";

if (!empty($searchTerm)) {
    $sql .= "WHERE E.LEAVE_TYPE LIKE '%$searchTerm%' 
              OR E.LEAVE_DURATION LIKE '%$searchTerm%' 
              OR E.LEAVE_STATUS LIKE '%$searchTerm%' 
               OR strftime('%H', E.LEAVE_TIME) LIKE '%$formattedSearchTerm%'  -- Ensure we only compare the hour (H)
              OR E.LEAVE_HOURS LIKE '%$searchTerm%' 
              OR E.OTHER_LEAVE_DETAILS LIKE '%$searchTerm%' 
              OR S.STAFF_NAME LIKE '%$searchTerm%' ";
}

$sql .= "ORDER BY E.LEAVE_TIME ASC";

$result = db_query($dbCon, $sql);

if (!$result) {
    die("Error executing query: " . db_error($dbCon));
}
?>

<main class="content px-3 py-4">
    <div class="container-fluid">
        <h3 class="mb-4 text-center">Emergency Leave Report List</h3>
        <p class="text-muted text-center">Below is the list of emergency leave requests along with their details.</p>

        <div class="table-container shadow-lg p-4 rounded" style="max-width: 1200px; margin: 0 auto;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" style="width: 100%; text-align: center;">
                    <thead class="thead-dark">
                        <tr style="background-color: #000000; color: white;">
                            <th scope="col">#</th>
                            <th scope="col">Staff Name</th>
                            <th scope="col">Leave Time</th>
                            <th scope="col">Type</th>
                            <th scope="col">Duration</th>
                            <th scope="col">Hours</th>
                            <th scope="col">Other Details</th>
                            <th scope="col">File</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $counter = 1;
                        while ($row = $result->fetch_assoc()) {
                            $leaveTime = $row["LEAVE_TIME"];
                            if ($leaveTime && strtotime($leaveTime)) {
                                $formattedLeaveTime = date('Y-m-d H:i:s', strtotime($leaveTime));
                            } else {
                                $formattedLeaveTime = 'Invalid Date';
                            }

                            // Check if the leave duration is 'Hourly' and display leave hours accordingly
                            $leaveHours = (strtolower(trim($row["LEAVE_DURATION"])) === "hourly" && !empty($row["LEAVE_HOURS"])) ? htmlspecialchars($row["LEAVE_HOURS"], ENT_QUOTES, 'UTF-8') : '-';

                            // Debugging output to check leave hours
                            // echo "Leave Duration: " . $row["LEAVE_DURATION"] . "<br>";
                            // echo "Leave Hours: " . $row["LEAVE_HOURS"] . "<br>";

                    ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo htmlspecialchars($row["STAFF_NAME"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($formattedLeaveTime, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row["LEAVE_TYPE"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row["LEAVE_DURATION"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo $leaveHours; ?></td>
                            <td><?php echo htmlspecialchars($row["OTHER_LEAVE_DETAILS"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php 
                                if (!empty($row["FILE_NAME"])) {
                                    $fileName = htmlspecialchars($row["FILE_NAME"], ENT_QUOTES, 'UTF-8');
                                    $filePath = "uploads/" . $fileName;
                                    echo "<a href='" . $filePath . "' target='_blank' class='text-primary'>View File</a>";
                                } else {
                                    echo "No file";
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row["LEAVE_STATUS"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <form action="update_statusEmergency.php" method="POST">
                                    <input type="hidden" name="leave_id" value="<?php echo $row['LEAVE_ID']; ?>">
                                    <button type="submit" class="btn btn-success btn-sm" name="update_status" value="seen">Mark as Seen</button>
                                </form>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='10'>No records found.</td></tr>"; 
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php
$dbCon = null;
?>

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