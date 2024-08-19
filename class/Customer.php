<?php
class Customer {
    private $conn;
    private $table_name = "Clients";

    public $customer_id;
    public $name;
    public $email;
    public $phone;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lire tous les clients
    public function read() {
        $query = "SELECT customer_id, name, email, phone FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire un client par ID
    public function readOne() {
        $query = "SELECT customer_id, name, email, phone FROM " . $this->table_name . " WHERE customer_id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->customer_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->name = $row['name'];
        $this->email = $row['email'];
        $this->phone = $row['phone'];
    }

    // Créer un nouveau client
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, email=:email, phone=:phone";
        $stmt = $this->conn->prepare($query);
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        return $stmt->execute();
    }

    // Mettre à jour un client
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name, email=:email, phone=:phone WHERE customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":customer_id", $this->customer_id);
        return $stmt->execute();
    }

    // Supprimer un client
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->customer_id);
        return $stmt->execute();
    }
}
?>
