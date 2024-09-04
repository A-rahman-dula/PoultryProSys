<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'admin_frame.php';
require_once 'Feedback.php'; // Include the Feedback class file

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$database = new Database();
$db = $database->getConnection();

// Create Feedback object
$feedback = new Feedback($db);

// Call the readFeedback method to get feedbacks
$feedbacks = $feedback->readFeedback();

// Check admin access using CheckLogin class
$user_id = $_SESSION['user_id'];
$admin = CheckLogin::checkLoginAndRole($user_id, 'admin');

// Create AdminFrame object
$adminframe = new AdminFrame();
$adminframe->first_part($admin);

?>

<div class="contentArea">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>REVIEWS</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">UserId</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Rate</th>
                                    <th scope="col">Comment</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $uid = 1;
                                foreach ($feedbacks as $feedback) {
                                ?>
                                    <tr>
                                        <td><?php echo $uid ?></td>
                                        <td><?php echo htmlspecialchars($feedback['user_id']) ?></td>
                                        <td><?php echo htmlspecialchars($feedback['username']) ?></td>
                                        <td><?php echo htmlspecialchars($feedback['rating']) ?></td>
                                        <td><?php echo htmlspecialchars($feedback['comment']) ?></td>
                                        <td><?php echo htmlspecialchars($feedback['created_at']) ?></td>
                                        <td>
                                            <button class="btn btn-danger">
                                                <a href="delete_feedback.php?delete=<?php echo htmlspecialchars($feedback['feedback_id']); ?>" class="text-light">Delete</a>
                                            </button>
                                        </td>
                                    </tr>
                                <?php
                                    $uid++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$adminframe->last_part();
?>