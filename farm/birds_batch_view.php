<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once 'frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Bird.php';

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

// Retrieve the user ID and batch ID from the session and query parameters
$user_id = $_SESSION["user_id"];
$batch_id = isset($_GET['batch_id']) ? $_GET['batch_id'] : '';

if (empty($batch_id)) {
    header("Location: birds_batch.php");
    exit();
}

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 mx-5 px-5">
            <div class="card p-0 " >
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                    <h5 class="card-title text-white mb-0"><strong>Batch Summary</strong></h5>
                    <a href="birds.php" class="btn btn-danger py-1 px-4">Back</a>
                </div>
                <div class="card-body" style="background-color: #EFFFFB;">
                    <?php
                    $database = new Database();
                    $db = $database->getConnection();

                    $bird = new Bird($db);
                    $batch = $bird->getBatchDetails($batch_id, $user_id);
                    $healthStatus = $bird->getHealthStatus($batch_id);
                    $productDetails = $bird->getProductDetails($batch_id);

                    if ($batch) {
                        $supplierName = Bird::getSupplier($batch['sup_id'], $db);

                        // Calculate batch age in days
                        $importDate = new DateTime($batch['date']);
                        $currentDate = new DateTime();
                        $interval = $currentDate->diff($importDate);
                        $batchAgeDays = $interval->days + (int) $batch['age'];
                        // Initialize totals
                        $totalQuantity = 0;
                        $totalValue = 0.0;
                        ?>
                        <table class="table table-striped table-hover">
                            <tbody>
                                <tr>
                                    <th scope="row">Batch</th>
                                    <td><?php echo htmlspecialchars($batch['batch']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Supplier</th>
                                    <td><?php echo htmlspecialchars($supplierName); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Type</th>
                                    <td><?php echo htmlspecialchars($batch['bird_type']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Unit Price</th>
                                    <td><?php echo htmlspecialchars($batch['unit_price']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Quantity</th>
                                    <td><?php echo htmlspecialchars($batch['quantity']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Total Cost</th>
                                    <td><?php echo number_format((float) $batch['total_cost'], 2, '.', ''); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Date</th>
                                    <td><?php echo htmlspecialchars($batch['date']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Age</th>
                                    <td><?php echo htmlspecialchars($batchAgeDays); ?> Days</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4 class="mt-4">Health Status</h4>
                        <?php
                        $totalIllness = 0;
                        $totalDeaths = 0;
                        ?>
                        <?php if ($healthStatus): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Illness</th>
                                        <th>Deaths</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($healthStatus as $status): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($status['created_at']); ?></td>
                                            <td><?php
                                                echo htmlspecialchars($status['no_illness']);
                                                $totalIllness += $status['no_illness'];
                                                ?></td>
                                            <td><?php
                                                echo htmlspecialchars($status['no_deaths']);
                                                $totalDeaths += $status['no_deaths'];
                                                ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><strong>Total:</strong></td>
                                        <td><strong><?php echo $totalIllness; ?></strong></td>
                                        <td><strong><?php echo $totalDeaths; ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php else: ?>
                            <p>No health status records available.</p>
                        <?php endif; ?>

                        <h4 class="mt-4">Product Details</h4>
                        <?php if ($productDetails): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Date</th>
                                        <th>Product ID</th>
                                        <th>Quantity</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productDetails as $product): ?>
                                        <tr class="text-center">
                                            <td><?php echo htmlspecialchars($product['created_at']); ?></td>
                                            <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                                            <td><?php echo number_format((float) $product['quantity'], 2, '.', ''); ?> Kg</td>
                                            <td>Rs. <?php echo number_format((float) $product['total'], 2, '.', ''); ?></td>
                                            <?php
                                            $totalQuantity += $product['quantity'];
                                            $totalValue += (float) $product['total'];
                                            ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="text-align: center;"><strong>Total:</strong></td>
                                        <td class="text-center"><strong><?php echo number_format($totalQuantity, 2, '.', ''); ?> Kg</strong></td>
                                        <td class="text-center"><strong>Rs. <?php echo number_format($totalValue, 2, '.', ''); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php else: ?>
                            <p>No product details available.</p>
                        <?php endif; ?>
                    <?php
                    } else {
                        echo '<p class="text-danger">Batch not found.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$frame->last_part();
?>
