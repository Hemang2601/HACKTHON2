<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize the new password
    $newPassword = $_POST['new_password'];

    // You should add proper password validation here

    $resetEmail = $_SESSION['resetEmail'];

    // Connect to the database using MySQLi
    $conn = new mysqli($hostname, $username, $password, $database);

    // Check for database connection errors
    if ($conn->connect_error) {
        die("Database Connection failed: " . $conn->connect_error);
    }

    try {
        // Update the user's password in the database
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param('ss', $hashedPassword, $resetEmail);
        $stmt->execute();

        // Display SweetAlert for success
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        title: "Success!",
                        text: "Password updated successfully!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "index.php"; // Redirect to the login page
                    });
                });
             </script>';
        exit();
    } finally {
        // Close the database connection
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <link rel="stylesheet" href="css/index.css"> <!-- Use the appropriate CSS file -->
</head>
<body>

<div class="container">
    <div class="login-box">
        <h2>Update Password</h2>

        <?php if (isset($resetError)): ?>
            <p class="login-error"><?= $resetError; ?></p>
        <?php endif; ?>

        <form method="post" action="update_password.php">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <br>
            <input type="submit" value="Update Password">
        </form>
    </div>
</div>

</body>
</html>
