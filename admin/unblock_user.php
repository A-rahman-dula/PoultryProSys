<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if a user ID is provided
if (!isset($_GET['unblock']) || !isset($_GET['role'])) {
    echo "<script>alert('No user found');</script>";
    header("Location: admin_dashboard.php?user_id=" . urlencode($_SESSION['user_id']));
    exit();
}

$unblocked_user_id = $_GET['unblock'];
$role = $_GET['role'];

// Database connection
$database = new Database();
$db = $database->getConnection();

// Update the status of the user to unblocked
$query = "UPDATE user SET status = 1 WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $unblocked_user_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "User successfully unblocked.";
} else {
    $_SESSION['error_message'] = "An error occurred while unblocking the user.";
}

// Redirect back to the appropriate dashboard
if ($role === 'customer') {
    header("Location: admin_customers.php?user_id=" . urlencode($_SESSION['user_id']));
} else {
    header("Location: admin_farms.php?user_id=" . urlencode($_SESSION['user_id']));
}
exit();
?>
