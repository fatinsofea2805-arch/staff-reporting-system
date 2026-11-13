<?php
// Include the database connection
include('connection.php');

// Check if the connection was established
if (!$dbCon) {  // Use $dbCon here instead of $conn
    die("Database connection failed");
}

// Check if there is already an admin registered
$result = db_query($dbCon, "SELECT * FROM ADMIN LIMIT 1");  // Use $dbCon here
if ($result->num_rows > 0) {
    echo "<script>alert('An admin account already exists. No new admin can be registered.'); window.location.href = 'admin_login.html';</script>";
    exit(); // Prevent further execution
}

// Process registration
if (isset($_POST['signUp'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security

    // Insert admin data into the database
    $sql = "INSERT INTO ADMIN (ADMIN_EMAIL, ADMIN_PASSWORD) VALUES (?, ?)";
    $statement = $dbCon->prepare($sql);

    if ($statement->execute([$email, $hashedPassword])) {  // Use $dbCon here instead of $conn
        echo "<script>alert('Admin registered successfully!'); window.location.href = 'admin_login.html';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . db_error($dbCon);  // Use $dbCon here instead of $conn
    }
}

// Close the database connection
$dbCon = null;  // No change needed for this line, as you're closing $dbCon
?>
