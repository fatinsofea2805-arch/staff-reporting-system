<?php
// Start the session at the very beginning of the file
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lateness Report Submission</title>
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
                    <form class="form-inline d-flex" method="GET" action="viewLateness.php">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search"
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </form>
                </div>
            </nav>
            <?php
            // Include the database connection
            include('connection.php');

            // Validate session and STAFF_ID
            if (!isset($_SESSION['STAFF_ID'])) {
                echo "Session expired. Please log in again.";
                exit();
            }

            $STAFF_ID = $_SESSION['STAFF_ID'];

            // Validate the database connection
            if (!$dbCon) {
                die("Connection failed: " . 'SQLite connection unavailable');
            }

            // Retrieve search term from the GET request (if any)
            $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

            // Sanitize search input (trim spaces, etc.)
            $searchTerm = trim($searchTerm);

            // Prepare the statement. Use LOWER() for case-insensitive comparison of status.
            $stmt = $dbCon->prepare("SELECT LATENESS_TIME, LATENESS_DATE, LATENESS_REASON, FILE_NAME, LATENESS_STATUS, LATENESS_ID
                                    FROM Lateness
                                    WHERE STAFF_ID = ? 
                                    AND (LOWER(LATENESS_TIME) LIKE LOWER(?) OR LOWER(LATENESS_DATE) LIKE LOWER(?) OR LOWER(LATENESS_REASON) LIKE LOWER(?) OR LOWER(LATENESS_STATUS) = LOWER(?))
                                    ORDER BY LATENESS_DATE ASC, LATENESS_TIME ASC");

            if ($stmt) {
                // Bind parameters. Case-insensitive comparison for all fields.
                $likeTerm = '%' . $searchTerm . '%';
                $statusTerm = $searchTerm;

                db_bind($stmt, "sssss", $STAFF_ID, $likeTerm, $likeTerm, $likeTerm, $statusTerm);

                $stmt->execute();
                $result = new DatabaseRows($stmt);

                if (!$result) {
                    die("Error executing query: " . db_error($stmt));
                }
            } else {
                die("Error preparing statement: " . db_error($dbCon));
            }
            ?>

            <main class="content px-3 py-4">
                <div class="container-fluid">
                    <h3 class="mb-4 text-center">My Lateness Reports</h3>
                    <p class="text-muted text-center">Below are your lateness reports and their details.</p>
                    <div class="table-container shadow-lg p-4 rounded"
                        style="max-width: 1800px; margin: 0 auto;">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered"
                                style="width: 100%; text-align: center;">
                                <thead class="thead-dark">
                                    <tr style="background-color: #000000; color: white;">
                                        <th scope="col">#</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Time</th>
                                        <th scope="col">Reason</th>
                                        <th scope="col">File</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        $counter = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            $latenessTime = $row["LATENESS_TIME"];
                                            $latenessDate = date("Y-m-d", strtotime($row["LATENESS_DATE"]));
                                    ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><?php echo htmlspecialchars($latenessDate, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($latenessTime, ENT_QUOTES, 'UTF-8'); ?></td>
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
                                            <a href="editLateness.php?id=<?php echo htmlspecialchars($row['LATENESS_ID'], ENT_QUOTES, 'UTF-8'); ?>"
                                                class="text-success" title="Edit" style="margin-right: 10px">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="deleteLateness.php?id=<?php echo htmlspecialchars($row['LATENESS_ID'], ENT_QUOTES, 'UTF-8'); ?>"
                                                class="text-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this lateness report?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>No lateness records found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

            <?php
            // Close the statement and database connection
            $stmt->closeCursor();
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