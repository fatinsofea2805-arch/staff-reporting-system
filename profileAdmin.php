<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Information</title>
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
                        <i class="bx bx-file"></i>
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
                                data-bs-target="#leave-categories" aria-expanded="false"
                                aria-controls="leave-categories">
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
                                data-bs-target="#holiday-categories" aria-expanded="false"
                                aria-controls="holiday-categories">
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
                    <form class="form-inline d-flex" method="GET">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search"
                            aria-label="Search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </form>
                </div>
            </nav>

            <?php
            // Include the database connection
            include('connection.php');

            // Check if the database connection is successful
            if (!$dbCon) {
                die("Connection failed: " . 'SQLite connection unavailable');
            }

            // Initialize an empty search query
            $searchQuery = "";

            // Check if the search form was submitted
            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                // Get the search term from the form
                $searchTerm = isset($_GET["search"]) ? db_escape($dbCon, $_GET["search"]) : "";

                // Build the search query
                $searchQuery = " WHERE (STAFF_NAME LIKE '%" . $searchTerm . "%' OR 
                                 DEPARTMENT LIKE '%" . $searchTerm . "%' OR
                                 UNIT LIKE '%" . $searchTerm . "%' OR
                                 POSITION LIKE '%" . $searchTerm . "%' OR
                                 PHONE_NUM LIKE '%" . $searchTerm . "%' OR
                                 STAFF_EMAIL LIKE '%" . $searchTerm . "%')";
            }

            // Fetch data from the staff table, applying the search query
            $sql = "SELECT STAFF_ID, STAFF_NAME, DEPARTMENT, UNIT, POSITION, PHONE_NUM, STAFF_EMAIL, PROFILE_PIC FROM staff" . $searchQuery . " ORDER BY STAFF_NAME ASC";
            $result = db_query($dbCon, $sql);

            // Check if the SQL query is successful
            if (!$result) {
                die("Error executing query: " . db_error($dbCon));
            }
            ?>

            <main class="content px-3 py-4">
                <div class="container-fluid">
                    <h3 class="mb-4 text-center">Staff List</h3>
                    <p class="text-muted text-center">Below is the list of staff along with their details.</p>
                    <div class="table-container shadow-lg p-4 rounded"
                        style="max-width: 1200px; margin: 0 auto;">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered"
                                style="width: 100%; text-align: center;">
                                <thead class="thead-dark">
                                    <tr style="background-color: #000000; color: white;">
                                        <th scope="col">#</th>
                                        <th id="staff_name" scope="col">Name</th>
                                        <th id="department" scope="col">Department</th>
                                        <th id="unit" scope="col">Unit</th>
                                        <th id="position" scope="col">Position</th>
                                        <th id="phone_num" scope="col">Phone Number</th>
                                        <th id="staff_email" scope="col">Email</th>
                                        <th id="profile_pic" scope="col">Profile</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        $counter = 1;
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><?php echo htmlspecialchars($row["STAFF_NAME"], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(str_replace('_', ' ', $row["DEPARTMENT"]), ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row["UNIT"], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row["POSITION"], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row["PHONE_NUM"], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row["STAFF_EMAIL"], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <?php
                                            if ($row["PROFILE_PIC"]) {
                                                echo "<img src='" . htmlspecialchars($row["PROFILE_PIC"], ENT_QUOTES, 'UTF-8') . "' alt='Profile Picture' style='width: 50px; height: 50px; border-radius: 50%;'>";
                                            } else {
                                                echo "No Image";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='8'>No staff found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <?php
            // Close the database connection
            $dbCon = null;
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="script.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hamBurger = document.querySelector(".toggle-btn");
            if (hamBurger) {
                hamBurger.addEventListener("click", function() {
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