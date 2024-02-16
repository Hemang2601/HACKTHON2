<?php
require_once '../Config/config.php';
require_once 'store_google_data.php';

// Check if user is authenticated
if (!isset($_SESSION['user_token'])) {
    header("Location: /portfoliohub/Login/index.php");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Setup</title>
    <link rel="stylesheet" href="/portfoliohub/css/password_setup.css"> 
</head>
<body>
    <div class="container">
        <h2>Password Setup</h2>
        <form method="post" action="password_setup_handler.php">
            <label for="password">Choose a secure password:</label>
            <input type="password" id="password" name="password">
            <br>
            <input type="submit" value="Set Password">
        </form>
    </div>
</body>

</html>
