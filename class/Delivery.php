<?php
class Delivery
{
    private $conn;
    private $deliveries = "tournees";

    public $delivery_id;
    public $delivery_date;
    public $recipient_type;
    public $customer_id;
    public $status;
    public $address;
    public $zipcode;
    public $city;
    public $pdf_report_path;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function Read()
    {
        try {
            $query = "SELECT * FROM tournees";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            echo json_encode(array("message" => "Read operation failed.", "error" => $e->getMessage()));
            return null;
        }
    }

    public function ReadOne()
    {
        try {
            $query = "SELECT * FROM tournees WHERE delivery_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->delivery_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo json_encode(array("message" => "Read operation failed.", "error" => $e->getMessage()));
            return null;
        }
    }

    // Lire toutes les tournées pour un utilisateur
    public function readAll($customer_id)
    {
        $query = "SELECT * FROM " . $this->deliveries . " WHERE customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->execute();
        return $stmt;
    }

    // Lire les tournées par statut
    public function readByStatus($customer_id, $status)
    {
        $query = "SELECT * FROM " . $this->deliveries . " WHERE customer_id = :customer_id AND status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt;
    }

    public function Create()
    {
        try {
            $query = "INSERT INTO tournees (delivery_date, recipient_type, customer_id, status, pdf_report_path) VALUES (:delivery_date, :recipient_type, :customer_id, :status, :pdf_report_path)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':delivery_date', $this->delivery_date);
            $stmt->bindParam(':recipient_type', $this->recipient_type);
            $stmt->bindParam(':customer_id', $this->customer_id);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':pdf_report_path', $this->pdf_report_path);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo json_encode(array("message" => "Create operation failed.", "error" => $e->getMessage()));
            return false;
        }
    }

    public function AddTournee()
    {
        $query = "INSERT INTO " . $this->deliveries . " (delivery_date, recipient_type, customer_id, status) VALUES (NOW(), :recipient_type, :customer_id, 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':recipient_type', $this->recipient_type);
        $stmt->bindParam(':customer_id', $this->customer_id);
        return $stmt->execute();
    }

    public function Update()
    {
        try {
            $query = "UPDATE tournees SET delivery_date = :delivery_date, recipient_type = :recipient_type, customer_id = :customer_id, status = :status, pdf_report_path = :pdf_report_path WHERE delivery_id = :delivery_id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':delivery_date', $this->delivery_date);
            $stmt->bindParam(':recipient_type', $this->recipient_type);
            $stmt->bindParam(':customer_id', $this->customer_id);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':pdf_report_path', $this->pdf_report_path);
            $stmt->bindParam(':delivery_id', $this->delivery_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo json_encode(array("message" => "Update operation failed.", "error" => $e->getMessage()));
            return false;
        }
    }

    public function Delete()
    {
        try {
            $query = "DELETE FROM tournees WHERE delivery_id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$this->delivery_id]);
        } catch (PDOException $e) {
            echo json_encode(array("message" => "Delete operation failed.", "error" => $e->getMessage()));
            return false;
        }
    }

    public function addAddress()
    {
        $query = "UPDATE " . $this->deliveries . " 
                  SET recipient_type = :recipient_type, 
                      address = :address, 
                      city = :city, 
                      zipcode = :zipcode 
                  WHERE delivery_id = :delivery_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':recipient_type', $this->recipient_type);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':zipcode', $this->zipcode);
        $stmt->bindParam(':delivery_id', $this->delivery_id);

        return $stmt->execute();
    }


    public function UpdateTourneePDF($fileName)
    {
        $query = "UPDATE " . $this->deliveries . " 
                  SET pdf_report_path = :pdf_report_path 
                  WHERE delivery_id = :delivery_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':pdf_report_path', $fileName);
        $stmt->bindParam(':delivery_id', $this->delivery_id);

        return $stmt->execute();
    }

    public function cancelDelivery($delivery_id, $customer_id)
    {
        $query = "UPDATE Tournees SET status = 3 WHERE delivery_id = :delivery_id AND customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':delivery_id', $delivery_id);
        $stmt->bindParam(':customer_id', $customer_id);
        return $stmt->execute();
    }

    public function GetTourneesParServiceBenevole($user_id)
    {
        $query = "
        SELECT
            t.delivery_id,
            t.delivery_date,
            t.start_time,
            t.end_time,
            t.pdf_report_path,
            t.address,
            t.status,
            tb.service_id,
            s.name AS service_name,
            b.name AS volunteer_name,
            b.volunteer_id
        FROM
            tournees t
        JOIN
            tournees_benevoles tb ON t.delivery_id = tb.delivery_id
        JOIN
            benevoles_services bs ON bs.service_id = tb.service_id
        JOIN
            benevoles b ON b.volunteer_id = bs.volunteer_id
        JOIN
            services s ON s.service_id = tb.service_id
        WHERE
            t.status = 0
            AND bs.volunteer_id = (
                SELECT
                    b.volunteer_id
                FROM
                    benevoles b
                WHERE
                    b.user_id = :user_id
            )
        ORDER BY
            t.delivery_date;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetTourneesParBenevole($user_id)
    {
        $query = "
        SELECT
            t.delivery_id,
            t.delivery_date,
            t.start_time,
            t.end_time,
            t.pdf_report_path,
            t.address,
            t.status,
            s.name AS service_name,
            b.name AS volunteer_name
        FROM
            " . $this->deliveries . " t
        JOIN
            tournees_benevoles tb ON t.delivery_id = tb.delivery_id
        JOIN
            benevoles b ON tb.volunteer_id = b.volunteer_id
        JOIN
            services s ON tb.service_id = s.service_id
        WHERE
            b.user_id = :user_id
        ORDER BY
            t.delivery_date;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour confirmer la récupération du lot
    public function confirmRecovery($delivery_id, $user_id)
    {
        $query = "
            UPDATE tournees
            SET status = 1  -- Supposons que '1' signifie 'Récupéré'
            WHERE delivery_id = :delivery_id
            AND EXISTS (
                SELECT 1 FROM tournees_benevoles 
                WHERE delivery_id = :delivery_id 
                AND volunteer_id = (
                    SELECT volunteer_id FROM benevoles WHERE user_id = :user_id
                )
            )
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':delivery_id', $delivery_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Méthode pour confirmer la livraison du lot
    public function confirmDelivery($delivery_id, $user_id)
    {
        $query = "
            UPDATE tournees
            SET status = 2  -- Supposons que '2' signifie 'Livré'
            WHERE delivery_id = :delivery_id
            AND EXISTS (
                SELECT 1 FROM tournees_benevoles 
                WHERE delivery_id = :delivery_id 
                AND volunteer_id = (
                    SELECT volunteer_id FROM benevoles WHERE user_id = :user_id
                )
            )
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':delivery_id', $delivery_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Dans la classe Delivery.php
    public function addServiceToDelivery($service_id)
    {
        $query = "INSERT INTO tournees_benevoles (delivery_id, service_id) VALUES (:delivery_id, :service_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':delivery_id', $this->delivery_id);
        $stmt->bindParam(':service_id', $service_id);

        return $stmt->execute();
    }
}
