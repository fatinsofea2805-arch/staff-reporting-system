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
    $LATENESS_ID = htmlspecialchars(trim($_GET['id']), ENT_QUOTES, 'UTF-8');

    // Fetch lateness details from the database using the provided ID
    $sql = "SELECT * FROM Lateness WHERE LATENESS_ID = ?";
    $stmt = $dbCon->prepare($sql);
    if ($stmt === false) {
        die('SQLite prepare failed: ' . db_error($dbCon));
    }

    db_bind($stmt, "s", $LATENESS_ID);
    $stmt->execute();
    $result = new DatabaseRows($stmt);

    // Check if the lateness record exists
    if ($result->num_rows > 0) {
        $lateness = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('Lateness report not found.');
                window.location.href='viewLateness.php';
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('Invalid lateness ID.');
            window.location.href='viewLateness.php';
          </script>";
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $LATENESS_DATE = htmlspecialchars(trim($_POST['lateness_date']), ENT_QUOTES, 'UTF-8');
    $LATENESS_REASON = htmlspecialchars(trim($_POST['lateness_reason']), ENT_QUOTES, 'UTF-8');

    // File upload logic
    $FILE_NAME = $lateness['FILE_NAME'];
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
    
    // Get the lateness time
    $LATENESS_TIME = isset($_POST['lateness_time']) && !empty($_POST['lateness_time']) 
        ? htmlspecialchars(trim($_POST['lateness_time']), ENT_QUOTES, 'UTF-8') 
        : date('Y-m-d H:i:s');  // Set current timestamp as default if not provided

    // Validate inputs
    if (empty($LATENESS_DATE) || empty($LATENESS_REASON)) {
        echo "<script>
                alert('Please fill in all required fields.');
              </script>";
    } elseif (!strtotime($LATENESS_DATE)) { // Validate date format
        echo "<script>
                alert('Invalid date format. Please enter a valid date.');
              </script>";
    } else {
        // Escape inputs for SQL query to prevent SQL injection




        // Prepare the SQL query to update lateness details
        $stmt = $dbCon->prepare("UPDATE Lateness 
             SET LATENESS_DATE = ?, LATENESS_TIME = ?, LATENESS_REASON = ?, FILE_NAME = ?, LATENESS_STATUS = 'Not Seen' 
             WHERE LATENESS_ID = ?");
        if ($stmt === false) {
            die('SQLite prepare failed: ' . db_error($dbCon));
        }

        // Bind parameters
        db_bind($stmt, "sssss", $LATENESS_DATE, $LATENESS_TIME, $LATENESS_REASON, $FILE_NAME, $LATENESS_ID);

       // Execute and handle the result
if ($stmt->execute()) {
    echo "<script>
            let userChoice = confirm('Lateness report updated successfully! Click OK to continue or Cancel to stay on this page.');
            if (userChoice) {
                window.location.href = 'viewLateness.php';
            }
          </script>";
} else {
    echo "<script>
            alert('Failed to update lateness report: " . db_error($stmt) . "');
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
    <title>Edit Lateness Report Application</title>
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
                            <a href="viewEmergency.ph" class="sidebar-link">Emergency Log</a>
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
                                    <h3 class="mb-0">Edit Lateness Report</h3>
                                </div>
                                <div class="card-body">
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <div class="form-group mb-3">
                                            <label for="lateness_date">Lateness Date</label>
                                            <input type="date" class="form-control" id="lateness_date" name="lateness_date" value="<?php echo $lateness['LATENESS_DATE']; ?>" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="lateness_reason">Reason for Lateness</label>
                                            <textarea class="form-control" id="lateness_reason" name="lateness_reason" rows="3" required><?php echo $lateness['LATENESS_REASON']; ?></textarea>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="file">Upload File (Optional)</label>
                                            <input type="file" class="form-control" name="file" id="file">
                                        </div>
                                        <div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-success me-2 w-40">Save</button>
    <button type="button" class="btn btn-danger w-20" onclick="resetFormAndRedirect()">Cancel</button>
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
        // Reset the form (if needed)
        document.querySelector('form').reset();
        // Redirect to the emergency view page
        window.location.href = "viewLateness.php";
    }
    </script>

</body>

</html>
