<!-- deleteannouncement.php -->
<?php
session_start();

// Add database connection code here
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $announcementId = $_GET['id'];

    // Retrieve announcement details
    $getAnnouncementSql = "SELECT * FROM announcements WHERE announcement_id = '$announcementId'";
    $getAnnouncementResult = mysqli_query($conn, $getAnnouncementSql);

    if ($getAnnouncementResult && mysqli_num_rows($getAnnouncementResult) > 0) {
        // Fetch the image path
        $announcementDetails = mysqli_fetch_assoc($getAnnouncementResult);
        $imagePath = $announcementDetails['image_path'];

        // Delete announcement from the database
        $deleteAnnouncementSql = "DELETE FROM announcements WHERE announcement_id = '$announcementId'";
        $deleteAnnouncementResult = mysqli_query($conn, $deleteAnnouncementSql);

        if (!$deleteAnnouncementResult) {
            echo "Error deleting announcement: " . mysqli_error($conn);
        } else {
            // Delete the associated image file
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Display SweetAlert for success
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "The announcement has been deleted.",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "manageannouncements.php";
                    });
                });
            </script>';
            die();
        }
    } else {
        // Handle case where announcement is not found
        echo "Announcement not found.";
    }
} else {
    // Handle invalid request
    echo "Invalid request.";
}
?>
