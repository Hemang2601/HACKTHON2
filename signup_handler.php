<?php
require_once 'config.php';

// Check if the user is not logged in, then display the Google login button
if (!isset($_SESSION['user_token'])) {
    $authUrl = $client->createAuthUrl();
} else {
    // Redirect to the welcome page if the user is already logged in
    header("Location: profile_page.php");
    die();
}

// Manual signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    $username = strtoupper($_POST['username']);
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Function to generate a 21-digit random number
    function generateRandomToken() {
        return bcpow('10', '20', 0); // 10^20
    }

    // Check if the email is already registered
    $checkEmailSql = "SELECT * FROM users WHERE email = '{$email}'";
    $checkEmailResult = mysqli_query($conn, $checkEmailSql);

    if (!$checkEmailResult) {
        echo "Error: " . mysqli_error($conn);
        die();
    }

    if (mysqli_num_rows($checkEmailResult) > 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Email is already registered!',
                }).then(function() {
                    window.history.back();
                });
            });
        </script>";
        die();
    } else {
        // Generate and check the uniqueness of the token
        do {
            $token = (string) generateRandomToken(); // Explicitly cast to string

            $checkTokenSql = "SELECT * FROM users WHERE token = '{$token}'";
            $checkTokenResult = mysqli_query($conn, $checkTokenSql);

            if (!$checkTokenResult) {
                echo "Error: " . mysqli_error($conn);
                die();
            }

            // If the token already exists, regenerate a new one and retry
        } while (mysqli_num_rows($checkTokenResult) > 0);

        // Insert new user into the database with verifiedEmail set to '1' and the generated token
        $insertUserSql = "INSERT INTO users (username, email, password, verifiedEmail, token) VALUES ('{$username}', '{$email}', '{$password}', '1', '{$token}')";
        $insertUserResult = mysqli_query($conn, $insertUserSql);

        if (!$insertUserResult) {
            echo "Error: " . mysqli_error($conn);
            die();
        }

        // Set the user token and redirect to the welcome page
        $_SESSION['user_token'] = $token;
        header("Location: profile_page.php");
        die();
    }
}
?>
