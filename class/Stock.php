<?php
class Stock {
    private $conn;
    private $table_name = "Stocks";

    public $stock_id;
    public $product_id;
    public $quantity;
    public $location;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE stock_id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->stock_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->product_id = $row['product_id'];
            $this->quantity = $row['quantity'];
            $this->location = $row['location'];
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (product_id, quantity, location) VALUES (:product_id, :quantity, :location)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":location", $this->location);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET product_id=:product_id, quantity=:quantity, location=:location WHERE stock_id = :stock_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":stock_id", $this->stock_id);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":location", $this->location);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE stock_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->stock_id);
        return $stmt->execute();
    }
}
?>
