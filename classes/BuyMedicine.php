<?php

require_once 'crud.php';

class BuyMedicine implements crud {

    private $conn;
    private $table_name = "buy_medicine";
    private $user_id;
    private $med_id;
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

    function getMed_id() {
        return $this->med_id;
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

    function setMed_id($med_id) {
        $this->med_id = $med_id;
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
        $query = "INSERT INTO " . $this->table_name . " (user_id, med_id, sup_id, unit_price, quantity, total) 
                  VALUES (:user_id, :med_id, :sup_id, :unit_price, :quantity, :total)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':med_id', $this->med_id);
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

    public function readOne($buymed_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE buymed_id = :buymed_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':buymed_id', $buymed_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->med_id = $row['med_id'];
            $this->user_id = $row['user_id'];
            $this->sup_id = $row['sup_id'];
            $this->unit_price = $row['unit_price'];
            $this->quantity = $row['quantity'];
            $this->total = $row['total'];
        }

        return $row;
    }

    public function update($buymed_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET med_id = :med_id, sup_id = :sup_id, unit_price = :unit_price, quantity = :quantity, total = :total 
                  WHERE buymed_id = :buymed_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':buymed_id', $buymed_id);
        $stmt->bindParam(':med_id', $this->med_id);
        $stmt->bindParam(':sup_id', $this->sup_id);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':total', $this->total);

        return $stmt->execute();
    }

    public function delete($buymed_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE buymed_id = :buymed_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':buymed_id', $buymed_id);

        return $stmt->execute();
    }

    public function getPurchases($med_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE med_id = :med_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':med_id', $med_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStockSortedByDate($med_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE med_id = :med_id ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':med_id', $med_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
