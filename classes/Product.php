<?php

require_once 'crud.php';

class Product implements crud {

    private $conn;
    private $table_name = "products";
    private $user_id;
    private $product_name;
    private $category_id;
    private $product_price; // Changed from selling_price to product_price
    private $unit;
    private $product_img;
    private $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters and Setters
    function getUser_id() {
        return $this->user_id;
    }

    function getProduct_name() {
        return $this->product_name;
    }

    function getCategory_id() {
        return $this->category_id;
    }

    function getProduct_price() { // Changed from getSelling_price to getProduct_price
        return $this->product_price;
    }

    function getUnit() {
        return $this->unit;
    }

    function getProduct_img() {
        return $this->product_img;
    }

    function getDescription() {
        return $this->description;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    function setProduct_name($product_name) {
        $this->product_name = $product_name;
    }

    function setCategory_id($category_id) {
        $this->category_id = $category_id;
    }

    function setProduct_price($product_price) { // Changed from setSelling_price to setProduct_price
        $this->product_price = $product_price;
    }

    function setUnit($unit) {
        $this->unit = $unit;
    }

    function setProduct_img($product_img) {
        $this->product_img = $product_img;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    // Create Product
    public function create($user_id) {
        $query = "INSERT INTO " . $this->table_name . " (farm_id, product_name, unit, category_id, product_price, product_img, description) 
              VALUES (:user_id, :product_name, :unit, :category_id, :product_price, :product_img, :description)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters using bindValue
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':product_name', $this->product_name);
        $stmt->bindValue(':unit', $this->unit);
        $stmt->bindValue(':category_id', $this->category_id);
        $stmt->bindValue(':product_price', $this->product_price); // Changed from selling_price to product_price
        $stmt->bindValue(':product_img', $this->product_img); // Use filename directly
        $stmt->bindValue(':description', $this->description);

        return $stmt->execute();
    }

    // Read Products by User ID
    public function read($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE farm_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Read One Product by ID
    public function readOne($product_id) {
        $query = "SELECT product_name, unit, category_id, product_price, product_img, description
                  FROM " . $this->table_name . "
                  WHERE product_id = :product_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':product_id', $product_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->product_name = $row['product_name'];
            $this->category_id = $row['category_id'];
            $this->unit = $row['unit'];
            $this->product_price = $row['product_price']; // Changed from selling_price to product_price
            $this->product_img = $row['product_img'];
            $this->description = $row['description'];
        }

        return $row;
    }

    // Update Product
    public function update($product_id) {
        $query = "UPDATE " . $this->table_name . " 
              SET product_name = :product_name, 
                  category_id = :category_id, 
                  unit = :unit, 
                  product_price = :product_price,
                  product_img = :product_img, 
                  description = :description 
              WHERE product_id = :product_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->product_name = htmlspecialchars(strip_tags($this->product_name));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->product_price = htmlspecialchars(strip_tags($this->product_price)); // Changed from selling_price to product_price
        $this->product_img = htmlspecialchars(strip_tags($this->product_img));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind values using bindValue
        $stmt->bindValue(':product_name', $this->product_name);
        $stmt->bindValue(':category_id', $this->category_id);
        $stmt->bindValue(':unit', $this->unit);
        $stmt->bindValue(':product_price', $this->product_price); // Changed from selling_price to product_price
        $stmt->bindValue(':product_img', $this->product_img ? $this->product_img : 'default_image.jpg');
        $stmt->bindValue(':description', $this->description);
        $stmt->bindValue(':product_id', $product_id);

        if ($stmt->execute()) {
            return true;
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "SQL Error: " . $errorInfo[2];
            return false;
        }
    }

    // Delete Product
    public function delete($product_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        // Bind the product_id parameter
        $stmt->bindValue(':product_id', $product_id);

        return $stmt->execute();
    }

    public function getProductName($product_id) {
        // Prepare the SQL query
        $query = "SELECT product_name FROM " . $this->table_name . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':product_id', $product_id);

        // Execute the query
        if ($stmt->execute()) {
            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if a result was returned
            if ($result) {
                return $result['product_name'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getProductPrice($product_id) {
        // Prepare the SQL query
        $query = "SELECT product_price FROM " . $this->table_name . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':product_id', $product_id);

        // Execute the query
        if ($stmt->execute()) {
            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if a result was returned
            if ($result) {
                return $result['product_price'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getProductStock($product_id) {
        $query = "SELECT SUM(quantity) AS productTotal FROM product_stock WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['productTotal'] ?? 0;
    }

    public function getConfirmedOrder($product_id) {
        $query = "SELECT SUM(quantity) AS productOrder FROM `orders` WHERE product_id = :product_id AND status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindValue(':status', 1); 
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['productOrder'] ?? 0;
    }

    public function getAllBatches($user_id) {
        // Prepare the SQL query
        $query = "SELECT batch_id, batch FROM birds WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':user_id', $user_id);

        // Execute the query
        $stmt->execute();

        // Fetch all results
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function updatePrice($product_id, $new_price) {
        $query = "UPDATE products SET product_price = :new_price WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':new_price', $new_price);
        $stmt->bindParam(':product_id', $product_id);

        return $stmt->execute();
    }

    public function productExists($user_id) {
        // Modify the query to remove spaces and make it case-insensitive
        $query = "SELECT product_id FROM " . $this->table_name . " 
              WHERE LOWER(REPLACE(product_name, ' ', '')) = LOWER(REPLACE(:product_name, ' ', '')) 
              AND user_id = :user_id 
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

}
