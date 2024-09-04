<?php
if (isset($_POST["submit"])) {
    require 'PHPMailer/PHPMailerAutoload.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'arahmandulapandan@gmail.com';
    $mail->Password = 'peqc pndj viho pslc';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom($email, $name);
    $mail->addAddress('cst21057@std.uwu.ac.lk', 'A. A. A. Haamidh');
    $mail->addAddress('cst21048@std.uwu.ac.lk', 'A.R. Dulapandan');
    $mail->addAddress('cst21049@std.uwu.ac.lk', 'H.T.D. De Zoysa');
    $mail->addAddress('cst21072@std.uwu.ac.lk', 'V.D.S. Premachandra');
    $mail->addAddress('cst21094@std.uwu.ac.lk', 'B.S.J. Jayoda');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;

    $statusMessage = '';
    if (!$mail->send()) {
        $statusMessage = '<div class="alert alert-warning d-flex align-items-center" role="alert">
                              <i class="bi bi-exclamation-triangle-fill"></i>&nbsp;Message could not be sent.
                          </div>';
    } else {
        $statusMessage = '<div class="alert alert-success d-flex align-items-center" role="alert">
                              <i class="bi bi-check-circle-fill"></i>&nbsp;Message has been sent.
                          </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoultryPro: Poultry Farm Management Platform</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .navbar {
            background-color: #356854;
        }

        .navbar .nav-link {
            color: white !important;
            font-weight: bold;
        }

        .navbar .nav-link:hover {
            color: #ddd !important;
        }

        .navbar .nav-item.active .nav-link {
            color: #fff !important;
            text-decoration: underline;
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .sign-in-btn {
            background-color: #B7BF4A !important;
            color: white !important;
        }

        .contact-header {
            background-color: #356854;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .contact-info h4 {
            margin-top: 20px;
        }

        .contact-info a {
            color: #356854;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .contact-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .contact-form label {
            font-weight: bold;
        }

        .contact-form .btn {
            background-color: #356854;
            color: white;
            font-weight: bold;
        }

        .contact-form .btn:hover {
            background-color: #2c5443;
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

        .map-container {
            margin-top: 30px;
        }

        .map-container iframe {
            border: 0;
            width: 100%;
            height: 450px;
        }

        .contact-section.with-overlay {
            position: relative;
            background-image: url('images/img3.jpg');
            background-size: 500px 600px;
        }

        .contact-section.with-overlay::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.4);
            z-index: 1;
        }

        .contact-info {
            position: relative;
            z-index: 2;
            margin: 60px 20px;
        }

        #row1 {
            margin: auto;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand mx-5" href="index.html">
            <img src="images/logo-poultryPro2.jpeg" alt="PoultryPro Logo">
            PoultryPro
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item mx-4">
                    <a class="nav-link" href="index.html">About Us</a>
                </li>
                <li class="nav-item mx-4">
                    <a class="nav-link" href="marketplace.php">Market Place</a>
                </li>
                <li class="nav-item mx-4">
                    <a class="nav-link" href="contact_us.php">Contact</a>
                </li>
                <li class="nav-item mx-4">
                    <a class="nav-link" href="feedbacks.php">Review</a>
                </li>
                <li class="nav-item mx-4">
                    <a class="nav-link" href="login.php">Log In</a>
                </li>
                <li class="nav-item mx-4">
                    <a class="nav-link sign-in-btn" href="register.php">Sign Up</a>
                </li>
            </ul>
        </div>
    </nav>


    <?php if (isset($statusMessage)) echo $statusMessage; ?>


    <div class="container contact-container">
        <div class="contact-header" style="margin-top: 30px;">
            <h2>Contact Us</h2>
            <p>We'd love to hear from you! Please reach out to us through any of the following methods.</p>
        </div>
        <div class="row" id="row1">
            <div class="col-md-6 contact-section with-overlay">
                <div class="contact-info">
                    <div>
                        <h4>WhatsApp Us</h4>
                        <a href="https://wa.me/+94768821356?text=I'm%20interested%20in%20your%20car%20for%20sale">
                            <i class="bi bi-whatsapp" style="font-size: 24px;"></i> +94 76 882 1356
                        </a>
                    </div>
                    <div>
                        <h4>FAQ Chat</h4>
                        <a href="chatFAQ.php">
                            <i class="bi bi-wechat" style="font-size: 24px;"></i> Start a chat
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 contact-section" style="margin-top: 15px;">
                <div class="contact-form">
                    <h4>Email Us</h4>
                    <a href="mailto:cst21057@std.uwu.ac.lk" style="color: #356854; text-decoration: none;">
                        <i class="bi bi-envelope-at-fill" style="font-size: 24px;"></i> support@poultrypro.com
                    </a>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                        <div class="form-group mb-3">
                            <label for="name">Full Name:</label>
                            <input class="form-control" type="text" name="name" id="name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email:</label>
                            <input class="form-control" type="email" name="email" id="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="subject">Subject:</label>
                            <input class="form-control" type="text" name="subject" id="subject" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="message">Message:</label>
                            <textarea class="form-control" name="message" id="message" rows="4" required></textarea>
                        </div>
                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-primary" name="submit" value="Send">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row map-container">
            <div class="col-md-12">
                <h4>Our Location</h4>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.2212940273894!2d81.07679557373318!3d6.98319141765334!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae4618a1a9fec37%3A0x1dd900702229654b!2sUva%20Wellassa%20University%20of%20Sri%20Lanka!5e0!3m2!1sen!2slk!4v1721728842713!5m2!1sen!2slk" loading="lazy"></iframe>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div style="align-items: center; text-align: center;">
            <a href="index.html" style="color: white;">About Us</a>&nbsp;&nbsp;
            <a href="#" style="color: white;">Market Place</a>&nbsp;&nbsp;
            <a href="contact_us.php" style="color: white;">Contact</a>&nbsp;&nbsp;
            <a href="feedbacks.php" style="color: white;">Review</a>&nbsp;&nbsp;
            <a href="login.php" style="color: white;">Log In</a>&nbsp;&nbsp;
            <a href="register.php" style="color: white;">Sign Up</a>
        </div>
        <div style="align-items: center; text-align: center;">
            <hr style="border: 1px solid white; border-radius: 5px; margin-left: 50px; margin-right: 50px;">
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
                <a href="https://www.facebook.com/abdulrahman.dulapandan?mibextid=JRoKGi"><i class="bi bi-facebook"></i></a>&nbsp;
                <a href="https://wa.me/+94768821356?text=I'm%20interested%20in%20your%20car%20for%20sale"><i class="bi bi-whatsapp"></i></a>
            </div>
        </div>
    </footer>
</body>

</html>