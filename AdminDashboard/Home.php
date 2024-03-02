<?php
session_start();

// Create connection
$conn = new mysqli('localhost', 'root', '', 'portfoliohub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is authenticated
if (!isset($_SESSION['user_token'])) {
    header("Location: /portfoliohub/Login/index.php");
    die();
}

// Retrieve user info from the database
$sql = "SELECT * FROM users WHERE token = '{$_SESSION['user_token']}'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
    die();
}

if (mysqli_num_rows($result) > 0) {
    $userinfo = mysqli_fetch_assoc($result);
} else {
    header("Location: /portfoliohub/Login/index.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
       .tutorial-icon {
    position: pos-fixed;
    margin-left: 1380px;
    margin-top: 10px;
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    z-index: 1000; /* Ensure it's above other elements */
    width: 80px; /* Set the width as needed */
}

    /* Add this inside your existing <style> tag or your CSS file */

.lightbox {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    z-index: 1000; /* Ensure it's above other elements */
}

.form-container {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    max-width: 600px;
    width: 80%;
    max-height: 80vh; /* Adjust the maximum height as needed */
    overflow-y: auto;
    position: relative;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
}

.close-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    background: black; /* Adjust color as needed */
    border: none;
    padding: 6px;
    border-radius: 50%;
    font-size: 18px;
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Add this if you're using Font Awesome icons */
.close-icon i {
    font-size: inherit;
    margin: 0; /* Remove any default margin */
    padding: 0; /* Remove any default padding */
}

h2 {
    text-align: center;
}

p {
    margin-bottom: 15px;
}

.tutorial-step {
    margin-bottom: 20px;
}

.tutorial-step h3 {
    margin-bottom: 10px;
}

/* Customize the styles as needed */

    </style>
</head>

<body>

<header>
        <span id="menu-icon" onclick="toggleNav()">&#9776; Menu</span>
        <span class="profile-icon" onclick="toggleProfile()"><i class="fas fa-user"></i></span>
        <div id="user-info">
            <div class="user-info-item">
                <p id="username"><?= isset($userinfo['username']) ? $userinfo['username'] : '' ?></p>
            </div>
            <div class="user-info-item">
                <a href="/portfoliohub/Logout/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <h1>Home Page</h1>
    </header>

    <nav id="mySidenav">
        <a onclick="navigateTo(event, 'home.php')">
            <i class="fas fa-home"></i> Home
        </a>
        <a onclick="navigateTo(event, 'announcements.php')">
            <i class="fas fa-bullhorn"></i> Announcements
        </a>
        <a onclick="navigateTo(event, 'placement.php')">
            <i class="fas fa-briefcase"></i> Placement
        </a>
        <a onclick="navigateTo(event, 'profile.php')">
            <i class="fas fa-user"></i> Profile
        </a>
        <a onclick="navigateTo(event, 'settings.php')">
            <i class="fas fa-cog"></i> Settings
        </a>
    </nav>
    <!-- Add this inside your <body> tag, after the existing content -->
<div class="tutorial-icon" id="tutorialIcon" onclick="openTutorial()">
    <i class="fas fa-question-circle"></i> Tutorial
</div>

    <!-- Add this inside your <div class="container"> -->
    <div class="card-container">
        <?php
        $pendingCertificatesSql = "SELECT c.*, u.username 
            FROM certificates c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.validation_status = 'Pending'";
        $pendingCertificatesResult = mysqli_query($conn, $pendingCertificatesSql);

        if ($pendingCertificatesResult && mysqli_num_rows($pendingCertificatesResult) > 0) {
            while ($certificate = mysqli_fetch_assoc($pendingCertificatesResult)) {
                ?>
                <div class="card">
                    <h2><?php echo $certificate['certificate_name']; ?></h2>
                    <p>Organization: <?php echo $certificate['organization']; ?></p>
                    <p>Name: <?php echo $certificate['username']; ?></p>
                    <p>Category: <?php echo $certificate['category']; ?></p>
                    <p>Date: <?php echo $certificate['date']; ?></p>

                    <?php
                    $fileExtension = pathinfo($certificate['file_path'], PATHINFO_EXTENSION);

                    if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif'])) {
                        // Display image
                        echo '<img src="/portfoliohub/UserDashboard/' . $certificate['file_path'] . '" alt="Certificate Image" style="width: 300px; height: 200px;">';
                    } elseif (in_array(strtolower($fileExtension), ['pdf'])) {
                        // Display PDF
                        echo '<iframe src="/portfoliohub/UserDashboard/' . $certificate['file_path'] . '" width="300px" height="200px"></iframe>';
                    } else {
                        // Handle other file types or display an error message
                        echo '<p>Unsupported file type</p>';
                    }
                    ?>

                    <p>Status: <?php echo $certificate['validation_status']; ?></p>

                    <!-- Validation button -->
                    <button class="validate-button" data-certificate-id="<?php echo $certificate['certificate_id']; ?>">Validation</button>
                </div>
                <?php
            }
        } else {
            echo "<p>No pending certificates found.</p>";
        }
        ?>
    </div>

    <div class="lightbox" id="tutorialLightbox">
    <div class="form-container">
        <span class="close-icon" onclick="closeTutorial()">
            <i class="fas fa-times"></i>
        </span>
        <h2>Validation Feature Tutorial</h2>
        <p>Welcome to the validation feature tutorial. Follow the steps below to validate or reject certificates:</p>
        
        <div class="tutorial-step">
            <h3>Step 1: View Pending Certificates</h3>
            <p>On the home page, you can see a list of pending certificates awaiting validation.</p>
        </div>
        
        <div class="tutorial-step">
            <h3>Step 2: Click on Validation Button</h3>
            <p>For each certificate, click on the "Validation" button to either validate or reject the certificate.</p>
        </div>
        
        <div class="tutorial-step">
            <h3>Step 3: Confirm Action</h3>
            <p>A dialog will appear. Click "Validate" to approve the certificate or "Reject" to reject it.</p>
        </div>

        <!-- Add the video element with your video source -->
        <video controls width="100%" controls autoplay>
            <source src="Video/3.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
</div>


    <script src="js/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Add an event listener to all validate buttons
            var validateButtons = document.querySelectorAll('.validate-button');
            validateButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    // Get the certificate ID from the button's data attribute
                    var certificateId = this.getAttribute('data-certificate-id');

                    // Show SweetAlert dialog
                    Swal.fire({
                        title: 'Certificate Validation',
                        text: 'Choose an action:',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Validate',
                        cancelButtonText: 'Reject'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // User clicked "Validate"
                            performValidation(certificateId, 'validate');
                        } else {
                            // User clicked "Reject"
                            performValidation(certificateId, 'reject');
                        }
                    });
                });
            });

            function performValidation(certificateId, action) {
                // Use fetch to send a POST request to validate_certificate.php
                fetch('validate_certificate.php?action=' + action + '&certificate_id=' + certificateId, {
                        method: 'POST'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Handle the response
                        if (data.status === 'success') {
                            // Show SweetAlert success notification with certificate ID
                            Swal.fire({
                                icon: 'success',
                                title: 'Certificate Validation',
                                text: `Certificate ${action} successfully by Admin.`,
                            }).then(() => {
                                window.location.reload(); // Refresh the page for simplicity
                            });
                        } else {
                            // Show SweetAlert error notification
                            Swal.fire({
                                icon: 'error',
                                title: 'Certificate Validation',
                                text: `Error ${action} certificate.`,
                            });
                        }
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                    });
            }
        });

     
    function openTutorial() {
        document.getElementById('tutorialLightbox').style.display = 'flex';
    }

    function closeTutorial() {
        document.getElementById('tutorialLightbox').style.display = 'none';
    }

    </script>
</body>

</html>
