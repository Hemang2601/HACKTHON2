<?php
// Start the session (ensure this is at the beginning)
session_start();

// Check if the email is in the session
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

// If email is not found in the session, redirect to emailverification.php
if (!$email) {
    header("Location: emailverification.php");
    exit(); 
}

// Create connection
$conn = new mysqli('localhost', 'root', '', 'portfoliohub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Manual signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    $username = strtoupper($_POST['username']);
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Generate a unique 21-digit token
    $token = generateUniqueToken($conn);

    // Check if the email is already registered
    $checkEmailSql = "SELECT * FROM users WHERE email = '{$email}'";
    $checkEmailResult = mysqli_query($conn, $checkEmailSql);

    if (!$checkEmailResult) {
        logError("Error checking email: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($checkEmailResult) > 0) {

        // Display SweetAlert for success
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        title: "Email is already registered!",
                        text: "Error",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "emailverification.php"; // Redirect to the login page
                    });
                });
             </script>';
        exit();
    } else {
        // Insert new user into the database with verifiedEmail set to '1' and the generated token
        $insertUserSql = "INSERT INTO users (username, email, password, verifiedEmail, token) VALUES ('{$username}', '{$email}', '{$password}', '1', '{$token}')";

        $insertUserResult = mysqli_query($conn, $insertUserSql);

        if (!$insertUserResult) {
            logError("Error inserting user: " . mysqli_error($conn));
        }

        // Destroy the session and unset the user_token session variable
        session_destroy();
        unset($_SESSION['email']);

        // Display SweetAlert for success
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
        echo '<script>
                  document.addEventListener("DOMContentLoaded", function () {
                      Swal.fire({
                          title: "Success!",
                          text: "Signup successful!",
                          icon: "success",
                          confirmButtonText: "OK"
                      }).then(() => {
                          window.location.href = "/portfoliohub/Login/index.php"; // Redirect to the login page
                      });
                  });
               </script>';
        exit();
    }
}

function logError($message) {
    echo "<script>
            console.error('$message');
          </script>";
}

function generateUniqueToken($conn) {
    $token = '';
    do {
        $token = '';
        for ($i = 1; $i <= 21; $i++) {
            $token .= rand(0, 9);
        }
    } while (tokenExistsInDatabase($conn, $token));

    return $token;
}

function tokenExistsInDatabase($conn, $token) {
    $checkTokenSql = "SELECT * FROM users WHERE token = '{$token}'";
    $checkTokenResult = mysqli_query($conn, $checkTokenSql);

    if (!$checkTokenResult) {
        logError("Error checking token: " . mysqli_error($conn));
    }

    return mysqli_num_rows($checkTokenResult) > 0;
}
?>
