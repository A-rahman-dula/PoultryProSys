<?php
require_once 'marketplace/marketplaceFrame.php';
require 'classes/config.php';

$marketPlaceFrame = new marketPlaceFrame();
$marketPlaceFrame->navbar();

$db = new Database();
$conn = $db->getConnection();
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <link rel="stylesheet" href="marketplace/marketPlaceStyle.css">
    <title>MarketPlace - PoultryPro</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <ol class="carousel-indicators">
        <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></li>
        <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></li>
        <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img class="d-block w-100" src="images/slider2.jpg" alt="First slide">
        </div>
        <div class="carousel-item">
            <img class="d-block w-100" src="images/slider3.jpg" alt="Second slide">
        </div>
        <div class="carousel-item">
            <img class="d-block w-100" src="images/slider2.jpg" alt="Third slide">
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>
<div class="container mt-5">
    <div class="row">
        <?php
        $sql = "SELECT product_id,product_name,  category_id, product_price, product_img, description FROM products ORDER BY product_id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="'.htmlspecialchars($row["product_img"]).'" alt="'.htmlspecialchars($row["product_name"]).'" class="card-img-top p-3">
                        <div class="card-body">
                            <h5 class="card-title">'.htmlspecialchars($row["product_name"]).'</h5>
                            <p class="card-text">'.htmlspecialchars($row["description"]).'</p>
                            <h5 class="card-text">Rs. '.htmlspecialchars($row["product_price"]).'</h5>
                            <a href="viewProduct.php?product_id='.htmlspecialchars($row["product_id"]).'" class="btn btn-primary">Buy</a>
                        </div>
                    </div>
                </div>
                ';
            }
        } else {
            echo '<p>No products found.</p>';
        }
        ?>
    </div>
</div>
</body>
</html>
