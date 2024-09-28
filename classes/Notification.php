<?php

class Notification {

    private $conn;
    private $user_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    function getUser_id() {
        return $this->user_id;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    public function getMedNotificationCount() {
        $query = "SELECT COUNT(*) AS notification_count FROM medicine WHERE 
            (SELECT COALESCE(SUM(quantity), 0) FROM buy_medicine WHERE med_id = medicine.med_id) - 
            (SELECT COALESCE(SUM(quantity), 0) FROM use_medicine WHERE med_id = medicine.med_id) <= least_quantity AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['notification_count'];
    }

    public function getMedNotifications() {
        $query = "SELECT med_name FROM medicine WHERE 
            (SELECT COALESCE(SUM(quantity), 0) FROM buy_medicine WHERE med_id = medicine.med_id) - 
            (SELECT COALESCE(SUM(quantity), 0) FROM use_medicine WHERE med_id = medicine.med_id) <= least_quantity 
            AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();

        $notifications = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $notifications[] = $row['med_name'];
        }

        return $notifications;
    }

    public function getFeedNotificationCount() {
        $query = "SELECT COUNT(*) AS notification_count FROM feed WHERE 
            (SELECT COALESCE(SUM(quantity), 0) FROM buy_feed WHERE feed_id = feed.feed_id) - 
            (SELECT COALESCE(SUM(quantity), 0) FROM use_feed WHERE feed_id = feed.feed_id) <= least_quantity AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['notification_count'];
    }

    public function getFeedNotifications() {
        $query = "SELECT feed_name FROM feed WHERE 
            (SELECT COALESCE(SUM(quantity), 0) FROM buy_feed WHERE feed_id = feed.feed_id) - 
            (SELECT COALESCE(SUM(quantity), 0) FROM use_feed WHERE feed_id = feed.feed_id) <= least_quantity 
            AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();

        $notifications = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $notifications[] = $row['feed_name'];
        }

        return $notifications;
    }

    public function getProductsNotificationCount() {
        $query = "
       SELECT COUNT(*) AS notification_count
       FROM (
           SELECT p.product_name, p.least_quantity, SUM(ps.quantity) - COALESCE(SUM(o.quantity), 0) AS available_stock
           FROM product_stock ps
           JOIN products p ON ps.product_id = p.product_id
           LEFT JOIN orders o ON ps.product_id = o.product_id
           WHERE ps.user_id = :user_id
           GROUP BY ps.product_id, p.product_name, p.least_quantity
           HAVING available_stock <= p.least_quantity
       ) AS subquery
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['notification_count'] ?? 0; // If no row is returned, default to 0
    }

    public function getProductsNotifications() {
        $query = "
       SELECT p.product_name, p.least_quantity, SUM(ps.quantity) - COALESCE(SUM(o.quantity), 0) AS available_stock
       FROM product_stock ps
       JOIN products p ON ps.product_id = p.product_id
       LEFT JOIN orders o ON ps.product_id = o.product_id
       WHERE ps.user_id = :user_id
       GROUP BY ps.product_id, p.product_name, p.least_quantity
       HAVING available_stock <= p.least_quantity
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();

        $notifications = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $notifications[] = $row['product_name'];
        }

        return $notifications;
    }

    // New method to get the total notification count
    public function getAllNotificationCount() {
        $med_count = $this->getMedNotificationCount();
        $feed_count = $this->getFeedNotificationCount();
        $product_count = $this->getProductsNotificationCount();

        return $med_count + $feed_count + $product_count;
    }

    // New method to get all notifications combined
    public function getAllNotifications() {
        $med_notifications = $this->getMedNotifications();
        $feed_notifications = $this->getFeedNotifications();
        $product_notifications = $this->getProductsNotifications();

        // Merging the notifications from all sources
        return array_merge($med_notifications, $feed_notifications, $product_notifications);
    }

}
