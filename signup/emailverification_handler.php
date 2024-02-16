<?php


// Create connection
$conn = new mysqli('localhost', 'root', '', 'portfoliohub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();


include '../otpsystem/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$emailSent = false; // Flag to indicate if the email has been sent

// Check if the email is in the POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $_SESSION['email'] = $email; // Store the email in the session for later use
} else {
    // If the email is not in the POST data, check if it's in the session
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

    // Display an error message using SweetAlert and redirect
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
                    window.location.href = '/portfoliohub/signup/emailverification.php';
                });
            });
        </script>";
        exit();
    }
}

// Check if the email is empty
if (empty($email)) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Email cannot be empty.',
                showConfirmButton: false,
                timer: 1000
            }).then(() => {
                window.location.href = '/portfoliohub/signup/emailverification.php';
            });
        });
    </script>";
    exit();
}


$verificationCode = generateVerificationCode();

// Store the verification code in the database
$insertSql = "INSERT INTO otp (email, otp_code) VALUES (?, ?)";
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, "ss", $email, $verificationCode);
mysqli_stmt_execute($insertStmt);

if ($insertStmt) {
    // Send verification email
    sendVerificationEmail($email, $verificationCode);
    $emailSent = true; // Set the flag to true if the email is sent successfully
} else {
    die("Error inserting verification code: " . mysqli_error($conn));
}

// Redirect only if the email is successfully sent
if ($emailSent) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Verification $email sent successfully. Check your email for the verification code.',
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                window.location.href = '/portfoliohub/signup/emailverification_otp.php';
            });
        });
    </script>";
    die();
}


function generateVerificationCode() {
    return mt_rand(100000, 999999); // Generates a random 6-digit OTP
}

function sendVerificationEmail($email, $verificationCode) {
    $mail = new PHPMailer(true);

    try {
        // Server settings for Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hemanglakhadiya49@gmail.com'; // Your Gmail email address
        $mail->Password = 'tstx towt ggis bvle'; // Your Gmail password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('hemanglakhadiya49@gmail.com', 'Mr. Hemang Lakhadiya');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body    = 'Your verification code is: ' . $verificationCode;

        $mail->send();
    } catch (Exception $e) {
        die("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
?>
