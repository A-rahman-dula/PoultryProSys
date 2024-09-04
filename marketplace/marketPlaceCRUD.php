<?php
class MarketPlaceCRUD
{
    private $db;
    private $conn;

    public function __construct()
    {
        require_once 'classes/config.php';
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function getProductId(): string
    {
        return isset($_GET["product_id"]) ? $_GET["product_id"] : '';
    }

    public function viewProduct(string $product_id)
    {
        $sql = "SELECT product_id, product_name, quantity, unit, category_id, product_price, product_img, description FROM products WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);

        try {
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return $row;
            } else {
                return 'Product not found';
            }
        } catch (PDOException $e) {
            return 'Database error: ' . $e->getMessage();
        }
    }
}
