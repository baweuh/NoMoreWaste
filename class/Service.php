<?php
class Service
{
    private $conn;
    private $table_name = "Services";

    public $service_id;
    public $name;
    public $description;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function Read()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function ReadOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE service_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->service_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
        }
    }

    public function Create()
    {
        $query = "INSERT INTO " . $this->table_name . " (name, description) VALUES (:name, :description)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);

        return $stmt->execute();
    }

    public function Update()
    {
        $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description WHERE service_id = :service_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':service_id', $this->service_id);

        return $stmt->execute();
    }

    public function Delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE service_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->service_id);

        return $stmt->execute();
    }

    public function exists($service_id) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE service_id = :service_id";

        // Préparer la requête
        $stmt = $this->conn->prepare($query);

        // Lier le service_id
        $stmt->bindParam(":service_id", $service_id, PDO::PARAM_INT);

        // Exécuter la requête
        $stmt->execute();

        // Obtenir le nombre de lignes
        $count = $stmt->fetchColumn();

        // Retourner true si le service existe, sinon false
        return $count > 0;
    }
}
