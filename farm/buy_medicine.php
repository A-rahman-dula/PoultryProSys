<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Medicine.php';
require_once '../classes/BuyMedicine.php';
require_once '../classes/UseMedicine.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION["user_id"];
$med_id = $_GET["med_id"];

$database = new Database();
$con = $database->getConnection();

$medicine = new Medicine($con);
$buyMedicine = new BuyMedicine($con);
$useMedicine = new UseMedicine($con);

$med_name = $medicine->getMedName($med_id);
$suppliers = $medicine->getAllSuppliers($user_id);
$usage = $useMedicine->getUsage($med_id);

// Calculate stock using the Feed class method
$stockDetails = $medicine->calculateStock($med_id, $usage);
$available_stock = $stockDetails['available_stock'];
$stock_value = $stockDetails['stock_value'];

$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buy_med'])) {
    $sup_id = $_POST['sup_id'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $total = $_POST['total'];
    // Set values and call create method
    $buyMedicine->setUser_id($user_id);
    $buyMedicine->setMed_id($med_id);
    $buyMedicine->setSup_id($sup_id);
    $buyMedicine->setQuantity($quantity);
    $buyMedicine->setUnit_price($unit_price);
    $buyMedicine->setTotal($total);

    if ($buyMedicine->create($user_id)) {
        $success_message = "Medicine added to stock successfully.";
    } else {
        $error_message = "Failed to add Medicine to stock.";
    }

    // Recalculate stock after purchase
    $stockDetails = $medicine->calculateStock($med_id, $usage);
    $available_stock = $stockDetails['available_stock'];
    $stock_value = $stockDetails['stock_value'];
}
?>

<!-- The rest of your HTML code for the form and stock display remains unchanged -->

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

                        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?med_id=" . $med_id; ?>" method="POST">
                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Med Name:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="med_name" name="med_name" value="<?php echo htmlspecialchars($med_name); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Supplier:</label>
                                        <div class="col-sm-9">
                                            <select name="sup_id" id="sup_id" class="form-control" required>
                                                <option value="" selected>Select Supplier</option>
                                                <?php foreach ($suppliers as $supplier): ?>
                                                    <option value="<?php echo htmlspecialchars($supplier['sup_id']); ?>">
                                                        <?php echo htmlspecialchars($supplier['sup_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
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
                                            <input type="text" class="form-control" name="quantity" id="quantity" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Unit Price:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="unit_price" id="unitPrice" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Total:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="totalCost" name="total" required readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row px-3" style="text-align:center;">
                                <button type="submit" class="btn btn-primary" name="buy_med">Add</button>
                            </div>
                            <div class="row px-3 mt-2" style="text-align:center;">
                                <a href="medicine.php" class="btn btn-danger">Back</a>
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
                                        <input type="text" class="form-control" id="med_stck" value="<?php echo htmlspecialchars(number_format($available_stock, 2)); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-2">
                            <div class="col">
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-white">Value:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="medstock_val" name="medstock_val" value="<?php echo htmlspecialchars(number_format($stock_value, 2)); ?>" readonly>
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
    // Get references to input fields
    const unitPriceInput = document.getElementById('unitPrice');
    const quantityInput = document.getElementById('quantity');
    const totalCostInput = document.getElementById('totalCost');
    const supIdSelect = document.getElementById('sup_id');
    const supNameInput = document.getElementById('sup_name');

    // Calculate total cost when unit price or quantity changes
    unitPriceInput.addEventListener('input', calculateTotalCost);
    quantityInput.addEventListener('input', calculateTotalCost);

    function calculateTotalCost() {
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const quantity = parseFloat(quantityInput.value) || 0;
        const totalCost = unitPrice * quantity;
        totalCostInput.value = totalCost.toFixed(2);
    }

</script>

<?php
$frame->last_part();
?>
