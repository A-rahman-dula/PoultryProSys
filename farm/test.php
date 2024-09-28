<?php

// Start the session
session_start();

// Include the database configuration file
require_once '../classes/config.php';

// Initialize the database connection
$database = new Database();
$db = $database->getConnection();

// Check if the session variable for user_id is set
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
} else {
    echo "Error: User ID not set in session.";
    exit();
}

try {
    // Define the query to fetch individual records for debugging
    $query = "
    SELECT ps.product_id, p.product_name, 
           SUM(ps.quantity) AS total_stock_quantity, 
           ps.unit_price AS stock_unit_price, 
           COALESCE(SUM(o.quantity), 0) AS total_ordered_quantity,
           (SUM(ps.quantity) - COALESCE(SUM(o.quantity), 0)) AS remaining_quantity,
           (ps.unit_price * (SUM(ps.quantity) - COALESCE(SUM(o.quantity), 0))) AS stock_value
    FROM product_stock ps
    LEFT JOIN orders o ON ps.product_id = o.product_id AND o.status = :status
    JOIN products p ON ps.product_id = p.product_id
    WHERE ps.user_id = :farm_id
    GROUP BY ps.product_id, p.product_name, ps.unit_price
    ";

    // Prepare the statement using the correct database connection ($db)
    $stmt = $db->prepare($query);

    // Bind the farm_id parameter (using $user_id from the session)
    $stmt->bindParam(':farm_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':status', 1, PDO::PARAM_INT); // Assuming status = 1 represents confirmed orders
    // Execute the query
    $stmt->execute();

    // Fetch all the results
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Print the results for debugging
    echo "<pre>";
    if (!empty($rows)) {
        foreach ($rows as $row) {
            echo "Product: " . $row['product_name'] . "\n";
            echo "Stock Quantity in 'product_stock': " . $row['total_stock_quantity'] . "\n"; // Changed here
            echo "Unit Price in 'product_stock': " . $row['stock_unit_price'] . "\n";
            echo "Order Quantity from 'orders' (confirmed orders): " . $row['total_ordered_quantity'] . "\n"; // Changed here
            echo "Remaining Stock Quantity: " . $row['remaining_quantity'] . "\n";
            echo "Total Stock Value (Unit Price * Remaining Quantity): " . $row['stock_value'] . "\n\n";
        }
    } else {
        echo "No results found.\n";
    }
    echo "</pre>";
} catch (PDOException $e) {
    // Print error for debugging
    echo "Error: " . $e->getMessage();
}
?>
