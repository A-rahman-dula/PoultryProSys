<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'admin_frame.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$admin = CheckLogin::checkLoginAndRole($user_id, 'admin');

$adminframe = new AdminFrame();
$adminframe->first_part($admin);

?>


<div class="contentArea">

    <div class="row2" style="height: 200px">
        <div class="col1">
            <h5 style="height: 150px;padding-left: 100px;padding-top: 50px">TOTAL CUSTOMERS</h5>
            <hr class="dropdown-divider" style="font-weight: bold;">
            <h5 style="background-color: whitesmoke; height: 50px;padding-top: 8px">Content area</h5>
        </div>
        <div class="col2">
            <h5 style="height: 150px;padding-left: 100px;padding-top: 50px">TOTAL FARMS</h5>
            <h5 style="background-color: whitesmoke; height: 50px;padding-top: 8px">Content area</h5>
        </div>
    </div>

    <hr class="dropdown-divider" style="color:black">

    <div class="row3">
        <h1>I am row 3</h1>
    </div>

</div>

<?php
$adminframe->last_part();
?>