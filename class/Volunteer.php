<?php
class Volunteer {
    private $conn;
    private $table_name = "benevoles";

    public $volunteer_id;
    public $name;
    public $email;
    public $phone;
    public $skills;
    public $status;

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
        $query = "SELECT * FROM " . $this->table_name . " WHERE volunteer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->volunteer_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (name, email, phone, skills, status) VALUES (:name, :email, :phone, :skills, :status)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':skills', $this->skills);
        $stmt->bindParam(':status', $this->status);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name = :name, email = :email, phone = :phone, skills = :skills, status = :status WHERE volunteer_id = :volunteer_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':volunteer_id', $this->volunteer_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':skills', $this->skills);
        $stmt->bindParam(':status', $this->status);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE volunteer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->volunteer_id);
        return $stmt->execute();
    }
}
?>
