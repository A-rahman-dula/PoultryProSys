<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Supplier.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

// Instantiate the Supplier class
$supplier = new Supplier($con);

// Get supplier ID from GET request
$sup_id = isset($_GET['sup_id']) ? $_GET['sup_id'] : '';

if ($sup_id) {
    // Get supplier details
    $supplier_details = $supplier->readOne($sup_id);
    if ($supplier_details) {
        $supplier_name = $supplier_details['sup_name'];
        $supplier_address = $supplier_details['address'];
        $supplier_contact = $supplier_details['mobile'];  // Changed from 'mobile' to 'contact'
        $supplier_email = $supplier_details['email'];  // Added if email is needed for form
    } else {
        echo "No supplier found with the provided ID.";
        exit();
    }
} else {
    echo "Supplier ID is required.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set supplier properties
    $supplier->setSup_name($_POST['supplier_name']);
    $supplier->setAddress($_POST['address']);
    $supplier->setMobile($_POST['mobile']);  // Changed from 'setMobile' to 'setContact'
    $supplier->setEmail($_POST['email']);  // Added if email is needed for update

    // Update supplier details
    if ($supplier->update($sup_id)) {
        header("Location: supplier.php?msg=Supplier Updated Successfully");
        ob_end_flush();
        exit();
    } else {
        echo "<p>Failed to update supplier.</p>";
    }
}
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 text-center justify-content-center">
            <div class="col-lg-6 col-md-10 col-12 mb-3 my-5 px-5">
                <div class="card">
                    <div class="card-header p-3 text-center" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Edit Supplier</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #D4C8DE;">
                        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sup_id=" . $sup_id; ?>" method="POST">
                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Supplier Name:</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="text" name="supplier_name" id="supplier_name" value="<?php echo htmlspecialchars($supplier_name); ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Address:</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($supplier_address); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Contact:</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="text" name="mobile" id="contact" value="<?php echo htmlspecialchars($supplier_contact); ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Email:</label>  <!-- Added if email is needed -->
                                        <div class="col-sm-8">
                                            <input class="form-control" type="email" name="email" id="email" value="<?php echo htmlspecialchars($supplier_email); ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row px-3" style="text-align:center;">
                                <button type="submit" class="btn btn-primary" name="Update">Update</button>
                            </div>
                            <div class="row px-3 mt-2" style="text-align:center;">
                                <a href="supplier.php" class="btn btn-danger">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$frame->last_part();
?>
