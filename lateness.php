

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lateness Report Application</title>
    <!-- Add Font Awesome (if not already included) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <h1 class="fw-bold fs-4 mb-0">Lateness Report Application</h1>
                    </div>
                    <div class="card-body">
                        <form id="latenessForm" action="submitLateness.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <div class="sub-heading mb-4">
                                    <h3 class="fw-bold fs-5">
                                        Please fill in your lateness report application and ensure all required fields are completed correctly to avoid any issues with processing.
                                    </h3>
                                </div>

                                <!-- Date of Lateness -->
                                <div class="mb-3">
                                    <label for="lateness_date" class="form-label">Date of Lateness:</label>
                                    <input type="date" id="lateness_date" name="lateness_date" class="form-control" required>
                                </div>

                                <!-- Reason for Lateness -->
                                <div class="mb-3">
                                    <label for="lateness_reason" class="form-label">Reason for Lateness:</label>
                                    <textarea id="lateness_reason" name="lateness_reason" class="form-control" rows="3" required></textarea>
                                </div>

                                <!-- File Upload Section -->
                                <div class="mb-3">
				                <label for="file" class="form-label">Select file</label>
				                <input type="file" class="form-control" name="file" id = "file">
			                    </div>

                                <!-- Error Message for Invalid File -->
                                <p id="file-error" class="text-danger" style="display: none;">
                                    Invalid file type. Please upload a PDF, DOC, DOCX, JPG, PNG, or JPEG file.
                                </p>

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

         // Show buttons when a file is selected
    document.getElementById('lateness_file').addEventListener('change', function () {
        const fileActions = document.getElementById('fileActions');
        if (this.files.length > 0) {
            fileActions.style.display = 'block';
        } else {
            fileActions.style.display = 'none';
        }
    });

    // Function to confirm upload
    function confirmUpload() {
        alert('File uploaded successfully!');
        const fileActions = document.getElementById('fileActions');
        fileActions.style.display = 'none';
    }

    // Function to cancel upload
    function cancelUpload() {
        const fileInput = document.getElementById('lateness_file');
        fileInput.value = ''; // Clear the file input
        const fileActions = document.getElementById('fileActions');
        fileActions.style.display = 'none';
        alert('File upload canceled.');
    }

    // Function to reset form
    function resetForm() {
        document.getElementById('latenessForm').reset();
        document.getElementById('fileActions').style.display = 'none';
    }
        </script>

</body>

</html>