<?php
// Include the database connection
include('connection.php'); // Ensure this file defines the $dbCon variable

// Set the timezone to Asia/Kuala_Lumpur
date_default_timezone_set('Asia/Kuala_Lumpur');

// Default values for file if none is uploaded
$file_name = 'no file';  // Default file name if no file is uploaded
$file_type = 'no file';  // Default file type if no file is uploaded

// Check if the 'id' parameter is provided in the URL
if (isset($_GET['id'])) {
    $LEAVE_ID = htmlspecialchars(trim($_GET['id']), ENT_QUOTES, 'UTF-8');

    // Fetch leave details from the database using the provided ID
    $sql = "SELECT * FROM Emergency WHERE LEAVE_ID = ?";
    $stmt = $dbCon->prepare($sql);
    if ($stmt === false) {
        die('SQLite prepare failed: ' . db_error($dbCon));
    }

    db_bind($stmt, "s", $LEAVE_ID);
    $stmt->execute();
    $result = new DatabaseRows($stmt);

    $sql = "SELECT * FROM Emergency ORDER BY LEAVE_TIME DESC"; // Most recent leave first

    // Check if the emergency leave record exists
    if ($result->num_rows > 0) {
        $leave = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('Leave request not found.');
                window.location.href='viewEmergency.php';
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('Invalid leave ID.');
            window.location.href='viewEmergency.php';
          </script>";
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $LEAVE_TIME = isset($_POST['leave_time']) && !empty($_POST['leave_time']) 
        ? htmlspecialchars(trim($_POST['leave_time']), ENT_QUOTES, 'UTF-8') 
        : date('Y-m-d H:i:s');  // Use current time if leave time is not provided
    $LEAVE_DATE = trim($_POST['leave_date'] ?? $leave['LEAVE_DATE']);
    $LEAVE_TYPE = htmlspecialchars(trim($_POST['leave_type']), ENT_QUOTES, 'UTF-8');
    $OTHER_DETAILS = isset($_POST['other_leave_details']) && !empty($_POST['other_leave_details']) 
        ? htmlspecialchars(trim($_POST['other_leave_details']), ENT_QUOTES, 'UTF-8') 
        : '-'; // Default value for other leave details
    $LEAVE_DURATION = htmlspecialchars(trim($_POST['leave_duration']), ENT_QUOTES, 'UTF-8');
    $LEAVE_HOURS = isset($_POST['leave_hours']) && !empty($_POST['leave_hours']) 
        ? htmlspecialchars(trim($_POST['leave_hours']), ENT_QUOTES, 'UTF-8') 
        : '-'; // Default value for leave hours (empty or - when not selected)

    // If not hourly, set leave hours to '-'
    if ($LEAVE_DURATION != 'Hourly') {
        $LEAVE_HOURS = '-';
    }

    // File upload logic
    $FILE_NAME = $leave['FILE_NAME'];
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/"; // Directory to store uploaded files
        $fileTmpName = $_FILES['file']['tmp_name'];
        $fileName = basename($_FILES['file']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Allowed file extensions (e.g., pdf, jpg, png, docx)
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'docx', 'doc'];

        // Validate file extension
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "<script>alert('Invalid file type. Only PDF, JPG, PNG, DOCX are allowed.');</script>";
        } else {
            // Generate a shorter unique name for the file
            $uniqueFileName = substr(uniqid('', true), 0, 8) . '-' . $fileName; // Limit to 8 characters
            $targetFile = $targetDir . $uniqueFileName;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($fileTmpName, $targetFile)) {
                $FILE_NAME = $uniqueFileName; // Save the file name for database
            } else {
                echo "<script>alert('File upload failed.');</script>";
            }
        }
    }

    // Validate inputs
    if (empty($LEAVE_TYPE) || empty($LEAVE_DURATION)) {
        echo "<script>
                alert('Please fill in all required fields.');
              </script>";
    } else {
        // Escape inputs for SQL query to prevent SQL injection






        // Prepare the SQL query to update emergency leave details
        $stmt = $dbCon->prepare("UPDATE Emergency
             SET LEAVE_DATE = ?, LEAVE_TIME = ?, LEAVE_TYPE = ?, OTHER_LEAVE_DETAILS = ?, LEAVE_DURATION = ?, LEAVE_HOURS = ?, FILE_NAME = ?
             WHERE LEAVE_ID = ?");
        if ($stmt === false) {
            die('SQLite prepare failed: ' . db_error($dbCon));
        }

        // Bind parameters
        db_bind($stmt, "ssssssss", $LEAVE_DATE, $LEAVE_TIME, $LEAVE_TYPE, $OTHER_DETAILS, $LEAVE_DURATION, $LEAVE_HOURS, $FILE_NAME, $LEAVE_ID);

        // Execute and handle the result
        if ($stmt->execute()) {
            echo "<script>
                    let userChoice = confirm('Emergency leave updated successfully! Click OK to continue or Cancel to stay on this page.');
                    if (userChoice) {
                        window.location.href = 'viewEmergency.php';
                    }
                  </script>";
        } else {
            echo "<script>
                    alert('Failed to update emergency leave: " . db_error($stmt) . "');
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
    <title>Edit Emergency Leave Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

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
</nav>
<main class="content px-3 py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><?php echo "Edit Emergency Leave Application"; ?></h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="form-group mb-3">
                                <label for="leave_date"><?php echo "Leave Date"; ?></label>
                                <input type="date" class="form-control" id="leave_date" name="leave_date" value="<?php echo $leave['LEAVE_DATE']; ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="leave_type"><?php echo "Leave Type"; ?></label>
                                <select class="form-control" id="leave_type" name="leave_type" required onchange="showOtherField(this)">
                                    <option value="Medical" <?php echo ($leave['LEAVE_TYPE'] == 'Medical') ? 'selected' : ''; ?>><?php echo "Medical"; ?></option>
                                    <option value="Family Emergency" <?php echo ($leave['LEAVE_TYPE'] == 'Family Emergency') ? 'selected' : ''; ?>><?php echo "Family Emergency"; ?></option>
                                    <option value="Personal" <?php echo ($leave['LEAVE_TYPE'] == 'Personal') ? 'selected' : ''; ?>><?php echo "Personal"; ?></option>
                                    <option value="Other" <?php echo ($leave['LEAVE_TYPE'] == 'Other') ? 'selected' : ''; ?>><?php echo "Other"; ?></option>
                                </select>
                            </div>

                            <!-- "Other Leave Type" field displayed only if 'Other' is selected -->
                            <div class="form-group mb-3" id="other_leave_field" style="<?php echo ($leave['LEAVE_TYPE'] == 'Other') ? 'display:block;' : 'display:none;'; ?>">
                                <label for="other_leave_details"><?php echo "Specify Other Leave Type"; ?></label>
                                <input type="text" class="form-control" id="other_leave_details" name="other_leave_details" value="<?php echo $leave['OTHER_LEAVE_DETAILS']; ?>">
                            </div>
                            <div class="form-group mb-3">
    <label for="leave_duration">Leave Duration</label>
    <select class="form-control" id="leave_duration" name="leave_duration" required onchange="showLeaveHoursField(this)">
        <option value="Full Day" <?php echo ($leave['LEAVE_DURATION'] == 'Full Day') ? 'selected' : ''; ?>>Full Day</option>
        <option value="Half Day" <?php echo ($leave['LEAVE_DURATION'] == 'Half Day') ? 'selected' : ''; ?>>Half Day</option>
        <option value="Hourly" <?php echo ($leave['LEAVE_DURATION'] == 'Hourly') ? 'selected' : ''; ?>>Hourly</option>
    </select>
</div>
                            <!-- Leave Hours (only visible if 'Hourly' is selected) -->
                            <div class="form-group mb-3" id="leave_hours_field" style="<?php echo ($leave['LEAVE_DURATION'] == 'Hourly') ? 'display:block;' : 'display:none;'; ?>">
    <label for="leave_hours">Leave Hours</label>
    <input type="number" class="form-control" id="leave_hours" name="leave_hours" value="<?php echo ($leave['LEAVE_HOURS'] != '-') ? $leave['LEAVE_HOURS'] : ''; ?>" <?php echo ($leave['LEAVE_DURATION'] == 'Hourly') ? 'required' : ''; ?>>
</div>

                            <div class="form-group mb-3">
                                <label for="file"><?php echo "Upload File (Optional)"; ?></label>
                                <input type="file" class="form-control" name="file" id="file">
                            </div>
                            <form action="editEmergency.php" method="post">
    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-success me-2 w-40">Save</button>
        <button type="button" class="btn btn-danger w-20" onclick="resetFormAndRedirect()">Cancel</button>
    </div>
</form>
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

        
        function showOtherField(select) 
        {
            document.getElementById('other_leave_field').style.display = select.value === "Other" ? "block" : "none";
        }



    function showLeaveHoursField(select)
     {
            document.getElementById('leave_hours_field').style.display = select.value === "Hourly" ? "block" : "none";
        }

        function resetFormAndRedirect() {
        // Reset the form (if needed)
        document.querySelector('form').reset();
        // Redirect to the emergency view page
        window.location.href = "viewEmergency.php";
    }
    </script>

</body>

</html>
