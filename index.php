<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

<div class="container">
    <div class="login-box">
        <h2>Login</h2>
        <form class="login-form" method="post" action="login_handler.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <?php if (!isset($_SESSION['user_token'])): ?>
            <p class="forgot-password"><a href="forgot_password.php">Forgot Password?</a></p>
            <?php endif; ?>
            
            <input type="submit" value="Login">
            <?php if (isset($loginError)): ?>
                <p class="login-error"><?= $loginError; ?></p>
            <?php endif; ?>
        </form>

        <?php if (!isset($_SESSION['user_token'])): ?>
            <!-- Show registration link only when the user is not logged in -->
            <p>Don't have an account? <a href="emailverification.php">Register here</a>.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
