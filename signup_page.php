<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="css/signup.css">
</head>
<body>

<div class="container">
    <div class="signup-box">
            <form class="signup-form" method="post" action="signup_handler.php">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required style="text-transform: uppercase;">
                <br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <input type="submit" value="Sign Up">
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
