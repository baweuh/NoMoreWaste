<?php
class Collection
{
    private $conn;
    private $table_name = "collectes";

    // Propriétés
    public $collection_id;
    public $merchant_id;
    public $collection_date;
    public $total_items;
    public $status;
    public $created_at;
    public $updated_at;

    // Constructeur
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lire toutes les collectes
    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY collection_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire une collecte spécifique
    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE collection_id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->collection_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->merchant_id = $row['merchant_id'];
            $this->collection_date = $row['collection_date'];
            $this->total_items = $row['total_items'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
        }
    }

    // Créer une nouvelle collecte
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET merchant_id=:merchant_id, collection_date=:collection_date, total_items=:total_items, status=:status";
        $stmt = $this->conn->prepare($query);

        // Sécuriser les données
        $this->merchant_id = htmlspecialchars(strip_tags($this->merchant_id));
        $this->collection_date = htmlspecialchars(strip_tags($this->collection_date));
        $this->total_items = htmlspecialchars(strip_tags($this->total_items));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Lier les paramètres
        $stmt->bindParam(":merchant_id", $this->merchant_id);
        $stmt->bindParam(":collection_date", $this->collection_date);
        $stmt->bindParam(":total_items", $this->total_items);
        $stmt->bindParam(":status", $this->status);

        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Mettre à jour une collecte existante
    public function update()
    {
        $query = "UPDATE collectes
                  SET merchant_id = :merchant_id, collection_date = :collection_date, total_items = :total_items, status = :status
                  WHERE collection_id = :collection_id";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->merchant_id = htmlspecialchars(strip_tags($this->merchant_id));
        $this->collection_date = htmlspecialchars(strip_tags($this->collection_date));
        $this->total_items = htmlspecialchars(strip_tags($this->total_items));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->collection_id = htmlspecialchars(strip_tags($this->collection_id));

        // Lier les variables
        $stmt->bindParam(':merchant_id', $this->merchant_id);
        $stmt->bindParam(':collection_date', $this->collection_date);
        $stmt->bindParam(':total_items', $this->total_items);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':collection_id', $this->collection_id);

        // Exécuter la requête
        return $stmt->execute();
    }

    // Supprimer une collecte
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE collection_id = ?";
        $stmt = $this->conn->prepare($query);

        // Sécuriser les données
        $this->collection_id = htmlspecialchars(strip_tags($this->collection_id));

        // Lier le paramètre
        $stmt->bindParam(1, $this->collection_id);

        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
