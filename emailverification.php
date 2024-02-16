<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Signup</title>
    <link rel="stylesheet" href="css/signup.css">
</head>
<body>

<div class="container">
    <div class="signup-box">
    <h2>Signup</h2>
        <form class="signup-form" method="post" action="emailverification_handler.php">
            <label for="email">Email</label>
            <input type="email" id="email" name="email">
            <br>
            <input type="submit" value="Email Verification">
        </form>

        <?php
        // Check if the user is not logged in, then display the Google login button
        if (!isset($_SESSION['user_token'])):
        ?>
            <a class='google-btn' href='<?php echo $client->createAuthUrl(); ?>'>
                <span class='google-icon'></span>
                Google Signup
            </a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
