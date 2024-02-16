<?php
session_start();

$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!$email) {
    
    header("Location: emailverification.php");
    exit();
}
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
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?php echo $email; ?>">
            <br>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required style="text-transform: uppercase;">
            <br>
        
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <input type="submit" value="Sign Up">
        </form>
    </div>
</div>

</body>
</html>
