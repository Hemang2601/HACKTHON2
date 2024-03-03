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
    $userSql = "SELECT * FROM users WHERE email = ?";
    $userStmt = mysqli_prepare($conn, $userSql);
    mysqli_stmt_bind_param($userStmt, "s", $email);
    mysqli_stmt_execute($userStmt);
    $userResult = mysqli_stmt_get_result($userStmt);

    $companySql = "SELECT * FROM companies WHERE company_email = ?";
    $companyStmt = mysqli_prepare($conn, $companySql);
    mysqli_stmt_bind_param($companyStmt, "s", $email);
    mysqli_stmt_execute($companyStmt);
    $companyResult = mysqli_stmt_get_result($companyStmt);

    if (!$userResult || !$companyResult) {
        die("Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($userResult) > 0) {
        $user = mysqli_fetch_assoc($userResult);

        // Check if the password matches
        if (password_verify($password, $user['password'])) {
            // Check if the user's active_status is -1
            if ($user['active_status'] == -1) {
                showAlert('warning', 'Account Pending', 'Your account is pending activation. Please wait for 24 hours.');
            } elseif ($user['active_status'] == 0) {
                // Check if the user's active_status is 0 and update to 1
                $updateSql = "UPDATE users SET active_status = 1 WHERE email = ?";
                $updateStmt = mysqli_prepare($conn, $updateSql);
                mysqli_stmt_bind_param($updateStmt, "s", $email);
                mysqli_stmt_execute($updateStmt);

                if (!$updateStmt) {
                    die("Error updating active status: " . mysqli_error($conn));
                }

                $_SESSION['user_token'] = $user['token'];
                $_SESSION['user_email'] = $user['email'];

                // Check user role and redirect accordingly
                if ($user['role'] == 0) {
                    header("Location: /portfoliohub/UserDashboard/home.php");
                } elseif ($user['role'] == 1) {
                    header("Location: /portfoliohub/AdminDashboard/home.php");
                }

                die();
            } else {
                showAlert('error', 'Account Already Active', 'Your account is already active.');
            }
        } else {
            showAlert('error', 'Invalid Password', 'Please check your password.');
        }
    } elseif (mysqli_num_rows($companyResult) > 0) {
        $company = mysqli_fetch_assoc($companyResult);

        // Check if the password matches
        if (password_verify($password, $company['password'])) {
            // Check if the company's active_status is -1
            if ($company['active_status'] == -1) {
                showAlert('warning', 'Account Pending', 'Your account is pending activation. Please wait for 24 hours.');
            } elseif ($company['active_status'] == 0) {
                // Check if the company's active_status is 0 and update to 1
                $updateSql = "UPDATE companies SET active_status = 1 WHERE company_email = ?";
                $updateStmt = mysqli_prepare($conn, $updateSql);
                mysqli_stmt_bind_param($updateStmt, "s", $email);
                mysqli_stmt_execute($updateStmt);
        
                // Check if the update was successful
                if ($updateStmt) {
                    $_SESSION['company_token'] = $company['token'];
                    $_SESSION['company_email'] = $company['company_email'];
        
                    // Redirect to the company dashboard or profile page
                    header("Location: /portfoliohub/CompanyDashboard/dashboard.php");
                    die();
                } else {
                    die("Error updating active status: " . mysqli_error($conn));
                }
            } elseif ($company['active_status'] == 1) {
                showAlert('error', 'Account Already Active', 'Company account is already active.');
            } else {
                showAlert('error', 'Invalid Credentials', 'Please check your email and password.');
            }
        } else {
            showAlert('error', 'Invalid Password', 'Please check your password.');
        }
        
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
?>
