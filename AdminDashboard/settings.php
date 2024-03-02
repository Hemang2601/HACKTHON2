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
$sql = "SELECT * FROM users WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['user_token']);
$stmt->execute();
$result = $stmt->get_result();

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateProfile'])) {
    $enrollmentNumber = $_POST['enrollmentNumber'];
    $department = $_POST['department'];
    $programName = $_POST['programName'];
    $userId = $userinfo['user_id'];

    // Check if user already has a row in userInformation table
    $checkSql = "SELECT * FROM userInformation WHERE user_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult && $checkResult->num_rows > 0) {
        // User already has a row, update it
        $updateSql = "UPDATE userInformation SET enrollment_number=?, department=?, program_name=? WHERE user_id=?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sssi", $enrollmentNumber, $department, $programName, $userId);
        $updateStmt->execute();

        // Display success alert after update
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Success!",
                    text: "Profile updated successfully!",
                    icon: "success",
                    confirmButtonText: "OK"
                });
            });
        </script>';
    } else {
        // User doesn't have a row, insert a new one
        $insertSql = "INSERT INTO userInformation (user_id, enrollment_number, department, program_name) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("isss", $userId, $enrollmentNumber, $department, $programName);
        $insertStmt->execute();

        // Display success alert after insert
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Success!",
                    text: "Profile created successfully!",
                    icon: "success",
                    confirmButtonText: "OK"
                });
            });
        </script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/settings.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <div class="user-info-item">
                <p id="username"><?= isset($userinfo['username']) ? $userinfo['username'] : '' ?></p>
            </div>
            <div class="user-info-item">
                <a href="/portfoliohub/Logout/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <h1>Settings</h1>
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

    <div class="container mt-4 d-flex justify-content-center">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <!-- Your card content goes here -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">User Profile</h5>
                            <p class="card-text" id="usernameDisplay"><?= isset($userinfo['username']) ? $userinfo['username'] : '' ?></p>
                        </div>
                        <?php if ($userHasInfo) : ?>
                            <!-- User has information, show Update Profile button -->
                            <button id="toggleEditProfileBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#profileModal">
                                Update Profile <i class="fas fa-edit"></i>
                            </button>
                        <?php else : ?>
                            <!-- User does not have information, show Setup Profile button -->
                            <button id="toggleEditProfileBtn" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#profileModal">
                                Setup Profile <i class="fas fa-edit"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="user_id" value="<?= $userinfo['user_id'] ?>">
                        <!-- Display data from userInformation table -->
                        <?php if ($userHasInfo) : ?>
                           
                           <?php endif; ?>

                        <div class="mb-3">
                            <label for="enrollmentNumber" class="form-label">Enrollment Number: <?= $userInformationData['enrollment_number'] ?? '' ?>   </label><p></p>
                            <input type="text" name="enrollmentNumber" class="form-control" value="<?= $userinfo['enrollment_number'] ?? '' ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="department" class="form-label">Department: <?= $userInformationData['department'] ?? '' ?></label>
                            <input type="text" name="department" class="form-control" value="<?= $userinfo['department'] ?? '' ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="programName" class="form-label">Program Name: <?= $userInformationData['program_name'] ?? '' ?></label>
                            <input type="text" name="programName" class="form-control" value="<?= $userinfo['program_name'] ?? '' ?>" required>
                        </div>

                        

                        <button type="submit" name="updateProfile" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>

</body>

</html>
