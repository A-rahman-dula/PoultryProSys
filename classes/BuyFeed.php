<?php

require_once 'crud.php';

class BuyFeed implements crud {

    private $conn;
    private $table_name = "buy_feed";
    private $user_id;
    private $feed_id;
    private $sup_id;
    private $unit_price;
    private $quantity;
    private $total;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters
    function getUser_id() {
        return $this->user_id;
    }

    function getFeed_id() {
        return $this->feed_id;
    }

    function getSup_id() {
        return $this->sup_id;
    }

    function getUnit_price() {
        return $this->unit_price;
    }

    function getQuantity() {
        return $this->quantity;
    }

    function getTotal() {
        return $this->total;
    }

    // Setters
    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    function setFeed_id($feed_id) {
        $this->feed_id = $feed_id;
    }

    function setSup_id($sup_id) {
        $this->sup_id = $sup_id;
    }

    function setUnit_price($unit_price) {
        $this->unit_price = $unit_price;
    }

    function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    function setTotal($total) {
        $this->total = $total;
    }

    // CRUD methods
    public function create($user_id) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, feed_id, sup_id, unit_price, quantity, total) 
                  VALUES (:user_id, :feed_id, :sup_id, :unit_price, :quantity, :total)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':feed_id', $this->feed_id);
        $stmt->bindParam(':sup_id', $this->sup_id);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':quantity', $this->quantity);
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

    public function readOne($buyFeed_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE buyfeed_id = :buyfeed_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':buyfeed_id', $buyfeed_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->feed_id = $row['feed_id'];
            $this->user_id = $row['user_id'];
            $this->sup_id = $row['sup_id'];
            $this->unit_price = $row['unit_price'];
            $this->quantity = $row['quantity'];
            $this->total = $row['total'];
        }

        return $row;
    }

    public function update($buyfeed_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET feed_id = :feed_id, sup_id = :sup_id, unit_price = :unit_price, quantity = :quantity, total = :total 
                  WHERE buyfeed_id = :buyfeed_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':buyfeed_id', $buyfeed_id);
        $stmt->bindParam(':feed_id', $this->feed_id);
        $stmt->bindParam(':sup_id', $this->sup_id);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':total', $this->total);

        return $stmt->execute();
    }

    public function delete($buyfeed_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE buyfeed_id = :buyfeed_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':buyfeed_id', $buyfeed_id);

        return $stmt->execute();
    }

   
    public function getPurchases($feed_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE feed_id = :feed_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':feed_id', $feed_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStockSortedByDate($feed_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE feed_id = :feed_id ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':feed_id', $feed_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
