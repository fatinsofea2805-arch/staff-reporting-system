<?php
// Include the database connection
include('connection.php'); // Ensure this file defines the $dbCon variable

// Check if the database connection is successful
if (!$dbCon) {
    die("Connection failed: " . 'SQLite connection unavailable');
}

// Initialize $searchQuery to an empty string in case it's not set
$searchQuery = isset($_GET['search_query']) ? db_escape($dbCon, $_GET['search_query']) : '';
$sql = "SELECT L.LATENESS_TIME, L.LATENESS_DATE, L.LATENESS_REASON, L.FILE_NAME, L.LATENESS_STATUS, L.LATENESS_ID, S.STAFF_NAME
        FROM Lateness L
        JOIN Staff S ON L.STAFF_ID = S.STAFF_ID
        WHERE S.STAFF_NAME LIKE '%$searchQuery%' 
           OR L.LATENESS_REASON LIKE '%$searchQuery%'
           OR strftime('%Y-%m-%d', L.LATENESS_DATE) LIKE '%$searchQuery%' 
            OR LOWER(TRIM(L.LATENESS_STATUS)) LIKE LOWER('%$searchQuery%')
        ORDER BY L.LATENESS_DATE ASC, L.LATENESS_TIME ASC";



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
    <title>Admin Lateness</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
    <form class="form-inline d-flex" method="GET" action="adminLateness.php">
                        <input class="form-control me-2" type="search" name="search_query" placeholder="Search" aria-label="Search" value="<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </form>
    </div>
</nav>
<main class="content px-3 py-4">
    <div class="container-fluid">
        <h3 class="mb-4 text-center">Lateness Report List</h3>
        <p class="text-muted text-center">Below is the list of lateness reports along with their details.</p>

        <!-- Table Container -->
        <div class="table-container shadow-lg p-4 rounded" style="max-width: 1200px; margin: 0 auto;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" style="width: 100%; text-align: center;">
                    <thead class="thead-dark">
                        <tr style="background-color: #000000; color: white;">
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Date</th>
                            <th scope="col">Time</th>
                            <th scope="col">Reason</th>
                            <th scope="col">File Name</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th> <!-- New Action column -->
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Counter variable to display incremental numbers
                        $counter = 1;
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            // Format the LATENESS_TIME if necessary
                            $formattedLatenessTime = date('Y-m-d H:i:s', strtotime($row["LATENESS_TIME"]));
                    ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo htmlspecialchars($row["STAFF_NAME"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row["LATENESS_DATE"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($formattedLatenessTime, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row["LATENESS_REASON"], ENT_QUOTES, 'UTF-8'); ?></td>
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
                            <td><?php echo htmlspecialchars($row["LATENESS_STATUS"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <form action="update_status.php" method="POST">
                                    <input type="hidden" name="lateness_id" value="<?php echo $row['LATENESS_ID']; ?>">
                                    <button type="submit" class="btn btn-success" name="update_status" value="seen">Mark as Seen</button>
                                </form>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='8'>No records found.</td></tr>"; // Adjusted column span to 8
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