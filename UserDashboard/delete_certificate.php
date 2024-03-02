<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    header("Location: login.php");
    exit();
}

// Ensure that the certificate ID is provided through the query parameter
if (!isset($_GET['id'])) {
    header("Location: activity.php"); // Redirect to activity page if no ID is provided
    exit();
}

// Get the certificate ID from the query parameter
$certificateId = $_GET['id'];

// Fetch user details based on the user_token
$userToken = $_SESSION['user_token'];
$sqlUser = "SELECT user_id FROM users WHERE token = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $userToken);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if (!$resultUser) {
    echo "Error: " . $conn->error;
    die();
}

if ($resultUser->num_rows > 0) {
    $userRow = $resultUser->fetch_assoc();
    $userId = $userRow['user_id'];

    // Check if the certificate belongs to the logged-in user
    $sqlCertificate = "SELECT * FROM certificates WHERE certificate_id = ? AND user_id = ?";
    $stmtCertificate = $conn->prepare($sqlCertificate);
    $stmtCertificate->bind_param("ii", $certificateId, $userId);
    $stmtCertificate->execute();
    $resultCertificate = $stmtCertificate->get_result();

    if ($resultCertificate->num_rows > 0) {
        // Certificate belongs to the logged-in user, proceed with deletion
        $deleteSql = "DELETE FROM certificates WHERE certificate_id = ?";
        $stmtDelete = $conn->prepare($deleteSql);
        $stmtDelete->bind_param("i", $certificateId);

        if ($stmtDelete->execute()) {
            // Get the file path before deletion
            $fileSql = "SELECT file_path FROM certificates WHERE certificate_id = ?";
            $stmtFile = $conn->prepare($fileSql);
            $stmtFile->bind_param("i", $certificateId);
            $stmtFile->execute();
            $resultFile = $stmtFile->get_result();

            if ($resultFile->num_rows > 0) {
                $fileRow = $resultFile->fetch_assoc();
                $filePath = $fileRow['file_path'];

                // Delete the file from the directory
                if (file_exists($filePath)) {
                    unlink($filePath);

                    // Display SweetAlert after successful deletion
                    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                    echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    title: "Certificate Deleted!",
                                    text: "Your certificate has been deleted successfully!",
                                    icon: "success",
                                }).then(() => {
                                    window.location.href = "activity.php"; // Change to the appropriate page
                                });
                            });
                          </script>';
                    exit();
                }
            }

            // Deletion successful (without file deletion)
            header("Location: activity.php"); // Redirect to activity page after deletion
            exit();
        } else {
            // Deletion failed
            displayErrorAlert("Error Deleting Certificate");
        }
    } else {
        // Certificate does not belong to the logged-in user
        displayErrorAlert("Unauthorized Access");
    }
} else {
    // Redirect if the user is not found
    header("Location: login.php");
    exit();
}

// Close the database connection
$conn->close();

function displayErrorAlert($message) {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error",
                    text: "' . $message . '",
                    icon: "error",
                }).then(() => {
                    window.location.href = "activity.php"; // Change to the appropriate page
                });
            });
          </script>';
}
?>
