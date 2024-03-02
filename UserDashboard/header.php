<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
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
        <h1><?php echo $pageTitle; ?></h1>
    </header>

    <nav id="mySidenav">
    <a onclick="navigateTo(event, 'home.php')">
        <i class="fas fa-home"></i> Home
    </a>
    <a onclick="navigateTo(event, 'upload.php')">
        <i class="fas fa-cloud-upload-alt"></i> Upload Document
    </a>
    <a onclick="navigateTo(event, 'portfolio.php')">
        <i class="fas fa-clipboard"></i> Portfolio
    </a>
    <a onclick="navigateTo(event, 'activity.php')" class="view-activity">
        <i class="fas fa-chart-line"></i> Activity Status
    </a>
    <a onclick="navigateTo(event, 'profile.php')">
        <i class="fas fa-user"></i> Profile
    </a>    
    <a onclick="navigateTo(event, 'settings.php')">
        <i class="fas fa-cog"></i> Settings
    </a>
</nav>


