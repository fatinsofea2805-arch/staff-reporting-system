<?php
// Include the database connection
include('connection.php'); // Ensure this file defines the $dbCon variable

// Check if the ID is passed in the URL
if (isset($_GET['id'])) {
    // Get the LATENESS_ID from the URL
    $latenessId = $_GET['id'];

    // Validate that the ID is exactly 8 characters
    if (strlen($latenessId) === 8) {
        // Prepare the DELETE SQL query for a specific lateness record
        $sql = "DELETE FROM lateness WHERE LATENESS_ID = ?";

        // Prepare the statement
        if ($stmt = $dbCon->prepare($sql)) {
            // Bind the LATENESS_ID (as a string) to the query
            db_bind($stmt, "s", $latenessId);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect to the lateness list page after successful deletion
                echo "<script>alert('Lateness record deleted successfully!'); window.location.href = 'viewLateness.php';</script>";
            } else {
                // Error occurred during deletion
                echo "<script>alert('Error deleting lateness record.'); window.location.href = 'viewLateness.php';</script>";
            }

            // Close the prepared statement
            $stmt->closeCursor();
        } else {
            // Error with preparing the query
            echo "<script>alert('Failed to prepare the query.'); window.location.href = 'viewLateness.php';</script>";
        }
    } else {
        // If the ID is not 8 characters, redirect to the lateness list page
        echo "<script>alert('Invalid ID format.'); window.location.href = 'viewLateness.php';</script>";
    }
} else {
    // If no ID is passed, redirect to the lateness list page
    echo "<script>window.location.href = 'viewLateness.php';</script>";
}

// Close the database connection
$dbCon = null;
?>
