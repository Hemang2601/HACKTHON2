<?php
require_once 'config.php';


// Check if user is authenticated
if (!isset($_SESSION['user_token'])) {
    header("Location: index.php");
    die();
}

// Retrieve user info from the database
$sql = "SELECT * FROM users WHERE token ='{$_SESSION['user_token']}'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
    die();
}

if (mysqli_num_rows($result) > 0) {
    // user exists
    $userinfo = mysqli_fetch_assoc($result);

    // Redirect to password setup if the user has not set a password
    if (empty($userinfo['password'])) {
        header("Location: password_setup.php");
        die();
    }
} else {
    // user not found in the database
    header("Location: index.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            padding: 30px;
            text-align: left;
            width: 300px;
        }

        .profile-image {
            border-radius: 50%;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        table {
            width: 100%;
        }

        table td {
            padding: 10px;
        }

        strong {
            color: #3498db;
        }

        .logout-btn {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-right: 10px;
        }

        .logout-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <img class="profile-image" src="<?= $userinfo['picture'] ?>" alt="Profile Image" width="90px" height="90px">
        <table>
            <tr>
                <td><strong>Name:</strong></td>
                <td><?= isset($userinfo['full_name']) ? $userinfo['full_name'] : '' ?></td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td><?= isset($userinfo['email']) ? $userinfo['email'] : '' ?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <a href="logout.php" class="logout-btn">Logout</a>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
