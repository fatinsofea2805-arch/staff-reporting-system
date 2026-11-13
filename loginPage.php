<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="loginPage.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
        <a href="homepage.html" class="logo">TimeTrack</a>
        <ul class="nav-links">
            <li><a href="homepage.html#about-us">About Us</a></li>
            <li><a href="homepage.html#services">Services</a></li>
            <li><a href="homepage.html#contact">Contact</a></li>
            <li><a href="homepage.html#social-media">Social Media</a></li>
        </ul>
    </div>
</nav>
<div class = "container">
    <div class = "form-box">
        <h2 class="tittle">Welcome Back!</h2>
        <h2 class = "title">“Choose your role and make your mark”</h2>
<div class = "row">
        <div class = "column admin-column">
        <img src="Pictures/admin profile.png" alt="Admin">
            <div class = "login-options">
            <a href="admin_login.html" class="login-option">Admin</a>
            </div>
        </div>
        <div class = "column">
        <img src="Pictures/staff profile.png" alt="Customer">
            <div class="login-options">
                <a href="staff_login.php" class="login-option">Staff</a>
        </div>
            </div>
    </div>
</div>
</div>

</body>
</html>
