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
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <header>
        <span id="menu-icon" onclick="toggleNav()">&#9776; Menu</span>
        <span class="profile-icon" onclick="toggleProfile()"><i class="fas fa-user"></i></span>
        <div id="user-info">
            <p id="username"><?= isset($userinfo['username']) ? $userinfo['username'] : '' ?></p>
            <a href="/portfoliohub/Logout/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <h1>Home Page</h1>
    </header>

    <nav id="mySidenav">
        <a onclick="navigateTo(event, 'home.php')">
            <i class="fas fa-home"></i> Home
        </a>
        <a onclick="navigateTo(event, 'upload.php')">
            <i class="fas fa-cloud-upload-alt"></i> Upload Document
        </a>
        <a onclick="navigateTo(event, 'profile.php')">
            <i class="fas fa-user"></i> Profile
        </a>
        <a onclick="navigateTo(event, 'view.php')" class="view-images">
            <i class="fas fa-images"></i> View Images
        </a>
        <a onclick="navigateTo(event, 'settings.php')">
            <i class="fas fa-cog"></i> Settings
        </a>
    </nav>
    
    <div class="container">
        
    </div>

    <script src="js/script.js"></script>
</body>

</html>






