<!-- Your main HTML file -->
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Include your head content here -->
    <title>Enhanced Bootstrap Navigation</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom Styles */
        body {
            padding-top: 56px;
            background-color: #f5f5f5; /* Light background color */
            color: #333; /* Dark text color */
        }

        /* Add your other custom styles here */

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
            margin-bottom: 20px;
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .form-control {
            background-color: #fff; /* White background color for form inputs */
            color: #333; /* Dark text color for form inputs */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
        }

        /* Add other styles as needed */
    </style>
</head>

<body>

    <?php include 'navigation.php'; ?>

    <!-- Announcement Form Cards -->
    <div class="container mt-5">
        <div class="row">
            <?php
            // Database connection
            $host = 'localhost';
            $username = 'root';
            $password = '';
            $database = 'portfoliohub';

            $conn = new mysqli($host, $username, $password, $database);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch all rows from the certificates table
            $query = "SELECT * FROM certificates";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">' . $row['certificate_name'] . '</h5>
                                    <p class="card-text"><strong>Organization:</strong> ' . $row['organization'] . '</p>
                                    <p class="card-text"><strong>Category:</strong> ' . $row['category'] . '</p>
                                    <p class="card-text"><strong>Date:</strong> ' . $row['date'] . '</p>
                                    <p class="card-text"><strong>Validation Status:</strong> ' . $row['validation_status'] . '</p>';
                        
                    // Get file extension
                    $fileExtension = strtolower(pathinfo($row['file_path'], PATHINFO_EXTENSION));
                        
                    // Display image or PDF based on file type
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        // Display image
                        echo '<img src="/portfoliohub/UserDashboard/' . $row['file_path'] . '" alt="Certificate Image" style="width: 300px; height: 200px;">';
                    } elseif ($fileExtension === 'pdf') {
                        // Display PDF
                        echo '<iframe src="/portfoliohub/UserDashboard/' . $row['file_path'] . '" width="300px" height="200px"></iframe>';
                    } else {
                        // Handle other file types or display an error message
                        echo '<p>Unsupported file type</p>';
                    }
                    
                    // Verification button
                    echo '<button type="button" class="btn btn-primary">Verify</button>';
                    
                    // Close the card tags
                    echo '</div></div></div>';
                }
            } else {
                echo '<p>No certificates found.</p>';
            }
            
          

            // Close database connection
            $conn->close();
            ?>
        </div>
    </div>


    <!-- Modals -->
    <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
        aria-hidden="true">
        <!-- Your existing modal content goes here -->
    </div>

    <!-- Bootstrap JS and Popper.js (required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
