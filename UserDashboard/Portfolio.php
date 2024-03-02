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

// Retrieve user info from the database using user_token
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

// Retrieve certificate information from the database using user_id
$certificatesQuery = "SELECT * FROM certificates WHERE user_id = '{$userinfo['user_id']}' AND validation_status = 'Validated'";
$certificatesResult = mysqli_query($conn, $certificatesQuery);

if (!$certificatesResult) {
    echo "Error: " . mysqli_error($conn);
    die();
}

// Get distinct categories
$categoriesQuery = "SELECT DISTINCT category FROM certificates";
$categoriesResult = mysqli_query($conn, $categoriesQuery);

if (!$categoriesResult) {
    echo "Error: " . mysqli_error($conn);
    die();
}

$pageTitle = "Portfolio";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
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

        .certificate-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            height: 300px; /* Fixed height for each card */
            overflow: hidden;
            position: relative; /* Added position relative for absolute positioning of download icon */
        }

        .certificate-card p {
            margin: 0;
        }

        .certificate-image,
        .certificate-pdf {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .category-heading {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .download-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            color: #007bff;
            cursor: pointer;
        }
    </style>
</head>

<body>

<?php include('header.php'); ?>

<div class="container mt-4">
    <div class="row">

        <?php while ($category = mysqli_fetch_assoc($categoriesResult)) : ?>
            <div class="col-md-12">
                <div class="category-heading"><?php echo $category['category']; ?></div>
            </div>

            <?php
            $certificatesQuery = "SELECT * FROM certificates WHERE user_id = '{$userinfo['user_id']}' AND category = '{$category['category']}' AND validation_status = 'Validated'";
            $certificatesResult = mysqli_query($conn, $certificatesQuery);

            if (!$certificatesResult) {
                echo "Error: " . mysqli_error($conn);
                die();
            }

            $count = 0;

            while ($certificate = mysqli_fetch_assoc($certificatesResult)) {
                // Display the certificate card
                echo '<div class="col-md-4">';
                echo '<div class="certificate-card">';

                echo "<p><strong>Organization:</strong> {$certificate['organization']}</p>";
                echo "<p><strong>Certificate Name:</strong> {$certificate['certificate_name']}</p>";
                echo "<p><strong>Date:</strong> {$certificate['date']}</p>";

                // Display PDF or image directly
                $filePath = $certificate['file_path'];

                if (!empty($filePath) && file_exists($filePath)) {
                    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

                    if (strtolower($fileExtension) === 'pdf') {
                        echo "<object data='{$filePath}' type='application/pdf' class='certificate-pdf'></object>";
                    } else {
                        echo "<img src='{$filePath}' alt='Certificate Image' class='certificate-image'>";
                    }
                }

                // Download icon
                echo '<i class="fas fa-download download-icon" onclick="downloadCertificate(\'' . $filePath . '\')"></i>';

                echo '</div>'; // Close the certificate card div
                echo '</div>'; // Close the col-md-4 div

                // Start a new row after every 3 certificates
                $count++;
                if ($count % 3 === 0) {
                    echo '</div><div class="row">';
                }
            }
            ?>
        <?php endwhile; ?>

    </div>
</div>

<?php include('footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>

<script>
    function downloadCertificate(filePath) {
        // Trigger download of the certificate file
        var link = document.createElement('a');
        link.href = filePath;
        link.download = filePath.split('/').pop();
        link.click();
    }
</script>

</body>

</html>
