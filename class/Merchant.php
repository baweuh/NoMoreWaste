<?php
class Merchant {
    private $conn;
    private $table_name = "commercants";

    public $merchant_id;
    public $name;
    public $address;
    public $phone;
    public $email;
    public $membership_start_date;
    public $membership_end_date;
    public $renewal_reminder_sent;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create Merchant
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (name, address, phone, email, membership_start_date, ) :name, :address, =:phone, =:email, =:membership_start_date, membership_end_date=:membership_end_date, renewal_reminder_sent=:renewal_reminder_sent";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->membership_start_date = htmlspecialchars(strip_tags($this->membership_start_date));
        $this->membership_end_date = htmlspecialchars(strip_tags($this->membership_end_date));
        $this->renewal_reminder_sent = htmlspecialchars(strip_tags($this->renewal_reminder_sent));

        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":membership_start_date", $this->membership_start_date);
        $stmt->bindParam(":membership_end_date", $this->membership_end_date);
        $stmt->bindParam(":renewal_reminder_sent", $this->renewal_reminder_sent);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read single Merchant
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE merchant_id = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->merchant_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->address = $row['address'];
            $this->phone = $row['phone'];
            $this->email = $row['email'];
            $this->membership_start_date = $row['membership_start_date'];
            $this->membership_end_date = $row['membership_end_date'];
            $this->renewal_reminder_sent = $row['renewal_reminder_sent'];
        }
    }

    // Update Merchant
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name = :name, address = :address, phone = :phone, email = :email, membership_start_date = :membership_start_date, membership_end_date = :membership_end_date, renewal_reminder_sent = :renewal_reminder_sent WHERE merchant_id = :merchant_id";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->membership_start_date = htmlspecialchars(strip_tags($this->membership_start_date));
        $this->membership_end_date = htmlspecialchars(strip_tags($this->membership_end_date));
        $this->renewal_reminder_sent = htmlspecialchars(strip_tags($this->renewal_reminder_sent));
        $this->merchant_id = htmlspecialchars(strip_tags($this->merchant_id));

        // bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':membership_start_date', $this->membership_start_date);
        $stmt->bindParam(':membership_end_date', $this->membership_end_date);
        $stmt->bindParam(':renewal_reminder_sent', $this->renewal_reminder_sent);
        $stmt->bindParam(':merchant_id', $this->merchant_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete Merchant
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE merchant_id = ?";
        $stmt = $this->conn->prepare($query);

        $this->merchant_id = htmlspecialchars(strip_tags($this->merchant_id));
        $stmt->bindParam(1, $this->merchant_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
