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


// Retrieve announcements for the user
$announcementsSql = "SELECT * FROM announcements WHERE user_id = '{$userinfo['user_id']}' ORDER BY created_at DESC";
$announcementsResult = mysqli_query($conn, $announcementsSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Your custom CSS file -->
    <link rel="stylesheet" href="css/com.css">
    <link rel="stylesheet" href="css/manageannouncements.css">
    <style>
        /* Add your additional styles for manageannouncements.php here */
        body {
            background-color: #f8f9fa;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 30px;
        }

        .table-container {
            margin-top: 20px;
            overflow-x: auto; /* Add this for horizontal scrolling on small screens */
        }

        th,
        td {
            text-align: center;
        }

        img.img-fluid {
            max-width: 100%; /* Update to 100% for responsive images */
            max-height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
    <h2 class="text-center mt-4 mb-4" style="color: black;">Manage Announcements</h2>

        <?php if ($announcementsResult && mysqli_num_rows($announcementsResult) > 0) : ?>
            <div class="table-container">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($announcement = mysqli_fetch_assoc($announcementsResult)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($announcement['title']) ?></td>
                                <td><?= nl2br(htmlspecialchars($announcement['description'])) ?></td>
                                <td><?= $announcement['created_at'] ?></td>
                                <td>
                                    <?php if (!empty($announcement['image_path']) || !empty($announcement['pdf_path'])) : ?>
                                        <div class="announcement-media">
                                            <?php if (!empty($announcement['image_path'])) : ?>
                                                <img src="<?= htmlspecialchars($announcement['image_path']) ?>" alt="Announcement Image" class="img-fluid">
                                            <?php elseif (!empty($announcement['pdf_path'])) : ?>
                                                <object data="<?= htmlspecialchars($announcement['pdf_path']) ?>" type="application/pdf" width="100%" height="500px">
                                                    <p>PDF cannot be displayed. <a href="<?= htmlspecialchars($announcement['pdf_path']) ?>" target="_blank">Download PDF</a></p>
                                                </object>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="editannouncement.php?action=update&id=<?= $announcement['announcement_id'] ?>" title="Update" class="btn btn-primary"><i class="fas fa-edit"></i> Update</a>
                                    <a href="deleteannouncement.php?action=delete&id=<?= $announcement['announcement_id'] ?>" title="Delete" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <a href="announcements.php" class="btn btn-secondary mt-3">Back to Announcements</a>
        <?php else : ?>
            <p class="text-center">No announcements found.</p>
            <a href="announcements.php" class="btn btn-secondary mt-3">Back to Announcements</a>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Your custom JS file -->
    <script src="js/script.js"></script>
</body>

</html>