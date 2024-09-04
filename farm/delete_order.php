<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Order.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?msg=Please Login before Proceeding');
    exit();
}

$user_id = $_SESSION['user_id'];

$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Create an instance of the Medicine class
    $order = new Order($con);

    // Delete the medicine using the delete method from the Medicine class
    if ($order->delete($order_id)) {
        header('Location: orders.php?msg=Medicine Deleted Successfully');
    } else {
        header('Location: orders.php?msg=Failed to Delete Medicine');
    }
    exit();
} else {
    // If med_id is not set, redirect with an error message
    header('Location: orders.php?msg=No Medicine ID Provided');
    exit();
}
