<?php
session_start();
include 'connection.php'; // Ensure this file is correctly included

// Check if the admin is logged in
if (isset($_SESSION['admin_id'])) {
    // Admin is logged in, so update the database to set LOGGED_IN = 0
    $admin_id = $_SESSION['admin_id'];
    
    // Update the logged-in status in the database
    db_query($dbCon, "UPDATE ADMIN SET LOGGED_IN = 0 WHERE ADMIN_ID = $admin_id");

    // Destroy session data
    session_destroy();

    // Redirect to index.html after logging out
    echo "<script>alert('You have been logged out successfully.'); window.location.href = 'homepage.html';</script>";
    exit();
} else {
    // If no admin is logged in, redirect to the login page
    echo "<script>window.location.href = 'homepage.html';</script>";
    exit();
}
?>
