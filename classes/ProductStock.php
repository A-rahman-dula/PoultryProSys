<?php

require_once 'crud.php';

class ProductStock implements crud {

    private $conn;
    private $table_name = "product_stock";
    private $user_id;
    private $product_id;
    private $batch_id;
    private $quantity;
    private $unit_price;
    private $total;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters
    function getUser_id() {
        return $this->user_id;
    }

    function getProduct_id() {
        return $this->product_id;
    }

    function getBatch_id() {
        return $this->batch_id;
    }

    function getQuantity() {
        return $this->quantity;
    }

    function getUnit_price() {
        return $this->unit_price;
    }

    function getTotal() {
        return $this->total;
    }

    // Setters
    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    function setProduct_id($product_id) {
        $this->product_id = $product_id;
    }

    function setBatch_id($batch_id) {
        $this->batch_id = $batch_id;
    }

    function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    function setUnit_price($unit_price) {
        $this->unit_price = $unit_price;
    }

    function setTotal($total) {
        $this->total = $total;
    }

    // CRUD methods
    public function create($user_id) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, product_id, batch_id,  quantity, unit_price, total) 
                  VALUES (:user_id, :product_id, :batch_id, :quantity, :unit_price, :total)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':batch_id', $this->batch_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':total', $this->total);

        return $stmt->execute();
    }

    public function read($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readOne($ps_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ps_id = :ps_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ps_id', $ps_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->product_id = $row['product_id'];
            $this->user_id = $row['user_id'];
            $this->batch_id = $row['batch_id'];
            $this->quantity = $row['quantity'];
            $this->unit_price = $row['unit_price'];
            $this->total = $row['total'];
        }

        return $row;
    }

    public function update($ps_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET product_id = :product_id, batch_id = :batch_id,  quantity = :quantity, unit_price = :unit_price, total = :total 
                  WHERE ps_id = :ps_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':buyfeed_id', $buyfeed_id);
        $stmt->bindParam(':feed_id', $this->feed_id);
        $stmt->bindParam(':sup_id', $this->sup_id);

        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':total', $this->total);

        return $stmt->execute();
    }

    public function delete($ps_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE ps_id = :ps_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':ps_id', $ps_id);

        return $stmt->execute();
    }

    

}

?>
