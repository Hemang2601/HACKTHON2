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

// Retrieve announcements from the database
$announcementsQuery = "SELECT * FROM announcements";
$announcementsResult = mysqli_query($conn, $announcementsQuery);
$pageTitle = "Announcements";
if (!$announcementsResult) {
    echo "Error: " . mysqli_error($conn);
    die();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-cD9IBY4F7kC8rp6jYt+KcmgA8oYe49ubEtqQgX2HrGOl1Wq6Tb7Vk4MvFE/XUdiJ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
         #mySidenav a {
            color: white;
            text-decoration: none;
            padding: 15px;
            display: block;
        }

        #mySidenav a:hover {
            background-color: #555;
        }
        .carousel-inner img {
            width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .announcement {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .announcement-text {
            flex-grow: 1;
        }

        #placement-section {
            margin-top: 40px;
        }
        .about-section {
            background-color: #f8f9fa; /* Light gray background */
            padding: 40px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .about-section h2 {
            color: #007bff; /* Blue color for headings */
            font-weight: bold;
        }

        .about-section p {
            color: #495057; /* Dark gray color for text */
        }

        .cta-button {
            background-color: #28a745; /* Green color for the button */
            color: #fff; /* White color for text */
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .cta-button:hover {
            background-color: #218838; /* Darker green color on hover */
        }

        .card {
            border: none;
            background-color: #f8f9fa; /* Light gray background */
            border-radius: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            color: #007bff; /* Blue color for card title */
            font-weight: bold;
        }

        .list-unstyled li {
            margin-bottom: 10px;
        }

        .list-unstyled i {
            margin-right: 10px;
        }
    </style>
</head>

<body>

    <?php include('header.php'); ?>
   
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel"  data-bs-interval="3000">
        <div class="carousel-inner">
            <?php
            $counter = 0;
            while ($announcement = mysqli_fetch_assoc($announcementsResult)) {
            ?>
                <div class="carousel-item <?php echo $counter === 0 ? 'active' : ''; ?>">
                    <div class="announcement">
                        <img src='/portfoliohub/ADminDashboard/<?php echo $announcement['image_path']; ?>' alt='Announcement Image'>
                        <div class="announcement-text">
                            <h3><?php echo $announcement['title']; ?></h3>
                            <p><?php echo $announcement['description']; ?></p>
                        </div>
                    </div>
                </div>
            <?php
                $counter++;
            }
            ?>
        </div>
        <!-- <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button> -->
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="about-section">
                    <h2>About Student Portfolio Hub</h2>
                    <p>
                        Student Portfolio Hub is a powerful platform designed to empower students in showcasing their skills, achievements, and projects. It serves as a dynamic portfolio that illuminates your academic and extracurricular accomplishments. Leverage this platform to present yourself compellingly to potential employers and elevate your prospects of securing an exceptional placement.
                    </p>
                    <a href="Upload.php" class="cta-button">Get Started</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Connect with Us</h5>
                        <ul class="list-unstyled">
                            <li><i class="fab fa-facebook-f"></i> Facebook</li>
                            <li><i class="fab fa-twitter"></i> Twitter</li>
                            <li><i class="fab fa-linkedin-in"></i> LinkedIn</li>
                            <!-- Add more social media icons as needed -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


