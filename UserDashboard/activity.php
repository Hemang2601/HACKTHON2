<?php
// activity.php

// Start or resume the session
session_start();

// Include the database connection file
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    // Redirect to login or handle unauthorized access
    header("Location: /portfoliohub/Login/index.php"); // Change login.php to your login page
    exit();
}

// Get the user's token and fetch the associated user_id and username from the database
$userToken = $_SESSION['user_token'];

// Fetch the user_id and username based on the user_token
$sql = "SELECT user_id, username FROM users WHERE token = '$userToken'";
$result = $conn->query($sql);

if (!$result) {
    echo "Error: " . $conn->error;
    die();
}

if ($result->num_rows > 0) {
    $userRow = $result->fetch_assoc();
    $userId = $userRow['user_id'];
    $username = $userRow['username'];
} else {
    // Redirect or handle the case where the user is not found
    header("Location: login.php");
    exit();
}

// Fetch all certificates for the user
$sqlCertificates = "SELECT * FROM certificates WHERE user_id = '$userId' AND (validation_status = 'Pending' OR validation_status = 'Rejected')";
$resultCertificates = $conn->query($sqlCertificates);

if (!$resultCertificates) {
    echo "Error fetching certificates: " . $conn->error;
    die();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Certificates</title>
    <link rel="stylesheet" href="css/activity.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        integrity="sha384-ezCm7Mz4tSP3CFZKRzZl3HzF4CJhV5f5xRR08Zc3YA+eHuoERkM+Kdbww9zFcsK6"
        crossorigin="anonymous">

    <style>
        /* Add styles for action icons */
        .action-box {
            display: inline-block;
            margin-right: 5px;
            cursor: pointer;
        }

        .action-box i {
            font-size: 18px;
            margin-right: 5px;
        }

        .action-box:hover {
            color: #007bff;
        }

        /* Add styles for the lightbox */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 1;
        }

        .lightbox-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
        }
        .view-container {
            text-align: center; /* Center the contents horizontally */
        }

        #viewImage {
            max-width: 300px; /* Ensure the image doesn't exceed the lightbox width */
            max-height: 300px; /* Ensure the image doesn't exceed the viewport height */
            margin: 0 auto; /* Center the image horizontally */
            display: none; /* Hide the image by default */
        }

        #viewPdf {
            width: 100%;
            height: 100%;
            display: none; /* Hide the PDF by default */
        }
    </style>
</head>

