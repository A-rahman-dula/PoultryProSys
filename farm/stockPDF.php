<?php

// Start session if not started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Stocks.php';
require_once '../classes/Product.php';
require_once dirname(__FILE__) . '/../vendor/autoload.php'; // Correct path to autoload.php

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: login.php");
    exit();
}

// Retrieve the user id from the session
$user_id = $_SESSION['user_id'];

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$farm_name = $farm['username'];
$address = $farm['address'];

// Get 'from' and 'to' dates from the form submission, if available
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize the Stocks class
$stocks = new Stocks($db, $user_id);

// Fetch all stock data and totals
$feedStockData = $stocks->getAllStockData($from_date, $to_date);
$medicineStockData = $stocks->getAllMedicineStockData($from_date, $to_date);
$productStockData = $stocks->getAllProductStockData($from_date, $to_date);

$totalFeedStockAmount = $stocks->getTotalStockAmount($from_date, $to_date);
$totalMedicineStockAmount = $stocks->getTotalMedicineStockAmount($from_date, $to_date);
$totalProductStockAmount = $stocks->getTotalProductStockAmount($from_date, $to_date);

$totalStocks = $totalFeedStockAmount + $totalMedicineStockAmount + $totalProductStockAmount;

// Generate HTML content for PDF
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo" style="text-align: center;">
            <a class="navbar-brand mx-5">
                <img src="../images/logo-poultryPro2.jpeg" alt="logo-poultryPro" style="border-radius: 50%; width: 40px; height: 30px;">
                <span style="font-size: 25px; font-weight: bold;">PoultryPro</span>
            </a>
        </div>
        <p>Farm Name&nbsp;: &nbsp;' . htmlspecialchars($farm_name) . '</p>
        <p>Address&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; ' . htmlspecialchars($address) . '</p>
        <p>From&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;' . htmlspecialchars($from_date) . '</p>
        <p>To&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;' . htmlspecialchars($to_date) . '</p>
    </div>

    <h2 style="text-align:center;">Stock Report</h2>

    <h3>Feed Stock</h3>
    <table>
        <thead>
            <tr>
                <th>Feed Name</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>';

if (!empty($feedStockData)) {
    foreach ($feedStockData as $data) {
        $html .= '<tr>
            <td>' . htmlspecialchars($data['detail']) . '</td>
            <td class="text-right">' . number_format($data['quantity'], 2) . '</td>
            <td class="text-right">' . number_format($data['amount'], 2) . '</td>
        </tr>';
    }

    $html .= '<tr>
        <td><strong>Total Feed Stock Value</strong></td>
        <td></td>
        <td class="text-right"><strong>' . number_format($totalFeedStockAmount, 2) . '</strong></td>
    </tr>';
} else {
    $html .= '<tr>
        <td colspan="3" style="text-align: center;">No feed stock data found</td>
    </tr>';
}

$html .= '</tbody>
    </table>

    <br><br>

    <h3>Medicine Stock</h3>
    <table>
        <thead>
            <tr>
                <th>Medicine Name</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>';

if (!empty($medicineStockData)) {
    foreach ($medicineStockData as $data) {
        $html .= '<tr>
            <td>' . htmlspecialchars($data['detail']) . '</td>
            <td class="text-right">' . number_format($data['quantity'], 2) . '</td>
            <td class="text-right">' . number_format($data['amount'], 2) . '</td>
        </tr>';
    }

    $html .= '<tr>
        <td><strong>Total Medicine Stock Value</strong></td>
        <td></td>
        <td class="text-right"><strong>' . number_format($totalMedicineStockAmount, 2) . '</strong></td>
    </tr>';
} else {
    $html .= '<tr>
        <td colspan="3" style="text-align: center;">No medicine stock data found</td>
    </tr>';
}

$html .= '</tbody>
    </table>

    <br><br>

    <h3>Product Stock</h3>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>';

if (!empty($productStockData)) {
    foreach ($productStockData as $data) {
        $html .= '<tr>
            <td>' . htmlspecialchars($data['detail']) . '</td>
            <td class="text-right">' . number_format($data['quantity'], 2) . '</td>
            <td class="text-right">' . number_format($data['amount'], 2) . '</td>
        </tr>';
    }

    $html .= '<tr>
        <td><strong>Total Product Stock Value</strong></td>
        <td></td>
        <td class="text-right"><strong>' . number_format($totalProductStockAmount, 2) . '</strong></td>
    </tr>';
} else {
    $html .= '<tr>
        <td colspan="3" style="text-align: center;">No product stock data found</td>
    </tr>';
}

$html .= '</tbody>
    </table>

    <br><br>

    <h3>Total Stock Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Total Feed Stock Value</th>
                <th>Total Medicine Stock Value</th>
                <th>Total Product Stock Value</th>
                <th>Total Stocks Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-right">' . number_format($totalFeedStockAmount, 2) . '</td>
                <td class="text-right">' . number_format($totalMedicineStockAmount, 2) . '</td>
                <td class="text-right">' . number_format($totalProductStockAmount, 2) . '</td>
                <td class="text-right"><strong>' . number_format($totalStocks, 2) . '</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>';

// Generate PDF
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output('stock_report.pdf', 'I');

?>
