<?php
session_start();

// Check if user is authenticated
if (!isset($_SESSION['user_token'])) {
    header("Location: /portfoliohub/Login/index.php");
    die();
}

// Create connection
$conn = new mysqli('localhost', 'root', '', 'portfoliohub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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

// Check if the user has a row in the userInformation table
$checkInfoSql = "SELECT * FROM userInformation WHERE user_id = ?";
$checkInfoStmt = $conn->prepare($checkInfoSql);
$checkInfoStmt->bind_param("i", $userinfo['user_id']);
$checkInfoStmt->execute();
$checkInfoResult = $checkInfoStmt->get_result();

// Check if the query was successful
if (!$checkInfoResult) {
    echo "Error: " . mysqli_error($conn);
    die();
}

// Determine if the user has information in userInformation table
$userHasInfo = mysqli_num_rows($checkInfoResult) > 0;

// Fetch user information from the userInformation table
$userInfoSql = "SELECT * FROM userInformation WHERE user_id = ?";
$userInfoStmt = $conn->prepare($userInfoSql);
$userInfoStmt->bind_param("i", $userinfo['user_id']);
$userInfoStmt->execute();
$userInfoResult = $userInfoStmt->get_result();

if (!$userInfoResult) {
    echo "Error: " . mysqli_error($conn);
    die();
}

// Check if there is user information
if ($userInfoResult->num_rows > 0) {
    $userInformationData = $userInfoResult->fetch_assoc();
}

$pageTitle = "Profile";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .center-screen {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            margin-left: 500px;
            
        }
    </style>
</head>

<body>

    <?php include('header.php'); ?>

    <div class="center-screen">
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">User Profile</h5>
                            <p class="card-text"><strong>Username:</strong> <?= isset($userinfo['username']) ? $userinfo['username'] : '' ?></p>
                            <?php if ($userHasInfo) : ?>
                                <p class="card-text"><strong>Enrollment Number:</strong> <?= $userInformationData['enrollment_number'] ?? '' ?></p>
                                <p class="card-text"><strong>Department:</strong> <?= $userInformationData['department'] ?? '' ?></p>
                                <p class="card-text"><strong>Program Name:</strong> <?= $userInformationData['program_name'] ?? '' ?></p>
                            <?php else : ?>
                                <p class="card-text">No additional information available. <a href="settings.php">Setup your profile</a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>

</body>

</html>

