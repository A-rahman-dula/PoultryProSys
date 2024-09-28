<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once 'Frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Order.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

// Retrieve the id from the session
$user_id = $_SESSION["user_id"];

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);
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
        <div class="row my-5 justify-content-center">
            <div class="col-lg-12 col-md-12 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title p-2 text-white mb-0"><strong style="font-size:25px;">Order Details</strong></h5>
                        <div>
                            <a href="add_order.php" class="btn btn-outline-light"><i class="bi bi-house-add-fill"></i> Add New Order</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th scope="col">#</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Product</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Unit Price</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $database = new Database();
                                    $db = $database->getConnection();

                                    $order = new Order($db);
                                    $orders = $order->read($farm['user_id']);

                                    if (!$orders) {
                                        $orders = [];
                                    }

                                    $uid = 1;

                                    foreach ($orders as $row) {
                                        $result = $order->getCustomer($row['cus_id']);
                                        ?>
                                        <tr>
                                            <td><?php echo $uid; ?></td>
                                            <td><?php echo htmlspecialchars($result['username']); ?></td>
                                            <td><?php echo htmlspecialchars($result['address']); ?>.",".<?php echo htmlspecialchars($result['city']); ?></td>
                                            <td style="text-align:center;"><?php echo htmlspecialchars($order->getProduct($row['product_id'])); ?></td>
                                            <td style="text-align:right;"><?php echo number_format((float) $row['quantity'], 2, '.', ''); ?></td>
                                            <td style="text-align:right;"><?php echo number_format((float) $row['unit_price'], 2, '.', ''); ?></td>
                                            <td style="text-align:right;"><?php echo number_format((float) $row['total'], 2, '.', ''); ?></td>
                                            <td style="text-align:center;"><?php echo htmlspecialchars($row['ordered_date']); ?></td>
                                            <td style="text-align:center;">
                                                <?php if ($row['status'] == 0) { ?>
                                                    <a class="btn btn-success py-1 px-2" href="order_confirm.php?confirm=<?php echo urlencode($row['order_id']); ?>" class="text-light">Confirm</a>
                                                <?php } else { ?>
                                                    <a class="btn btn-danger py-1 px-2" href="order_cancel.php?cancel=<?php echo urlencode($row['order_id']); ?>" class="text-light">Cancel</a>
                                                <?php } ?>
                                                <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $row['order_id']; ?>)">Delete</button>
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
</main>



<?php
$frame->last_part();
?>

<script>
    function myFunction(order_id) {
        if (confirm("Are you sure you want to delete this order?")) {
            window.location.href = "delete_order.php?order_id=" + order_id;
        }
    }

</script>