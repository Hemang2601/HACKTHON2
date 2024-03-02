<?php
// Include the database connection file
include('db.php');

// Function to log messages
function logMessage($message, $logFilePath = 'C:\\xampp\\htdocs\\portfoliohub\\UserDashboard\\Log\\error.log') {
    // Adjust the log file path based on your setup
    error_log($message . PHP_EOL, 3, $logFilePath);
}

// Check if the certificate ID is provided in the URL
if (isset($_GET['id'])) {
    $certificateId = $_GET['id'];

    // Fetch the file path based on the certificate ID using prepared statement
    $fetchFilePathSql = "SELECT file_path FROM certificates WHERE certificate_id = ?";
    $stmt = $conn->prepare($fetchFilePathSql);
    $stmt->bind_param("i", $certificateId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $relativeFilePath = $row['file_path'];

        // Prepend the base path to the relative file path
        $basePath = 'C:/xampp/htdocs/portfoliohub/UserDashboard/';
        $filePath = $basePath . DIRECTORY_SEPARATOR . $relativeFilePath;

        // Check if the file exists
        if (file_exists($filePath)) {
            // Determine Content-Type based on the file extension
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            switch ($fileExtension) {
                case 'pdf':
                    header('Content-Type: application/pdf');
                    break;
                case 'png':
                    header('Content-Type: image/png');
                    break;
                case 'jpeg':
                case 'jpg':
                    header('Content-Type: image/jpeg');
                    break;
                default:
                    header('Content-Type: application/octet-stream');
                    break;
            }

            // Set the appropriate headers for file download
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename="certificate_' . $certificateId . '.' . $fileExtension . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));

            // Log before reading and outputting the file
            logMessage("Info: Attempting to read and output file - Certificate ID: $certificateId, File Path: $filePath");

            // Read the file and output it to the browser
            ob_clean(); // Clear the output buffer
            flush();    // Flush system output buffer
            readfile($filePath);
            exit();
        } else {
            // Handle the case where the file does not exist
            logMessage("Error: File not found - Certificate ID: $certificateId, File Path: $filePath");
        }
    } else {
        // Handle the case where the certificate ID is not valid
        logMessage("Error: Certificate not found - Certificate ID: $certificateId");
    }
} else {
    // Handle the case where the certificate ID is not provided
    logMessage("Error: Certificate ID not provided in the URL");
}

// Redirect to the certificates page if there is an issue
header("Location: activity.php");
exit();
?>
