<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Feed.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?msg=Please Login before Proceeding');
    exit();
}

$user_id = $_SESSION['user_id'];

$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

if (isset($_GET['feed_id'])) {
    $feed_id = $_GET['feed_id'];
    
    // Create an instance of the Feed class
    $feed = new Feed($con);
    
    // Delete the feed using the delete method from the Feed class
    if ($feed->delete($feed_id)) {
        header('Location: feed.php?msg=Feed Deleted Successfully');
    } else {
        header('Location: feed.php?msg=Failed to Delete Feed');
    }
    exit();
}
