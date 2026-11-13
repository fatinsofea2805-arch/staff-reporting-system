<?php
// Include the database connection
session_start();
include('connection.php');
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.html');
    exit;
} // Ensure this file defines the $dbCon variable

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the $dbCon variable is defined
    if (!isset($dbCon)) {
        die(json_encode(['error' => 'Database connection not found.']));
    }

    // Collect and sanitize form data
    // Collect and sanitize form data
    $HOLIDAY_NAME = htmlspecialchars(trim($_POST['holiday_name'])); // Initial sanitization
    $HOLIDAY_NAME = html_entity_decode($HOLIDAY_NAME); // Decode any HTML entities like &#039;

    $HOLIDAY_START = htmlspecialchars(trim($_POST['holiday_start']));
    $HOLIDAY_END = htmlspecialchars(trim($_POST['holiday_end']));
    $DURATION = (int)$_POST['holiday_duration'];

    // Validate required fields
    if (empty($HOLIDAY_NAME) || empty($HOLIDAY_START) || empty($HOLIDAY_END) || $DURATION <= 0) {
        echo "<script>
                alert('Please fill in all required fields correctly.');
                window.location.href='addHoliday.php';
              </script>";
        exit;
    }

    $HOLIDAY_ID = substr(bin2hex(random_bytes(4)), 0, 8);
    $ADMIN_ID = $_SESSION['admin_id'];
    $stmt = $dbCon->prepare("INSERT INTO holiday (HOLIDAY_ID, HOLIDAY_NAME, START_DATE, END_DATE, HOLIDAY_DURATION, ADMIN_ID) VALUES (?, ?, ?, ?, ?, ?)");
    db_bind($stmt, "ssssii", $HOLIDAY_ID, $HOLIDAY_NAME, $HOLIDAY_START, $HOLIDAY_END, $DURATION, $ADMIN_ID);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        // Success: Show a success message and redirect to addHoliday.php
        echo "<script>
                alert('Holiday added successfully!');
                window.location.href='addHoliday.php';
              </script>";
    } else {
        // Failure: Show an error message
        echo "<script>
                alert('Failed to add holiday: " . db_error($dbCon) . "');
              </script>";
    }
} else {
    // Invalid request method
    echo "<script>
            alert('Invalid request method.');
          </script>";
}
?>
