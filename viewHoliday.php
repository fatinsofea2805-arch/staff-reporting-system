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
    <title>Admin Holiday</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
        <form class="form-inline d-flex" method="GET" action="viewHoliday.php">
            <input class="form-control me-2" type="search" name="search" placeholder="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>
</nav>

<main class="content px-3 py-4">
                <div class="container-fluid">
                    <!-- Heading -->
                    <h3 class="mb-4 text-center">Holiday List</h3>
                    <p class="text-muted text-center">Below is the list of holidays along with their details.</p>

                    <!-- Table Container -->
                    <div class="table-container shadow-lg p-4 rounded" style="max-width: 1200px; margin: 0 auto;">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" style="width: 100%; text-align: center;">
                                <thead class="thead-dark">
                                    <tr style="background-color: #000000; color: white;">
                                        <th scope="col">#</th>
                                        <th scope="col">Holiday Name</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Start Date</th>
                                        <th scope="col">End Date</th>
                                        <th scope="col">Duration</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    // Counter variable to display incremental numbers
                                    $counter = 1;
                                    // Output data of each row
                                    while ($row = $result->fetch_assoc()) {
                                        // Format the HOLIDAY_TIME if necessary
                                        $formattedTime = date('Y-m-d H:i:s', strtotime($row["HOLIDAY_TIME"]));
                                ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td> <!-- Incremental number -->
                                        <td><?php echo htmlspecialchars($row["HOLIDAY_NAME"], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($formattedTime, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row["START_DATE"], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row["END_DATE"], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo (int)$row["HOLIDAY_DURATION"]; ?> days</td>
                                        <td style="text-align: center;">
                                            <a href="editHoliday.php?id=<?php echo htmlspecialchars($row['HOLIDAY_ID'], ENT_QUOTES, 'UTF-8'); ?>" class="text-success" title="Edit" style="margin-right: 15px;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="deleteHoliday.php?id=<?php echo htmlspecialchars($row['HOLIDAY_ID'], ENT_QUOTES, 'UTF-8'); ?>" class="text-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this holiday?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No holidays found</td></tr>";
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

        function editHoliday(id) {
    alert(`Edit holiday with ID: ${id}`);
    // Add logic for editing the holiday
}

function deleteHoliday(id) {
    if (confirm("Are you sure you want to delete this holiday?")) {
        alert(`Deleted holiday with ID: ${id}`);
        // Add logic for deleting the holiday
    }
}

        </script>

</body>

</html>