<body>

    <?php
    $pageTitle = "Activity Status";
    include('header.php');
    ?>

    <div class="certificates-container">
        <h2>Your Certificates</h2>

        <table class="certificate-table">
            <thead>
                <tr>
                    <th>Certificate Name</th>
                    <th>Category</th>
                    <th>Organization</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>View Options</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($certificateRow = $resultCertificates->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($certificateRow['certificate_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($certificateRow['category']) . "</td>";
                    echo "<td>" . htmlspecialchars($certificateRow['organization']) . "</td>";
                    echo "<td>" . htmlspecialchars($certificateRow['date']) . "</td>";
                    echo "<td>" . htmlspecialchars($certificateRow['validation_status']) . "</td>";

                    // View Options column with view icon
                    // View Options column with view icon
                    echo "<td>
                        <div class='action-box' onclick='openViewLightbox(\"" . htmlspecialchars($certificateRow['file_path']) . "\", \"" . htmlspecialchars($certificateRow['certificate_name']) . "\")'>
                            <i class='fas fa-eye'></i> View
                        </div>
                        </td>";


                    // Actions column with edit, delete, and download options
                    echo "<td>
                            <div class='action-box' onclick='openEditLightbox(" . $certificateRow['certificate_id'] . ", \"" . htmlspecialchars($certificateRow['certificate_name']) . "\", \"" . htmlspecialchars($certificateRow['category']) . "\", \"" . htmlspecialchars($certificateRow['organization']) . "\", \"" . htmlspecialchars($certificateRow['date']) . "\", \"" . htmlspecialchars($certificateRow['file_path']) . "\")'><i class='fas fa-edit'></i></div>
                            <div class='action-box'><a href='delete_certificate.php?id=" . $certificateRow['certificate_id'] . "'><i class='fas fa-trash-alt'></i></a></div>
                            <div class='action-box'><a href='download_certificate.php?id=" . $certificateRow['certificate_id'] . "'><i class='fas fa-download'></i></a></div>
                        </td>";

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="viewLightbox" class="lightbox" onclick="closeLightbox()">
        <div class="lightbox-content" onclick="event.stopPropagation();">
            <span class="close-btn" onclick="closeLightbox()">&times;</span>
            <div class="view-container">
                <label class="view-label">View</label>
                <img id="viewImage" class="certificate-image" src="" alt="Certificate Image">
                <iframe id="viewPdf" class="certificate-pdf" src=""></iframe>
            </div>
        </div>
    </div>

    <!-- Add this HTML structure at the end of your body tag -->
    <div id="editLightbox" class="lightbox" onclick="closeLightbox()">
        <div class="lightbox-content" onclick="event.stopPropagation();">
            <span class="close-btn" onclick="closeLightbox()">&times;</span>
            <h2>Edit Certificate</h2>
            <form id="editForm" action="edit_certificate_details.php" method="POST" enctype="multipart/form-data">
                <!-- The form fields will be dynamically populated here using JavaScript -->
                <label for="editCertificateName">Certificate Name:</label>
                <input type="text" id="editCertificateName" name="editCertificateName" required>

                <label for="editCategory">Category:</label>
               
                <select id="editCategory" name="editCategory" required>
                    <option value="Sport">Sport</option>
                    <option value="Education">Education</option>
                    <option value="Culture">Culture</option>
                    <option value="Other">Other</option>
                </select>

                <label for="editOrganization">Organization:</label>
                <input type="text" id="editOrganization" name="editOrganization" required>

                <label for="editDate">Date:</label>
                <input type="text" id="editDate" name="editDate" required>

                <label for="editCertificateFile">Upload Certificate File:</label>

                <input type="file" id="editCertificateFile" name="editCertificateFile">
                <!-- Display the current file path or a placeholder -->
                <span id="currentFilePath"></span>

                <input type="hidden" id="editCertificateId" name="editCertificateId" value="">
                <input type="submit" value="Save Changes">
            </form>
        </div>
    </div>

    <!-- Add this script in your HTML, preferably just before the closing body tag -->
    <script>
        // Function to open the lightbox and populate the form fields
        function openEditLightbox(certificateId, certificateName, category, organization, date, filePath) {
            // Open the lightbox
            document.getElementById('editLightbox').style.display = 'flex';

            // Populate the form fields with the provided values
            document.getElementById('editCertificateId').value = certificateId;
            document.getElementById('editCertificateName').value = certificateName;
            document.getElementById('editCategory').value = category;
            document.getElementById('editOrganization').value = organization;
            document.getElementById('editDate').value = date;

            // Display the current file path for reference
            document.getElementById('currentFilePath').innerText = filePath;

            // Set the file input to empty initially
            document.getElementById('editCertificateFile').value = '';

            // Display the current file path or a placeholder
            document.getElementById('currentFilePath').innerText = filePath ? filePath : 'No file chosen';
        }

        function openViewLightbox(filePath, certificateName) {
    // Open the lightbox
    var lightbox = document.getElementById('viewLightbox');
    lightbox.style.display = 'flex';

    // Display the image or PDF based on the file extension
    var fileExtension = filePath.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
        // Display the image
        document.getElementById('viewImage').style.display = 'block';
        document.getElementById('viewImage').src = filePath;
        document.getElementById('viewImage').alt = certificateName;
        document.getElementById('viewPdf').style.display = 'none';
    } else if (fileExtension === 'pdf') {
        // Display the PDF
        document.getElementById('viewPdf').style.display = 'block';
        document.getElementById('viewPdf').src = filePath;
        document.getElementById('viewImage').style.display = 'none';
    }

    // Ensure the close button is associated with the closeLightbox function
    var closeButton = lightbox.querySelector('.close-btn');
    if (closeButton) {
        closeButton.onclick = function () {
            closeLightbox();
        };
    }
}

// Function to close the lightbox
function closeLightbox() {
    document.getElementById('viewLightbox').style.display = 'none';
    document.getElementById('editLightbox').style.display = 'none';
}



    </script>

    <?php include('footer.php'); ?>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>
