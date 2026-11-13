<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Holiday</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="adminPanel.css">
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
    <a href="adminPanel.php" class="sidebar-link">
        <i class="fas fa-chart-line"></i> <!-- Chart icon for Dashboard -->
        <span>Dashboard</span>
    </a>
</li>
                <li class="sidebar-item">
                    <a href="profileAdmin.php" class="sidebar-link">
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
                            <a href="adminLateness.php" class="sidebar-link">Lateness</a>
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
                data-bs-target="#leave-categories" aria-expanded="false" aria-controls="leave-categories">
                Leave Categories
            </a>
            <ul id="leave-categories" class="sidebar-dropdown list-unstyled collapse">
                <li class="sidebar-item">
                    <a href="adminEmergency.php" class="sidebar-link">Emergency Leave</a>
                </li>
            </ul>
        </li>
        
        <!-- Holiday Section -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                data-bs-target="#holiday-categories" aria-expanded="false" aria-controls="holiday-categories">
                Holiday
            </a>
            <ul id="holiday-categories" class="sidebar-dropdown list-unstyled collapse">
                <li class="sidebar-item">
                    <a href="addHoliday.php" class="sidebar-link">Add Holiday</a>
                </li>
                <li class="sidebar-item">
                    <a href="viewHoliday.php" class="sidebar-link">View Holidays</a>
                </li>
            </ul>
        </li>
    </ul>
</li>


            </ul>
            <div class="sidebar-footer">
                <a href="adminLogout.php" class="sidebar-link">
                    <i class="lni lni-exit"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <div class="main">
        <nav class="navbar navbar-expand-lg navbar-light bg-white px-4 py-3 rounded shadow">
    <div class="d-flex align-items-center ms-auto">
    <form class="form-inline d-flex" method="GET" action="adminEmergency.php">
                        <input class="form-control me-2" type="search" placeholder="Search" name="search" aria-label="Search">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </form>

    </div>
</nav>    <main class="content px-3 py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h1 class="fw-bold fs-4 mb-0">Add Holiday</h1>
                    </div>
                    <div class="card-body">
                        <form id="holidayForm" action="submitHoliday.php" method="POST">
                            <div class="form-group">
                                <div class="sub-heading mb-4">
                                    <h3 class="fw-bold fs-5">Please fill in the holiday details below to add a new holiday.</h3>
                                </div>

                                <!-- Holiday Name -->
                                <div class="mb-3">
                                    <label for="holiday_name" class="form-label">Holiday Name:</label>
                                    <input type="text" id="holiday_name" name="holiday_name" class="form-control" required>
                                </div>

                                <!-- Holiday Start Date -->
                                <div class="mb-3">
                                    <label for="holiday_start" class="form-label">Start Date:</label>
                                    <input type="date" id="holiday_start" name="holiday_start" class="form-control" required onchange="calculateHolidayDays()">
                                </div>

                                <!-- Holiday End Date -->
                                <div class="mb-3">
                                    <label for="holiday_end" class="form-label">End Date:</label>
                                    <input type="date" id="holiday_end" name="holiday_end" class="form-control" required onchange="calculateHolidayDays()">
                                </div>

                                <!-- Duration Calculation -->
                                <div class="mb-3">
                                    <label for="holiday_duration" class="form-label">Holiday Duration (Days):</label>
                                    <input type="text" id="holiday_duration" name="holiday_duration" class="form-control" readonly>
                                </div>

                                <!-- Buttons -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-submit me-2">Submit</button>
                                    <button type="button" class="btn btn-cancel" onclick="resetForm()">Cancel</button>
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

        function calculateHolidayDays() {
        const startDate = document.getElementById('holiday_start').value;
        const endDate = document.getElementById('holiday_end').value;

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);

            const timeDiff = end - start;
            const daysDiff = timeDiff / (1000 * 3600 * 24); 

            if (daysDiff >= 0) {
                document.getElementById('holiday_duration').value = daysDiff + 1; 
            } else {
                alert("End date cannot be before start date.");
                document.getElementById('holiday_duration').value = '';
            }
        }
    }

    function resetForm() {
        document.getElementById('holidayForm').reset();
    }

        </script>

</body>

</html>