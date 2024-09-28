<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';
require_once '../classes/Supplier.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

$supplier = new Supplier($con);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_supplier'])) {
        $sup_name = $_POST['sup_name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $mobile = $_POST['mobile'];  // Changed from 'mobile' to 'contact'
        $email = $_POST['email'];

        $supplier->setSup_name($sup_name);
        $supplier->setAddress($address);
        $supplier->setCity($city);
        $supplier->setMobile($mobile);  // Changed from 'setMobile' to 'setContact'
        $supplier->setEmail($email);

        if ($supplier->supplierExists($user_id)) {
            $error_message = "This supplier already exists";
        } else if ($supplier->supplierEmailExists($user_id)) {
            $error_message = "This supplier email already exists";
        } else {

            if ($supplier->create()) {
                $success_message = "Supplier added successfully.";
            } else {
                $error_message = "Failed to add supplier.";
            }
        }
    }
}

$query = "SELECT city FROM city";
$stmt = $con->prepare($query);
$stmt->execute();
$cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Read all suppliers
$suppliers = $supplier->readAll();
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 text-center">

            <div class="col-lg-5 col-md-10 col-12 mb-3 my-5 px-5">
                <div class="card">
                    <div class="card-header p-3 text-center" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">New Supplier</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #D4C8DE;">

                        <?php if (isset($success_message)) : ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Supplier Name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="sup_name" name="sup_name" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Address:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="address" name="address" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">City:</label>
                                        <div class="col-sm-8">

                                            <select class="form-control" name="city" id="city" required>
                                                <option value="" disabled selected>Select city</option>
                                                <?php foreach ($cities as $city) : ?>
                                                    <option value="<?php echo htmlspecialchars($city['city']); ?>">
                                                        <?php echo htmlspecialchars($city['city']); ?>
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
                                        <label class="col-sm-4 col-form-label">Contact:</label>  <!-- Changed from 'Mobile' to 'Contact' -->
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="contact" name="mobile" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Email:</label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row px-3" style="text-align:center;">
                                <button type="submit" class="btn btn-primary" name="add_supplier">Add Supplier</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-md-10 col-12 mb-3 my-2">
                <div class="row p-2">
                    <div class="col-sm-12">
                        <input type="text" id="searchSupplierInput" class="form-control" placeholder="Search supplier..." onkeyup="searchSupplier()">
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title p-2 text-white mb-0"><strong style="font-size:25px;">Supplier Details</strong></h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th scope="col">#</th>
                                        <th scope="col">Supplier Name</th>
                                        <th scope="col">Contact</th>  <!-- Changed from 'Mobile' to 'Contact' -->
                                        <th scope="col">Email</th>
                                        <th scope="col" style="width:32%">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialnum = 0;
                                    foreach ($suppliers as $supplier) {
                                        $serialnum++;
                                        ?>
                                        <tr>
                                            <th><?php echo $serialnum; ?></th>
                                            <td><?php echo $supplier['sup_name']; ?></td>
                                            <td><?php echo $supplier['mobile']; ?></td>  <!-- Changed from 'mobile' to 'contact' -->
                                            <td><?php echo $supplier['email']; ?></td>
                                            <td style="text-align:center;">
                                                <a href="edit_supplier.php?sup_id=<?php echo $supplier['sup_id']; ?>" class="btn btn-success text-light py-1 px-2">Edit</a>
                                                <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $supplier['sup_id']; ?>)">Delete</button>
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
    function myFunction(sup_id) {
        if (confirm("Are you sure you want to delete this supplier?")) {
            window.location.href = "delete_supplier.php?sup_id=" + sup_id;
        }
    }

    function searchSupplier() {
        // Get the value from the input field
        var input = document.getElementById("searchSupplierInput");
        var filter = input.value.toUpperCase();

        // Get the table and all its rows
        var table = document.querySelector(".table");
        var rows = table.getElementsByTagName("tr");

        // Loop through all rows in the table (starting from 1, skipping the header row)
        for (var i = 1; i < rows.length; i++) {
            var supplierName = rows[i].getElementsByTagName("td")[0]; // Supplier Name
            var contact = rows[i].getElementsByTagName("td")[1]; // Contact
            var email = rows[i].getElementsByTagName("td")[2]; // Email

            // Check if the row matches the search query
            if (supplierName || contact || email) {
                var nameValue = supplierName.textContent || supplierName.innerText;
                var contactValue = contact.textContent || contact.innerText;
                var emailValue = email.textContent || email.innerText;

                // Show row if the input matches the supplier name, contact, or email
                if (
                        nameValue.toUpperCase().indexOf(filter) > -1 ||
                        contactValue.toUpperCase().indexOf(filter) > -1 ||
                        emailValue.toUpperCase().indexOf(filter) > -1
                        ) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }
</script>

