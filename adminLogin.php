<?php
session_start();
include 'connection.php'; // Include the database connection

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if there is an admin already logged in
    $result = db_query($dbCon, "SELECT * FROM ADMIN WHERE LOGGED_IN = 1 LIMIT 1"); // Use $dbCon instead of $conn
    if ($result->num_rows > 0) {
        echo "<script>alert('An admin is already logged in. Please log out first.'); window.location.href = 'admin_login.html';</script>";
        exit(); // Prevent further login
    }

    // Check if the admin credentials are valid
    $stmt = $dbCon->prepare("SELECT * FROM ADMIN WHERE ADMIN_EMAIL = ?"); // Use $dbCon instead of $conn
    db_bind($stmt, "s", $email);
    $stmt->execute();
    $result = new DatabaseRows($stmt);
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['ADMIN_PASSWORD'])) {
        // Mark admin as logged in
        $update = $dbCon->prepare("UPDATE ADMIN SET LOGGED_IN = 1 WHERE ADMIN_EMAIL = ?");
        $update->execute([$email]); // Use $dbCon instead of $conn

        // Store session data
        $_SESSION['admin_id'] = $admin['ADMIN_ID'];
        $_SESSION['email'] = $admin['ADMIN_EMAIL'];

        echo "<script>alert('Admin logged in successfully!'); window.location.href = 'adminPanel.php';</script>";
    } else {
        echo "<script>alert('Invalid email or password!'); window.location.href = 'admin_login.html';</script>";
    }

    $stmt->closeCursor();
}

$dbCon = null; // Use $dbCon instead of $conn
?>
