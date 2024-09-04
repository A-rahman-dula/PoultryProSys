<?php
require_once '../classes/config.php';
require_once '../classes/Product.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $new_price = $_POST['new_price'];

    // Initialize database connection and product class
    $database = new Database();
    $db = $database->getConnection();
    $product = new Product($db);

    // Update price
    if ($product->updatePrice($product_id, $new_price)) {
        // Redirect back to the page with the product list
        header("Location: dashboard.php");
    } else {
        echo "Failed to update price.";
    }
}
?>
