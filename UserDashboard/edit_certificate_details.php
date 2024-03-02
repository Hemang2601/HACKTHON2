<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details based on the user_token
$userToken = $_SESSION['user_token'];
$sql = "SELECT user_id, username FROM users WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userToken);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo "Error: " . $conn->error;
    die();
}

if ($result->num_rows > 0) {
    $userRow = $result->fetch_assoc();
    $userId = $userRow['user_id'];
    $username = $userRow['username'];
} else {
    // Redirect if the user is not found
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
    // Check if the file input is set and has no errors
    if (isset($_FILES["editCertificateFile"]) && $_FILES["editCertificateFile"]["error"] == UPLOAD_ERR_OK) {
        // Process the uploaded file
        $category = strtoupper($_POST['editCategory']);
        $organization = strtoupper($_POST['editOrganization']);
        $certificateName = $_POST['editCertificateName'];
        $date = $_POST['editDate'];
        $certificateId = $_POST['editCertificateId'];

        // Retrieve the old file path from the database
        $oldFilePathSql = "SELECT file_path FROM certificates WHERE certificate_id = ?";
        $stmtOldFilePath = $conn->prepare($oldFilePathSql);
        $stmtOldFilePath->bind_param("i", $certificateId);
        $stmtOldFilePath->execute();
        $resultOldFilePath = $stmtOldFilePath->get_result();

        if ($resultOldFilePath->num_rows > 0) {
            $oldFilePathRow = $resultOldFilePath->fetch_assoc();
            $oldFilePath = $oldFilePathRow['file_path'];

            // Create category-specific subdirectory if not exists
            $categoryDir = $userDir . $category . "/";
            if (!file_exists($categoryDir)) {
                mkdir($categoryDir, 0777, true);
            }

            // Get the file name and extension
            $fileName = basename($_FILES["editCertificateFile"]["name"]);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            // Define allowed file types
            $allowedFileTypes = array('jpeg', 'jpg', 'png', 'gif', 'bmp', 'pdf');

            // Check if the file type is allowed
            if (in_array(strtolower($fileExtension), $allowedFileTypes)) {
                // Move the file to the specified directory
                $targetFile = $categoryDir . $fileName;

                // Check if the file already exists in the target directory
                if (file_exists($targetFile)) {
                    // File already exists, display an error message
                    displayErrorAlert("File with the same name already exists. Please choose a different file name.");
                } else {
                    if (move_uploaded_file($_FILES["editCertificateFile"]["tmp_name"], $targetFile)) {
                        // File upload successful

                        $validationStatus = 'Pending'; // Default status

                        // Use a transaction to ensure data consistency
                        $conn->begin_transaction();

                        $updateSql = "UPDATE certificates SET category=?, organization=?, certificate_name=?, date=?, file_path=?, validation_status=? WHERE certificate_id=?";
                        $stmtUpdate = $conn->prepare($updateSql);
                        $stmtUpdate->bind_param("ssssssi", $category, $organization, $certificateName, $date, $targetFile, $validationStatus, $certificateId);

                        if ($stmtUpdate->execute()) {
                            // Commit the transaction if update is successful
                            $conn->commit();

                            // Delete the old file from the directory
                            if (file_exists($oldFilePath)) {
                                unlink($oldFilePath);
                            }

                            // Display SweetAlert after successful file upload
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                            echo '<script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        Swal.fire({
                                            title: "Certificate Updated!",
                                            text: "Your certificate has been updated successfully!",
                                            icon: "success",
                                        }).then(() => {
                                            window.location.href = "activity.php"; // Change to the appropriate page
                                        });
                                    });
                                  </script>';
                        } else {
                            // Roll back the transaction if update fails
                            $conn->rollback();

                            // File upload failed
                            displayErrorAlert("Error Updating Certificate");
                        }
                    } else {
                        // File upload failed
                        displayErrorAlert("Error Uploading Certificate");
                    }
                }
            } else {
                // File type not allowed
                displayErrorAlert("Invalid File Type");
            }
        } else {
            // Old file path not found in the database
            displayErrorAlert("Error Retrieving Old File Path");
        }
    } else {
        // Handle the case where the file input is not set or there is an error
        displayErrorAlert("No File Uploaded or File Upload Error");
    }
} else {
    // Redirect if accessed directly without submitting the form
    header("Location: activity.php"); // Change to the appropriate page
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
