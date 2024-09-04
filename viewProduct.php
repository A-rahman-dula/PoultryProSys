<?php
require_once 'marketplace/marketplaceFrame.php';
require 'classes/config.php';
require 'marketplace/marketPlaceCRUD.php';

$marketPlaceFrame = new marketPlaceFrame();
$marketPlaceFrame->navbar();

$marketPlaceCRUD = new MarketPlaceCRUD();
$product_id = $marketPlaceCRUD->getProductId();
$row = $marketPlaceCRUD->viewProduct($product_id);

if (!empty($row)) { // Check if product found
?>
<!doctype HTML>
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
  <div class="container mt-5 mb-5">
    <div class="row d-flex justify-content-center">
      <div class="col-md-10">
        <div class="card">
          <div class="row">
            <div class="col-md-6">
              <div class="images p-3">
                  <div class="text-center p-4"> <img src=".<?php echo $row['product_img'] ?>" width="250" /> </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="product p-4">
                <div class="mt-4 mb-3">
                  <span class="text-uppercase text-muted">PoultryPro</span>
                  <h3 class="text-uppercase"><?php echo $row['product_name']; ?></h3>
                  <div class="price d-flex flex-row align-items-center">
                    <h6>Rs. <?php echo $row['product_price']; ?></h6>
                  </div>
                </div>
                <p class="about"><?php echo $row['description']; ?></p>
                <div class="sizes mt-5">
                  <h6 class="text">Available Stock : <?php echo $row["quantity"] . " " . (isset($row["unit"]) ? $row["unit"] : ""); ?></h6>
                </div>
                <form action="processOrder.php?product_id=<?php echo $row["product_id"]?>" method="post"> <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" min="1" max="<?php echo $row["quantity"]; ?>" class="form-control col-2" id="quantity" name="quantity" required>
                  </div>

                  <div class="cart mt-4 align-items-center">
                    <button type="submit" class="btn btn-danger text-uppercase mr-2 px-4">Buy</button>
                    <i class="fa fa-heart text-muted"></i>
                    <i class="fa fa-share-alt text-muted"></i>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php } else {
  echo 'Product not found.';
} ?>
