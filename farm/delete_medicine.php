<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Medicine.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?msg=Please Login before Proceeding');
    exit();
}

$user_id = $_SESSION['user_id'];

$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

if (isset($_GET['med_id'])) {
    $med_id = $_GET['med_id'];

    // Create an instance of the Medicine class
    $medicine = new Medicine($con);

    // Delete the medicine using the delete method from the Medicine class
    if ($medicine->delete($med_id)) {
        header('Location: medicine.php?msg=Medicine Deleted Successfully');
    } else {
        header('Location: medicine.php?msg=Failed to Delete Medicine');
    }
    exit();
} else {
    // If med_id is not set, redirect with an error message
    header('Location: medicine.php?msg=No Medicine ID Provided');
    exit();
}
