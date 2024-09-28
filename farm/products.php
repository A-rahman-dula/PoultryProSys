<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Product.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

// Initialize the Product object
$product = new Product($con);

$success_message = '';
$error_message = '';

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

    if ($product->productExists($user_id)) {
        $error_message = "This product already exists";
    } else {

        // Create a new product
        if ($product->create($user_id)) {
            $success_message = "Added successfully.";
        } else {
            $error_message = "Failed to add.";
        }
    }
}

// Fetch product categories for dropdown
function fetchCategories($con) {
    $query = $con->prepare('SELECT * FROM product_categories');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

$categories = fetchCategories($con);

$products = $product->read($user_id);
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 text-center">

            <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <div class="card">
                    <div class="card-header p-3 text-center" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Add New Product</strong></h5>
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

                        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">

                            <div class="row p-2">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <label class="col-sm-8 col-form-label">Product Name:</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <input type="text" name="product_name" class="form-control" required>
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
                                                    <option value="<?= $category['category_id'] ?>">
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
                                            <input type="text" name="selling_price" class="form-control" required>
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
                                                <input type="radio" class="form-check-input" name="unit" value="kilogram" id="kilogram" required>
                                                <label class="form-check-label" for="Kg">kg</label>
                                            </div>
                                            <div class="col-sm-6 form-check">
                                                <input type="radio" class="form-check-input" name="unit" value="pieces" id="pieces" required>
                                                <label class="form-check-label" for="Pieces">Pieces</label>
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
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Description:</label>
                                        <div class="col-sm-12">
                                            <textarea name="description" class="form-control" rows="3" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row px-3" style="text-align:center;">
                                <button type="submit" class="btn btn-primary" name="add_product">Add Product</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-10 col-12 mb-3 my-2">
                <div class="row p-2">
                    <div class="col-sm-12">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search for products..." onkeyup="searchProduct()">
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title p-2 text-white mb-0"><strong style="font-size:25px;">Product Details</strong></h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th scope="col">#</th>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialnum = 0;
                                    foreach ($products as $product) {
                                        $serialnum++;
                                        ?>
                                        <tr>
                                            <th><?php echo $serialnum; ?></th>
                                            <td><?php echo $product['product_name']; ?></td>
                                            <td><?php echo $product['product_price']; ?></td>
                                            <td><?php echo $product['unit']; ?></td>
                                            <td style="text-align:center;">
                                                <a href="product_stock.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-primary text-dark py-1 px-2"><i class="bi bi-plus-square-fill" style="font-size:18px;"></i></a>
                                                <a href="edit_product.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-success text-light py-1 px-2">Edit</a>
                                                <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $product['product_id']; ?>)">Delete</button>
                                            </td>

                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</main>

<?php $frame->last_part(); ?>
<script>
    function myFunction(product_id) {
        if (confirm("Are you sure you want to delete this product?")) {
            window.location.href = "delete_product.php?product_id=" + product_id;
        }
    }

    function searchProduct() {
        // Get the input value
        var input = document.getElementById("searchInput");
        var filter = input.value.toUpperCase();

        // Get the table and rows
        var table = document.querySelector(".table");
        var rows = table.getElementsByTagName("tr");

        // Loop through all table rows (except the first one, which is the header)
        for (var i = 1; i < rows.length; i++) {
            var productName = rows[i].getElementsByTagName("td")[0];
            var productPrice = rows[i].getElementsByTagName("td")[1];
            var productUnit = rows[i].getElementsByTagName("td")[2];

            // Check if the current row matches the search query
            if (productName || productPrice || productUnit) {
                var nameValue = productName.textContent || productName.innerText;
                var priceValue = productPrice.textContent || productPrice.innerText;
                var unitValue = productUnit.textContent || productUnit.innerText;

                // If the input matches any of these values, show the row
                if (
                        nameValue.toUpperCase().indexOf(filter) > -1 ||
                        priceValue.toUpperCase().indexOf(filter) > -1 ||
                        unitValue.toUpperCase().indexOf(filter) > -1
                        ) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }
</script>
