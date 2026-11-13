<?php
// Start the session
session_start();

// Include the database connection
include('connection.php');

// Validate session and STAFF_ID
if (!isset($_SESSION['STAFF_ID'])) {
    header("Location: login.php?error=Session expired. Please log in again.");
    exit();
}

// Retrieve STAFF_ID from the session
$STAFF_ID = $_SESSION['STAFF_ID'];

// Validate STAFF_ID format
if (empty($STAFF_ID) || !preg_match('/^[A-Za-z0-9]{8}$/', $STAFF_ID)) {
    header("Location: staff_login.php?error=Invalid STAFF_ID retrieved from session. Please log in again.");
    exit();
}

// Validate the database connection
if (!$dbCon) {
    header("Location: error.php?error=Database connection failed. Please try again later.");
    exit();
}

// Validate STAFF_ID exists in the database
$query = $dbCon->prepare("SELECT COUNT(*), STAFF_NAME FROM Staff WHERE STAFF_ID = ?");
db_bind($query, "s", $STAFF_ID);
$query->execute();
[$count, $STAFF_NAME] = $query->fetch(PDO::FETCH_NUM) ?: [null, null];
$query->closeCursor();
if ($count == 0) {
    header("Location: error.php?error=Invalid STAFF_ID. No matching staff found in the database.");
    exit();
}

// Process leave submission if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $LEAVE_DATE = htmlspecialchars(trim($_POST['leave_date'] ?? ''));
    $LEAVE_TYPE = htmlspecialchars(trim($_POST['leave_type'] ?? ''));
    $LEAVE_DURATION = htmlspecialchars(trim($_POST['leave_duration'] ?? ''));
    $LEAVE_HOURS = htmlspecialchars(trim($_POST['leave_hours'] ?? ''));
    $OTHER_LEAVE_DETAILS = htmlspecialchars(trim($_POST['other_leave_details'] ?? ''));

    // Escape special characters for SQL query





    // Validate and set default values based on leave duration
    if ($LEAVE_DURATION === 'Hourly') {
        // Validate LEAVE_HOURS for hourly leave
        if (empty($LEAVE_HOURS) || !is_numeric($LEAVE_HOURS) || $LEAVE_HOURS <= 0) {
            header("Location: emergency.php?error=Please specify valid hours for hourly leave");
            exit();
        }
    } else {
        $LEAVE_HOURS = 0; // Set to 0 for non-hourly leave
    }

    // Validate OTHER_LEAVE_DETAILS
    if (empty($OTHER_LEAVE_DETAILS)) {
        $OTHER_LEAVE_DETAILS = '-'; // Default value if no other leave details are provided
    }

    // Generate a unique 8-character LEAVE_ID
    $LEAVE_ID = 'LV' . substr(md5(uniqid(rand(), true)), 0, 6);

    // Default values for file if none is uploaded
    $file_name = 'no file'; // Default file name if no file is uploaded
    $file_type = 'no file'; // Default file type if no file is uploaded

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_size = $_FILES['file']['size']; // Get file size

        // Check if the file size is within the limit (5 MB = 5 * 1024 * 1024 bytes)
        $max_file_size = 5 * 1024 * 1024; // 5 MB in bytes
        if ($file_size > $max_file_size) {
            header("Location: emergency.php?error=File size exceeds the maximum limit of 5 MB");
            exit();
        }

        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png", "pdf", "doc", "docx", "zip");

        // Check if the file type is allowed and move the uploaded file
        if (in_array($file_type, $allowed_types) && move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_name = $_FILES["file"]["name"];
            $file_type = $_FILES["file"]["type"];
        } else {
            header("Location: emergency.php?error=Failed to upload the file. Please try again.");
            exit();
        }
    }

    // Insert leave data into the database
    $stmt = $dbCon->prepare("INSERT INTO emergency (LEAVE_ID, LEAVE_DATE, LEAVE_TYPE, LEAVE_DURATION, LEAVE_HOURS, OTHER_LEAVE_DETAILS, FILE_NAME, FILE_TYPE, STAFF_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    db_bind($stmt, "ssssissss", $LEAVE_ID, $LEAVE_DATE, $LEAVE_TYPE, $LEAVE_DURATION, $LEAVE_HOURS, $OTHER_LEAVE_DETAILS, $file_name, $file_type, $STAFF_ID);

    if ($stmt->execute()) {
        echo "<script>
                alert('Emergency Leave submitted successfully');
                window.location.href = 'emergency.php';
              </script>";
    } else {
        echo json_encode(['error' => 'Failed to submit leave. Please try again later.']);
    }

    $stmt->closeCursor();
}

// Close the database connection
$dbCon = null;
?>
