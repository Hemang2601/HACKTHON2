<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<!-- Coding by CodingLab | www.codinglabweb.com-->
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Responsive Login and Signup Form </title>

        <!-- CSS -->
        <link rel="stylesheet" href="css/style.css">
                
        <!-- Boxicons CSS -->
        <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
                        
    </head>
    <body>
        <section class="container forms">
            <div class="form login">
                <div class="form-content">
                    <header>Login</header>
                    <form method="post" action="login_handler.php">
                        <div class="field input-field">
                            <input type="email" id="email" name="email" placeholder="Email" class="input">
                        </div>

                        <div class="field input-field">
                            <input type="password" id="password" name="password" placeholder="Password" class="password">
                            <i class='bx bx-hide eye-icon'></i>
                        </div>

                        <div class="form-link">
                            <a href="forgot_password.php" class="forgot-pass">Forgot password?</a>
                        </div>

                        <div class="field button-field">
                            <button type="Submit">Login</button>
                        </div>
                    </form>

                    <div class="form-link">
                        <span>Don't have an account? <a href="#" class="link signup-link">Signup</a></span>
                    </div>
                </div>

                <div class="line"></div>


                <div class="media-options">
                    <a href="#" class="field google">
                        <img src="images/google.png" alt="" class="google-img">
                        <span>Login with Google</span>
                    </a>
                </div>

            </div>

            <div class="form signup">
                <div class="form-content">
                    <header>Signup</header>
                    <form method="post" action="signup_handler.php">
                    <div class="field input-field">
                            <input type="text" id="username" name="username" placeholder="Username"  class="input">
                        </div>

                        <div class="field input-field">
                            <input type="email" id="email" name="email" placeholder="Email" class="input">
                        </div>

                        <div class="field input-field">
                            <input type="password" id="password" name="password" placeholder="Create password" class="password">
                            <i class='bx bx-hide eye-icon'></i>
                        </div>


                        <div class="field button-field">
                            <button>Signup</button>
                        </div>
                    </form>

                    <div class="form-link">
                        <span>Already have an account? <a href="#" class="link login-link">Login</a></span>
                    </div>
                </div>

                <div class="line"></div>

                <div class="media-options">
                    <a href='<?php echo $client->createAuthUrl(); ?>' class="field google">
                        <img src="images/google.png" alt="" class="google-img">
                        <span>Login with Google</span>
                    </a>
                </div>

            </div>
        </section>

        <!-- JavaScript -->
        <script src="js/script.js"></script>
    </body>
</html>