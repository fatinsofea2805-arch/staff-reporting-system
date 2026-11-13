<?php
// Start the session
session_start();

// Include the database connection
include('connection.php');

// Validate session and STAFF_ID
if (!isset($_SESSION['STAFF_ID'])) {
    echo json_encode(['error' => 'Session expired. Please log in again.']);
    exit();
}

// Retrieve STAFF_ID from the session
$STAFF_ID = $_SESSION['STAFF_ID'];

// Validate STAFF_ID format
if (empty($STAFF_ID) || !preg_match('/^[A-Za-z0-9]{8}$/', $STAFF_ID)) {
    echo json_encode(['error' => 'Invalid STAFF_ID retrieved from session. Please log in again.']);
    exit();
}

// Validate the database connection
if (!$dbCon) {
    echo json_encode(['error' => 'Database connection failed. Please try again later.']);
    exit();
}

// Validate STAFF_ID exists in the database
$query = $dbCon->prepare("SELECT COUNT(*), STAFF_NAME FROM Staff WHERE STAFF_ID = ?");
db_bind($query, "s", $STAFF_ID);
$query->execute();
[$count, $STAFF_NAME] = $query->fetch(PDO::FETCH_NUM) ?: [null, null];
$query->closeCursor();

if ($count == 0) {
    echo json_encode(['error' => 'Invalid STAFF_ID. No matching staff found in the database.']);
    exit();
}

// Process lateness report submission if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $LATENESS_DATE = htmlspecialchars(trim($_POST['lateness_date'] ?? ''));
    $LATENESS_REASON = htmlspecialchars(trim($_POST['lateness_reason'] ?? ''));

    // Escape special characters for SQL query


    // Generate a unique 8-character LATENESS_ID
    $LATENESS_ID = 'LR' . substr(md5(uniqid(rand(), true)), 0, 6);

    // Default values for file if none is uploaded
    $file_name = 'no file';  // Default file name if no file is uploaded
    $file_type = 'no file';  // Default file type if no file is uploaded

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_size = $_FILES['file']['size'];  // Get file size

        // Check if the file size is within the limit (5 MB = 5 * 1024 * 1024 bytes)
        $max_file_size = 5 * 1024 * 1024; // 5 MB in bytes
        if ($file_size > $max_file_size) {
            echo json_encode(['error' => 'File size exceeds the maximum limit of 5 MB']);
            exit();
        }

        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Add "zip" to the allowed types
        $allowed_types = array("jpg", "jpeg", "png", "pdf", "doc", "docx", "zip");

        if (in_array($file_type, $allowed_types) && move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_name = $_FILES["file"]["name"];
            $file_type = $_FILES["file"]["type"];
        }
    }

    // Insert lateness data into the database (without file size)
    $stmt = $dbCon->prepare("INSERT INTO Lateness (LATENESS_ID, LATENESS_DATE, LATENESS_REASON, FILE_NAME, FILE_TYPE, STAFF_ID)
                             VALUES (?, ?, ?, ?, ?, ?)");
    db_bind($stmt, "ssssss", $LATENESS_ID, $LATENESS_DATE, $LATENESS_REASON, $file_name, $file_type, $STAFF_ID);

    if ($stmt->execute()) {
        // Redirect to the "viewLateness.php" page after success
        echo "<script>
                alert('Lateness report submitted successfully');
                window.location.href = 'lateness.php';
              </script>";
    } else {
        echo json_encode(['error' => 'Failed to submit lateness report']);
    }

    $stmt->closeCursor();
}

// Close the database connection
$dbCon = null;
?>
