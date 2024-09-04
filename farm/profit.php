<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once '../classes/config.php';
require_once 'Frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Incomes.php';
require_once '../classes/Expenses.php';
require_once '../classes/Stocks.php';
require_once '../classes/Product.php';

// Check if user is logged in and has appropriate role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION["user_id"];

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);

// Get 'from' and 'to' dates from the form submission, if available
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate Incomes, Expenses, and Stocks classes
$incomes = new Incomes($db, $user_id, $from_date, $to_date);
$expenses = new Expenses($db, $user_id, $from_date, $to_date);
$stocks = new Stocks($db, $user_id, $from_date, $to_date);

// Fetch data from Incomes, Expenses, and Stocks classes
$incomeData = $incomes->getAllData();
$totalIncome = $incomes->getTotalAmount();

$expensesDataCategorized = $expenses->getAllDataCategorized();
$totalExpense = $expenses->getTotalAmount();

$stocksDataCategorized = $stocks->getAllDataCategorized($from_date, $to_date);
$totalStocks = $stocks->getTotalStockValue($from_date, $to_date);

$profitLoss = $totalIncome + $totalStocks - $totalExpense;  // Profit or loss
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container py-4">
        <div class="row py-5 px-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>PROFIT & LOSS STATEMENT</h3>
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
                                                <a href="profitPDF.php?from_date=<?php echo htmlspecialchars($from_date); ?>&to_date=<?php echo htmlspecialchars($to_date); ?>" class="text-light" style="text-decoration: none;">Export PDF</a>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Display Income Table -->
                        <div class="row my-4 py-4">
                            <div class="col-12">

                                <table class="table table-bordered border-dark" >
                                    <thead>
                                        <tr>
                                            <th scope="col" colspan="5" class="table-info">Incomes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="padding-left: 200px;">Total Orders</td>
                                            <td style="text-align: right; padding-right: 20px;"><?php echo number_format($totalIncome, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="padding-left: 200px;"><strong>Total Income</strong></td>
                                            <td style="text-align: right; padding-right: 20px;"><strong><?php echo number_format($totalIncome, 2); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                                <!-- Display Expenses Table -->
                                <table class="table table-bordered border-dark">
                                    <thead>
                                        <tr>
                                            <th scope="col" colspan="5" class="table-info">Expenses</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="padding-left: 200px;">Total Birds</td>
                                            <td style="text-align: right; padding-right: 20px;"><?php echo number_format($expensesDataCategorized['total_birds'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="padding-left: 200px;">Total Medicine</td>
                                            <td style="text-align: right; padding-right: 20px;"><?php echo number_format($expensesDataCategorized['total_medicine'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="padding-left: 200px;">Total Feeds</td>
                                            <td style="text-align: right; padding-right: 20px;"><?php echo number_format($expensesDataCategorized['total_feeds'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="padding-left: 200px;">Total Miscellaneous</td>
                                            <td style="text-align: right; padding-right: 20px;"><?php echo number_format($expensesDataCategorized['total_miscellaneous'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="padding-left: 200px;"><strong>Total Expenses</strong></td>
                                            <td style="text-align: right; padding-right: 20px;"><strong><?php echo number_format($totalExpense, 2); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                                <!-- Display Stocks Table -->
                                <table class="table table-bordered border-dark">
                                    <thead>
                                        <tr>
                                            <th scope="col" colspan="5" class="table-info">Stocks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="padding-left: 200px;">Total Products Stock Value</td>
                                            <td style="text-align: right; padding-right: 20px;"><?php echo number_format($stocksDataCategorized['total_products'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="padding-left: 200px;">Total Medicine Stock Value</td>
                                            <td style="text-align: right; padding-right: 20px;"><?php echo number_format($stocksDataCategorized['total_medicine'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="padding-left: 200px;">Total Feeds Stock Value</td>
                                            <td style="text-align: right; padding-right: 20px;"><?php echo number_format($stocksDataCategorized['total_feeds'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="padding-left: 200px;"><strong>Total Stocks Value</strong></td>
                                            <td style="text-align: right; padding-right: 20px;"><strong><?php echo number_format($totalStocks, 2); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                                <!-- Display Profit/Loss -->
                                <h4>Profit/Loss</h4>
                                <table class="table table-bordered border-dark">
                                    <thead>
                                        <tr>
                                            <th class="table-info">Total Income</th>
                                            <th class="table-info">Total Expenses</th>
                                            <th class="table-info">Stock Available</th>
                                            <th class="table-info">Profit/Loss</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align: center;"><?php echo number_format($totalIncome, 2); ?></td>
                                            <td style="text-align: center;"><?php echo number_format($totalExpense, 2); ?></td>
                                            <td style="text-align: center;"><?php echo number_format($totalStocks, 2); ?></td>
                                            <td style="text-align: center;"><strong><?php echo number_format($profitLoss, 2); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
