<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once 'Frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Bird.php';

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
            <div class="col-lg-10 col-md-12 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title p-2 text-white mb-0"><strong style="font-size:25px;">Batch Details</strong></h5>
                        <div>
                            <a href="add_birds.php" class="btn btn-outline-light"><i class="bi bi-house-add-fill"></i> Add New Batch</a>
                            <a href="birds_health.php" class="btn btn-outline-light"><i class="bi bi-calendar-heart-fill"></i> Add Health Problems</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th scope="col">#</th>
                                        <th scope="col">Batch</th>
                                        <th scope="col">Supplier</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Unit Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $database = new Database();
                                    $db = $database->getConnection();

                                    $bird = new Bird($db);
                                    $birds = $bird->read($farm['user_id']);

                                    if (!$birds) {
                                        $birds = [];
                                    }

                                    $uid = 1;

                                    foreach ($birds as $row) {
                                        $supplierName = Bird::getSupplier($row['sup_id'], $db);
                                        ?>
                                        <tr>
                                            <td><?php echo $uid; ?></td>
                                            <td><?php echo htmlspecialchars($row['batch']); ?></td>
                                            <td><?php echo htmlspecialchars($supplierName); ?></td>
                                            <td style="text-align:center;"><?php echo htmlspecialchars($row['bird_type']); ?></td>
                                            <td style="text-align:right;"><?php echo number_format((float) $row['unit_price'], 2, '.', ''); ?></td>
                                            <td style="text-align:right;"><?php echo htmlspecialchars($row['quantity']); ?></td>
                                            <td style="text-align:right;"><?php echo number_format((float) $row['total_cost'], 2, '.', ''); ?></td>
                                            <td style="text-align:center;"><?php echo htmlspecialchars($row['date']); ?></td>
                                            <td style="text-align:center;">
                                                <a href="birds_batch_view.php?batch_id=<?php echo htmlspecialchars($row['batch_id']); ?>" class="btn btn-warning text-light py-1 px-2 ">View</a>
                                                <a href="update_birds.php?edit=<?php echo htmlspecialchars($row['batch_id']); ?>" class="btn btn-success text-light py-1 px-2 ">Edit</a>
                                                <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $row['batch_id']; ?>)">Delete</button>
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
    function myFunction(batch_id) {
        if (confirm("Are you sure you want to delete this batch?")) {
            window.location.href = "delete_birds.php?batch_id=" + batch_id;
        }
    }

</script>