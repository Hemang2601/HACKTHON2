<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS (Updated link) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.19.0/font/bootstrap-icons.css">
    <title>Login Page</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        .mb-3 {
            position: relative;
        }

        .form-control-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 10px;
            color: #6c757d;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="container login-container">
        <h2 class="text-center mb-4">Login <i class="bi bi-box-arrow-in-right"></i></h2>
        <form id="loginForm" action="#" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username <i class="bi bi-person form-control-icon"></i>:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password <i class="bi bi-lock form-control-icon"></i>:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="button" class="btn btn-primary animate__animated animate__fadeIn"
                onclick="validateLogin()">Login <i class="bi bi-box-arrow-in-right"></i></button>
        </form>
    </div>

    <!-- Bootstrap JS and Popper.js (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Animate.css (Optional for animations) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <script>
        function validateLogin() {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;

            // Basic validation
            if (username === '' || password === '') {
                alert('Please enter both username and password.');
            } else {
                // You can add an AJAX request to your server for more secure validation.
                // For simplicity, we'll just display an alert for successful login.
                alert('Login successful!');
                document.getElementById('loginForm').reset();
            }
        }
    </script>
</body>

</html>
