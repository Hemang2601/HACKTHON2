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

// Check if it's a POST request with the required parameters
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && isset($_GET['certificate_id'])) {
    $action = $_GET['action'];
    $certificateId = $_GET['certificate_id'];

    // Perform the corresponding action based on the request
    if ($action === 'validate') {
        validateCertificate($conn, $certificateId);
    } elseif ($action === 'reject') {
        rejectCertificate($conn, $certificateId);
    } else {
        // Invalid action parameter
        echo json_encode(['status' => 'error', 'message' => 'Invalid action parameter']);
    }
} else {
    // Invalid request
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

// Function to validate the certificate
function validateCertificate($conn, $certificateId) {
    // Perform the validation logic here
    // Update the certificate status to 'Validated' in the database

    $updateSql = "UPDATE certificates SET validation_status = 'Validated' WHERE certificate_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("i", $certificateId);
    $result = $stmt->execute();
    
    if ($result) {
        // Success
        echo json_encode(['status' => 'success', 'message' => 'Certificate validated successfully']);
    } else {
        // Error updating database
        echo json_encode(['status' => 'error', 'message' => 'Error validating certificate']);
    }
}

// Function to reject the certificate
function rejectCertificate($conn, $certificateId) {
    // Perform the rejection logic here
    // Update the certificate status to 'Rejected' in the database

    $updateSql = "UPDATE certificates SET validation_status = 'Rejected' WHERE certificate_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("i", $certificateId);
    $result = $stmt->execute();
    
    if ($result) {
        // Success
        echo json_encode(['status' => 'success', 'message' => 'Certificate rejected successfully']);
    } else {
        // Error updating database
        echo json_encode(['status' => 'error', 'message' => 'Error rejecting certificate']);
    }
}
$conn->close();
?>