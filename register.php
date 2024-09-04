<?php
session_start();
require 'classes/config.php';
require 'classes/User.php';
require 'classes/Validation.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$query = "SELECT city FROM city";
$stmt = $db->prepare($query);
$stmt->execute();
$cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mobileErr = $emailErr = $textErr = $passwordErr = $roleErr = $cityErr = $addressErr = "";
$errors = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate and assign form data
    $user->username = $_POST['username'];
    $user->role = $_POST['role'];
    $user->address = $_POST['address'];
    $user->city = $_POST['city'];
    $user->mobile = $_POST['mobile'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];
    $user->status = 1;

    // Validate mobile
    if (!Validation::validateMobile($user->mobile, $mobileErr)) {
        $errors = true;
    }

    // Validate email
    if (!Validation::validateEmail($user->email, $emailErr)) {
        $errors = true;
    }

    // Validate name
    if (!Validation::validateTextField($user->username, $textErr)) {
        $errors = true;
    }

    // Validate password
    if (!Validation::validatePasswordField($user->password, $passwordErr)) {
        $errors = true;
    }

    // Validate role
    if (empty($user->role)) {
        $roleErr = "*Please select your role";
        $errors = true;
    }

    // Validate city
    if (empty($user->city)) {
        $cityErr = "*Please select city";
        $errors = true;
    }

    // Validate address
    if (!Validation::validateAddressField($user->address, $addressErr)) {
        $errors = true;
    }

    if (!$errors) {
        if ($user->emailExists()) {
?>
            <script type="text/javascript">
                alert("<?php echo "Email already exists. Please login using your email and password."; ?>");
                window.location.href = 'login.php';
            </script>
<?php
        } else {
            if ($user->register()) {
                $_SESSION['user_id'] = $user->user_id;
                $_SESSION['username'] = $user->username;
                $_SESSION['role'] = $user->role;
                $_SESSION['status'] = $user->status;

                switch ($user->role) {
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
                // Debugging output
                echo "<script>alert('Something went wrong.'); window.location.href = 'register.php';</script>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        .navbar-nav .nav-item a {
            font-weight: bold;
        }

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
                <div class="justify-content-center mt-2" style="color:white;">Already have an account?</div>
                <li class="nav-item align-items-center mx-4" style="background-color:#B7BF4A;">
                    <a class="nav-link" href="login.php" style="font-weight: bold; color:white;">Log In</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
        <div class="row">
            <div class="col-md-4">
                <div class="card" style="width:450px;">
                    <div class="card-header" style="background-color: #40826D;color: white;">
                        <h4>Sign Up</h4>
                    </div>
                    <div class="card-body">
                        <form id="batchForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                            <div class="row">
                                <label>Name:<span style="color: red;"><?php echo $textErr ?></span></label>
                                <input type="text" class="form-control" name="username" placeholder="Business name/Your name" required>
                            </div>

                            <div class="row">
                                <label>Role:<span style="color: red;"><?php echo $roleErr ?></span></label>
                                <select name="role" required>
                                    <option value="" disabled selected>Select role</option>
                                    <option value="farm">Farm</option>
                                    <option value="customer">Customer</option>
                                </select>
                            </div>

                            <div class="row">
                                <label>Address:<span style="color: red;"><?php echo $addressErr ?></span></label>
                                <input type="text" class="form-control" name="address" required>
                            </div>

                            <div class="row">
                                <label>City:<span style="color: red;"><?php echo $cityErr ?></span></label>
                                <select name="city" required>
                                    <option value="" disabled selected>Select city</option>
                                    <?php foreach ($cities as $city) : ?>
                                        <option value="<?php echo htmlspecialchars($city['city']); ?>">
                                            <?php echo htmlspecialchars($city['city']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row">
                                <label>Mobile:<span style="color: red;"><?php echo $mobileErr ?></span></label>
                                <input type="text" class="form-control" name="mobile" placeholder="0777777777" required>
                            </div>

                            <div class="row">
                                <label>Email:<span style="color: red;"><?php echo $emailErr ?></span></label>
                                <input type="email" class="form-control" name="email" placeholder="abc123@gmail.com" required>
                            </div>

                            <div class="row">
                                <label>Password:<span style="color: red;"><?php echo $passwordErr ?></span></label>
                                <input type="password" class="form-control" name="password" required>
                            </div>

                            <br>
                            <input type="submit" class="btn btn-primary" name="submit" value="Sign Up" style="margin-left: 150px">
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