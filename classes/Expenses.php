<?php

require_once 'Bird.php';
require_once 'Medicine.php';
require_once 'Feed.php';

//require_once 'Supplier.php';

class Expenses {

    private $db;
    private $user_id;
    private $from_date;
    private $to_date;

    public function __construct($db, $user_id, $from_date = '', $to_date = '') {
        $this->db = $db;
        $this->user_id = $user_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function getBirdData() {
        // Query to get birds data
        $query = "SELECT * FROM birds WHERE user_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND date BETWEEN :from_date AND :to_date";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        if ($this->from_date && $this->to_date) {
            $stmt->bindParam(':from_date', $this->from_date);
            $stmt->bindParam(':to_date', $this->to_date);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Instantiate the Bird class if needed
        $bird = new Bird($this->db); // Assuming you pass the database connection to Bird class

        foreach ($results as &$result) {
            $result['type'] = 'bird';
            $result['date'] = $result['date'];
            $result['detail'] = "Import " . $result['bird_type'];
            $result['paid_to'] = $bird->getSupplier($result['sup_id'], $this->db);
            $result['amount'] = $result['total_cost'];
        }

        return $results;
    }

    public function getMedicineData() {
        $medicine = new Medicine($this->db);
        $bird = new Bird($this->db);
        $query = "SELECT * FROM buy_medicine WHERE user_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND created_at BETWEEN :from_date AND :to_date";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        if ($this->from_date && $this->to_date) {
            $stmt->bindParam(':from_date', $this->from_date);
            $stmt->bindParam(':to_date', $this->to_date);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$result) {
            $result['type'] = 'medicine';
            $result['date'] = $result['created_at'];
            $result['detail'] = "Import " . $medicine->getMedName($result['med_id']);
            $result['paid_to'] = $bird->getSupplier($result['sup_id'], $this->db);
            $result['amount'] = $result['total'];
        }
        return $results;
    }

    public function getFeedData() {
        $feed = new Feed($this->db);
        $bird = new Bird($this->db);
        $query = "SELECT * FROM buy_feed WHERE user_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND created_at BETWEEN :from_date AND :to_date";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        if ($this->from_date && $this->to_date) {
            $stmt->bindParam(':from_date', $this->from_date);
            $stmt->bindParam(':to_date', $this->to_date);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$result) {
            $result['type'] = 'feed';
            $result['date'] = $result['created_at'];
            $result['detail'] = "Import " . $feed->getFeedName($result['feed_id']);
            $result['paid_to'] = $bird->getSupplier($result['sup_id'], $this->db);
            $result['amount'] = $result['total'];
        }
        return $results;
    }

    public function getMiscData() {
        $bird = new Bird($this->db);
        $query = "SELECT * FROM miscellaneous WHERE user_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND created_at BETWEEN :from_date AND :to_date";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        if ($this->from_date && $this->to_date) {
            $stmt->bindParam(':from_date', $this->from_date);
            $stmt->bindParam(':to_date', $this->to_date);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$result) {
            $result['type'] = 'miscellaneous';
            $result['date'] = $result['created_at'];
            $result['detail'] = "Import " . $result['misc_name'];
            $result['paid_to'] = $bird->getSupplier($result['sup_id'], $this->db);
            $result['amount'] = $result['total'];
        }
        return $results;
    }

    public function getAllData() {
        $bird_data = $this->getBirdData();
        $medicine_data = $this->getMedicineData();
        $feed_data = $this->getFeedData();
        $misc_data = $this->getMiscData();

        $all_data = array_merge($bird_data, $medicine_data, $feed_data, $misc_data);

        // Sort data by date
        usort($all_data, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $all_data;
    }

    public function getAllDataCategorized() {
        return [
            'total_birds' => array_sum(array_column($this->getBirdData(), 'amount')),
            'total_medicine' => array_sum(array_column($this->getMedicineData(), 'amount')),
            'total_feeds' => array_sum(array_column($this->getFeedData(), 'amount')),
            'total_miscellaneous' => array_sum(array_column($this->getMiscData(), 'amount')),
        ];
    }

    public function getTotalAmount() {
        return array_sum(array_column($this->getAllData(), 'amount'));
    }

}
