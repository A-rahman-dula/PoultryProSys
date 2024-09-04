<?php

class User
{

    private $conn;
    private $table_name = "user";
    public $user_id;
    public $username;
    public $role;
    public $address;
    public $city;
    public $mobile;
    public $email;
    public $password;
    public $status;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function emailExists()
    {
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function register()
    {
        $query = "INSERT INTO " . $this->table_name . " (username, role, address, city, mobile, email, password,status) VALUES (:username, :role, :address, :city, :mobile, :email, :password, :status)";
        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->mobile = htmlspecialchars(strip_tags($this->mobile));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":mobile", $this->mobile);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":status", $this->status);


        if ($stmt->execute()) {
            $this->user_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function login()
    {
        $query = "SELECT user_id, username, password,role,status FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($this->password, $row['password'])) {
            $this->user_id = $row['user_id'];
            $this->username = $row['username'];
            $this->role = $row['role'];
            $this->status = $row['status'];

            return true;
        }
        return false;
    }
    
    
}
