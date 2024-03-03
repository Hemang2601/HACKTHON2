<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'portfoliohub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the submitted data
$companyName = isset($_POST['company_name']) ? $_POST['company_name'] : '';
$companyEmail = isset($_POST['company_email']) ? $_POST['company_email'] : '';
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Hash the password using bcrypt
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Set active_status to -1 during registration
$activeStatus = -1;

// Generate a unique 21-digit token
$token = generateUniqueToken($conn);

// Set verifiedEmail to 1
$verifiedEmail = 1;

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO companies (company_name, company_email, username, password, active_status, verifiedEmail, token) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssiss", $companyName, $companyEmail, $username, $hashedPassword, $activeStatus, $verifiedEmail, $token);

if ($stmt->execute()) {
    // Registration successful
    echo "<script defer src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script defer>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Registration successful!',
                    text: 'We will contact you within 24 hours.',
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/portfoliohub/Login/index.php';
                    }
                });
            });
          </script>";
    // You can redirect to another page or perform additional actions here
} else {
    // Registration failed
    echo "<script defer src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script defer>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Error during registration',
                    text: '" . $stmt->error . "',
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            });
          </script>";
}

// Close the statement and connection
$stmt->close();
$conn->close();

function generateUniqueToken($conn)
{
    $token = '';
    do {
        $token = '';
        for ($i = 1; $i <= 21; $i++) {
            $token .= rand(0, 9);
        }
    } while (tokenExistsInDatabase($conn, $token));

    return $token;
}

function tokenExistsInDatabase($conn, $token)
{
    $checkTokenSql = "SELECT * FROM companies WHERE token = '{$token}'";
    $checkTokenResult = mysqli_query($conn, $checkTokenSql);

    if (!$checkTokenResult) {
        logError("Error checking token: " . mysqli_error($conn));
    }

    return mysqli_num_rows($checkTokenResult) > 0;
}

function logError($message)
{
    echo "<script>
            console.error('$message');
          </script>";
}
?>
