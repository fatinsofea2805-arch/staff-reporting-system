<?php
session_start(); // Start the session

// Clear cache to prevent users from using the back button to access login page
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Include the database connection file
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get user inputs
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Input validation (optional but good practice)
    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in both fields.'); window.location.href='staff_login.php';</script>";
        exit;
    }

    // Query to check if the email exists in the database
    $query = "SELECT * FROM staff WHERE STAFF_EMAIL = ?";
    if ($stmt = $dbCon->prepare($query)) {
        // Bind email to the prepared statement
        db_bind($stmt, "s", $email);
        $stmt->execute();
        $result = new DatabaseRows($stmt);

        // Check if email is found in the database
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc(); // Fetch user data

            // Verify the password
            if (password_verify($password, $user['STAFF_PASSWORD'])) {
                // Password is correct, set session variables and redirect
                $_SESSION['STAFF_ID'] = $user['STAFF_ID'];
                $_SESSION['STAFF_NAME'] = $user['STAFF_NAME'];
                $_SESSION['STAFF_EMAIL'] = $user['STAFF_EMAIL'];

                // Redirect to profileStaff.php
                header("Location: staffPanel.php");
                exit;
            } else {
                echo "<script>alert('Incorrect password.'); window.location.href='staff_login.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Email not found.'); window.location.href='staff_login.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Error in query execution.'); window.location.href='staff_login.php';</script>";
        exit;
    }
}
?>
