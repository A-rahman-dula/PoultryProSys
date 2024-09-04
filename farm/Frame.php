<?php

class Frame {

    public function __construct() {
        
    }

    public function first_part($farm) {
        ?>
        <!DOCTYPE html>
        <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Farm-PoultryPro</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
                <style>
                    .nav-item{
                        font-weight: bold;
                    }

                    .nav-item:hover {
                        background-color: #355f4c;
                    }

                    .dropdown-item {
                        font-weight: bold;
                    }
                    .dropdown-item:hover {
                        background-color: #355f4c;
                        color: white;
                    }
                </style>
            </head>

            <body>
                <nav class="navbar navbar-expand-lg " style="background-color: #D0D4CA;">
                    <div class="container-fluid p-0">
                        <div class="navbar-brand text-center mx-auto">
                            <h4 style="font-weight: bold; font-size: 20px;"><?php echo strtoupper(htmlspecialchars($farm['username'])); ?></h4>
                            <h6 style="font-weight: bold;"><?php echo strtoupper(htmlspecialchars($farm['address']) . " - " . htmlspecialchars($farm['city'])); ?></h6>
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                </nav>

                <div class="container-fluid p-0">
                    <div class="row g-0">
                        <nav class="col-lg-2 col-md-3 col-sm-4 ">
                            <div class="navbar-collapse collapse show p-3 vh-100" id="navbarNavDropdown" style="background-color: #40826D;">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="dashboard.php">
                                            <i class="bi bi-window-dash"></i> Dashboard
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="birds.php">
                                            <i class="bi bi-twitter"></i> Batches
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="#">
                                            <i class="bi bi-wallet-fill"></i> Miscellaneous
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="feed.php">
                                            <i class="bi bi-backpack4-fill"></i> Feeds
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="medicine.php">
                                            <i class="bi bi-capsule-pill"></i> Medicine
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="orders.php">
                                            <i class="bi bi-cash-coin"></i> Order
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="products.php">
                                            <i class="bi bi-cart4"></i> Product
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="supplier.php">
                                            <i class="bi bi-person-fill-up"></i> Supplier
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-clipboard-data-fill"></i> Reports
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <li><a class="dropdown-item" href="stock.php">Stock Report</a></li>
                                            <li><a class="dropdown-item" href="incomes.php">Income Report</a></li>
                                            <li><a class="dropdown-item" href="expenses.php">Expenses Report</a></li>
                                            <li><a class="dropdown-item" href="profit.php">Profit Report</a></li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a href="../logout.php" class="nav-link text-white">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </nav>

                        <?php
                    }

                    public function last_part() {
                        ?>

                    </div>
                </div>
            </body>

        </html>
        <?php
    }

}
?>
