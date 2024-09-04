<?php

class Incomes
{
    private $conn;
    private $user_id;
    private $from_date;
    private $to_date;

    public function __construct($db, $user_id, $from_date = '', $to_date = '')
    {
        $this->conn = $db;
        $this->user_id = $user_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function getOrderData()
    {
        $query = "SELECT * FROM orders WHERE farmer_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND ordered_date BETWEEN :from_date AND :to_date";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        if ($this->from_date && $this->to_date) {
            $stmt->bindParam(':from_date', $this->from_date);
            $stmt->bindParam(':to_date', $this->to_date);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$result) {
            $result['type'] = 'order';
            $result['date'] = $result['ordered_date'];
            $result['detail'] = "Sold " . $result['product_id'];
            $result['received_from'] = $result['cus_id'];
            $result['total'] = $result['total'];
        }
        return $results;
    }

    public function getAllData()
    {
        $order_data = $this->getOrderData();
        $all_data = $order_data;

        // Sort data by date
        usort($all_data, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $all_data;
    }

    public function getTotalAmount()
    {
        $all_data = $this->getAllData();
        $total_amount = array_sum(array_column($all_data, 'amount'));
        return $total_amount;
    }
}
?>
