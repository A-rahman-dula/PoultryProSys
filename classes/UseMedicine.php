<?php

require_once 'crud.php';

class UseMedicine implements crud {

    private $conn;
    private $table_name = "use_medicine";
    private $user_id;
    private $med_id;
    private $quantity;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters
    public function getUser_id() {
        return $this->user_id;
    }

    public function getMed_id() {
        return $this->med_id;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    // Setters
    public function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    public function setMed_id($med_id) {
        $this->med_id = $med_id;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    // CRUD methods
    public function create($user_id) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, med_id, quantity) 
                  VALUES (:user_id, :med_id, :quantity)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':med_id', $this->med_id);
        $stmt->bindParam(':quantity', $this->quantity);

        return $stmt->execute();
    }

    public function read($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readOne($useMed_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE useMed_id = :useMed_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':useMed_id', $useMed_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->med_id = $row['med_id'];
            $this->user_id = $row['user_id'];
            $this->quantity = $row['quantity'];
        }

        return $row;
    }

    public function update($useMed_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET med_id = :med_id, quantity = :quantity
                  WHERE useMed_id = :useMed_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':useMed_id', $useMed_id);
        $stmt->bindParam(':med_id', $this->med_id);
        $stmt->bindParam(':quantity', $this->quantity);

        return $stmt->execute();
    }

    public function delete($useMed_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE useMed_id = :useMed_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':useMed_id', $useMed_id);

        return $stmt->execute();
    }

    public function getUsage($med_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE med_id = :med_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':med_id', $med_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
