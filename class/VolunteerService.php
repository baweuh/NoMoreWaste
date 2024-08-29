<?php

class TourneesBenevoles
{
    private $conn;
    private $table_name = "Tournees_benevoles";

    public $delivery_id;
    public $volunteer_id;
    public $service_id;
    public $date;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (delivery_id, volunteer_id, service_id, date)
                  VALUES (:delivery_id, :volunteer_id, :service_id, :date)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':delivery_id', $this->delivery_id);
        $stmt->bindParam(':volunteer_id', $this->volunteer_id);
        $stmt->bindParam(':service_id', $this->service_id);
        $stmt->bindParam(':date', $this->date);

        return $stmt->execute();
    }

    // Read All
    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Read One
    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE delivery_id = :delivery_id AND volunteer_id = :volunteer_id AND service_id = :service_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':delivery_id', $this->delivery_id);
        $stmt->bindParam(':volunteer_id', $this->volunteer_id);
        $stmt->bindParam(':service_id', $this->service_id);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update
    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET date = :date
                  WHERE delivery_id = :delivery_id AND volunteer_id = :volunteer_id AND service_id = :service_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':delivery_id', $this->delivery_id);
        $stmt->bindParam(':volunteer_id', $this->volunteer_id);
        $stmt->bindParam(':service_id', $this->service_id);

        return $stmt->execute();
    }

    // Delete
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE delivery_id = :delivery_id AND volunteer_id = :volunteer_id AND service_id = :service_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':delivery_id', $this->delivery_id);
        $stmt->bindParam(':volunteer_id', $this->volunteer_id);
        $stmt->bindParam(':service_id', $this->service_id);

        return $stmt->execute();
    }

    public function RegisterForDelivery()
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET volunteer_id = :volunteer_id, date = :date 
                  WHERE delivery_id = :delivery_id AND service_id = :service_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':volunteer_id', $this->volunteer_id);
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':delivery_id', $this->delivery_id);
        $stmt->bindParam(':service_id', $this->service_id);

        return $stmt->execute();
    }

    // Method to get the date of the tournee
    public function GetTourneeDate($delivery_id, $service_id)
    {
        $query = 'SELECT t.delivery_date 
                  FROM Tournees t
                  JOIN Tournees_benevoles tb ON t.delivery_id = tb.delivery_id
                  WHERE t.delivery_id = :delivery_id AND tb.service_id = :service_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':delivery_id', $delivery_id, PDO::PARAM_INT);
        $stmt->bindParam(':service_id', $service_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['delivery_date'] : null;
    }

    // Method to get available slots
    public function getAvailableSlots()
    {
        // SQL query to fetch available slots
        $query = "SELECT date 
                  FROM " . $this->table_name . " 
                  WHERE delivery_id = :delivery_id AND service_id = :service_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':delivery_id', $this->delivery_id);
        $stmt->bindParam(':service_id', $this->service_id);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
