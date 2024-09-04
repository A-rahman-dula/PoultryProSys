<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once 'Frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Incomes.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farm') {
    header("Location: ../login.php");
    exit();
}

// Retrieve the id from session
$user_id = $_SESSION["user_id"];

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);

// Get "from" and "to" date from the submission, if available
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate the Income class
$income = new Incomes($db, $farm['user_id'], $from_date, $to_date);

// Fetch all data and total amount
$all_data = $income->getAllData();
$total_amount = $income->getTotalAmount();
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container py-4">
        <div class="row py-5 px-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>INCOMES DETAILS</h3>
                    </div>
                    <div class="card-body">
                        <!-- Add date filter form -->

                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-6">
                                    <label for="from_date">From Date:</label>
                                    <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" class="form-control">  </div>
                                <div class="col-lg-4 col-md-6 col-6">
                                    <label for="to_date">To Date:</label>
                                    <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" class="form-control">
                                </div>
                                <div class="col-lg-4 col-md-6 col-12 pt-4 text-center">
                                    <div class="row px-5">
                                        <div class="col-lg-6 col-md-6 col-6">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-6">
                                            <button class="btn btn-danger">
                                                <a href="incomesPDF.php?from_date=<?php echo htmlspecialchars($from_date); ?>&to_date=<?php echo htmlspecialchars($to_date); ?>" class="text-light" style="text-decoration: none;">Export PDF</a>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Add the submit button -->
                    <div class="row my-4">
                        <div class="col-12">
                            <table class="table">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th scope="col">#</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Income Detail</th>
                                        <th scope="col">Received From</th>
                                        <th scope="col" >Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
// Display the sorted data
                                    if (!empty($all_data)) {
                                        $uid = 1;
                                        foreach ($all_data as $data) {
                                            ?>
                                            <tr>
                                                <td><?php echo $uid ?></td>
                                                <td><?php echo htmlspecialchars(substr($data['date'], 0, 10)) ?></td>
                                                <td><?php echo htmlspecialchars($data['detail']) ?></td>
                                                <td><?php echo htmlspecialchars($data['received_from']) ?></td>
                                                <td style="text-align:right;"><?php echo number_format($data['amount'], 2) ?></td>
                                            </tr>
                                            <?php
                                            $uid++;
                                        }
                                        ?>

                                        <!-- Total amount row -->
                                        <tr>
                                            <td colspan="4" style="text-align: right; padding-right: 80px;"><strong>Total Amount</strong></td>
                                            <td style="text-align: right; padding-right: 20px;"><strong><?php echo number_format($total_amount, 2); ?></strong></td>
                                        </tr>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center;">No data found</td>
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