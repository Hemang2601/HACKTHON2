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
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <!-- Bootstrap CSS link -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS link (if not included in Bootstrap) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <!-- SweetAlert2 CSS link -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
     <style>
        #mySidenav a {
        color: white;
        text-decoration: none; /* Remove underline */
        padding: 15px; /* Add padding for better readability */
        display: block;
        }

        #mySidenav a:hover {
            background-color: #555; /* Add a background color on hover for better feedback */
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
        <h1>Profile</h1>
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

<div class="container mt-4">
    <div class="row ">
        <div class="col-md-6">
            <div class="card">
                
                <div class="card-body">
                    <h5 class="card-title">User Profile</h5>
                    <p class="card-text"><strong>Username:</strong> <?= isset($userinfo['username']) ? $userinfo['username'] : '' ?></p>
                    <p class="card-text"><strong>Email:</strong> <?= isset($userinfo['email']) ? $userinfo['email'] : '' ?></p>

                    <!-- Check if user has additional information -->
                    <?php if (isset($userinfo['user_id']) && $userinfo['user_id']) : ?>
                        <?php
                            // Retrieve additional information from userInformation table
                            $additionalInfoSql = "SELECT * FROM userInformation WHERE user_id = {$userinfo['user_id']}";
                            $additionalInfoResult = mysqli_query($conn, $additionalInfoSql);

                            if ($additionalInfoResult && mysqli_num_rows($additionalInfoResult) > 0) {
                                $additionalInfo = mysqli_fetch_assoc($additionalInfoResult);
                        ?>
                            <p class="card-text"><strong>Enrollment Number:</strong> <?= $additionalInfo['enrollment_number'] ?? 'N/A' ?></p>
                            <p class="card-text"><strong>Department:</strong> <?= $additionalInfo['department'] ?? 'N/A' ?></p>
                            <p class="card-text"><strong>Program Name:</strong> <?= $additionalInfo['program_name'] ?? 'N/A' ?></p>
                        <?php } else { ?>
                            <p class="card-text">Additional information not available.</p>
                        <?php } ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
</body>

</html>
