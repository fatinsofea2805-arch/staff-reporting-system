<?php
// Clear browser cache by setting appropriate headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Include the database connection file
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $dbCon;

    // Retrieve and sanitize the input data
    $STAFF_NAME = htmlspecialchars(trim($_POST['name'])); // Initial sanitization
    $DEPARTMENT = htmlspecialchars(trim($_POST['department'])); 
    $UNIT = htmlspecialchars(trim($_POST['unit']));
    $PHONE_NUM = htmlspecialchars(trim($_POST['phone']));
    $POSITION = htmlspecialchars(trim($_POST['position']));
    $STAFF_EMAIL = htmlspecialchars(trim($_POST['email']));
    $STAFF_PASSWORD = htmlspecialchars(trim($_POST['password']));

    // Prevent specific department name "khidmat_pengurusan"
    if (strtolower($DEPARTMENT) === 'khidmat_pengurusan') {
        echo "<script>
                alert('The department name cannot be " . htmlentities('khidmat_pengurusan') . ". Please select a different department.');
                window.history.back();
              </script>";
        exit;
    }

    // Generate a unique STAFF_ID using a combination of prefix and a random 8-character UUID
    $STAFF_ID = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)); // Unique 8-character ID

    // Input validation
    if (empty($STAFF_NAME) || empty($DEPARTMENT) || empty($UNIT) || empty($POSITION) || empty($STAFF_EMAIL) || empty($STAFF_PASSWORD) || empty($PHONE_NUM)) {
        echo "<script>
                alert('All fields are required.');
                window.history.back();
              </script>";
        exit;
    }

    // Validate email format
    if (!filter_var($STAFF_EMAIL, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Invalid email format.');
                window.history.back();
              </script>";
        exit;
    }

    // Validate password length (at least 8 characters)
    if (strlen($STAFF_PASSWORD) < 8) {
        echo "<script>
                alert('Password must be at least 8 characters long.');
                window.history.back();
              </script>";
        exit;
    }

    // Hash the password for security
    $hashedPassword = password_hash($STAFF_PASSWORD, PASSWORD_BCRYPT);

    // Check if the email already exists in the database
    $checkEmailQuery = $dbCon->prepare("SELECT * FROM staff WHERE STAFF_EMAIL = ?");
    db_bind($checkEmailQuery, "s", $STAFF_EMAIL);
    $checkEmailQuery->execute();
    $resultEmail = new DatabaseRows($checkEmailQuery);

    if ($resultEmail->num_rows > 0) {
        // Email already exists
        echo "<script>
                alert('Email already exists. Please try a different one.');
                window.location.href='staff_login.php';
              </script>";
        exit;
    }

    // Check if the phone number already exists in the database
    $checkPhoneQuery = $dbCon->prepare("SELECT * FROM staff WHERE PHONE_NUM = ?");
    db_bind($checkPhoneQuery, "s", $PHONE_NUM);
    $checkPhoneQuery->execute();
    $resultPhone = new DatabaseRows($checkPhoneQuery);

    if ($resultPhone->num_rows > 0) {
        // Phone number already exists
        echo "<script>
                alert('Phone number already exists. Please use a different one.');
                window.history.back();
              </script>";
        exit;
    }

    // Insert the new staff member into the database
    $insertQuery = $dbCon->prepare("INSERT INTO staff (STAFF_ID, STAFF_NAME, DEPARTMENT, UNIT, POSITION, PHONE_NUM, STAFF_EMAIL, STAFF_PASSWORD) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    db_bind($insertQuery, "ssssssss", $STAFF_ID, $STAFF_NAME, $DEPARTMENT, $UNIT, $POSITION, $PHONE_NUM, $STAFF_EMAIL, $hashedPassword);

    if ($insertQuery->execute()) {
        echo "<script>
                alert('Registration successful!');
                window.location.href='staff_login.php';
              </script>";
    } else {
        echo "<script>
                alert('Unable to complete the registration process. Please try again later.');
                window.location.href='staff_login.php';
              </script>";
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
