<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/index.css"> <!-- Adjust the CSS file path as needed -->
</head>
<body>

<div class="container">
    <div class="login-box">
        <h2>Forgot Password</h2>
        <form class="reset-form" method="post" action="reset_handler.php">
            <!-- Change the action attribute to reset_handler.php -->
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <input type="submit" value="Reset Password">
            <?php if (isset($resetError)): ?>
                <p class="reset-error"><?= $resetError; ?></p>
            <?php endif; ?>
        </form>

        <p>Remember your password? <a href="index.php">Login here</a>.</p>
    </div>
</div>

</body>
</html>
