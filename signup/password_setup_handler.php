<?php
require_once '../Config/config.php';

// Check if the user is authenticated
if (!isset($_SESSION['user_token'])) {
    header("Location: index.php");
    die();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the password from the form
    $password = $_POST['password'];

    // Validate if the password is not empty
    if (empty($password)) {
        showAlert('error', 'Password Error', 'Password cannot be empty. Please enter a password.', 'password_setup.php');
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Use prepared statements to prevent SQL injection
    $updateSql = "UPDATE users SET password = ? WHERE token = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);

    if ($updateStmt) {
        mysqli_stmt_bind_param($updateStmt, "ss", $hashedPassword, $_SESSION['user_token']);
        $updateResult = mysqli_stmt_execute($updateStmt);

        if ($updateResult) {
            // Password set successfully, destroy the session
            session_destroy();
            unset($_SESSION['user_token']);
            showAlert('success', 'Signup Successful', 'You have successfully set up your password and signed up with Google.', '/portfoliohub/Login/index.php');
        } else {
            showAlert('error', 'Password Error', 'Error updating password. Please try again.');
        }

        mysqli_stmt_close($updateStmt);
    } else {
        showAlert('error', 'Password Error', 'Error preparing the password update statement. Please try again.');
    }
}

function showAlert($icon, $title, $text, $redirect = null) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '{$icon}',
                title: '{$title}',
                text: '{$text}',
            }).then(function() {
                " . ($redirect ? "window.location.href = '{$redirect}';" : "") . "
            });
        });
    </script>";
    die();
}
?>
