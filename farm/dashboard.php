<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';
require_once '../classes/Expenses.php';
require_once '../classes/Incomes.php';
require_once '../classes/Stocks.php';
require_once '../classes/Product.php';
require_once '../classes/Order.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];


$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);

$database = new Database();
$db = $database->getConnection();

$from_date = date('Y-m-d');
$to_date = date('Y-m-d', strtotime('+1 day'));

$expenses = new Expenses($db, $farm['user_id'], $from_date, $to_date);
$total_expenses = $expenses->getTotalAmount();
$incomes = new Incomes($db, $farm['user_id'], $from_date, $to_date);
$total_incomes = $incomes->getTotalAmount();
$stocks = new Stocks($db, $farm['user_id']);

$total_stocks = $stocks->getTotalStockValue($from_date, $to_date);
$total_profit = $total_incomes + $total_stocks - $total_expenses;

$order = new Order($db);
$today_orders = $order->todayOrders(date('Y-m-d'), $farm['user_id']);

$product = new Product($db);
$products = $product->read($user_id);
?>
<style>
    .card-title {
        font-weight: bold; 
        font-size: 18px;
    }
    .card-text{
        font-size: 20px; 
        font-weight: bold;
    }    
</style>
<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-4 mx-4 text-center">

            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white">TODAY ORDERS</h5>
                        <h6 class="card-text text-white" > <?php echo number_format($today_orders); ?></h6>
                    </div>
                    <div class="card-footer" style="background-color: #D4C8DE;">
                        <a href="#" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #989E12;">
                        <h5 class="card-title text-white">TODAY INCOME</h5>
                        <h6 class="card-text text-white" >RS. <?php echo number_format($total_incomes, 2); ?></h6>
                    </div>
                    <div class="card-footer" style="background-color: #F1F4B0;">
                        <a href="incomes.php" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #B71717;">
                        <h5 class="card-title text-white">TODAY EXPENSES</h5>
                        <h6 class="card-text text-white" >RS. <?php echo number_format($total_expenses, 2); ?></h6>
                    </div>
                    <div class="card-footer" style="background-color: #F0A9A9;">
                        <a href="expenses.php" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #1E8449;">
                        <h5 class="card-title text-white">TODAY PROFIT</h5>
                        <h6 class="card-text text-white">RS. <?php echo number_format($total_profit, 2); ?></h6>
                    </div>
                    <div class="card-footer" style="background-color: #D4EAE2;">
                        <a href="profit.php" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="row my-2 justify-content-center">
            <div class="col-lg-8 col-md-12 col-12">
                <div class="card">
                    <div class="card-body text-center p-3" style="background-color: #6c757d;">
                        <h5 class="card-title text-white mb-0"><strong style="font-size:25px;">Today's Price List</strong></h5>
                    </div>
                    <div class="card-footer p-0">
                        <div class="table-responsive" >
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="thead-dark text-center">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Today's Price</th>
                                        <th scope="col">Unit</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialnum = 0;
                                    foreach ($products as $product) {
                                        $serialnum++;
                                        ?>
                                        <tr>
                                            <th class="text-center"><?php echo $serialnum; ?></th>
                                            <td><?php echo $product['product_name']; ?></td>
                                            <td style="text-align:right">
                                                <form action="update_product_price.php" method="POST" style="display: inline;">
                                                    <span><?php echo "Rs. " . number_format($product['product_price'], 2); ?></span>
                                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                    <input type="text" name="new_price" class="form-control d-none" value="<?php echo $product['product_price']; ?>">
                                                </form>
                                            </td>
                                            <td class="text-center"><?php echo "1 " . $product['unit']; ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger text-light py-1 px-2" onclick="makeEditable(this)">Change Price</button>
                                            </td>
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
</main>


<?php
$frame->last_part();
?>
<script>
    function makeEditable(button) {
        const td = button.closest('tr').querySelector('td:nth-child(3)');
        const form = td.querySelector('form');
        const span = form.querySelector('span');
        const input = form.querySelector('input[name="new_price"]');

        // Toggle visibility
        span.classList.toggle('d-none');
        input.classList.toggle('d-none');

        if (!input.classList.contains('d-none')) {
            input.focus();
        } else {
            // Submit the form when input field is hidden
            form.submit();
        }
    }
</script>



