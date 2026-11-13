<?php
// Include the database connection
include('connection.php'); // Ensure this file defines the $dbCon variable

// Check if the id is passed in the URL
if (isset($_GET['id'])) {
    // Get the holiday ID from the URL
    $holidayId = $_GET['id'];

    // Prepare the DELETE SQL query
    $sql = "DELETE FROM holiday WHERE HOLIDAY_ID = ?";

    // Prepare the statement
    if ($stmt = $dbCon->prepare($sql)) {
        // Bind the parameters to the query
        db_bind($stmt, "s", $holidayId);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to the holiday list page after successful deletion
            echo "<script>alert('Holiday deleted successfully!'); window.location.href = 'viewHoliday.php';</script>";
        } else {
            // Error occurred during deletion
            echo "<script>alert('Error deleting holiday.'); window.location.href = 'viewHoliday.php';</script>";
        }

        // Close the prepared statement
        $stmt->closeCursor();
    } else {
        // Error with preparing the query
        echo "<script>alert('Failed to prepare the query.'); window.location.href = 'viewHoliday.php';</script>";
    }
} else {
    // If no ID is passed, redirect to the holiday list page
    echo "<script>window.location.href = 'viewHoliday.php';</script>";
}

// Close the database connection
$dbCon = null;
?>
