<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Expenses.php';
require_once dirname(__FILE__) . '/../vendor/autoload.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

// Retrieve the id from the session
$user_id = $_SESSION["user_id"];

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

// Instantiate the Expenses class
$expenses = new Expenses($db, $farm['user_id'], $from_date, $to_date);

// Fetch all data and total amount
$all_data = $expenses->getAllData();
$total_amount = $expenses->getTotalAmount();

// Generate HTML content for PDF
$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expenses Report</title>
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
    <h2 style="text-align:center;">Expenses Report</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Payment Date</th>
                <th>Payment Detail</th>
                <th>Paid To</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>';

if (!empty($all_data)) {
    $uid = 1;
    foreach ($all_data as $data) {
        $html .= '<tr>
            <td>' . $uid . '</td>
            <td>' . htmlspecialchars(substr($data['date'], 0, 10)) . '</td>
            <td>' . htmlspecialchars($data['detail']) . '</td>
            <td>' . htmlspecialchars($data['paid_to']) . '</td>
            <td class="text-right">' . number_format($data['amount'], 2) . '</td>
        </tr>';
        $uid++;
    }

    $html .= '<tr>
        <td colspan="4" class="text-right"><strong>Total Amount</strong></td>
        <td class="text-right"><strong>' . number_format($total_amount, 2) . '</strong></td>
    </tr>';
} else {
    $html .= '<tr>
        <td colspan="5" style="text-align: center;">No data found</td>
    </tr>';
}

$html .= '</tbody>
    </table>
</body>
</html>';

// Generate PDF
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output('expenses_report.pdf', 'I');
?>
