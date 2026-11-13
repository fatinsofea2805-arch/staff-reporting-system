<?php
// Include the database connection
include('connection.php');

// Check if the lateness_id is provided via POST and is exactly 8 characters long
if (isset($_POST['lateness_id']) && strlen($_POST['lateness_id']) == 8) {
    $latenessId = $_POST['lateness_id'];

    // Prepare an SQL query to update the status to "Seen"
    $sql = "UPDATE Lateness SET LATENESS_STATUS = 'Seen' WHERE LATENESS_ID = ?";

    // Prepare and execute the statement
    if ($stmt = $dbCon->prepare($sql)) {
        // Bind the parameter to the query
        db_bind($stmt, "s", $latenessId);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect back to the previous page or show a success message
            header("Location: adminLateness.php"); // Redirect to the report page after updating
            exit();
        } else {
            // Handle execution failure
            echo "Error updating record: " . db_error($dbCon);
        }

        // Close the statement
        $stmt->closeCursor();
    } else {
        echo "Error preparing statement: " . db_error($dbCon);
    }
} else {
    echo "Invalid Lateness ID. It should be 8 characters long!";
}

// Close the database connection
$dbCon = null;
?>
