<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_token'])) {
    require_once 'config.php';

    // Retrieve the user's token from the session
    $token = $_SESSION['user_token'];

    // Update the user's active_status to 0 based on the token
    $updateSql = "UPDATE users SET active_status = 0 WHERE token = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "s", $token);

    // Execute the update statement
    mysqli_stmt_execute($updateStmt);

    // Check for success
    if ($updateStmt) {
        // Unset the session variables related to the user
        unset($_SESSION['user_token']);
        unset($_SESSION['user_email']);

        // Destroy the session
        session_destroy();

        // Redirect to the index page
        header("Location: index.php");
        exit();
    } else {
        die("Error updating active status: " . mysqli_error($conn));
    }
} else {
    // If the user is not logged in, just redirect to the index page
    header("Location: index.php");
    exit();
}
?>
