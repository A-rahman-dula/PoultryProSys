<?php

class Supplier {

    private $conn;
    private $table_name = "supplier";
    // Properties
    private $sup_id;
    private $sup_name;
    private $address;
    private $mobile;
    private $city;
    private $email;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Setter methods
    public function setSup_id($id) {
        $this->sup_id = $id;
    }

    public function setSup_name($name) {
        $this->sup_name = $name;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function setMobile($mobile) {
        $this->mobile = $mobile;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    // Getter methods
    public function getSup_id() {
        return $this->sup_id;
    }

    public function getSup_name() {
        return $this->sup_name;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getCity() {
        return $this->city;
    }

    public function getEmail() {
        return $this->email;
    }

    // Method to create a new supplier
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (sup_name, address, mobile, city, email) VALUES (:sup_name, :address, :mobile, :city, :email)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':sup_name', $this->sup_name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':mobile', $this->mobile);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':email', $this->email);

        // Execute the query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Method to update supplier details
    public function update($sup_id) {
        $query = "UPDATE " . $this->table_name . " SET 
                  sup_name = :sup_name, 
                  address = :address, 
                  mobile = :mobile, 
                  city = :city, 
                  email = :email
                  WHERE sup_id = :sup_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':sup_name', $this->sup_name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':mobile', $this->mobile);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':sup_id', $sup_id);

        // Execute the query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Method to delete a supplier
    public function delete($sup_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE sup_id = :sup_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sup_id', $sup_id);
        return $stmt->execute();
    }

    // Method to read one supplier's details
    public function readOne($sup_id) {
        $query = "SELECT sup_name, address, mobile, city, email FROM " . $this->table_name . " WHERE sup_id = :sup_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sup_id', $sup_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to read all suppliers
    public function readAll() {
        $query = "SELECT sup_id, sup_name, address, mobile, city, email FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function supplierExists($user_id) {
        // Modify the query to remove spaces and make it case-insensitive
        $query = "SELECT sup_id FROM " . $this->table_name . " 
              WHERE LOWER(REPLACE(sup_name, ' ', '')) = LOWER(REPLACE(:sup_name, ' ', '')) 
              AND user_id = :user_id 
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sup_name', $this->sup_name);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function supplierEmailExists($user_id) {

        $query = "SELECT sup_id FROM " . $this->table_name . " WHERE email = :email AND user_id = :user_id  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

}

?>
