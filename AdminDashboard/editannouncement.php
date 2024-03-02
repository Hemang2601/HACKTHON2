<?php
session_start();

// Include your database connection code here
include('db.php');

// Check if user is authenticated
if (!isset($_SESSION['user_token'])) {
    header("Location: /portfoliohub/Login/index.php");
    die();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $announcement_id = $_POST['announcement_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Fetch old image path
    $getOldImagePathSql = "SELECT image_path FROM announcements WHERE announcement_id = '$announcement_id'";
    $getOldImagePathResult = mysqli_query($conn, $getOldImagePathSql);

    if ($getOldImagePathResult && mysqli_num_rows($getOldImagePathResult) > 0) {
        $oldImagePath = mysqli_fetch_assoc($getOldImagePathResult)['image_path'];

        // Check if a new image file is uploaded
        if (!empty($_FILES['new_image']['name'])) {
            // Delete old image file from the directory
            if (!empty($oldImagePath) && file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            // Process uploaded image
            $targetDirectory = "CERTIFICATE/ADMIN/ANNOUNCEMENT/";
            if (!is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0755, true);
            }

            $targetFile = $targetDirectory . basename($_FILES["new_image"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if the image file is a valid image
            $check = getimagesize($_FILES["new_image"]["tmp_name"]);
            if ($check === false) {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
            } else {
                // Move the uploaded file to the CERTIFICATE/ADMIN/ANNOUNCEMENT directory
                if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $targetFile)) {
                    // Update announcement with new image path
                    $updateAnnouncementSql = "UPDATE announcements SET title=?, description=?, image_path=? WHERE announcement_id=?";
                    $updateStmt = $conn->prepare($updateAnnouncementSql);
                    $updateStmt->bind_param("sssi", $title, $description, $targetFile, $announcement_id);
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            // Update announcement without changing the image
            $updateAnnouncementSql = "UPDATE announcements SET title=?, description=? WHERE announcement_id=?";
            $updateStmt = $conn->prepare($updateAnnouncementSql);
            $updateStmt->bind_param("ssi", $title, $description, $announcement_id);
        }

        // Execute the update statement
        if ($updateStmt->execute()) {
            // Display SweetAlert for success with automatic redirect
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        icon: "success",
                        title: " Announcement Updated!",
                        text: "The announcement has been updated successfully.",
                        showConfirmButton: true
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            window.location.href = "manageannouncements.php";
                        }
                    });
                });
            </script>';
        } else {
            // Display SweetAlert for error with automatic redirect
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Error updating announcement: ' . $updateStmt->error . '",
                        showConfirmButton: true
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            window.location.href = "manageannouncements.php";
                        }
                    });
                });
            </script>';
        }

        $updateStmt->close();
    } else {
        echo "Error fetching old image path: " . mysqli_error($conn);
    }
}

// Include your HTML header and any additional styling or scripting here
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Announcement</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Add your custom stylesheets and scripts here -->
    <style>
        /* Add your custom styles here */
    </style>
</head>

<body class="container mt-5">

    <div class="border p-4">
        <h2 class="mb-4">Update Announcement</h2>

        <?php
        // Include your database connection
        include_once 'db.php';

        // Fetch announcement details for pre-filling the form
        if (isset($_GET['id'])) {
            $announcementId = $_GET['id'];
            $getAnnouncementSql = "SELECT * FROM announcements WHERE announcement_id = '$announcementId'";
            $getAnnouncementResult = mysqli_query($conn, $getAnnouncementSql);

            if ($getAnnouncementResult && mysqli_num_rows($getAnnouncementResult) > 0) {
                $announcementDetails = mysqli_fetch_assoc($getAnnouncementResult);
        ?>

                <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="announcement_id" value="<?= $announcementDetails['announcement_id'] ?>">

                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($announcementDetails['title']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($announcementDetails['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="new_image">Upload New Image:</label>
                        <input type="file" class="form-control-file" id="new_image" name="new_image" accept="image/*,application/pdf">
                    </div>

                    <?php
                    // Show the existing file path
                    if (!empty($announcementDetails['image_path'])) {
                        echo '<p>Current File Path: ' . htmlspecialchars($announcementDetails['image_path']) . '</p>';
                    }
                    ?>

                    <!-- Add other form fields here -->

                    <button type="submit" class="btn btn-primary">Update Announcement</button>

                    <!-- Back Button -->
                    <a href="manageannouncements.php" class="btn btn-secondary">Back</a>
                </form>

        <?php
            } else {
                // Handle case where announcement is not found
                echo '<div class="alert alert-danger">Announcement not found.</div>';
            }
        } else {
            // Handle invalid request
            echo '<div class="alert alert-danger">Invalid request.</div>';
        }

        // Include your HTML footer and any additional scripting here
        ?>

    </div>

    <!-- Add Bootstrap JS and your custom scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- Add your custom scripts here -->

    <?php
    // Close your database connection
    $conn->close();
    ?>
</body>

</html>


