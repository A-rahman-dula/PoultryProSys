<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Supplier.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?msg=Please Login before Proceeding');
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

if (isset($_GET['sup_id'])) {
    $sup_id = $_GET['sup_id'];
    
    // Create an instance of the Supplier class
    $supplier = new Supplier($con);
    
    // Delete the supplier using the delete method from the Supplier class
    if ($supplier->delete($sup_id)) {
        header('Location: supplier.php?msg=Supplier Deleted Successfully');
    } else {
        header('Location: supplier.php?msg=Failed to Delete Supplier');
    }
    exit();
} else {
    header('Location: supplier.php?msg=Supplier ID not provided');
    exit();
}
