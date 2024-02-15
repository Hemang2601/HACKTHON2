<?php
require_once 'config.php';

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
        'first_name' => $google_account_info['givenName'],
        'last_name' => $google_account_info['familyName'],
        'full_name' => $google_account_info['name'],
        'picture' => $google_account_info['picture'],
        'verifiedEmail' => $google_account_info['verifiedEmail'],
        'token' => $google_account_info['id'],
    ];

    // checking if user already exists in the database
    $sql = "SELECT * FROM users WHERE email ='{$userinfo['email']}'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo "Error: " . mysqli_error($conn);
        die();
    }

    if (mysqli_num_rows($result) === 0) {
        // convert username to uppercase
        $uppercaseUsername = strtoupper($userinfo['full_name']);

        // user does not exist
        $sql = "INSERT INTO users (email, first_name, last_name, username, picture, verifiedEmail, token, active_status) VALUES ('{$userinfo['email']}', '{$userinfo['first_name']}', '{$userinfo['last_name']}','{$uppercaseUsername}', '{$userinfo['picture']}', '{$userinfo['verifiedEmail']}', '{$userinfo['token']}', 1)";
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
        header("Location: password_setup.php");
        die();
    }
    // Redirect to the profile page
    header("Location: profile_page.php");
    die();
}
?>
