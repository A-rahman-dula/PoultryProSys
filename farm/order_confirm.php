<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: login.php");
    exit();
}

// Check if a user ID is provided
if (!isset($_GET['confirm'])) {
    echo "<script>alert('Order Not Found');</script>";
    header("Location: orders.php");
    exit();
}

$confirmed_order_id = $_GET['confirm'];


// Database connection
$database = new Database();
$db = $database->getConnection();

// Update the status
$query = "UPDATE orders SET status = 1 WHERE order_id = :order_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':order_id', $confirmed_order_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Order is confirmed.";
} else {
    $_SESSION['error_message'] = "Order Confirmation Failed.";
}
header("Location: orders.php");
exit();
?>
