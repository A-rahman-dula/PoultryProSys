<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/UseFeed.php';
require_once '../classes/BuyFeed.php';
require_once '../classes/Feed.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$database = new Database();
$con = $database->getConnection();

$feed_id = $_GET["feed_id"];
$user_id = $_SESSION["user_id"];
$feed = new Feed($con);
$feed_name = $feed->getFeedName($feed_id);
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$useFeed = new UseFeed($con);

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deduct_feed'])) {
    $quantity = $_POST['quantity'];

    // Set values and call create method
    $useFeed->setUser_id($user_id);
    $useFeed->setFeed_id($feed_id);
    $useFeed->setQuantity($quantity);

    if ($useFeed->create($user_id)) {
        $success_message = "Feed withdrawn successfully.";
    } else {
        $error_message = "Failed to withdraw feed.";
    }

    // Recalculate stock after updating
    $stock_data = $feed->calculateStock($feed_id, $useFeed->getUsage($feed_id));
    $available_stock = $stock_data['available_stock'];
    $stock_value = $stock_data['stock_value'];
} else {
    // Initial stock calculation if form was not submitted
    $stock_data = $feed->calculateStock($feed_id, $useFeed->getUsage($feed_id));
    $available_stock = $stock_data['available_stock'];
    $stock_value = $stock_data['stock_value'];
}

$frame = new Frame();
$frame->first_part($farm);
?>
<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5">

            <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <div class="card">
                    <div class="card-header p-3 text-center" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Deduct From Stock</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #D4C8DE;">

                        <?php if (isset($success_message)) : ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?feed_id=" . $feed_id; ?>" method="POST">

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Feed Name:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="feed_name" name="feed_name" value="<?php echo htmlspecialchars($feed_name); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Quantity:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="quantity" id="quantity" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row px-3" style="text-align:center;">
                                <button type="submit" class="btn btn-primary" name="deduct_feed">Deduct</button>
                            </div>
                            <div class="row px-3 mt-2" style="text-align:center;">
                                <a href="feed.php" class="btn btn-danger">Back</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-10 col-12 mb-3 px-5 py-3">
                <div class="card">
                    <div class="card-header p-2 text-center" style="background-color: #1E8449;">
                        <h5 class="card-title text-white"><span style="font-weight: bold;font-size: 24px;">Stock Available</span></h5>
                    </div>
                    <div class="card-body p-4" style="background-color: #1E8449;">
                        <div class="row p-2">
                            <div class="col">
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-white">Stock:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="feed_stock" value="<?php echo htmlspecialchars(number_format($available_stock, 2)); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-2">
                            <div class="col">
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-white">Value:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="feedstock_val" name="feed_name" value="<?php echo htmlspecialchars(number_format($stock_value, 2)); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center" style="background-color: #D4EAE2;">
                        <a href="stock.php" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php
$frame->last_part();
?>
