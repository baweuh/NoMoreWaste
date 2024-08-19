<?php
class Product
{
    private $conn;
    private $product = "produits";
    private $collect = "collectes";

    public $product_id;
    public $barcode;
    public $name;
    public $quantity;
    public $expiry_date;
    public $collection_id;
    public $merchant_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function Read()
    {
        $query = "SELECT * FROM " . $this->product . " ORDER BY product_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function ReadOne()
    {
        $query = "SELECT * FROM produits WHERE product_id = :product_id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->execute();
        return $stmt;
    }

    public function ReadByMerchantId($merchant_id)
    {
        $query = "SELECT p.product_id, p.barcode, p.name, p.quantity, p.expiry_date, p.collection_id, 
                         c.name AS collection_name, c.collection_date, 
                         COUNT(p.product_id) as total_items
                  FROM produits p
                  JOIN collectes c ON p.collection_id = c.collection_id
                  WHERE c.merchant_id = :merchant_id
                  GROUP BY p.product_id, p.barcode, p.name, p.quantity, p.expiry_date, p.collection_id, c.name, c.collection_date";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':merchant_id', $merchant_id);
        $stmt->execute();
        return $stmt;
    }

    public function Create()
    {
        $query = "INSERT INTO " . $this->product . " (barcode, name, quantity, expiry_date, collection_id) VALUES (:barcode, :name, :quantity, :expiry_date, :collection_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":barcode", $this->barcode);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":expiry_date", $this->expiry_date);
        $stmt->bindParam(":collection_id", $this->collection_id);

        return $stmt->execute();
    }

    public function Update()
    {
        $query = "UPDATE " . $this->product . " SET barcode = :barcode, name = :name, quantity = :quantity, expiry_date = :expiry_date, collection_id = :collection_id WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":barcode", $this->barcode);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":expiry_date", $this->expiry_date);
        $stmt->bindParam(":collection_id", $this->collection_id);
        $stmt->bindParam(":product_id", $this->product_id);

        return $stmt->execute();
    }

    public function Delete()
    {
        $query = "DELETE FROM " . $this->product . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->product_id);
        return $stmt->execute();
    }

    public function ReadByStatus()
    {
        $query = "SELECT p.*, c.collection_id, c.name AS collection_name, c.merchant_id, c.status FROM " . $this->product . " p 
                  JOIN " . $this->collect . " c 
                  ON c.collection_id = p.collection_id 
                  WHERE c.status = 'completed' AND quantity > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function UpdateQuantity($product_id, $quantity)
    {
        $query = "UPDATE " . $this->product . " SET quantity = quantity + :quantity WHERE product_id = :product_id AND quantity + :quantity >= 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
