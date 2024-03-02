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

// Fetch user details based on the provided username
if (isset($_GET['username'])) {
    $selectedUsername = mysqli_real_escape_string($conn, $_GET['username']);

    $userDetailsQuery = "SELECT * FROM users WHERE username = '$selectedUsername'";
    $userDetailsResult = mysqli_query($conn, $userDetailsQuery);

    if (!$userDetailsResult) {
        echo "Error fetching user details: " . mysqli_error($conn);
        die();
    }

    if (mysqli_num_rows($userDetailsResult) > 0) {
        $selectedUserDetails = mysqli_fetch_assoc($userDetailsResult);

        // Fetch certificates for the selected user based on user_id
        $userId = $selectedUserDetails['user_id'];
        $certificatesQuery = "SELECT * FROM certificates WHERE user_id = '$userId'";
        $certificatesResult = mysqli_query($conn, $certificatesQuery);

        if (!$certificatesResult) {
            echo "Error fetching certificates: " . mysqli_error($conn);
            die();
        }
    } else {
        echo "User not found.";
        die();
    }
} else {
    echo "Username not provided.";
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: block;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .main-container {
            width: 80%;
            max-width: 800px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px;
            margin-left: 300px;
            box-sizing: border-box;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .back-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        h1 {
            margin: 0;
            color: #495057;
        }

        .form-container {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        form {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        label {
            font-size: 16px;
            margin-right: 10px;
            color: #495057;
        }

        select {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .certificate-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-around;
        }

        .certificate-card {
            box-sizing: border-box;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: calc(50.33% - 20px);
            margin-bottom: 20px;
            color: #495057;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
            overflow: hidden;
        }

        .certificate-card:hover {
            transform: scale(1.05);
        }

        .certificate-card-content {
            padding: 20px;
        }

        .certificate-card img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 5px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .certificate-card embed,
        .certificate-card iframe {
            width: 100%;
            height: 200px;
            border-radius: 5px;
        }
        .certificate-count-container {
    position: fixed;
    top: 20px;
    right:20px; /* Adjust the right distance as needed */
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 15px;
    z-index: 2; /* Ensure it's above other elements */
}

    </style>
</head>

<body>

    <!-- Main Container for User Details -->
    <div class="main-container">
        <div class="header">
            <a href="placement.php" class="back-button">&#9665; Back</a>
            <h1>User Details</h1>
        </div>

        <!-- Form to select category -->
        <div class="form-container">
            <form method="POST" action="">
                <label for="categorySelect">Select Category:</label>
                <select id="categorySelect" name="selectedCategory">
                    <option value="all">All</option>
                    <?php
                    // Fetch unique categories for the user
                    $categoriesQuery = "SELECT DISTINCT category FROM certificates WHERE user_id = '$userId'";
                    $categoriesResult = mysqli_query($conn, $categoriesQuery);

                    if (!$categoriesResult) {
                        echo "Error fetching categories: " . mysqli_error($conn);
                        die();
                    }

                    while ($categoryRow = mysqli_fetch_assoc($categoriesResult)) {
                        $category = $categoryRow['category'];
                        echo "<option value='$category'>$category</option>";
                    }
                    ?>
                </select>
                <button type="submit">Show Certificates</button>
            </form>
        </div>

        <!-- Display certificates for the selected category or all categories -->
        <div class='certificate-list'>
            <?php
            // Check if the form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Get the selected category
                $selectedCategory = mysqli_real_escape_string($conn, $_POST['selectedCategory']);

                // Modify the query based on the selected category
                $categoryFilter = ($selectedCategory === 'all') ? '' : " AND category = '$selectedCategory'";

                // Fetch certificates based on the selected category
                $selectedCategoryCertificatesQuery = "SELECT * FROM certificates WHERE user_id = '$userId'$categoryFilter";
                $selectedCategoryCertificatesResult = mysqli_query($conn, $selectedCategoryCertificatesQuery);

                if (!$selectedCategoryCertificatesResult) {
                    echo "Error fetching certificates: " . mysqli_error($conn);
                    die();
                }

                // Display certificates for the selected category or all categories
                while ($certificate = mysqli_fetch_assoc($selectedCategoryCertificatesResult)) {
                    echo '<div class="certificate-card">';
                    echo '<div class="certificate-card-content">';
                    echo "<p><strong>Certificate Name:</strong> " . $certificate['certificate_name'] . "</p>";
                    echo "<p><strong>Issued By:</strong> " . $certificate['organization'] . "</p>";
                    echo "<p><strong>Issue Date:</strong> " . $certificate['date'] . "</p>";
                    echo "<p><strong>Category:</strong> " . $certificate['category'] . "</p>";

                    // Display image if the file is an image
                    $fileExtension = pathinfo($certificate['file_path'], PATHINFO_EXTENSION);
                    if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo "<img src='/portfoliohub/UserDashboard/" . $certificate['file_path'] . "' alt='Certificate Image'>";
                    }

                    // Display PDF if the file is a PDF
                    elseif (strtolower($fileExtension) == 'pdf') {
                        echo "<embed src='/portfoliohub/UserDashboard/" . $certificate['file_path'] . "' type='application/pdf'>";
                    }

                    // Add more details as needed
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
       
    </div>
      <!-- Container to show count of all certificates for each category -->
      <div class="certificate-count-container">
            <h2>Certificate Counts</h2>
            <?php
            // Fetch counts of all certificates for each category
            $allCategoriesCountQuery = "SELECT category, COUNT(*) as count FROM certificates WHERE user_id = '$userId' GROUP BY category";
            $allCategoriesCountResult = mysqli_query($conn, $allCategoriesCountQuery);

            if (!$allCategoriesCountResult) {
                echo "Error fetching certificate counts: " . mysqli_error($conn);
                die();
            }

            // Display counts for each category
            while ($categoryCount = mysqli_fetch_assoc($allCategoriesCountResult)) {
                echo "<p><strong>{$categoryCount['category']}:</strong> {$categoryCount['count']}</p>";
            }
            ?>
        </div>
    

</body>

</html>
