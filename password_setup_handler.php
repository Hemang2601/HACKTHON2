<?php
require_once 'config.php';

// Check if user is authenticated
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
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Error',
                        text: 'Password cannot be empty. Please enter a password.',
                    }).then(function() {
                        window.location.href = 'password_setup.php';
                    });
                });
              </script>";
        die();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update the user's password in the database
    $updateSql = "UPDATE users SET password = '{$hashedPassword}' WHERE token = '{$_SESSION['user_token']}'";
    $updateResult = mysqli_query($conn, $updateSql);

    if ($updateResult) {
        // Password set successfully, redirect to the welcome page
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Set Successfully',
                        text: 'You can now log in with your password.',
                    }).then(function() {
                        window.location.href = 'index.php';
                    });
                });
              </script>";
        die();
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Error',
                        text: 'Error updating password. Please try again.',
                    });
                });
              </script>";
    }
}
?>
