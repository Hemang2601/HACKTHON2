<?php
// Start the session
session_start();

// Create connection
$conn = new mysqli('localhost', 'root', '', 'portfoliohub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the email is in the session
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

// If email is not found in the session, redirect to emailverification.php
if (!$email) {
    header("Location: emailverification.php");
    exit(); // Ensure that script execution stops after redirection
}

// Display an error message and redirect if the email is not found
if (!$email) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Email not found.',
                showConfirmButton: false,
                timer: 1000
            }).then(() => {
                window.location.href = 'emailverification.php';
            });
        });
    </script>";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $enteredOTP = $_POST['otp'];

    // Check the entered OTP against the database
    $selectSql = "SELECT * FROM otp WHERE email = ? AND otp_code = ?";
    $selectStmt = mysqli_prepare($conn, $selectSql);
    mysqli_stmt_bind_param($selectStmt, "ss", $email, $enteredOTP);
    mysqli_stmt_execute($selectStmt);
    $result = mysqli_stmt_get_result($selectStmt);

    if ($row = mysqli_fetch_assoc($result)) {
        
         // Delete the record from the database based on OTP
         $deleteSql = "DELETE FROM otp WHERE otp_code = ?";
         $deleteStmt = mysqli_prepare($conn, $deleteSql);
         mysqli_stmt_bind_param($deleteStmt, "s", $enteredOTP);
         mysqli_stmt_execute($deleteStmt);

         
        // Redirect or show a success message
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Email verified successfully.',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    window.location.href = 'signup_page.php';
                });
            });
        </script>";
        exit();
    } else {
        // Invalid OTP, show an error message
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid verification code. Please try again.',
                    showConfirmButton: false,
                    timer: 1000
                });
            });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
<p>Please check your email (<strong><?php echo $email; ?></strong>) for the verification code.</p>
    <div class="container">
    <div class="otp-verification-box">
    
        <form class="otp-verification-form" method="post" action="">
            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" required>
            <br>
            <input type="submit" value="Verify OTP">
        </form>
    </div>
</body>

</html>
