<?php
// Start the session at the very beginning of the file
session_start();

// Check if the STAFF_ID session is set
if (!isset($_SESSION['STAFF_ID'])) {
    die("No STAFF_ID found in session.");
}

// Debugging: Check if the staff ID is stored in the session
$staff_id = $_SESSION['STAFF_ID'];

// Include the database connection
include('connection.php'); // Ensure this file defines the $dbCon variable

// Check if the database connection is successful
if (!$dbCon) {
    die("Connection failed: " . 'SQLite connection unavailable');
}

// Fetch the specific staff data using the staff_id from the session
$sql = "SELECT STAFF_ID, STAFF_NAME, DEPARTMENT, UNIT, POSITION, PHONE_NUM, STAFF_EMAIL, PROFILE_PIC 
        FROM staff WHERE STAFF_ID = ?";
$stmt = $dbCon->prepare($sql);
db_bind($stmt, "s", $staff_id); // Assuming STAFF_ID is stored as a string
$stmt->execute();
$result = new DatabaseRows($stmt);

// Check if the staff record exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $staff_name = $row['STAFF_NAME'];
    $department = $row['DEPARTMENT'];
    $unit = $row['UNIT'];
    $position = $row['POSITION'];
    $phone_number = $row['PHONE_NUM'];
    $staff_email = $row['STAFF_EMAIL'];
    $profile_pic = $row['PROFILE_PIC'] ?: "https://www.w3schools.com/w3images/avatar2.png";  // Default if no pic
} else {
    echo "Staff profile not found.";
    exit;
}

// Close the prepared statement
$stmt->closeCursor();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile</title>
    <!-- Add Font Awesome (if not already included) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="profile.css">
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
        <form class="form-inline d-flex">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>
</nav>            <main class="content px-3 py-4">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card shadow">
                                <div class="card-header bg-primary text-white">
                                    <h1 class="fw-bold fs-4 mb-0">Staff Profile</h1>
                                </div>
                                <div class="card-body">
                                    <!-- Profile Picture -->
                                    <div class="text-center mb-4">
                                        <img src="<?php echo htmlspecialchars($profile_pic, ENT_QUOTES, 'UTF-8'); ?>" 
                                             alt="Profile Picture" 
                                             class="rounded-circle" 
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    </div>

                                    <!-- Staff Information -->
                                    <div class="form-group">
                                        <div class="sub-heading mb-4">
                                            <h3 class="fw-bold fs-5">Staff Information</h3>
                                        </div>

                                        <!-- Staff Name -->
                                        <div class="mb-3">
                                            <label for="staff_name" class="form-label">Name:</label>
                                            <p id="staff_name" class="form-control-plaintext"><?php echo htmlspecialchars($staff_name, ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>

                                        <!-- Department -->
                                        <div class="mb-3">
                                            <label for="department" class="form-label">Department:</label>
                                            <p id="department" class="form-control-plaintext"><?php echo htmlspecialchars(str_replace('_', ' ', $department), ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>

                                        <!-- Unit -->
                                        <div class="mb-3">
                                            <label for="unit" class="form-label">Unit:</label>
                                            <p id="unit" class="form-control-plaintext"><?php echo htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>

                                        <!-- Position -->
                                        <div class="mb-3">
                                            <label for="position" class="form-label">Position:</label>
                                            <p id="position" class="form-control-plaintext"><?php echo htmlspecialchars($position, ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>

                                        <!-- Phone Number -->
                                        <div class="mb-3">
                                            <label for="phone_number" class="form-label">Phone Number:</label>
                                            <p id="phone_number" class="form-control-plaintext"><?php echo htmlspecialchars($phone_number, ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>

                                        <!-- Email -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email:</label>
                                            <p id="email" class="form-control-plaintext"><?php echo htmlspecialchars($staff_email, ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>
                                    </div>

                                    <!-- Edit Profile Button -->
                                    <div class="d-flex justify-content-end mt-4">
                                        <a href="editProfile.php" class="btn btn-primary">Edit Profile</a>
                                    </div>
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