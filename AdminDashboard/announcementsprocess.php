<?php
session_start();

// Check if the user is authenticated
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

// Initialize $stmt
$stmt = null;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process other form data
    $title = $_POST["title"];
    $description = $_POST["description"];

    // Process uploaded file
    $targetDirectory = "CERTIFICATE/ADMIN/ANNOUNCEMENT/";
    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0755, true);
    }

    $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an image or PDF
    $allowedFormats = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
    if (!in_array($imageFileType, $allowedFormats)) {
        echo "Sorry, only JPG, JPEG, PNG, GIF, and PDF files are allowed.";
        $uploadOk = 0;
    }

    // Check if the file size is within limits (adjust as needed)
    if ($_FILES["image"]["size"] > 5242880) { // 5 MB
        echo "Sorry, your file is too large. Maximum file size is 5 MB.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Move the uploaded file to the CERTIFICATE/ADMIN/announcement directory
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Check if the file already exists in the database
            $checkDuplicateSql = "SELECT COUNT(*) AS count FROM announcements WHERE image_path = ?";
            $checkStmt = $conn->prepare($checkDuplicateSql);
            $checkStmt->bind_param("s", $targetFile);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $checkData = $checkResult->fetch_assoc();

            if ($checkData['count'] == 0) {
                // Fetch user ID based on some unique identifier (e.g., username)
                $user_token = $_SESSION['user_token'];
                $userQuery = "SELECT user_id FROM users WHERE token = ?";

                // Use prepared statement to avoid SQL injection
                $stmt = $conn->prepare($userQuery);
                $stmt->bind_param("s", $user_token);
                $stmt->execute();
                $userResult = $stmt->get_result();

                if ($userResult->num_rows > 0) {
                    $userData = $userResult->fetch_assoc();
                    $userId = $userData['user_id'];

                    // Insert data into the database using prepared statement
                    $insertSql = "INSERT INTO announcements (user_id, title, description, image_path) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($insertSql);
                    $stmt->bind_param("isss", $userId, $title, $description, $targetFile);

                    if ($stmt->execute()) {
                    // Display SweetAlert success notification and redirect to announcements.php
                    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function () {
                            Swal.fire({
                                icon: "success",
                                title: "Announcement Declared!",
                                text: "The latest announcement has been declared for everyone.",
                                showConfirmButton: true
                            }).then(function(result) {
                                if (result.isConfirmed) {
                                    window.location.href = "announcements.php";
                                }
                            });
                        });
                    </script>';

                    } else {
                        echo "Error inserting data into the database: " . $stmt->error;
                    }
                } else {
                    echo "User not found.";
                }
            } else {
                // Display SweetAlert error notification for duplicate image
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: "The file already exists in the database.",
                            confirmButtonText: "OK"
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                window.location.href = "announcements.php";
                            }
                        });
                    });
                </script>';
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Close the statement if it's not null
if ($stmt !== null) {
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
