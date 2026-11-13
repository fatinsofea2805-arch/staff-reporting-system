<?php
// PHP: Define arrays for units based on the department
$units = [
    'management_services' => [
        'Administration Unit',
        'Finance Unit',
        'Technology & Information Unit',
        'Events and Ceremonies Unit'
    ],
    'land' => [
        'Land Disposal Unit',
        'Enforcement & Technical Unit',
        'Revenue Unit',
        'Registration Unit'
    ],
    'district_development' => [
        'Community Development Unit',
        'Physical Development Unit'
    ]
];

// Include database connection
include('connection.php');

// Start session
session_start();

// Check database connection
if (!$dbCon) {
    die("Connection failed: " . 'SQLite connection unavailable');
}

// Check if logged in
if (!isset($_SESSION['STAFF_ID'])) {
    header('Location: staff_login.php');
    exit();
}

$staff_id = $_SESSION['STAFF_ID'];

// Fetch staff data (using prepared statement for security)
$sql = "SELECT STAFF_ID, STAFF_NAME, DEPARTMENT, UNIT, POSITION, PHONE_NUM, STAFF_EMAIL, PROFILE_PIC 
        FROM staff WHERE STAFF_ID = ?";
$stmt = $dbCon->prepare($sql);
if ($stmt) {
    db_bind($stmt, "s", $staff_id);
    $stmt->execute();
    $result = new DatabaseRows($stmt);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $staff_name = $row['STAFF_NAME'];
        $department = $row['DEPARTMENT'];
        $unit = $row['UNIT'];
        $position = $row['POSITION'];
        $phone_number = $row['PHONE_NUM'];
        $profile_pic = $row['PROFILE_PIC'] ?: "https://www.w3schools.com/w3images/avatar2.png";
    } else {
        echo "Staff profile not found.";
        exit;
    }
    $stmt->closeCursor();
} else {
    echo "Error preparing statement: " . db_error($dbCon);
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Bind the form values directly; SQL escaping is not needed for prepared statements.
    $department = trim($_POST['department'] ?? '');
    $unit = trim($_POST['unit'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $phone_number = trim($_POST['phone'] ?? '');




    $profile_picture = "";

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['profile_picture'];
        $uploadDir = 'uploads/profile_pictures/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_message = 'Error uploading file.';
        } elseif (!in_array($file['type'], $allowedTypes)) {
            $error_message = 'Invalid file type. Only JPG, JPEG, and PNG are allowed.';
        } elseif ($file['size'] > $maxSize) {
            $error_message = 'File size exceeds the limit (5MB).';
        } else {
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $newFileName;
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $profile_picture = $uploadPath;
            } else {
                $error_message = "Failed to upload profile picture.";
            }
        }

        if (isset($error_message)) {
            echo "<div class='alert alert-danger'>" . $error_message . "</div>";
        }
    } else {
        $profile_picture = $row['PROFILE_PIC'] ?: "https://www.w3schools.com/w3images/avatar2.png";
    }

// Update database
$sql = "UPDATE staff SET DEPARTMENT = ?, UNIT = ?, POSITION = ?, PHONE_NUM = ?, PROFILE_PIC = ? WHERE STAFF_ID = ?";
$stmt = $dbCon->prepare($sql);
if ($stmt) {
    db_bind($stmt, "ssssss", $department, $unit, $position, $phone_number, $profile_picture, $staff_id);
    if ($stmt->execute()) {
        echo "<script>
                let userChoice = confirm('Profile updated successfully! Click OK to continue or Cancel to stay on this page.');
                if (userChoice) {
                    window.location.href = 'profileStaff.php';
                }
              </script>";
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating profile: " . db_error($stmt) . "</div>";
    }
    $stmt->closeCursor();
} else {
    echo "<div class='alert alert-danger'>Error preparing SQL query.</div>";
}


}

$dbCon = null;
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- Add Font Awesome (if not already included) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="staffPanel.css">
    <style>
        #profilePreview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="lni lni-grid-alt"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#">TimeTrack</a>
                </div>
            </div>
            <ul class="sidebar-nav">

            <li class="sidebar-item">
    <a href="staffPanel.php" class="sidebar-link">
        <i class="fas fa-chart-line"></i> <!-- Chart icon for Dashboard -->
        <span>Dashboard</span>
    </a>
