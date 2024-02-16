<?php
require_once '../Config/config.php';

// Initialize $userinfo array
$userinfo = [];

// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $userinfo = [
        'email' => $google_account_info['email'],
        'full_name' => $google_account_info['name'],
        'picture' => $google_account_info['picture'],
        'verifiedEmail' => $google_account_info['verifiedEmail'],
        'token' => $google_account_info['id'],
    ];

    // checking if the user already exists in the database
    $sql = "SELECT * FROM users WHERE email ='{$userinfo['email']}'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
         // Show SweetAlert for successful signup
         
    session_destroy();
    unset($_SESSION['user_token']);
         echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
         echo '<script>
             document.addEventListener("DOMContentLoaded", function () {
                 Swal.fire({
                     title: "Signup Successful!",
                     text: "You have successfully signed up.",
                     icon: "success",
                     confirmButtonText: "OK"
                 }).then(() => {
                     window.location.href = "/portfoliohub/Login/index.php";
                 });
             });
         </script>';
         die();
    }

    if (mysqli_num_rows($result) === 0) {
        // convert username to uppercase
        $uppercaseUsername = strtoupper($userinfo['full_name']);

        // user does not exist
        $sql = "INSERT INTO users (email, username, picture, verifiedEmail, token, active_status) VALUES ('{$userinfo['email']}','{$uppercaseUsername}', '{$userinfo['picture']}', '{$userinfo['verifiedEmail']}', '{$userinfo['token']}', 0)";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            echo "Error: " . mysqli_error($conn);
            die();
        }

       
    } else {
        // user exists, update active_status to 1
        $sql = "UPDATE users SET active_status = 1 WHERE email = '{$userinfo['email']}'";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            echo "Error: " . mysqli_error($conn);
            die();
        }
    }

    // save user data into session
    $_SESSION['user_token'] = $userinfo['token'];

    // Check if the user has set a password, and redirect to password setup if not
    $sql = "SELECT * FROM users WHERE email ='{$userinfo['email']}'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo "Error: " . mysqli_error($conn);
        die();
    }

    $user = mysqli_fetch_assoc($result);

    if (empty($user['password'])) {
        header("Location: /portfoliohub/signup/password_setup.php");
        die();
    }

    session_destroy();
    unset($_SESSION['user_token']);

    // Show SweetAlert for successful logout and redirect to the index page
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                title: "Already Email Registered",
                text: "You are already registered. Redirecting to login...",
                icon: "error",
                confirmButtonText: "OK"
            }).then(() => {
                window.location.href = "/portfoliohub/Login/index.php";
            });
        });
    </script>';
    die();
}
?>
