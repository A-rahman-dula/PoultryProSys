<?php

require_once 'crud.php';

class Medicine implements crud {

    private $conn;
    private $table_name = "medicine";
    private $user_id;
    private $med_name;
    private $description;

    public function __construct($db) {
        $this->conn = $db;
    }

// Getters
    function getUser_id() {
        return $this->user_id;
    }

    function getMed_name() {
        return $this->med_name;
    }

    function getDescription() {
        return $this->description;
    }

// Setters
    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    function setMed_name($med_name) {
        $this->med_name = $med_name;
    }

    function setDescription($description) {
        $this->description = $description;
    }

// Create a new feed
    public function create($user_id) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, med_name, description) VALUES (:user_id, :med_name, :description)";
        $stmt = $this->conn->prepare($query);

// Sanitize input
        $this->med_name = htmlspecialchars(strip_tags($this->med_name));
        $this->description = htmlspecialchars(strip_tags($this->description));

// Bind parameters
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':med_name', $this->med_name);
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
    public function readOne($med_id) {
        $query = "SELECT med_name, description FROM " . $this->table_name . " WHERE med_id = :med_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':med_id', $med_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->med_name = $row['med_name'];
            $this->description = $row['description'];
        }

        return $row;
    }

// Update a feed
    public function update($feed_id) {
        $query = "UPDATE " . $this->table_name . " SET med_name = :med_name, description = :description WHERE med_id = :med_id";

        $stmt = $this->conn->prepare($query);

// Sanitize input
        $this->med_name = htmlspecialchars(strip_tags($this->med_name));
        $this->description = htmlspecialchars(strip_tags($this->description));

// Bind values
        $stmt->bindParam(':med_id', $feed_id);
        $stmt->bindParam(':med_name', $this->med_name);
        $stmt->bindParam(':description', $this->description);

        return $stmt->execute();
    }

// Delete a feed
    public function delete($med_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE med_id = :med_id";
        $stmt = $this->conn->prepare($query);

// Bind the feed_id parameter
        $stmt->bindParam(':med_id', $med_id);

// Execute the query
        return $stmt->execute();
    }

// Get the name of a feed by ID
    public function getMedName($med_id) {
// Prepare the SQL query
        $query = "SELECT med_name FROM " . $this->table_name . " WHERE med_id = :med_id";
        $stmt = $this->conn->prepare($query);

// Bind the parameters
        $stmt->bindParam(':med_id', $med_id);

// Execute the query
        if ($stmt->execute()) {
// Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if a result was returned
            if ($result) {
                return $result['med_name'];
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

    public function calculateStock($med_id, $usage) {
        $buyMedi = new BuyMedicine($this->conn);
        $purchases = $buyMedi->getStockSortedByDate($med_id);

        $remaining_stock = [];
        $total_stock_value = 0;
        $available_stock = 0;

        // Initialize remaining stock array
        foreach ($purchases as $purchase) {
            $remaining_stock[] = [
                'buyMed_id' => $purchase['buyMedicine_id'],
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

    public function medicineExists($user_id) {
        // Modify the query to remove spaces and make it case-insensitive
        $query = "SELECT med_id FROM " . $this->table_name . " 
              WHERE LOWER(REPLACE(med_name, ' ', '')) = LOWER(REPLACE(:med_name, ' ', '')) 
              AND user_id = :user_id 
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':med_name', $this->med_name);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

}