</li>

                <li class="sidebar-item">
                    <a href="profileStaff.php" class="sidebar-link">
                        <i class="lni lni-user"></i>
                        <span>Profile</span>
                    </a>
                </li>

                <!-- Lateness Section -->
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#lateness" aria-expanded="false" aria-controls="lateness">
                        <i class="bx bx-file"></i> <!-- Using bx-file for report -->
                        <span>Report</span>
                    </a>
                    <ul id="lateness" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="lateness.php" class="sidebar-link">Lateness</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#leave" aria-expanded="false" aria-controls="leave">
                        <i class="lni lni-calendar"></i>
                        <span>Leave</span>
                    </a>
                    <ul id="leave" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                data-bs-target="#leave-types" aria-expanded="false" aria-controls="leave-types">
                                Leave Categories
                            </a>
                            <ul id="leave-types" class="sidebar-dropdown list-unstyled collapse">
                                <li class="sidebar-item">
                                    <a href="emergency.php" class="sidebar-link">Emergency Leave</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="staffHoliday.php" class="sidebar-link">Holiday</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#myLog" aria-expanded="false" aria-controls="myLog">
                        <i class="bx bx-history"></i>
                        <span>My Log</span>
                    </a>
                    <ul id="myLog" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">Emergency Log</a>
                        </li>
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">Lateness Log</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="homepage.html" class="sidebar-link">
                    <i class="lni lni-exit"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <div class="main">
        <nav class="navbar navbar-expand-lg navbar-light bg-white px-4 py-3 rounded shadow">
    <div class="d-flex align-items-center ms-auto">
        <form class="form-inline d-flex">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>
</nav>

            <main class="content px-3 py-4">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card shadow">
                                <div class="card-header bg-primary text-white">
                                    <h1 class="fw-bold fs-4 mb-0">Edit Profile</h1>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="profilePicture" class="form-label">Profile Picture:</label>
                                            <img id="profilePreview" src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="img-thumbnail mt-2">
                                            <input type="file" id="profilePicture" name="profile_picture" class="form-control mt-2" accept="image/*" onchange="previewImage(event)">
                                        </div>


                                        <div class="mb-3">
    <label for="department" class="form-label">Department:</label>
    <select id="department" name="department" class="form-control" onchange="updateUnits()">
        <option value="management_services" <?php echo $department == 'management_services' ? 'selected' : ''; ?>>Management Services</option>
        <option value="land" <?php echo $department == 'land' ? 'selected' : ''; ?>>Land</option>
        <option value="district_development" <?php echo $department == 'district_development' ? 'selected' : ''; ?>>District Development</option>
    </select>
</div>
<div class="mb-3">
    <label for="unit" class="form-label">Unit:</label>
    <select id="unit" name="unit" class="form-control" required>
        <option value="" disabled selected>Select Unit</option>
        <!-- Dynamic Units will be loaded here -->
    </select>
    <div class="invalid-feedback">Please select a unit.</div>
</div>


                                        <div class="mb-3">
                                            <label for="position" class="form-label">Position:</label>
                                            <input type="text" id="position" name="position" class="form-control" value="<?php echo $position; ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number:</label>
                                            <input type="tel" id="phone" name="phone" placeholder="Phone Number"
                                                inputmode="tel" pattern="\+60\d{2}-\d{7}" oninput="enforcePhoneFormat(this)"
                                                maxlength="15" class="form-control" value="<?php echo $phone_number; ?>">
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-success me-2 w-40">Save</button>
                                            <button type="button" class="btn btn-danger w-20" onclick="resetFormAndRedirect()">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="script.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateUnits(); 
            const hamBurger = document.querySelector(".toggle-btn");
            if (hamBurger) {
                hamBurger.addEventListener("click", function() {
                    const sidebar = document.querySelector("#sidebar");
                    sidebar.classList.toggle("expand");
                });
            } else {
                console.error("Toggle button not found.");
            }
        });

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profilePreview');
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function resetFormAndRedirect() {
            const form = document.querySelector('form');
            if (form) {
                form.reset();
            }
            window.location.href = 'profileStaff.php';
        }
        function updateUnits() {
    // Get the selected department value
    var department = document.getElementById("department").value;

    // Define the units array in JavaScript (same as PHP logic)
    var units = {
        'management_services': [
            'Administration Unit',
            'Finance Unit',
            'Technology & Information Unit',
            'Events and Ceremonies Unit'
        ],
        'land': [
            'Land Disposal Unit',
            'Enforcement & Technical Unit',
            'Revenue Unit',
            'Registration Unit'
        ],
        'district_development': [
            'Community Development Unit',
            'Physical Development Unit'
        ]
    };

    // Get the unit dropdown element
    var unitDropdown = document.getElementById("unit");

    // Clear existing options
    unitDropdown.innerHTML = '<option value="" disabled selected>Select Unit</option>';

    // Check if the department has units
    if (units[department]) {
        // Loop through the units for the selected department and create options
        units[department].forEach(function(unit) {
            var option = document.createElement("option");
            option.value = unit.toLowerCase().replace(/\s+/g, '_'); // Optional: format value for easier processing
            option.text = unit;
            unitDropdown.appendChild(option);
        });
    }
}

// Automatically update the units if a department is pre-selected (for editing a form)
window.onload = function() {
    if (document.getElementById("department").value) {
        updateUnits();
    }
};

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