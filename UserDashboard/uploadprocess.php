<?php
// Start or resume the session
session_start();

// Include the database connection file
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    // Redirect to login or handle unauthorized access
    header("Location: login.php"); // Change login.php to your login page
    exit();
}

// Get the user's token and fetch the associated user_id and username from the database
$userToken = $_SESSION['user_token'];

// Fetch the user_id and username based on the user_token
$sql = "SELECT user_id, username FROM users WHERE token = '$userToken'";
$result = $conn->query($sql);

if (!$result) {
    echo "Error: " . $conn->error;
    die();
}

if ($result->num_rows > 0) {
    $userRow = $result->fetch_assoc();
    $userId = $userRow['user_id'];
    $username = $userRow['username'];
} else {
    // Redirect or handle the case where the user is not found
    header("Location: login.php");
    exit();
}

// Directory where uploads will be stored
$uploadDir = "CERTIFICATES/";

// Create user-specific directory using the fetched username
$userDir = $uploadDir . strtoupper($username) . "/";
if (!file_exists($userDir)) {
    mkdir($userDir, 0777, true);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the uploaded file
    $category = strtoupper($_POST['category']);
    $organization = strtoupper($_POST['organization']);
    $certificateName = $_POST['certificate_name'];
    $date = $_POST['date'];

    // Create category-specific subdirectory if not exists
    $categoryDir = $userDir . $category . "/";
    if (!file_exists($categoryDir)) {
        mkdir($categoryDir, 0777, true);
    }

    // Get the file name and extension
    $fileName = basename($_FILES["file"]["name"]);
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    // Define allowed file types
    $allowedFileTypes = array('jpeg', 'jpg', 'png', 'gif', 'bmp', 'pdf');

    // Check if the file type is allowed
    if (in_array(strtolower($fileExtension), $allowedFileTypes)) {
        // Move the file to the specified directory
        $targetFile = $categoryDir . $fileName;
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            // File upload successful

            $validationStatus = 'Pending'; // Default status

             $sql = "INSERT INTO certificates (user_id, category, organization, certificate_name, date, file_path, validation_status) VALUES ('$userId', '$category', '$organization', '$certificateName', '$date', '$targetFile', '$validationStatus')";
            $conn->query($sql);

            // Display SweetAlert after successful image upload
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Certificate Uploaded!",
                            text: "Your certificate has been uploaded successfully! Faculty will validate it shortly. Afterward, you can check the validation status at the Activity Status page.",
                            icon: "success",
                        }).then(() => {
                            window.location.href = "upload.php";
                        });
                    });
                  </script>';
        } else {
            // File upload failed
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error Uploading Certificate",
                    text: "There was an issue uploading your certificate. Please try again. If the problem persists, contact support.",
                    icon: "error",
                }).then(() => {
                    window.location.href = "upload.php";
                });
            });
          </script>';
        }
    } else {
        // File type not allowed
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Invalid File Type",
                    text: "Only JPEG, PNG, GIF, BMP, and PDF files are allowed.",
                    icon: "error",
                }).then(() => {
                    window.history.back();
                });
            });
          </script>';
    }
} else {
    // Redirect if accessed directly without submitting the form
    header("Location: /portfoliohub/Login/index.php"); // Change index.php to your form page
    exit();
}

// Close the database connection
$conn->close();
?>
