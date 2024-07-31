<?php
class Product {
    private $conn;
    private $table_name = "produits";

    public $product_id;
    public $barcode;
    public $name;
    public $quantity;
    public $expiry_date;
    public $collection_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY product_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->product_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->barcode = $row['barcode'];
        $this->name = $row['name'];
        $this->quantity = $row['quantity'];
        $this->expiry_date = $row['expiry_date'];
        $this->collection_id = $row['collection_id'];
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (barcode, name, quantity, expiry_date, collection_id) VALUES (:barcode, :name, :quantity, :expiry_date, :collection_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":barcode", $this->barcode);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":expiry_date", $this->expiry_date);
        $stmt->bindParam(":collection_id", $this->collection_id);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET barcode = :barcode, name = :name, quantity = :quantity, expiry_date = :expiry_date, collection_id = :collection_id WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":barcode", $this->barcode);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":expiry_date", $this->expiry_date);
        $stmt->bindParam(":collection_id", $this->collection_id);
        $stmt->bindParam(":product_id", $this->product_id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->product_id);
        return $stmt->execute();
    }
}
?>
