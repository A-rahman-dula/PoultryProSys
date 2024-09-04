<?php

class AdminFrame
{

    public function __construct()
    {
    }

    public function first_part($admin)
    {
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin-PoultryPro</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
            <link rel="stylesheet" href="style.css">
        </head>

        <body>
            <div class="header pt-1 navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="#" style="font-weight: bold; font-size: 20px;">ADMIN - PoultryPro</a><br>

            </div>
            <div class="raw">
                <div class="sidebar show justify-content-center">
                    <aside class="sideNew" id="sidebar">
                        <nav class="navbar navbar-dark">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="admin_dashboard.php">
                                        <i class="bi bi-window-dash"></i>
                                        <span>DASHBOARD</span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="admin_farms.php" class="nav-link">
                                        <i class="bi bi-house-check-fill"></i>
                                        <span>FARMS</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="admin_customers.php" class="nav-link">
                                        <i class="bi bi-person-fill-check"></i>
                                        <span>CUSTOMERS</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link">
                                        <i class="bi bi-cash-coin"></i>
                                        <span>INCOMES</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="admin_reviews.php" class="nav-link">
                                        <i class="bi bi-box2-fill"></i>
                                        <span>REVIEWS</span>
                                    </a>
                                </li>


                            </ul>
                        </nav>
                        <div class="navbar-footer">
                            <a href="logout.php" class="nav-link">
                                <i class="bi bi-box-arrow-left"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                        </aside>
                </div>
            <?php
        }

        public function last_part()
        {
            ?>
            </div>
        </body>

        </html>
<?php
        }
    }
?>