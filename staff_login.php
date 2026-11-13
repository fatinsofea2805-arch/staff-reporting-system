<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login and Sign Up Form</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Itim&family=Poppins:wght@100..900&family=Sour+Gummy:wght@100..900&family=Water+Brush&display=swap" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="staff_login.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
    <a href="homepage.html" class="logo">Staff Panel</a>
        <ul class="nav-links">
            <li><a href="homepage.html#about-us">About Us</a></li>
            <li><a href="homepage.html#services">Services</a></li>
            <li><a href="homepage.html#contact">Contact</a></li>
            <li><a href="homepage.html#social-media">Social Media</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="form-box login">
        <form action="login.php" method="POST" autocomplete="off"> <!-- Added autocomplete="off" here -->
            <h1>Login</h1>

            <!-- Email -->
            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" autocomplete="off">
                <i class='bx bxs-envelope'></i>
            </div>

            <!-- Password -->
            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" autocomplete="off">
                <i class='bx bxs-lock-alt'></i>
            </div>

            <!-- Forgot password -->
            <div class="forgot-password">
                <a href="recover_psw.php">Forgot password?</a>
            </div>

            <!-- Login button -->
            <button type="submit" class="btn">Login</button>
        </form>
    </div>

    <!-- Register form -->
    <div class="form-box register">
        <form action="register.php" method="POST" autocomplete="off"> <!-- Added autocomplete="off" here -->
            <h1>Registration</h1>

            <!-- Name -->
            <div class="input-box">
                <input type="text" id="name" name="name" placeholder="Name" autocomplete="off">
                <i class='bx bxs-user'></i>
            </div>

            <!-- Department -->
            <div class="input-box">
                <select id="department" name="department" onchange="updateUnits()" autocomplete="off">
                    <option value="" disabled selected>Department</option>
                    <option value="management_services">Management Services</option>
                    <option value="land">Land</option>
                    <option value="district_development">District Development</option>
                </select>
                <i class='bx bxs-building'></i>
            </div>

            <!-- Unit -->
            <div class="input-box">
                <select id="unit" name="unit" disabled autocomplete="off">
                    <option value="" disabled selected>Unit</option>
                </select>
                <i class='bx bxs-user-badge'></i>
            </div>

            <!-- Position -->
            <div class="input-box">
                <input type="text" id="position" name="position" placeholder="Position" autocomplete="off">
                <i class='bx bxs-id-card'></i>
            </div>

            <!-- Phone Number -->
            <div class="input-box">
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    placeholder="Phone Number"
                    inputmode="tel" 
                    pattern="\+60\d{2}-\d{7}" 
                    oninput="enforcePhoneFormat(this)" 
                    maxlength="15" autocomplete="off">
                <i class='bx bxs-phone'></i>
            </div>

            <!-- Email -->
            <div class="input-box">
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Email" 
                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" autocomplete="off">
                <i class='bx bxs-envelope'></i>
            </div>

            <!-- Password -->
            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" autocomplete="off">
                <i class='bx bxs-lock-alt'></i>
            </div>

            <!-- Register button -->
            <button type="submit" class="btn" name="signUp" value="Register">Register</button>
        </form>
    </div>

    <!-- Toggle Panels -->
    <div class="toggle-box">
        <div class="toggle-panel toggle-left">
            <h1>Hello, Welcome!</h1>
            <p>Don't have an account?</p>
            <button class="btn register-btn">Register</button>
        </div>
        <div class="toggle-panel toggle-right">
            <h1>Welcome Back!</h1>
            <p>Already have an account?</p>
            <button class="btn login-btn">Login</button>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    const container = document.querySelector('.container');
    const registerBtn = document.querySelector('.register-btn');
    const loginBtn = document.querySelector('.login-btn');

    registerBtn.addEventListener('click', () => {
        container.classList.add('active');
    });

    loginBtn.addEventListener('click', () => {
        container.classList.remove('active');
    });

    function updateUnits() {
        const unitsByDepartment = {
            management_services: [
                { value: "administration", text: "Administration Unit" },
                { value: "finance", text: "Finance Unit" },
                { value: "technology", text: "Technology & Information Unit" },
                { value: "events", text: "Events and Ceremonies Unit" },
            ],
            land: [
                { value: "land_disposal", text: "Land Disposal Unit" },
                { value: "technical", text: "Enforcement & Technical Unit" },
                { value: "revenue", text: "Revenue Unit" },
                { value: "registration", text: "Registration Unit" },
            ],
            district_development: [
                { value: "community", text: "Community Development Unit" },
                { value: "physical", text: "Physical Development Unit" },
            ],
        };

        const department = document.getElementById("department").value;
        const unitDropdown = document.getElementById("unit");

        unitDropdown.innerHTML = '<option value="" disabled selected>Select Unit</option>';

        if (department && unitsByDepartment[department]) {
            unitDropdown.disabled = false;
            unitsByDepartment[department].forEach((unit) => {
                const option = document.createElement("option");
                option.value = unit.value;
                option.textContent = unit.text;
                unitDropdown.appendChild(option);
            });
        } else {
            unitDropdown.disabled = true;
        }
    }

    function enforcePhoneFormat(input) {
        let value = input.value;
        value = value.replace(/[^0-9+\-]/g, '');
        if (!value.startsWith('+60')) {
            value = '+60' + value.replace(/^\+/, '');
        }
        const parts = value.split('-');
        if (parts.length > 2) {
            value = parts[0] + '-' + parts.slice(1).join('');
        } else if (parts.length === 2) {
            value = parts[0] + '-' + parts[1].slice(0, 7);
        } else if (parts[0].length > 3 && !value.includes('-')) {
            value = value.slice(0, 4) + '-' + value.slice(4);
        }
        if (value.length > 15) {
            value = value.slice(0, 15);
        }
        input.value = value;
    }
</script>

</body>
</html>
