

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Leave Application</title>
    <!-- Add Font Awesome (if not already included) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Bebas+Neue&family=Bungee+Spice&family=Itim&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&family=Varela+Round&family=Water+Brush&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="staffPanel.css">
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
                            <a href="viewEmergency.php" class="sidebar-link">Emergency Log</a>
                        </li>
                        <li class="sidebar-item">
                            <a href="viewLateness.php" class="sidebar-link">Lateness Log</a>
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
                        <h1 class="fw-bold fs-4 mb-0">Emergency Leaves Application</h1>
                    </div>
                    <div class="card-body">
                        <form action="submitLeave.php" method="POST" enctype="multipart/form-data" id="leaveForm">
                            <div class="form-group">
                                <div class="sub-heading mb-4">
                                    <h3 class="fw-bold fs-5">Please fill in your leave application and ensure all required fields are completed correctly to avoid any issues with processing.</h3>
                                </div>

                                <!-- Leave Date -->
                                <div class="mb-3">
                                    <label for="leave_start" class="form-label">Date:</label>
                                    <input type="date" id="leave_start" name="leave_date" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="leave_type" class="form-label">Leave Type:</label>
                                    <select id="leave_type" name="leave_type" class="form-control" required
                                        onchange="showOtherField(this)">
                                        <option value="" disabled selected>Select Leave Type</option>
                                        <option value="Medical">Medical</option>
                                        <option value="Family Emergency">Family Emergency</option>
                                        <option value="Personal">Personal</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <!-- other Leave Details -->
                                <div class="mb-3" id="other_leave_field" style="display: none;">
                                    <label for="other_leave_details" class="form-label">Specify Other:</label>
                                    <input type="text" id="other_leave_details" name="other_leave_details"
                                        class="form-control">
                                </div>

                                <!-- Leave Duration -->
                                <div class="mb-3">
                                    <label for="leave_duration" class="form-label">Leave Duration:</label>
                                    <select id="leave_duration" name="leave_duration" class="form-control" required
                                        onchange="showLeaveHoursField(this)">
                                        <option value="" disabled selected>Select Duration</option>
                                        <option value="Full Day">Full Day</option>
                                        <option value="Half Day">Half Day</option>
                                        <option value="Hourly">Hourly Leave</option>
                                    </select>
                                </div>


<!-- Leave Hours (only visible if 'Hourly' is selected) -->
<div class="mb-3" id="leave_hours_field" style="display: none;">
                                    <label for="leave_hours" class="form-label">Hours:</label>
                                    <input type="number" id="leave_hours" name="leave_hours" class="form-control"
                                        min="1" max="24">
                                </div>

                                <!-- File Upload Section -->
                                <div class="mb-3">
                                    <label for="file" class="form-label">Upload Supporting Documents (Optional):</label>
                                    <input type="file" class="form-control" name="file" id="file">
                                </div>

                                <!-- Error Message for Invalid File -->
                                <p id="file-error" class="text-danger" style="display: none;">Invalid file type. Please upload a PDF, DOC, DOCX, JPG, PNG, or JPEG file.</p>

                                <!-- Buttons -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-submit me-2" style="background-color: green; color: white;">Submit</button>
                                    <button type="button" class="btn btn-cancel" onclick="resetForm()" style="background-color: red; color: white;">Cancel</button>
                                </div>
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

document.addEventListener("DOMContentLoaded", function () {
            const hamBurger = document.querySelector(".toggle-btn");
            if (hamBurger) {
                hamBurger.addEventListener("click", function () {
                    const sidebar = document.querySelector("#sidebar");
                    sidebar.classList.toggle("expand");
                });
            } else {
                console.error("Toggle button not found.");
            }
        });

        function showOtherField(select) 
        {
            document.getElementById('other_leave_field').style.display = select.value === "Other" ? "block" : "none";
        }



    function showLeaveHoursField(select)
     {
            document.getElementById('leave_hours_field').style.display = select.value === "Hourly" ? "block" : "none";
        }



        </script>

</body>

</html>