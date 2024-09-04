<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Product.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

// Instantiate the Product class
$product = new Product($con);

// Assuming you get the product_id from a GET request
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : '';

if ($product_id) {
    // Get product details
    $product_details = $product->readOne($product_id);
    if ($product_details) {
        $product_name = $product_details['product_name'];
        $category_id = $product_details['category_id'];
        $unit = $product_details['unit'];
        $product_price = $product_details['product_price'];
        $product_img = $product_details['product_img'];
        $description = $product_details['description'];
    } else {
        echo "No product found with the provided ID.";
        exit();
    }
} else {
    echo "Product ID is required.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $unit = $_POST['unit'];
    $category_id = $_POST['category_id'];
    $selling_price = number_format($_POST['selling_price'], 2);
    $description = $_POST['description'];

    // Initialize variables for file upload
    $target_file = 'default_image.jpg';
    $file_uploaded = false;

    // Handle file upload
    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../images/ProductImages/";
        $target_file = $target_dir . basename($_FILES["product_img"]["name"]);

        // Check if directory exists, if not, create it
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["product_img"]["tmp_name"], $target_file)) {
            $file_uploaded = true;
        } else {
            $error_message = "Failed to move uploaded file.";
        }
    } else {
        if ($_FILES['product_img']['error'] != UPLOAD_ERR_NO_FILE) {
            $error_message = "Upload error code: " . $_FILES['product_img']['error'];
        }
    }

    $product->setUser_id($user_id);
    $product->setProduct_name($product_name);
    $product->setUnit($unit);
    $product->setCategory_id($category_id);
    $product->setProduct_price($selling_price);
    $product->setProduct_img("./images/ProductImages/" . basename($_FILES["product_img"]["name"]));
    $product->setDescription($description);

    // Update the product
    if ($product->update($product_id)) {
        $success_message = "Product updated successfully.";
    } else {
        $error_message = "Failed to update product.";
    }
}

// Fetch product categories for dropdown
function fetchCategories($con) {
    $query = $con->prepare('SELECT * FROM product_categories');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

$categories = fetchCategories($con);
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 text-center">
            <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <div class="card">
                    <div class="card-header p-3 text-center" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Edit Product</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #D4C8DE;">

                        <?php if (!empty($success_message)) : ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error_message)) : ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?product_id=' . urlencode($product_id); ?>" method="POST" enctype="multipart/form-data">

                            <!-- Hidden input for product_id -->
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">

                            <div class="row p-2">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <label class="col-sm-8 col-form-label">Product Name:</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <input type="text" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product_name); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <label class="col-sm-8 col-form-label">Select Category:</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <select name="category_id" class="form-control" required>
                                                <?php foreach ($categories as $category) { ?>
                                                    <option value="<?= $category['category_id'] ?>" <?= $category['category_id'] == $category_id ? 'selected' : '' ?>>
                                                        <?= $category['category_name'] ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <label class="col-sm-8 col-form-label">Selling Price:</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <input type="text" name="selling_price" class="form-control" value="<?php echo htmlspecialchars($product_price); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Unit:</label>
                                    </div>

                                    <div class="col-sm-8">
                                        <div class="row mb-3 px-3">
                                            <div class="col-sm-6 form-check">
                                                <input type="radio" class="form-check-input" name="unit" value="kilogram" id="kilogram" <?= $unit == 'kilogram' ? 'checked' : '' ?> required>
                                                <label class="form-check-label" for="kilogram">kg</label>
                                            </div>
                                            <div class="col-sm-6 form-check">
                                                <input type="radio" class="form-check-input" name="unit" value="pieces" id="pieces" <?= $unit == 'pieces' ? 'checked' : '' ?> required>
                                                <label class="form-check-label" for="pieces">Pieces</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Product Image:</label>
                                    </div>
                                    <div class="row">
                                        <input type="file" name="product_img" class="form-control">
                                    </div>
                                    <?php if (!empty($product_img)) : ?>
                                        <div class="row mt-2">
                                            <p class="text-muted">Current Image: <?php echo htmlspecialchars(basename($product_img)); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Description:</label>
                                        <div class="col-sm-12">
                                            <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($description); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row px-3" style="text-align:center;">
                                <button type="submit" class="btn btn-primary" name="add_product">Update Product</button>
                            </div>

                            <div class="row px-3 mt-2" style="text-align:center;">
                                <a href="products.php" class="btn btn-danger">Back</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <div class="card">
                    <div class="card-body text-center" style="background-color: #D4C8DE;">
                        <img src=".<?php echo htmlspecialchars($product_img); ?>" alt="Product Image" class="img-fluid">
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php $frame->last_part(); ?>
