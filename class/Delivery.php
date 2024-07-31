<?php
class Delivery {
    private $conn;
    public $delivery_id;
    public $delivery_date;
    public $recipient_type;
    public $recipient_id;
    public $status;
    public $pdf_report_path;

    public function __construct($db) {
        $this->conn = $db;
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function read() {
        $query = "SELECT * FROM tournees";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }    

    public function readOne() {
        $query = "SELECT * FROM tournees WHERE delivery_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->delivery_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO tournees (delivery_date, recipient_type, recipient_id, status, pdf_report_path) VALUES (:delivery_date, :recipient_type, :recipient_id, :status, :pdf_report_path)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':delivery_date', $this->delivery_date);
        $stmt->bindParam(':recipient_type', $this->recipient_type);
        $stmt->bindParam(':recipient_id', $this->recipient_id);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':pdf_report_path', $this->pdf_report_path);
        return $stmt->execute();
    }    

    public function update() {
        $query = "UPDATE tournees SET delivery_date = :delivery_date, recipient_type = :recipient_type, recipient_id = :recipient_id, status = :status, pdf_report_path = :pdf_report_path WHERE delivery_id = :delivery_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':delivery_id', $this->delivery_id);
        $stmt->bindParam(':delivery_date', $this->delivery_date);
        $stmt->bindParam(':recipient_type', $this->recipient_type);
        $stmt->bindParam(':recipient_id', $this->recipient_id);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':pdf_report_path', $this->pdf_report_path);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM tournees WHERE delivery_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->delivery_id]);
    }
}
?>
