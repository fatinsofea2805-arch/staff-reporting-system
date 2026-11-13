
<?php
// Include the database connection
include('connection.php'); // Ensure this file defines the $dbCon variable

// Check if the database connection is successful
if (!$dbCon) {
    die("Connection failed: " . 'SQLite connection unavailable');
}

// Get the search term from the URL query string (if available)
$searchTerm = isset($_GET['search']) ? db_escape($dbCon, $_GET['search']) : '';

// Modify the query to filter based on the search term for multiple columns
$sql = "SELECT HOLIDAY_ID, HOLIDAY_NAME, HOLIDAY_TIME, START_DATE, END_DATE, HOLIDAY_DURATION FROM holiday ";
if (!empty($searchTerm)) {
    $sql .= "WHERE HOLIDAY_NAME LIKE '%$searchTerm%' 
              OR HOLIDAY_TIME LIKE '%$searchTerm%' 
              OR START_DATE LIKE '%$searchTerm%' 
              OR END_DATE LIKE '%$searchTerm%' ";

    // Check if HOLIDAY_DURATION is numeric or text and modify the query accordingly
    if (is_numeric($searchTerm)) {
        $sql .= "OR HOLIDAY_DURATION = '$searchTerm' "; // Use exact match for numeric values
    } else {
        $sql .= "OR HOLIDAY_DURATION LIKE '%$searchTerm%' "; // Use LIKE for text
    }
}
$sql .= "ORDER BY START_DATE ASC, END_DATE ASC";

// Execute the query
$result = db_query($dbCon, $sql);

// Check if the SQL query is successful
if (!$result) {
    die("Error executing query: " . db_error($dbCon));
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Holiday</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Add Font Awesome (if not already included) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    <form class="form-inline d-flex" method="GET" action="staffHoliday.php">
            <input class="form-control me-2" type="search" name="search" placeholder="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>
</nav>


<main class="content px-3 py-4">
    <div class="container-fluid">
        <!-- Add heading above the table -->
        <h3 class="mb-4 text-center">Holiday List</h3> <!-- Center the heading -->
        <p class="text-muted text-center">Below is the list of holidays along with their details.</p> <!-- Center the text -->

        <!-- Container with shadow under the table -->
        <div class="table-container shadow-lg p-4 rounded" style="max-width: 1200px; margin: 0 auto;"> <!-- Center the table container -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered" style="width: 100%; text-align: center;"> <!-- Center the table content -->
                    <thead class="thead-dark">
                        <tr style="background-color: #000000; color: white;">
                            <th scope="col">#</th> <!-- Number column -->
                            <th id="holiday_name" scope="col">Holiday Name</th>
                            <th id="holiday_start" scope="col">Start Date</th>
                            <th id="holiday_end" scope="col">End Date</th>
                            <th id="holiday_duration" scope="col">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Counter variable to display incremental numbers
                        $counter = 1;
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?php echo $counter++; ?></td> <!-- Display incremental number -->
                            <td><?php echo htmlspecialchars($row["HOLIDAY_NAME"], ENT_QUOTES, 'UTF-8'); ?></td> <!-- Sanitize output -->
                            <td><?php echo htmlspecialchars($row["START_DATE"], ENT_QUOTES, 'UTF-8'); ?></td> <!-- Sanitize output -->
                            <td><?php echo htmlspecialchars($row["END_DATE"], ENT_QUOTES, 'UTF-8'); ?></td> <!-- Sanitize output -->
                            <td><?php echo (int)$row["HOLIDAY_DURATION"]; ?> days</td> <!-- Cast to int for consistency -->
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='5'>No holidays found</td></tr>";
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
        </script>

</body>

</html>