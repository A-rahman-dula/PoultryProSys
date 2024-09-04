<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/Bird.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$batch_id = $_GET['edit'];

$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$database = new Database();
$db = $database->getConnection();

$bird = new Bird($db);

// Retrieve suppliers from the database and populate the select options
$query = "SELECT sup_id, sup_name FROM supplier";
$stmt = $db->prepare($query);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $batch = htmlspecialchars(strip_tags($_POST['batch']));
    $sup_id = htmlspecialchars(strip_tags($_POST['sup_id']));
    $bird_type = htmlspecialchars(strip_tags($_POST['bird_type']));
    $age = htmlspecialchars(strip_tags($_POST['age'])); // Add this line
    $unit_price = htmlspecialchars(strip_tags($_POST['unit_price']));
    $quantity = htmlspecialchars(strip_tags($_POST['quantity']));
    $total_cost = htmlspecialchars(strip_tags($_POST['total_cost']));
    $date = htmlspecialchars(strip_tags($_POST['date']));

    $bird->setBatch($batch);
    $bird->setSupId($sup_id);
    $bird->setBirdType($bird_type);
    $bird->setAge($age); // Add this line
    $bird->setUnitPrice($unit_price);
    $bird->setQuantity($quantity);
    $bird->setTotalCost($total_cost);
    $bird->setDate($date);

    if ($bird->update($batch_id)) {
        header("Location: birds.php");
        exit();
    } else {
        $error_message = "Something went wrong. Please try again.";
        echo $error_message;
    }
} else {
    if (isset($_GET['edit'])) {
        $batch_id = $_GET['edit'];
        $bird_data = $bird->readOne($batch_id);
    } else {
        header("Location: birds.php");
        exit();
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
                        <form id="batchForm" action="update_birds.php?edit=<?php echo htmlspecialchars($batch_id); ?>" method="post">

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Batch:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="batch" value="<?php echo htmlspecialchars($bird_data['batch']); ?>" required readonly>       </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Date:</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($bird_data['date']); ?>" required>
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
                                                <option value="" disabled>Select Supplier</option>
                                                <?php foreach ($suppliers as $supplier): ?>
                                                    <option value="<?php echo htmlspecialchars($supplier['sup_id']); ?>" <?php echo $bird_data['sup_id'] == $supplier['sup_id'] ? 'selected' : ''; ?>>
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
                                            <input type="number" class="form-control" name="age" id="age" value="<?php echo htmlspecialchars($bird_data['age']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2 mb-3">
                                <div class="form-group">
                                    <label class=" col-form-label">Type:</label>
                                    <div class="form-check form-check-inline mx-5" >
                                        <input class="form-check-input" type="radio" name="bird_type" value="CHICK" <?php echo $bird_data['bird_type'] == 'CHICK' ? 'checked' : ''; ?> required>
                                        <label class="form-check-label">Chick</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="bird_type" value="HEN" <?php echo $bird_data['bird_type'] == 'HEN' ? 'checked' : ''; ?> required>
                                        <label class="form-check-label">Hen</label>
                                    </div>
                                </div>

                            </div>


                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Unit Price:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="unit_price" id="unitPrice" value="<?php echo htmlspecialchars($bird_data['unit_price']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-form-label">Number of Birds:</label>
                                        <div class="col-sm-7">
                                            <input type="number" class="form-control" name="quantity" id="quantity" value="<?php echo htmlspecialchars($bird_data['quantity']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row mb-3 p-2 justify-content-center">
                                <label class="col-sm-2 col-form-label text-center">Total:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="total_cost" id="totalCost" value="<?php echo number_format((float) $bird_data['total_cost'], 2, '.', ''); ?>" readonly required>
                                </div>
                            </div>






                            <div class="row" style="text-align:center;">
                                <input type="submit" class="btn btn-primary" name="submit" value="Update">
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
    const unitPriceInput = document.getElementById('unitPrice');
    const quantityInput = document.getElementById('quantity');
    const totalCostInput = document.getElementById('totalCost');

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