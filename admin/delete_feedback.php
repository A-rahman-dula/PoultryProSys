<?php
session_start();

include 'config.php';
include 'Feedback.php'; 

if (isset($_GET['delete'])) {
    $feedback_id = $_GET['delete'];

    $database = new Database();
    $db = $database->getConnection();

    $feedback = new Feedback($db);

    // Call the deleteFeedback method to delete the feedback
    if ($feedback->deleteFeedback($feedback_id)) {
        header("Location: admin_reviews.php");
        exit();
    } else {
        echo "Failed to delete feedback.";
    }
} else {
    echo "Invalid request.";
}
?>
