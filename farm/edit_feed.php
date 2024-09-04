<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Feed.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

// Instantiate the Feed class
$feed = new Feed($con);

// Assuming you get the feed_id from a GET request
$feed_id = isset($_GET['feed_id']) ? $_GET['feed_id'] : '';

if ($feed_id) {
    // Get feed details
    $feed_details = $feed->readOne($feed_id);
    if ($feed_details) {
        $feed_name = $feed_details['feed_name'];
        $description = $feed_details['description'];
    } else {
        echo "No feed found with the provided ID.";
        exit();
    }
} else {
    echo "Feed ID is required.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set feed properties
    $feed->setFeed_name($_POST['feed_name']);
    $feed->setDescription($_POST['description']);

    // Update feed details
    if ($feed->update($feed_id)) {
        header("Location: feed.php?msg=Feed Updated Successfully");
        ob_end_flush();
        exit();
    } else {
        echo "<p>Failed to update feed.</p>";
    }
}
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 text-center justify-content-center">
            <div class="col-lg-6 col-md-10 col-12 mb-3 my-5 px-5">
                <div class="card">
                    <div class="card-header p-3 text-center" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Edit Feed</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #D4C8DE;">
                        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?feed_id=" . $feed_id; ?>" method="POST">
                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Feed Name:</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="text" name="feed_name" id="feed_name" value="<?php echo htmlspecialchars($feed_name); ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Description:</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($description); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row px-3" style="text-align:center;">
                                <button type="submit" class="btn btn-primary" name="Update">Update</button>
                            </div>
                            <div class="row px-3 mt-2" style="text-align:center;">
                                <a href="feed.php" class="btn btn-danger">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$frame->last_part();
?>
