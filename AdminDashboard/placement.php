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

// Fetch usernames of users with role = 0
$usernamesQuery = "SELECT username FROM users WHERE role = 0";
$usernamesResult = mysqli_query($conn, $usernamesQuery);

if (!$usernamesResult) {
    echo "Error fetching usernames: " . mysqli_error($conn);
    die();
}

// Store usernames in an array
$usernames = array();
while ($row = mysqli_fetch_assoc($usernamesResult)) {
    $usernames[] = $row['username'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Page</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .placement-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
        }

        .placement-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            width: 200px;
            text-align: center;
            background-color: #f9f9f9;
        }

        .placement-card:hover {
            background-color: #e1e1e1;
        }

        /* Style for the search bar */
        .search-bar {
            text-align: center; /* Center the content horizontally */
            margin-top: 10px;
        }

        .search-bar input {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Style for the search icon */
        .search-icon {
            position: relative;
            left: -25px;
            color: #555;
            cursor: pointer;
        }
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

    <!-- Header Section -->
    <header>
        <span id="menu-icon" onclick="toggleNav()">&#9776; Menu</span>
        <span class="profile-icon" onclick="toggleProfile()"><i class="fas fa-user"></i></span>
        <div id="user-info">
            <p id="username"><?= isset($userinfo['username']) ? $userinfo['username'] : '' ?></p>
            <a href="/portfoliohub/Logout/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <h1>Placement Page</h1>
    </header>

    <!-- Navigation Section -->
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

    <!-- Add Search Bar -->
    <div class="search-bar">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="searchUsername" placeholder="Search Username" oninput="filterUsernames()">
    </div>


    <div class="container">
        <h2>Placement History</h2>
        <div class="placement-container">
            <?php foreach ($usernames as $username) : ?>
                <div class="placement-card" onclick="showUserDetails('<?php echo $username; ?>')">
                    <h3><?php echo $username; ?></h3>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Script for Navigation and Profile toggles -->
    <script src="js/script.js"></script>

    <!-- Script for showing user details -->
    <script>
        function showUserDetails(username) {
            // Redirect to a details page or show details in a modal
            window.location.href = 'user_details.php?username=' + username;
        }

        // Filter usernames based on search input
        function filterUsernames() {
            var input, filter, container, cards, card, h3, i, txtValue;
            input = document.getElementById("searchUsername");
            filter = input.value.toUpperCase();
            container = document.getElementsByClassName("placement-container")[0];
            cards = container.getElementsByClassName("placement-card");

            for (i = 0; i < cards.length; i++) {
                card = cards[i];
                h3 = card.getElementsByTagName("h3")[0];
                txtValue = h3.textContent || h3.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            }
        }
        
        // Execute the filter function on page load
        filterUsernames();
    </script>

</body>

</html>
