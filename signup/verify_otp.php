<?php
require_once '../Config/config.php';


// Check if the user's email is set in the session
if (!isset($_SESSION['resetEmail'])) {
    // Redirect to the forgot password page if the email is not set
    header('Location: /portfoliohub/signup/forgot_password.php');
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Use the entered OTP without filtering
    $enteredOTP = $_POST['otp'];

    if ($enteredOTP) {
        $resetEmail = $_SESSION['resetEmail'];

        // Connect to the database using MySQLi
        $conn = new mysqli($hostname, $username, $password, $database);

        // Check for database connection errors
        if ($conn->connect_error) {
            die("Database Connection failed: " . $conn->connect_error);
        }

        try {
            // Check if the entered OTP matches the stored OTP
            $stmt = $conn->prepare("SELECT * FROM otp WHERE email = ? AND otp_code = ?");
            $stmt->bind_param('ss', $resetEmail, $enteredOTP);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // OTP verified successfully, delete the OTP data from the database
                $deleteStmt = $conn->prepare("DELETE FROM otp WHERE email = ?");
                $deleteStmt->bind_param('s', $resetEmail);
                $deleteStmt->execute();
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@latest"></script>';
                echo '<script>
                        document.addEventListener("DOMContentLoaded", function () {
                            Swal.fire({
                                title: "OTP Verified Successfully!",
                                text: "",
                                icon: "success",
                                confirmButtonText: "OK",
                                target: document.body
                            }).then(() => {
                                window.location.href = "/portfoliohub/signup/update_password.php";
                            });
                        });
                      </script>';
                exit();
            } else {
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@latest"></script>';
                echo '<script>
                        document.addEventListener("DOMContentLoaded", function () {
                            Swal.fire({
                                title: "Invalid OTP",
                                text: "Please try again with a valid OTP.",
                                icon: "error",
                                confirmButtonText: "OK",
                                target: document.body
                            });
                        });
                      </script>';
            }
        } finally {
            // Close the database connection
            $stmt->close();
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="/portfoliohub/css/index.css"> <!-- Use the appropriate CSS file -->
</head>
<body>

<div class="container">
    <div class="login-box">
        <h2>Verify OTP</h2>

        <?php if (isset($resetError)): ?>
            <p class="login-error"><?= $resetError; ?></p>
        <?php endif; ?>

        <form method="post" action="verify_otp.php">
            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" required>
            <br>
            <input type="submit" value="Verify OTP">
        </form>
    </div>
</div>

</body>
</html>
