<?php
session_start();

// Create connection
$conn = new mysqli('localhost', 'root', '', 'portfoliohub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is authenticated
if (!isset($_SESSION['company_token'])) {
    header("Location: /portfoliohub/Login/index.php");
    die();
}

// Retrieve company info from the database
$sql = "SELECT * FROM companies WHERE token = '{$_SESSION['company_token']}'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
    die();
}

if (mysqli_num_rows($result) > 0) {
    $companyInfo = mysqli_fetch_assoc($result);
    // Check if the company is active
    if ($companyInfo['active_status'] != 1) {
        // If not active, you may want to redirect to a different page or display an error message
        echo "Error: Company is not active.";
        die();
    }
} else {
    header("Location: /portfoliohub/Login/index.php");
    die();
}

// Fetch user names for the current company
$usersSql = "SELECT * FROM users";
$usersResult = mysqli_query($conn, $usersSql);

if (!$usersResult) {
    echo "Error fetching user names: " . mysqli_error($conn);
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Names</title>
    <!-- Include Bootstrap styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css"> <!-- Include your custom CSS file -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>

       
.user-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .user-info-item {
            margin: 0 10px;
        }

        .user-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
        }

        .user-card {
            width: 100%;
            border: 1px solid #ddd;
            margin: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: #fff;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        .user-card:hover {
            transform: scale(1.05);
        }

        .search-bar {
    margin-top: 20px;
    margin-bottom: 20px;
    position: relative; /* Set the position property to relative */
}

/* Additional CSS for the search bar */
.input-group {
    position: relative;
    width: 100%;
}

#searchInput {
    border-radius: 20px !important;
    padding-right: 40px; /* Adjust the padding to make space for the search icon */
}

#search-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer; /* Add cursor pointer for better usability */
}
    </style>
</head>

<body>

<header>
       
        <span class="profile-icon" onclick="toggleProfile()"><i class="fas fa-user"></i></span>
       
        <div id="user-info">
            <div class="user-info-item">
            <p id="username"><?= isset($companyInfo['username']) ? strtoupper($companyInfo['username']) : '' ?></p>
            </div>
            <div class="user-info-item">
                <a href="/portfoliohub/Logout/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- <span id="notification-icon"><i class="fas fa-bell"></i></span> -->
        <h1>Certificate</h1>
    </header>
    <main class="container">
    <div class="search-bar my-4">
        <div class="input-group">
            <input type="text" class="form-control rounded-pill py-2" id="searchInput" placeholder="Search users" aria-label="Search users" aria-describedby="search-icon">
            <button class="btn btn-outline-secondary rounded-pill" type="button" id="search-icon">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <div class="row">
        <?php
        $count = 0;
        while ($user = mysqli_fetch_assoc($usersResult)) {
            if ($count % 3 == 0) {
                echo '</div><div class="row">';
            }

            echo '<div class="col-md-4">';
            echo '<div class="user-card card p-3 mb-4"><a href="user_certificates.php?user_id=' . $user['user_id'] . '" class="text-decoration-none text-dark">';
            echo '<h3>' . $user['username'] . '</h3>';
            // Add a link to the user certificates page
            echo '</a></div>';
            echo '</div>';

            $count++;
        }
        ?>
    </div>
</main>



<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Include Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        // Live search functionality
        $("#searchInput").on("keyup", function () {
            var input, filter, cards, card, username, i;
            input = $(this).val().toLowerCase();
            cards = $(".user-card");
            
            for (i = 0; i < cards.length; i++) {
                card = cards[i];
                username = card.getElementsByTagName("h3")[0].innerText.toLowerCase();
                if (username.indexOf(input) > -1) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            }
        });
    });
</script>

<script src="js/script.js"></script>
</body>

</html>
