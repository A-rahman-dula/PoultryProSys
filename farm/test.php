<?php

// Sample data for remaining stock
$remaining_stock = [
        [
        'buyfeed_id' => 1,
        'unit_price' => 10,
        'quantity' => 100,
        'remaining' => 80
    ],
        [
        'buyfeed_id' => 2,
        'unit_price' => 15,
        'quantity' => 200,
        'remaining' => 150
    ],
        [
        'buyfeed_id' => 3,
        'unit_price' => 20,
        'quantity' => 300,
        'remaining' => 250
    ]
];

// Calculate the sum of 'remaining' values using a foreach loop
$sumRemaining = 0;
$sumStockValue=0;

foreach ($remaining_stock as $stock) {
    $sumRemaining += $stock['remaining'];
    echo "Remaining Stock: " . $sumRemaining . " added:" . $stock['remaining'];
    echo '<br>';
}

foreach ($remaining_stock as $stock) {
    $sumStockValue += $stock['remaining'] * $stock['quantity'];
    echo " Stock: " . $sumStockValue . " added:" . $stock['remaining'] . " * " . $stock['quantity'];
    echo '<br>';
}


// Output the total remaining stock
echo "Total Remaining Stock: " . $sumRemaining;
echo '<br>';
echo "Total Remaining Stock Value: " . $sumStockValue;
?>
