<?php

require_once '../classes/crud.php';

class UseFeed implements crud {

    private $conn;
    private $table_name = "use_feed";
    private $user_id;
    private $feed_id;
    private $quantity;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters
    public function getUser_id() {
        return $this->user_id;
    }

    public function getFeed_id() {
        return $this->feed_id;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    // Setters
    public function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    public function setFeed_id($feed_id) {
        $this->feed_id = $feed_id;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    // CRUD methods
    public function create($user_id) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, feed_id, quantity) 
                  VALUES (:user_id, :feed_id, :quantity)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':feed_id', $this->feed_id);
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

    public function readOne($useFeed_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE useFeed_id = :useFeed_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':useFeed_id', $useFeed_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->feed_id = $row['feed_id'];
            $this->user_id = $row['user_id'];
            $this->quantity = $row['quantity'];
        }

        return $row;
    }

    public function update($useFeed_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET feed_id = :feed_id, quantity = :quantity
                  WHERE useFeed_id = :useFeed_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':useFeed_id', $useFeed_id);
        $stmt->bindParam(':feed_id', $this->feed_id);
        $stmt->bindParam(':quantity', $this->quantity);

        return $stmt->execute();
    }

    public function delete($useFeed_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE useFeed_id = :useFeed_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':useFeed_id', $useFeed_id);

        return $stmt->execute();
    }

    // New method to get usage by feed_id
    public function getUsage($feed_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE feed_id = :feed_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':feed_id', $feed_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
