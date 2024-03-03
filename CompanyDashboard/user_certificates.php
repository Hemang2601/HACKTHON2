<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'portfoliohub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is authenticated
if (!isset($_SESSION['company_token'])) {
    header("Location: /portfoliohub/Login/index.php");
    die();
}

// Retrieve company info from the database
$sql = "SELECT * FROM companies WHERE token = '{$_SESSION['company_token']}'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
    die();
}

if (mysqli_num_rows($result) > 0) {
    $companyInfo = mysqli_fetch_assoc($result);
    if ($companyInfo['active_status'] != 1) {
        echo "Error: Company is not active.";
        die();
    }
} else {
    header("Location: /portfoliohub/Login/index.php");
    die();
}

// Check if the user_id is provided in the URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Fetch user details
    $userSql = "SELECT * FROM users WHERE user_id = $user_id";
    $userResult = mysqli_query($conn, $userSql);

    if ($userResult && mysqli_num_rows($userResult) > 0) {
        $user = mysqli_fetch_assoc($userResult);
    } else {
        echo "Error fetching user details.";
        die();
    }

    // Fetch certificates for the selected user with validation_status 'Validated'
    $certificatesSql = "SELECT * FROM certificates WHERE user_id = $user_id AND validation_status = 'Validated'";
    $certificatesResult = mysqli_query($conn, $certificatesSql);

    if (!$certificatesResult) {
        echo "Error fetching certificates: " . mysqli_error($conn);
        die();
    }

    // Fetch counts for each category
    $categoryCountsSql = "SELECT category, COUNT(*) AS count FROM certificates WHERE user_id = $user_id AND validation_status = 'Validated' GROUP BY category";
    $categoryCountsResult = mysqli_query($conn, $categoryCountsSql);

    if (!$categoryCountsResult) {
        echo "Error fetching category counts: " . mysqli_error($conn);
        die();
    }

    // Fetch total certificate count for all categories
    $totalCountSql = "SELECT category, COUNT(*) AS total FROM certificates WHERE user_id = $user_id AND validation_status = 'Validated' GROUP BY category";
    $totalCountResult = mysqli_query($conn, $totalCountSql);

    if (!$totalCountResult) {
        echo "Error fetching total certificate count: " . mysqli_error($conn);
        die();
    }

    $totalCounts = [];
    while ($row = mysqli_fetch_assoc($totalCountResult)) {
        $totalCounts[$row['category']] = $row['total'];
    }

    // Organize certificates by category
    $certificatesByCategory = [];
    while ($certificate = mysqli_fetch_assoc($certificatesResult)) {
        $certificatesByCategory[$certificate['category']][] = $certificate;
    }
} else {
    echo "User ID not provided.";
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Certificates</title>
    <!-- Include Bootstrap styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Add your custom CSS file -->
    <link rel="stylesheet" href="css/styles.css">

    <!-- Include SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Add any additional styles for the user certificates page -->
    <style>
        body {
            background-color: #f8f9fa; /* Set a light background color */
        }

        header {
            background-color: #007bff; /* Set a primary color for the header */
            color: #ffffff; /* Set text color for the header */
            text-align: center;
            padding: 20px;
        }

        main {
            padding: 20px;
        }

        .count-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #f1f1f1;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .filter-container {
            margin-bottom: 20px;
        }

        .category-container {
            margin-bottom: 20px;
        }

        .category-heading {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .certificate-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            overflow: hidden;
            width: 100%;
        }

        .certificate-card:hover {
            transform: scale(1.02);
        }

        .certificate-card-inner {
            padding: 20px;
        }

        .certificate-details {
            margin-top: 15px;
        }

        .certificate-details p {
            margin: 0;
            padding: 5px 0;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .certificate-card {
                flex-basis: calc(50% - 20px);
            }
        }
    </style>
</head>

<body>

    <header>
        <!-- Display user details in the header -->
        <h1 style="color: #fff;">Certificates - <?php echo $user['username']; ?></h1>
    </header>

    <main class="container">

        <div class="count-card">
            <div class="count-card-inner">
                <a href="javascript:history.go(-1)" class="btn btn-secondary btn-back">Back</a>
                <h3>Total Certificates Count</h3>
                <?php
                foreach ($totalCounts as $category => $count) {
                    echo '<p><strong>' . $category . ':</strong> ' . $count . '</p>';
                }
                ?>
            </div>

            <div class="filter-container">
                <label for="categoryFilter">Filter by Category:</label>
                <select id="categoryFilter" onchange="filterCertificates()" class="form-select">
                    <option value="all">All Categories</option>
                    <?php
                    foreach ($totalCounts as $category => $count) {
                        echo '<option value="' . $category . '">' . $category . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <?php
foreach ($certificatesByCategory as $category => $certificates) {
    echo '<div class="category-container">';
    echo '<h2 class="category-heading" style="color: black;">' . $category . '</h2>';

    // Display certificates in rows of three
    $count = 0;
    foreach ($certificates as $certificate) {
        if ($count % 3 == 0) {
            echo '<div class="row">';
        }

        echo '<div class="col-md-4">';
        echo '<div class="certificate-card">';
        echo '<div class="certificate-card-inner">';
        echo '<h3>' . $certificate['certificate_name'] . '</h3>';
        echo '<div class="certificate-details">';
        echo '<p><strong>Organization:</strong> ' . $certificate['organization'] . '</p>';
        echo '<p><strong>Category:</strong> ' . $certificate['category'] . '</p>';
        echo '<p><strong>Date:</strong> ' . $certificate['date'] . '</p>';

        // Display file based on extension
        $fileExtension = pathinfo($certificate['file_path'], PATHINFO_EXTENSION);
        $basePath = '/portfoliohub/UserDashboard/';

        if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif'])) {
            // Display image
            echo '<p><strong>File:</strong> <img src="' . $basePath . $certificate['file_path'] . '" alt="Certificate Image" style="width: 100%; height: auto;"></p>';
        } elseif (in_array(strtolower($fileExtension), ['pdf'])) {
            // Display PDF
            echo '<p><strong>File:</strong> <iframe src="' . $basePath . $certificate['file_path'] . '" width="100%" height="200px"></iframe></p>';
        } else {
            // Handle other file types or display an error message
            echo '<p style="color: red;"><strong>Unsupported file type:</strong> ' . $fileExtension . '</p>';
        }

        // Add any additional certificate details here
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        $count++;

        if ($count % 3 == 0) {
            echo '</div>'; // Close the row
        }
    }

    // Close the row if it's not closed already
    if ($count % 3 != 0) {
        echo '</div>';
    }

    echo '</div>'; // Closing category-container for this category
}
?>
    </main>

    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Add any additional scripts for the user certificates page -->
    <script>
        function filterCertificates() {
            var selectedCategory = document.getElementById('categoryFilter').value;
            var categories = document.getElementsByClassName('category-container');

            for (var i = 0; i < categories.length; i++) {
                var category = categories[i];

                if (selectedCategory === 'all' || category.querySelector('.category-heading').innerText === selectedCategory) {
                    category.style.display = 'block';
                } else {
                    category.style.display = 'none';
                }
            }
        }
    </script>

</body>

</html>

