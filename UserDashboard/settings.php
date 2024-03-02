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
$pageTitle="Settings";
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
        /* Styles for User Profile Card */
.container {
    margin-top: 4rem;
}

.card {
    width: 100%;
    padding: 1.5rem;
}

.card-title {
    font-size: 1.5rem;
}

#usernameDisplay {
    font-size: 1.2rem;
}

.btn-primary, .btn-secondary {
    margin-top: 1rem;
}

/* Styles for Edit Profile Modal */
.modal-content {
    padding: 1.5rem;
}

.modal-title {
    font-size: 1.5rem;
}

.mb-3 {
    margin-bottom: 1rem;
}

/* Center the modal vertically */
.modal-dialog {
    display: flex;
    align-items: center;
    min-height: calc(100vh - 10rem);
}

/* Optional: Add a subtle box-shadow to the modal */
.modal-content {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}
    </style>
</head>

<body>

    <?php include('header.php'); ?>

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
                            <!-- Display data from userInformation table -->
                            <div class="mb-3">
                                <label for="enrollmentNumber" class="form-label">Enrollment Number: <?= $userInformationData['enrollment_number'] ?? '' ?></label>
                            </div>
                            <div class="mb-3">
                                <label for="department" class="form-label">Department: <?= $userInformationData['department'] ?? '' ?></label>
                            </div>
                            <div class="mb-3">
                                <label for="programName" class="form-label">Program Name: <?= $userInformationData['program_name'] ?? '' ?></label>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="enrollmentNumber" class="form-label">Enrollment Number:</label>
                            <input type="text" name="enrollmentNumber" class="form-control" value="<?= $userInformationData['enrollment_number'] ?? '' ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="department" class="form-label">Department:</label>
                            <input type="text" name="department" class="form-control" value="<?= $userInformationData['department'] ?? '' ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="programName" class="form-label">Program Name:</label>
                            <input type="text" name="programName" class="form-control" value="<?= $userInformationData['program_name'] ?? '' ?>" required>
                        </div>

                        <button type="submit" name="updateProfile" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>

</body>

</html>
