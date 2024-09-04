<?php
session_start();
require 'classes/config.php';
require 'classes/User.php';
require 'classes/Validation.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$emailErr = $passwordErr = "";
$errors = false;
// Check when form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate password
    if (!Validation::validatePasswordField($_POST['password'], $passwordErr)) {
        $errors = true;
    } else {
        $user->password = $_POST['password'];
    }

    // Validate email
    if (!Validation::validateEmail($_POST['email'], $emailErr)) {
        $errors = true;
    } else {
        $user->email = $_POST['email'];
    }

    if (!$errors) {
        if ($user->login()) {
            // Successful login
            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['username'] = $user->username;
            $_SESSION['role'] = $user->role;
            $_SESSION['status'] = $user->status;

            switch ($_SESSION['role']) {
                case "admin":
                    header("Location: admin_dashboard.php");
                    exit();
                case "farm":
                    header("Location: farm/dashboard.php");
                    exit();
                case "customer":
                    header("Location: marketplace.php");
                    exit();
                default:

                    header("Location: login.php");
                    exit();
            }
        } else {
            // Invalid email or password
            $_SESSION['error_message'] = "Invalid email or password";
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title>Log in form</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            .footer {
                background-color: #356854;
                color: white;
                padding: 20px 0;
                margin-top: 100px;
                text-align: center;
            }

            .footer a {
                color: white;
                text-decoration: none;
                margin: 0 10px;
            }

            .footer a:hover {
                text-decoration: underline;
            }

            .footer img {
                width: 40px;
                height: 40px;
                border-radius: 50%;
            }

            .footer-icons {
                font-size: 28px;
                margin-top: 10px;
            }

            .footer-icons a {
                color: white;
                margin: 0 10px;
            }

            .footer-icons a:hover {
                color: #ddd;
            }
        </style>
    </head>

    <body>

        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #40826D;">
            <a class="navbar-brand mx-5" href="index.html" style="font-weight: bold">
                <img src="images/logo-poultryPro2.jpeg" alt="logo-poultryPro" style="border-radius: 50%; width: 40px; height: 40px;">
                PoultryPro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
                <ul class="navbar-nav align-items-center">
                    <div class="justify-content-center mt-2" style="color:white;">You don't have an account?</div>
                    <li class="nav-item align-items-center mx-4" style="background-color:#B7BF4A;">
                        <a class="nav-link" href="register.php" style="font-weight: bold; color:white;">Sign Up</a>
                    </li>
                </ul>
            </div>
        </nav>
        <?php
        if (isset($_GET['status']) && $_GET['status'] === 'blocked') {
            ?> <div class='alert alert-danger' role='alert'>
                Your account was temporarily blocked by admin. Contact admin via this <a href='https://mail.google.com/mail' class='alert-link'> arahmandulapandan@gmail.com</a> mail.
            </div><?php
    }
        ?>
        <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
            <div class="row">
                <div class="col-md-4">
                    <div class="card" style="width:450px;">
                        <div class="card-header" style="background-color: #40826D;color: white;">
                            <h4>Log In</h4>
                        </div>
                        <div class="card-body">

                            <?php if (isset($_SESSION['error_message'])) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $_SESSION['error_message']; ?>
                                </div>
                                <?php unset($_SESSION['error_message']); ?>
                            <?php endif; ?>

                            <form id="batchForm" action="login.php" method="post">
                                <div class="row">
                                    <label>Email:<span style="color: red;"><?php echo $emailErr ?></span></label>
                                    <input type="text" class="form-control" name="email" placeholder="abc123@gmail.com">
                                </div>
                                <br>
                                <div class="row">
                                    <label>Password:<span style="color: red;"><?php echo $passwordErr ?></span></label>
                                    <input type="password" class="form-control" name="password">
                                </div>
                                <br>
                                <input type="submit" class="btn btn-primary" name="submit" value="Log In" style="margin-left:100px">
                                <a class='mx-5' href="">Forget Password</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div style="align-items: center; text-align: center;">
                <a href="index.html" style="color: white;">About Us</a>&nbsp;&nbsp;
                <a href="#" style="color: white;">Market Place</a>&nbsp;&nbsp;
                <a href="contact_us.php" style="color: white;">Contact Us</a>&nbsp;&nbsp;
                <a href="feedbacks.php" style="color: white;">Review</a>&nbsp;&nbsp;
                <a href="login.php" style="color: white;">Log In</a>&nbsp;&nbsp;
                <a href="register.php" style="color: white;">Sign Up</a>
            </div>
            <div style="align-items: center; text-align: center;">
                <hr style="border: 1px solid white; border-radius: 5px;  margin-left: 50px; margin-right: 50px;">
            </div>
            <div style="display: flex; align-items: center;">
                <div style="flex: 1; text-align: center;">
                    <p>&copy; 2024 PoultryPro. All Rights Reserved.</p>
                </div>
                <div style="display: flex; align-items: center;">
                    <a class="navbar-brand mx-5" href="index.html">
                        <img src="images/logo-poultryPro2.jpeg" alt="logo-poultryPro" style="border-radius: 50%;">
                        PoultryPro
                    </a>
                </div>
                <div style="flex: 1; text-align: center; font-size: 28px;">
                    <a href="login.php" style="color: white;"><i class="bi bi-instagram"></i></a>&nbsp;
                    <a href="https://www.facebook.com/abdulrahman.dulapandan?mibextid=JRoKGi"><i class="bi bi-facebook"></i></a>
                    &nbsp;
                    <a href="https://wa.me/+94768821356?text=I'm%20interested%20in%20your%20car%20for%20sale"><i class="bi bi-whatsapp"></i></a>
                    &nbsp;



                </div>

            </div>
        </footer>

    </body>

</html>