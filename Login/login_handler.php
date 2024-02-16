<?php

session_start(); 

// Create connection
$conn = new mysqli('localhost', 'root', '', 'portfoliohub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect to the welcome page if the user is already logged in
if (isset($_SESSION['user_token'])) {
    header("Location: /portfoliohub/UserDashboard/profile_page.php");
    die();
}

// Check if the manual login form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the login credentials from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Check if the password matches
        if (password_verify($password, $user['password'])) {
            // Check if the user's active_status is 0 and update to 1
            if ($user['active_status'] == 0) {
                $updateSql = "UPDATE users SET active_status = 1 WHERE email = ?";
                $updateStmt = mysqli_prepare($conn, $updateSql);
                mysqli_stmt_bind_param($updateStmt, "s", $email);
                mysqli_stmt_execute($updateStmt);

                if (!$updateStmt) {
                    die("Error updating active status: " . mysqli_error($conn));
                }

                
                $_SESSION['user_token'] = $user['token'];
                $_SESSION['user_email'] = $user['email'];
                
                header("Location: /portfoliohub/UserDashboard/profile_page.php");
                die();
            } else {
                
                showAlert('error', 'Account Already Active', 'Your account is already active.');
            }
        } else {
            
            showAlert('error', 'Invalid Password', 'Please check your password.');
        }
    } else {
        
        showAlert('error', 'Invalid Credentials', 'Please check your email and password.');
    }
}

function showAlert($icon, $title, $text) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '{$icon}',
                title: '{$title}',
                text: '{$text}',
            }).then(function() {
                window.history.back();
            });
        });
    </script>";
}

