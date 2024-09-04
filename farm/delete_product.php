<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Product.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?msg=Please Login before Proceeding');
    exit();
}

$user_id = $_SESSION['user_id'];

$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Create an instance of the Product class
    $product = new Product($con);

    // Get the product details including the image path
    $product_details = $product->readOne($product_id);

    if ($product_details) {
        $image_path = "." . $product_details['product_img'];

        // Delete the image file from the server
        if (file_exists($image_path)) {
            unlink($image_path); // Delete the file
        }

        // Delete the product using the delete method from the Product class
        if ($product->delete($product_id)) {
            header('Location: products.php?msg=Product Deleted Successfully');
        } else {
            header('Location: products.php?msg=Failed to Delete Product');
        }
    } else {
        header('Location: products.php?msg=Product Not Found');
    }

    exit();
} else {
    header('Location: products.php?msg=No Product ID Provided');
    exit();
}
?>
