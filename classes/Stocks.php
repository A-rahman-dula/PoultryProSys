<?php

class Stocks {

    private $conn;
    private $farm_id;
    private $product;

    public function __construct($db, $farm_id) {
        $this->conn = $db;
        $this->farm_id = $farm_id;
        $this->product = new Product($db);
    }

    private function buildDateCondition($from_date, $to_date, $table_alias) {
        if (empty($from_date) && empty($to_date)) {
            return '';
        }
        if (empty($from_date)) {
            return " AND $table_alias.created_at <= :to_date";
        }
        if (empty($to_date)) {
            return " AND $table_alias.created_at >= :from_date";
        }
        return " AND $table_alias.created_at BETWEEN :from_date AND :to_date";
    }

    public function getAllStockData($from_date, $to_date) {
        $dateCondition = $this->buildDateCondition($from_date, $to_date, 'b');

        $query = "
            SELECT f.feed_name AS detail, 
                   SUM(b.quantity - COALESCE(u.quantity, 0)) AS quantity, 
                   SUM(b.unit_price * (b.quantity - COALESCE(u.quantity, 0))) AS amount
            FROM buy_feed b
            JOIN feed f ON b.feed_id = f.feed_id
            LEFT JOIN use_feed u ON b.feed_id = u.feed_id AND b.created_at <= u.created_at
            WHERE b.user_id = :farm_id
            $dateCondition
            GROUP BY f.feed_id, f.feed_name
            ORDER BY f.feed_name
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':farm_id', $this->farm_id);
        if (!empty($from_date)) {
            $stmt->bindParam(':from_date', $from_date);
        }
        if (!empty($to_date)) {
            $stmt->bindParam(':to_date', $to_date);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalStockAmount($from_date, $to_date) {
        $dateCondition = $this->buildDateCondition($from_date, $to_date, 'b');

        $query = "
            SELECT SUM(b.unit_price * (b.quantity - COALESCE(u.quantity, 0))) AS total_amount
            FROM buy_feed b
            LEFT JOIN use_feed u ON b.feed_id = u.feed_id AND b.created_at <= u.created_at
            WHERE b.user_id = :farm_id
            $dateCondition
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':farm_id', $this->farm_id);
        if (!empty($from_date)) {
            $stmt->bindParam(':from_date', $from_date);
        }
        if (!empty($to_date)) {
            $stmt->bindParam(':to_date', $to_date);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total_amount'] : 0;
    }

    public function getAllMedicineStockData($from_date, $to_date) {
        $dateCondition = $this->buildDateCondition($from_date, $to_date, 'b');

        $query = "
            SELECT m.med_name AS detail, 
                   SUM(b.quantity - COALESCE(u.quantity, 0)) AS quantity, 
                   SUM(b.unit_price * (b.quantity - COALESCE(u.quantity, 0))) AS amount
            FROM buy_medicine b
            JOIN medicine m ON b.med_id = m.med_id
            LEFT JOIN use_medicine u ON b.med_id = u.med_id AND b.created_at <= u.created_at
            WHERE b.user_id = :farm_id
            $dateCondition
            GROUP BY m.med_id, m.med_name
            ORDER BY m.med_name
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':farm_id', $this->farm_id);
        if (!empty($from_date)) {
            $stmt->bindParam(':from_date', $from_date);
        }
        if (!empty($to_date)) {
            $stmt->bindParam(':to_date', $to_date);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalMedicineStockAmount($from_date, $to_date) {
        $dateCondition = $this->buildDateCondition($from_date, $to_date, 'b');

        $query = "
            SELECT SUM(b.unit_price * (b.quantity - COALESCE(u.quantity, 0))) AS total_amount
            FROM buy_medicine b
            LEFT JOIN use_medicine u ON b.med_id = u.med_id AND b.created_at <= u.created_at
            WHERE b.user_id = :farm_id
            $dateCondition
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':farm_id', $this->farm_id);
        if (!empty($from_date)) {
            $stmt->bindParam(':from_date', $from_date);
        }
        if (!empty($to_date)) {
            $stmt->bindParam(':to_date', $to_date);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total_amount'] : 0;
    }

    public function getAllProductStockData($from_date, $to_date) {
        $dateCondition = $this->buildDateCondition($from_date, $to_date, 'ps');

        $query = "
            SELECT p.product_name AS detail, 
                   SUM(ps.quantity - COALESCE(o.quantity, 0)) AS quantity, 
                   SUM(ps.unit_price * (ps.quantity - COALESCE(o.quantity, 0))) AS amount
            FROM product_stock ps
            JOIN products p ON ps.product_id = p.product_id
            LEFT JOIN orders o ON ps.product_id = o.product_id AND ps.created_at <= o.created_at
            WHERE ps.user_id = :farm_id
            $dateCondition
            GROUP BY p.product_id, p.product_name
            ORDER BY p.product_name
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':farm_id', $this->farm_id);
        if (!empty($from_date)) {
            $stmt->bindParam(':from_date', $from_date);
        }
        if (!empty($to_date)) {
            $stmt->bindParam(':to_date', $to_date);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalProductStockAmount($from_date, $to_date) {
        $dateCondition = $this->buildDateCondition($from_date, $to_date, 'ps');

        $query = "
            SELECT SUM(ps.unit_price * (ps.quantity - COALESCE(o.quantity, 0))) AS total_amount
            FROM product_stock ps
            LEFT JOIN orders o ON ps.product_id = o.product_id AND ps.created_at <= o.created_at
            WHERE ps.user_id = :farm_id
            $dateCondition
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':farm_id', $this->farm_id);
        if (!empty($from_date)) {
            $stmt->bindParam(':from_date', $from_date);
        }
        if (!empty($to_date)) {
            $stmt->bindParam(':to_date', $to_date);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total_amount'] : 0;
    }

    public function getTotalStockValue($from_date, $to_date) {
        $totalFeedStockAmount = $this->getTotalStockAmount($from_date, $to_date);
        $totalMedicineStockAmount = $this->getTotalMedicineStockAmount($from_date, $to_date);
        $totalProductStockAmount = $this->getTotalProductStockAmount($from_date, $to_date);

        return $totalFeedStockAmount + $totalMedicineStockAmount + $totalProductStockAmount;
    }

    public function getAllDataCategorized($from_date, $to_date) {
        return [
            'total_products' => $this->getTotalProductStockAmount($from_date, $to_date),
            'total_medicine' => array_sum(array_column($this->getAllMedicineStockData($from_date, $to_date), 'amount')),
            'total_feeds' => array_sum(array_column($this->getAllStockData($from_date, $to_date), 'amount')),
        ];
    }

}
?>
