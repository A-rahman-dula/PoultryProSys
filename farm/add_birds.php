<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once '../classes/Bird.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$database = new Database();
$db = $database->getConnection();

$bird = new Bird($db);

$next_batch = $bird->getNextBatch($user_id);


$query = "SELECT sup_id,sup_name FROM supplier WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = htmlspecialchars(strip_tags($_POST['user_id']));
    $batch = htmlspecialchars(strip_tags($_POST['batch']));
    $sup_id = htmlspecialchars(strip_tags($_POST['sup_id']));
    $age = htmlspecialchars(strip_tags($_POST['age'])); // Correctly retrieve the age
    $bird_type = htmlspecialchars(strip_tags($_POST['bird_type']));
    $unit_price = htmlspecialchars(strip_tags($_POST['unit_price']));
    $quantity = htmlspecialchars(strip_tags($_POST['quantity']));
    $total_cost = htmlspecialchars(strip_tags($_POST['total_cost']));
    $date = htmlspecialchars(strip_tags($_POST['date']));

    $bird->setUserID($user_id);
    $bird->setBatch($batch);
    $bird->setSupId($sup_id);
    $bird->setAge($age);
    $bird->setBirdType($bird_type);
    $bird->setUnitPrice($unit_price);
    $bird->setQuantity($quantity);
    $bird->setTotalCost($total_cost);
    $bird->setDate($date);

    if ($bird->create($user_id)) {
        header("Location: birds.php?message=New batch added successfully.");
        exit();
    } else {
        $error_message = "Something went wrong. Please try again.";
        echo $error_message;
    }
}

$frame = new Frame();
$frame->first_part($farm);
?>
<style>
    .card-header {
        background-color: #3E497A;
        color: white;
    }

    .card {
        border-color: #3E497A;
    }

    .card-body {
        background-color: #EFFFFB;
        /*        background-image: url('../images/logo-poultryPro.png');
                background-repeat: no-repeat;
                
                background-size: 100% 90%;*/


    }

    .form-control {
        border-color: #3E497A;
    }

    .form-control:focus {
        border-color: #3E497A;
        box-shadow: 0 0 5px rgba(62, 73, 122, 0.5);
    }

    .btn-primary {
        background-color: #3E497A;
        border-color: #3E497A;
    }

    .btn-primary:hover {
        background-color: #2E3A5A;
        border-color: #2E3A5A;
    }

    .btn-danger {
        background-color: #D9534F;
        border-color: #D9534F;
    }

    .btn-danger:hover {
        background-color: #C9302C;
        border-color: #C9302C;
    }

    .col-form-label {
        color: #3E497A;
        font-weight: bold;
    }

    .form-check-input:checked {
        background-color: #3E497A;
        border-color: #3E497A;
    }

    .form-check-label {
        color: #3E497A;
    }

    #totalCost {
        background-color: #E9ECEF;
        border-color: #3E497A;
    }
</style>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 justify-content-center">
            <div class="col-lg-8 col-md-12 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title p-2 text-white mb-0"><strong style="font-size:25px;">Add New Batch</strong></h5>

                    </div>
                    <div class="card-body">
                        <form id="batchForm" action="add_birds.php" method="post">
                            <div class="row">
                                <div class="col">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Batch:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="batch" value="<?php echo $next_batch; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Date:</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="date" required>
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
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Age:</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" name="age" id="age" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2 mb-3">
                                <div class="form-group">
                                    <label class=" col-form-label">Type:</label>
                                    <div class="form-check form-check-inline mx-5" >
                                        <input class="form-check-input" type="radio" name="bird_type" value="CHICK" required>
                                        <label class="form-check-label">Chick</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="bird_type" value="HEN" required>
                                        <label class="form-check-label">Hen</label>
                                    </div>
                                </div>

                            </div>


                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Unit Price:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="unit_price" id="unitPrice" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-form-label">Number of Birds:</label>
                                        <div class="col-sm-7">
                                            <input type="number" class="form-control" name="quantity" id="quantity" required>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row mb-3 p-2 justify-content-center">
                                <label class="col-sm-2 col-form-label text-center">Total:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control text-center" name="total_cost" id="totalCost" readonly required>
                                </div>
                            </div>






                            <div class="row" style="text-align:center;">
                                <input type="submit" class="btn btn-primary" name="submit" value="Add">
                            </div>

                            <div class="row mt-2" style="text-align:center;">
                                <a href="birds.php" class="btn btn-danger">Back</a>
                            </div>
                        </form>
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
        // Get values of unit price and quantity
        const unitPrice = parseFloat(unitPriceInput.value);
        const quantity = parseFloat(quantityInput.value);

        // Calculate total cost
        const totalCost = unitPrice * quantity;

        // Update total cost input field
        totalCostInput.value = totalCost.toFixed(2); // Assuming you want to display the total with 2 decimal places
    }
</script>


<?php
$frame->last_part();
?>