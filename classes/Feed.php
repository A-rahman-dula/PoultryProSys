<?php

require_once 'crud.php';

class Feed implements crud {

    private $conn;
    private $table_name = "feed";
    private $user_id;
    private $feed_name;
    private $description;

    public function __construct($db) {
        $this->conn = $db;
    }

// Getters
    function getUser_id() {
        return $this->user_id;
    }

    function getFeed_name() {
        return $this->feed_name;
    }

    function getDescription() {
        return $this->description;
    }

// Setters
    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    function setFeed_name($feed_name) {
        $this->feed_name = $feed_name;
    }

    function setDescription($description) {
        $this->description = $description;
    }

// Create a new feed
    public function create($user_id) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, feed_name, description) VALUES (:user_id, :feed_name, :description)";
        $stmt = $this->conn->prepare($query);

// Sanitize input
        $this->feed_name = htmlspecialchars(strip_tags($this->feed_name));
        $this->description = htmlspecialchars(strip_tags($this->description));

// Bind parameters
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':feed_name', $this->feed_name);
        $stmt->bindParam(':description', $this->description);

        return $stmt->execute();
    }

// Read feeds by user ID
    public function read($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Read a single feed by ID
    public function readOne($feed_id) {
        $query = "SELECT feed_name, description FROM " . $this->table_name . " WHERE feed_id = :feed_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':feed_id', $feed_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->feed_name = $row['feed_name'];
            $this->description = $row['description'];
        }

        return $row;
    }

// Update a feed
    public function update($feed_id) {
        $query = "UPDATE " . $this->table_name . " SET feed_name = :feed_name, description = :description WHERE feed_id = :feed_id";

        $stmt = $this->conn->prepare($query);

// Sanitize input
        $this->feed_name = htmlspecialchars(strip_tags($this->feed_name));
        $this->description = htmlspecialchars(strip_tags($this->description));

// Bind values
        $stmt->bindParam(':feed_id', $feed_id);
        $stmt->bindParam(':feed_name', $this->feed_name);
        $stmt->bindParam(':description', $this->description);

        return $stmt->execute();
    }

// Delete a feed
    public function delete($feed_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE feed_id = :feed_id";
        $stmt = $this->conn->prepare($query);

// Bind the feed_id parameter
        $stmt->bindParam(':feed_id', $feed_id);

// Execute the query
        return $stmt->execute();
    }

// Get the name of a feed by ID
    public function getFeedName($feed_id) {
// Prepare the SQL query
        $query = "SELECT feed_name FROM " . $this->table_name . " WHERE feed_id = :feed_id";
        $stmt = $this->conn->prepare($query);

// Bind the parameters
        $stmt->bindParam(':feed_id', $feed_id);

// Execute the query
        if ($stmt->execute()) {
// Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if a result was returned
            if ($result) {
                return $result['feed_name'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getAllSuppliers($user_id) {
// Prepare the SQL query
        $query = "SELECT sup_id,sup_name FROM supplier WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calculateStock($feed_id, $usage) {
        $buyFeed = new BuyFeed($this->conn);
        $purchases = $buyFeed->getStockSortedByDate($feed_id);

        $remaining_stock = [];
        $total_stock_value = 0;
        $available_stock = 0;

        // Initialize remaining stock array
        foreach ($purchases as $purchase) {
            $remaining_stock[] = [
                'buyfeed_id' => $purchase['buyfeed_id'],
                'unit_price' => $purchase['unit_price'],
                'quantity' => $purchase['quantity'],
                'remaining' => $purchase['quantity']
            ];
        }

        // Apply usage to stock using FIFO
        foreach ($usage as $used) {
            $remaining_quantity = $used['quantity'];
            foreach ($remaining_stock as &$stock) {
                if ($remaining_quantity <= 0) {
                    break;
                }

                if ($stock['remaining'] > 0) {
                    if ($stock['remaining'] >= $remaining_quantity) {
                        $stock['remaining'] -= $remaining_quantity;
                        $remaining_quantity = 0;
                    } else {
                        $remaining_quantity -= $stock['remaining'];
                        $stock['remaining'] = 0;
                    }
                }
            }
        }

        // Calculate total remaining stock value
        for ($i = 0; $i < count($remaining_stock); $i++) {
            if ($remaining_stock[$i]['remaining'] > 0) {
                $total_stock_value += $remaining_stock[$i]['remaining'] * $remaining_stock[$i]['unit_price'];
                $available_stock += $remaining_stock[$i]['remaining'];
            }
        }


        return [
            'available_stock' => $available_stock,
            'stock_value' => $total_stock_value
        ];
    }

    public function feedExists($user_id) {
        // Modify the query to remove spaces and make it case-insensitive
        $query = "SELECT feed_id FROM " . $this->table_name . " 
              WHERE LOWER(REPLACE(feed_name, ' ', '')) = LOWER(REPLACE(:feed_name, ' ', '')) 
              AND user_id = :user_id 
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':feed_name', $this->feed_name);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

}
