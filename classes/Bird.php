<?php

require_once 'crud.php';

class Bird implements crud {

    private $conn;
    private $table_name = "birds";
    private $user_id;
    private $batch;
    private $sup_id;
    private $age;
    private $sup_name;
    private $bird_type;
    private $unit_price;
    private $quantity;
    private $total_cost;
    private $date;

    public function __construct($db) {
        $this->conn = $db;
    }

    //getters

    public function getUserId() {
        return $this->user_id;
    }

    public function getBatchId() {
        return $this->batch;
    }

    public function getSupId() {
        return $this->sup_id;
    }

    public function getSupName() {
        return $this->sup_name;
    }

    public function getAge() {
        return $this->age;
    }

    public function getBirdType() {
        return $this->bird_type;
    }

    public function getUnitPrice() {
        return $this->unit_price;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getTotalCost() {
        return $this->total_cost;
    }

    public function getDate() {
        return $this->date;
    }

    //setters

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setBatch($batch) {
        $this->batch = $batch;
    }

    public function setSupId($sup_id) {
        $this->sup_id = $sup_id;
    }

    public function setSupName($sup_name) {
        $this->sup_name = $sup_name;
    }

    public function setAge($age) {
        $this->age = $age;
    }

    public function setBirdType($bird_type) {
        $this->bird_type = $bird_type;
    }

    public function setUnitPrice($unit_price) {
        $this->unit_price = $unit_price;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function setTotalCost($total_cost) {
        $this->total_cost = $total_cost;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getNextBatch($user_id) {
        $query = "SELECT MAX(batch) as max_batch FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['max_batch']) {
            $max_batch = $row['max_batch'];
            $number = intval(substr($max_batch, 2)) + 1;
        } else {
            $number = 1;
        }

        return 'B ' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function create($user_id) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, batch, sup_id, bird_type, age, unit_price, quantity, total_cost, date) VALUES (:user_id, :batch, :sup_id, :bird_type, :age, :unit_price, :quantity, :total_cost, :date)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':batch', $this->batch);
        $stmt->bindParam(':sup_id', $this->sup_id);
        $stmt->bindParam(':bird_type', $this->bird_type);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':total_cost', $this->total_cost);
        $stmt->bindParam(':date', $this->date);

        return $stmt->execute();
    }

    public function read($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $all_birds = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sort data by date
        usort($all_birds, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $all_birds;
    }

    public function readOne($batch_id) {
        $query = "SELECT batch, sup_id, bird_type, age, unit_price, quantity, total_cost, date 
                  FROM " . $this->table_name . " 
                  WHERE batch_id = :batch_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':batch_id', $batch_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->batch = $row['batch'];
            $this->sup_id = $row['sup_id'];
            $this->bird_type = $row['bird_type'];
            $this->age = $row['age'];
            $this->unit_price = $row['unit_price'];
            $this->quantity = $row['quantity'];
            $this->total_cost = $row['total_cost'];
            $this->date = $row['date'];
        }

        return $row;
    }

    // Method to update a batch of birds
    public function update($batch_id) {
        $query = "UPDATE " . $this->table_name . " SET sup_id = :sup_id,  bird_type = :bird_type, age= :age, unit_price = :unit_price, quantity = :quantity, total_cost = :total_cost, date = :date WHERE batch_id = :batch_id ";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->sup_id = htmlspecialchars(strip_tags($this->sup_id));
        $this->bird_type = htmlspecialchars(strip_tags($this->bird_type));
        $this->age = htmlspecialchars(strip_tags($this->age));
        $this->unit_price = htmlspecialchars(strip_tags($this->unit_price));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->total_cost = htmlspecialchars(strip_tags($this->total_cost));
        $this->date = htmlspecialchars(strip_tags($this->date));

        // Bind values
        $stmt->bindParam(':batch_id', $batch_id);
        $stmt->bindParam(':sup_id', $this->sup_id);
        $stmt->bindParam(':bird_type', $this->bird_type);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':total_cost', $this->total_cost);
        $stmt->bindParam(':date', $this->date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($batch_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE batch_id = :batch_id ";
        $stmt = $this->conn->prepare($query);

        // Bind the batch_id parameter
        $stmt->bindParam(':batch_id', $batch_id);

        // Execute the query
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getBatchDetails($batch_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE batch_id = :batch_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':batch_id', $batch_id);

        $stmt->execute();

        $batch = $stmt->fetch(PDO::FETCH_ASSOC);

        return $batch;
    }

    // Method to get the health status for a specific batch ID
    public function getHealthStatus($batch_id) {
        $query = "SELECT * FROM health_status WHERE batch_id = :batch_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':batch_id', $batch_id);

        $stmt->execute();

        $healthStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $healthStatus;
    }

    // Method to get product details for a specific batch ID
    public function getProductDetails($batch_id) {
        $query = "SELECT * FROM product_stock WHERE batch_id = :batch_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':batch_id', $batch_id);

        $stmt->execute();

        $productDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $productDetails;
    }

    public static function getSupplier($sup_id, $conn) {
        // Prepare the SQL query
        $query = "SELECT sup_name FROM supplier WHERE sup_id = :sup_id";
        $stmt = $conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':sup_id', $sup_id);

        // Execute the query
        if ($stmt->execute()) {
            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if a result was returned
            if ($result) {
                return $result['sup_name'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getProductPrice($product_id) {
        $query = "SELECT unit_price FROM products WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['unit_price'] : null;
    }

}
