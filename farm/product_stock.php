<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Product.php';
require_once '../classes/ProductStock.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION["user_id"];
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$product_id = $_GET["product_id"];

$database = new Database();
$con = $database->getConnection();

$product = new Product($con);
$productStock = new ProductStock($con);

// Initialize variables for form values
$product_name = '';
$unit_price = 0;
$quantity = '';
$total = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $batch_id = $_POST['batch_id'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $total = $_POST['total'];

    $productStock->setUser_id($user_id);
    $productStock->setProduct_id($product_id);
    $productStock->setBatch_id($batch_id);
    $productStock->setQuantity($quantity);
    $productStock->setUnit_price($unit_price);
    $productStock->setTotal($total);

    if ($productStock->create($user_id)) {
        $success_message = "Added to stock successfully.";
        // Redirect to the same page to avoid resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF'] . "?product_id=" . $product_id);
        exit();
    } else {
        $error_message = "Failed to add to stock.";
    }
}

$product_name = $product->getProductName($product_id);
$unit_price = $product->getProductPrice($product_id);
$batches = $product->getAllBatches($user_id);

$product_stock = $product->getProductStock($product_id);
$confirmed_order = $product->getConfirmedOrder($product_id);

$stock = $product_stock - $confirmed_order;
$stock_value = $stock * $unit_price;

$frame = new Frame();
$frame->first_part($farm);
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5">
            <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <div class="card">
                    <div class="card-header p-3 text-center" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Add To Stock</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #D4C8DE;">
                        <?php if (isset($success_message)) : ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($success_message); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?product_id=" . $product_id; ?>" method="POST">
                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Product Name:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Batch:</label>
                                        <div class="col-sm-9">
                                            <select name="batch_id" id="batch_id" class="form-control" required>
                                                <option value="" selected disabled>Select Batch</option>
                                                <?php foreach ($batches as $batch): ?>
                                                    <option value="<?php echo htmlspecialchars($batch['batch_id']); ?>">
                                                        <?php echo htmlspecialchars($batch['batch']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                                <option value="">No batch</option>
                                            </select>    
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Quantity:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="quantity" id="quantity" value="<?php echo htmlspecialchars($quantity); ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Unit Price:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="unit_price" id="unitPrice" value="<?php echo number_format(htmlspecialchars($unit_price), 2); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Total:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="totalCost" name="total" value="<?php echo htmlspecialchars($total); ?>" required readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row px-3" style="text-align:center;">
                                <button type="submit" class="btn btn-primary" name="add_product">Add</button>
                            </div>
                            <div class="row px-3 mt-2" style="text-align:center;">
                                <a href="products.php" class="btn btn-danger">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-10 col-12 mb-3 px-5 py-5 my-5">
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
                                        <input type="text" class="form-control" id="feed_stck" value="<?php echo htmlspecialchars(number_format($stock, 2)); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-2">
                            <div class="col">
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-white">Value:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="feedstock_val" name="feedstock_val" value="<?php echo htmlspecialchars(number_format($stock_value, 2)); ?>" readonly>
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

<script>
    // Pass the unit price from PHP to JavaScript
    const unitPrice = parseFloat('<?php echo $unit_price; ?>') || 0;

    // Get references to input fields
    const quantityInput = document.getElementById('quantity');
    const totalCostInput = document.getElementById('totalCost');

    // Add an event listener to update the total cost whenever quantity changes
    quantityInput.addEventListener('input', function() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const totalCost = quantity * unitPrice;
        totalCostInput.value = totalCost.toFixed(2);
    });
</script>
<?php
$frame->last_part();
?>
