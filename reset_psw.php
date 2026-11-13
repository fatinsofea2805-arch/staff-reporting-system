<?php
// Clear browser cache by setting appropriate headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Include database connection
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the email from the URL
    $email = $_GET['email'];
    
    // Retrieve and sanitize the new password
    $new_password = htmlspecialchars(trim($_POST['password']));

    // Validate password length (at least 8 characters)
    if (strlen($new_password) < 8) {
        echo "<script>
                alert('Password must be at least 8 characters long.');
                window.history.back();
              </script>";
        exit;
    }

    // Hash the new password for security
    $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the password in the database
    $updateQuery = $dbCon->prepare("UPDATE staff SET STAFF_PASSWORD = ? WHERE STAFF_EMAIL = ?");
    db_bind($updateQuery, "ss", $hashedPassword, $email);

    if ($updateQuery->execute()) {
        echo "<script>
                alert('Your password has been updated successfully!');
                window.location.href='staff_login.php';
              </script>";
    } else {
        echo "<script>
                alert('An error occurred while updating your password.');
                window.location.href='staff_login.php';
              </script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="pass.css">

    <link rel="icon" href="Favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />

    <title>Password Reset</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="#">Password Reset Form</a>
    </div>
</nav>

<main class="login-form">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Reset Your Password</div>
                    <div class="card-body">
                        <form action="#" method="POST" name="reset_password">

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">New Password</label>
                                <div class="col-md-6">
                                    <input type="password" id="password" class="form-control" name="password" required autofocus>
                                    <i class="bi bi-eye-slash" id="togglePassword"></i>
                                </div>
                            </div>

                            <div class="col-md-6 offset-md-4">
                                <input type="submit" value="Reset" name="reset">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Toggle password visibility
    const togglePassword = document.querySelector("#togglePassword");
    const passwordField = document.querySelector("#password");

    togglePassword.addEventListener("click", function () {
        // Toggle the type attribute using classList.toggle
        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type", type);

        // Toggle the eye icon
        this.classList.toggle("bi-eye");
    });
</script>

</body>
</html>
