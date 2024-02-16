<?php

require_once '../Config/config.php';
require_once '../otpsystem/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if ($email) {
        // Generate a unique OTP code
        $otpCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expirationTime = time() + (1 * 60); // OTP expires in 1 minute

        // Connect to the database using MySQLi
        $conn = new mysqli($hostname, $username, $password, $database);

        // Check for database connection errors
        if ($conn->connect_error) {
            die("Database Connection failed: " . $conn->connect_error);
        }

        try {
            // Store the OTP in the database using MySQLi prepared statement
            $stmt = $conn->prepare("INSERT INTO otp (email, otp_code, expiration_time) VALUES (?, ?, ?)");
            $stmt->bind_param('ssi', $email, $otpCode, $expirationTime);
            $stmt->execute();
            $stmt->close();

            // Send the OTP to the user's email using SMTP
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hemanglakhadiya49@gmail.com'; // Your Gmail email address
            $mail->Password = 'tstx towt ggis bvle'; // Your Gmail password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('hemanglakhadiya49@gmail.com', 'Mr. Hemang Lakhadiya');
            $mail->addAddress($email);

            $mail->Subject = 'OTP for Password Reset';
            $mail->Body = "Your OTP for password reset is: $otpCode";

            $mail->send();

            // Store the email in the session for use in verify_otp.php
            $_SESSION['resetEmail'] = $email;

            // Redirect to a page where the user enters the received OTP
            header('Location: verify_otp.php');
            exit();
        } catch (Exception $e) {
            // Log the error, you may also want to display a generic error message
            error_log("Error sending OTP email: {$mail->ErrorInfo}");

            // Redirect back to the forgot password page with an error message
            $_SESSION['resetError'] = "Error sending OTP email. Please try again.";
            header('Location: forgot_password.php');
            exit();
        } finally {
            // Close the database connection
            $conn->close();
        }
    } else {
        // Invalid email format, redirect back to the forgot password page with an error message
        $_SESSION['resetError'] = "Invalid email format.";
        header('Location: forgot_password.php');
        exit();
    }
}
