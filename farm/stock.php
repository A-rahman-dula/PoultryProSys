<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once 'Frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Stocks.php';
require_once '../classes/Feed.php';
require_once '../classes/UseFeed.php';
require_once '../classes/BuyFeed.php';
require_once '../classes/Medicine.php';
require_once '../classes/UseMedicine.php';
require_once '../classes/BuyMedicine.php';
require_once '../classes/Product.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);

$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

if (empty($from_date) && empty($to_date)) {
    $to_date = date('Y-m-d');
} elseif (!empty($from_date) && empty($to_date)) {
    $to_date = date('Y-m-d');
}

$database = new Database();
$db = $database->getConnection();

$stocks = new Stocks($db, $farm['user_id']);

// Fetch data from Stocks class
$feed_data = $stocks->getAllStockData($from_date, $to_date);
$total_feed_amount = $stocks->getTotalStockAmount($from_date, $to_date);

$medicine_data = $stocks->getAllMedicineStockData($from_date, $to_date);
$total_medicine_amount = $stocks->getTotalMedicineStockAmount($from_date, $to_date);

$product_data = $stocks->getAllProductStockData($from_date, $to_date);
$total_product_amount = $stocks->getTotalProductStockAmount($from_date, $to_date);

?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row py-5 px-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>STOCK DETAILS</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-6">
                                    <label for="from_date">From Date:</label>
                                    <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" class="form-control">
                                </div>
                                <div class="col-lg-4 col-md-6 col-6">
                                    <label for="to_date">To Date:</label>
                                    <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" class="form-control">
                                </div>
                                <div class="col-lg-4 col-md-6 col-12 pt-4 text-center">
                                    <div class="row px-5">
                                        <div class="col-lg-6 col-md-6 col-6">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-6">
                                            <button class="btn btn-danger">
                                                <a href="stockPDF.php?from_date=<?php echo htmlspecialchars($from_date); ?>&to_date=<?php echo htmlspecialchars($to_date); ?>" class="text-light" style="text-decoration: none;">Export PDF</a>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Feed Stock Table -->
                        <div class="row my-5">
                            <div class="col-12">
                                <h4 class="mb-4">Feed Stock</h4>
                                <table class="table">
                                    <thead>
                                        <tr style="text-align:center;">
                                            <th scope="col">#</th>
                                            <th scope="col">Stock Detail</th>
                                            <th scope="col" class="text-center">Quantity</th>
                                            <th scope="col" class="text-center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($feed_data)) {
                                            $uid = 1;
                                            foreach ($feed_data as $data) {
                                                ?>
                                                <tr>
                                                    <td style="text-align:center;"><?php echo $uid; ?></td>
                                                    <td style="text-align:left;"><?php echo htmlspecialchars($data['detail']); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($data['quantity'], 2); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($data['amount'], 2); ?></td>
                                                </tr>
                                                <?php
                                                $uid++;
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="3" class="text-center"><strong>Total Value</strong></td>
                                                <td class="text-right pr-4"><strong><?php echo number_format($total_feed_amount, 2); ?></strong></td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No feed data found</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Medicine Stock Table -->
                        <div class="row my-5">
                            <div class="col-12">
                                <h4 class="mb-4">Medicine Stock</h4>
                                <table class="table">
                                    <thead>
                                        <tr style="text-align:center;">
                                            <th scope="col">#</th>
                                            <th scope="col">Stock Detail</th>
                                            <th scope="col" class="text-center">Quantity</th>
                                            <th scope="col" class="text-center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($medicine_data)) {
                                            $uid = 1;
                                            foreach ($medicine_data as $data) {
                                                ?>
                                                <tr>
                                                    <td style="text-align:center;"><?php echo $uid; ?></td>
                                                    <td style="text-align:left;"><?php echo htmlspecialchars($data['detail']); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($data['quantity'], 2); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($data['amount'], 2); ?></td>
                                                </tr>
                                                <?php
                                                $uid++;
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="3" class="text-center"><strong>Total Value</strong></td>
                                                <td class="text-right pr-4"><strong><?php echo number_format($total_medicine_amount, 2); ?></strong></td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No medicine data found</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Product Stock Table -->
                        <div class="row my-5">
                            <div class="col-12">
                                <h4 class="mb-4">Product Stock</h4>
                                <table class="table">
                                    <thead>
                                        <tr style="text-align:center;">
                                            <th scope="col">#</th>
                                            <th scope="col">Stock Detail</th>
                                            <th scope="col" class="text-center">Quantity</th>
                                            <th scope="col" class="text-center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($product_data)) {
                                            $uid = 1;
                                            foreach ($product_data as $data) {
                                                ?>
                                                <tr>
                                                    <td style="text-align:center;"><?php echo $uid; ?></td>
                                                    <td style="text-align:left;"><?php echo htmlspecialchars($data['detail']); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($data['quantity'], 2); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($data['amount'], 2); ?></td>
                                                </tr>
                                                <?php
                                                $uid++;
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="3" class="text-center"><strong>Total Value</strong></td>
                                                <td class="text-right pr-4"><strong><?php echo number_format($total_product_amount, 2); ?></strong></td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No product data found</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$frame->last_part();
?>
