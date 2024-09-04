<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';
require_once '../classes/Medicine.php';

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

$med = new Medicine($con);
$med->setUser_id($user_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_med'])) {
        $med_name = $_POST['med_name'];
        $description = $_POST['description'];

        $med->setMed_name($med_name);
        $med->setDescription($description);

        if ($med->medicineExists($user_id)) {
            $error_message = "This medicine already exists";
        } else {


            if ($med->create($user_id)) {
                $success_message = "Medicine added successfully.";
            } else {
                $error_message = "Failed to add Medicine.";
            }
        }
    }
}

$medicines = $med->read($user_id);
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 text-center">

            <div class="col-lg-5 col-md-10 col-12 mb-3 my-5 px-5">
                <div class="card">
                    <div class="card-header p-3 text-center" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">New Medicine</strong></h5>
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
                                        <label class="col-sm-4 col-form-label">Medicine Name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="med_name" name="med_name" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Description:</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row px-3" style="text-align:center;">
                                <button type="submit" class="btn btn-primary" name="add_med">Add Medi</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-md-10 col-12 mb-3 my-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title p-2 text-white mb-0"><strong style="font-size:25px;">Medicine Details</strong></h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th scope="col">#</th>
                                        <th scope="col">Med Name</th>
                                        <th scope="col" style="width:40%">Description</th>
                                        <th scope="col" style="width:32%">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialnum = 0;
                                    foreach ($medicines as $medicine) {
                                        $serialnum++;
                                        ?>
                                        <tr>
                                            <th><?php echo $serialnum; ?></th>
                                            <td><?php echo $medicine['med_name']; ?></td>
                                            <td><?php echo $medicine['description']; ?></td>
                                            <td style="text-align:center;">
                                                <a href="buy_medicine.php?med_id=<?php echo $medicine['med_id']; ?>" class="btn btn-primary text-dark  py-1 px-2 "><i class="bi bi-plus-square-fill" style="font-size:18px;"></i></a>
                                                <a href="use_medicine.php?med_id=<?php echo $medicine['med_id']; ?>" class="btn btn-warning text-dark py-1 px-2 "><i class="bi bi-dash-square-fill" style="font-size:18px;"></i></a>
                                                <a href="edit_medicine.php?med_id=<?php echo $medicine['med_id']; ?>" class="btn btn-success text-light py-1 px-2 ">Edit</a>
                                                <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $medicine['med_id']; ?>)">Delete</button>
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
    function myFunction(med_id) {
        if (confirm("Are you sure you want to delete this medicine?")) {
            window.location.href = "delete_medicine.php?med_id=" + med_id;
        }
    }

</script>