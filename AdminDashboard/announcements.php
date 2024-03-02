<!-- announcements.php -->

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
    <title>Announcements</title>
    <link rel="stylesheet" href="css/com.css">
    <link rel="stylesheet" href="css/announcements.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .main-container {
        max-width: 800px;
        margin: 0 auto;
        margin-top: 10px;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
      }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .action-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            margin-right: 10px;
        }

        .page-description {
            text-align: center;
            font-size: 18px;
            margin: 20px;
            padding: 20px;
        }
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
        }

        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            width: 80%;
            position: relative;
        }

        .close-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: black;
            border: none;
            padding: 6px;
            border-radius: 70%;
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

        .tutorial-icon {
        position: fixed;
        top: 100px; /* Adjust the top position as needed */
        right: 20px; /* Adjust the right position as needed */
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        z-index: 1000; /* Ensure it's above other elements */
    }
    /* Add this style to handle scrolling in tutorial lightbox */
.tutorial-lightbox-content {
    max-height: 80vh; /* Adjust the maximum height as needed */
    overflow-y: auto;
}

/* Additional style for tutorial steps */
.tutorial-step {
    margin-bottom: 20px;
}

    </style>

</head>

<body>

    <header>
        <span id="menu-icon" onclick="toggleNav()">&#9776; Menu</span>
        <span class="profile-icon" onclick="toggleProfile()"><i class="fas fa-user"></i></span>
        <div id="user-info">
            <p id="username"><?= isset($userinfo['username']) ? $userinfo['username'] : '' ?></p>
            <a href="/portfoliohub/Logout/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <h1>Announcements</h1>
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

    <div class="tutorial-icon" id="tutorialIcon" onclick="openTutorial()">
            <i class="fas fa-question-circle"></i> Tutorial
            </div>

    <div class="main-container">

            <div class="button-container">
                <a href="#" class="action-button" id="newAnnouncementButton">
                    <i class="fas fa-plus icon"></i> New Announcement
                </a>
                <a href="manageannouncements.php" class="action-button"><i class="fas fa-cogs icon"></i> Manage Announcements</a>
            </div>

            <div class="page-description">
                <p>Welcome to the Announcements page! This is the place where you can share important information, news, and updates with other students and faculty members. Create a new announcement or manage existing ones using the buttons above.</p>
            </div>

            <div class="lightbox" id="announcementLightbox">
                <div class="form-container">
                    <span class="close-icon" onclick="closeLightbox()">
                        <i class="fas fa-times"></i>
                    </span>
                    <form method="post" action="announcementsprocess.php" enctype="multipart/form-data">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required>

                        <label for="description">Description:</label>
                        <textarea id="description" name="description" required></textarea>

                        <label for="image">Upload Image:</label>
                        <input type="file" id="image" name="image" accept="image/*,.pdf">

                        <button type="submit">Submit Announcement</button>
                    </form>
                </div>
            </div>

           

        <!-- ... (your existing code) ... -->

        <div class="lightbox" id="tutorialLightbox">
        <div class="form-container tutorial-lightbox-content">
        <span class="close-icon" onclick="closeTutorial()">
            <i class="fas fa-times"></i>
        </span>
        <h2>Tutorial: Getting Started</h2>

        <div class="tutorial-step">
            <h3>Step 1: Creating a New Announcement</h3>
            <p>
                To create a new announcement, click on the <strong>New Announcement</strong> button.
            </p>
                        <!-- You can embed a video or add more detailed instructions here -->
             <video width="560" height="315" controls autoplay>
                <source src="Video/1.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>

        </div>

        <div class="tutorial-step">
            <h3>Step 2: Managing Announcements</h3>
            <p>
                To manage existing announcements, click on the <strong>Manage Announcements</strong> button.
            </p>
            <video width="560" height="315" controls autoplay>
                <source src="Video/2.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

    </div>
</div>



    </div>
    <script src="js/script.js"></script>

    <script>
      document.getElementById('newAnnouncementButton').addEventListener('click', openLightbox);

function openLightbox() {
    document.getElementById('announcementLightbox').style.display = 'flex';
}

function closeLightbox() {
    document.getElementById('announcementLightbox').style.display = 'none';
}
 // Function to open the tutorial lightbox
 function openTutorial() {
            document.getElementById('tutorialLightbox').style.display = 'flex';
        }

        // Function to close the tutorial lightbox
        function closeTutorial() {
            document.getElementById('tutorialLightbox').style.display = 'none';
        }
    </script>

</body>

</html>

