<?php

class Panier
{
    private $conn;
    private $panier = "Panier";
    private $produit = "Produits";

    // Propriétés du panier
    public $panier_id;
    public $customer_id;
    public $product_id;
    public $quantity;
    public $validated;
    public $added_at;

    // Constructeur pour la connexion à la base de données
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function ReadByCustomerId($customer_id)
    {
        $query = "SELECT pa.*, pr.product_id, pr.name, pr.expiry_date FROM " . $this->panier . " pa
                 JOIN " . $this->produit . " pr
                 ON pr.product_id = pa.product_id 
                 WHERE customer_id = :customer_id
                 AND validated = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_id", $customer_id);
        $stmt->execute();
        return $stmt;
    }

    public function Read()
    {
        $query = "SELECT * FROM " . $this->panier;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function ReadOne($panier_id)
    {
        $query = "SELECT * FROM " . $this->panier . " WHERE panier_id = :panier_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":panier_id", $panier_id);
        $stmt->execute();
        return $stmt;
    }

    public function Create()
    {
        $query = "INSERT INTO " . $this->panier . " (customer_id, product_id, quantity) VALUES (:customer_id, :product_id, :quantity)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':quantity', $this->quantity);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update($panier_id, $quantity) {
        $query = "UPDATE " . $this->panier . " SET quantity = :quantity WHERE panier_id = :panier_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':panier_id', $panier_id);
        $stmt->bindParam(':quantity', $quantity);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Ajouter un produit au panier
    public function addProduct()
    {
        // Vérifier si le produit existe déjà dans le panier
        $query = "SELECT quantity FROM " . $this->panier . " WHERE customer_id = :customer_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        // Bind des valeurs
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':product_id', $this->product_id);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Si le produit existe, on met à jour la quantité
            $new_quantity = $row['quantity'] + $this->quantity;
            $query = "UPDATE " . $this->panier . " SET quantity = :quantity WHERE customer_id = :customer_id AND product_id = :product_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quantity', $new_quantity);
        } else {
            // Sinon, on insère un nouveau produit dans le panier
            $query = "INSERT INTO " . $this->panier . " (customer_id, product_id, quantity) VALUES (:customer_id, :product_id, :quantity)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quantity', $this->quantity);
        }

        // Bind des valeurs
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':product_id', $this->product_id);

        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Récupérer tous les produits du panier pour un client
    public function getCartByCustomerId($customer_id)
    {
        $query = "SELECT p.*, pr.name, pr.price FROM " . $this->panier . " p 
                  JOIN Products pr ON p.product_id = pr.product_id
                  WHERE p.customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->execute();

        return $stmt;
    }

    // Supprimer un produit du panier
    public function removeProduct()
    {
        // Commencez par récupérer l'ID du produit associé au panier
        $query = "SELECT product_id FROM " . $this->panier . " WHERE panier_id = :panier_id";
        $stmt = $this->conn->prepare($query);
    
        // Bind des valeurs
        $stmt->bindParam(':panier_id', $this->panier_id);
    
        // Exécuter la requête
        if ($stmt->execute()) {
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            $productId = $product['product_id'];
    
            // Incrémenter la quantité du produit dans la table product
            $updateQuery = "UPDATE produits SET quantity = quantity + 1 WHERE product_id = :product_id";
            $updateStmt = $this->conn->prepare($updateQuery);
    
            // Bind de l'ID du produit
            $updateStmt->bindParam(':product_id', $productId);
    
            // Exécuter la requête de mise à jour
            if ($updateStmt->execute()) {
                // Maintenant, supprimez le produit du panier
                $deleteQuery = "DELETE FROM " . $this->panier . " WHERE panier_id = :panier_id";
                $deleteStmt = $this->conn->prepare($deleteQuery);
    
                // Bind des valeurs
                $deleteStmt->bindParam(':panier_id', $this->panier_id);
    
                // Exécuter la requête de suppression
                if ($deleteStmt->execute()) {
                    return true;
                }
            }
        }
    
        return false;
    }
    

    // Vider le panier pour un client donné (par exemple, après un paiement)
    public function emptyCart($customer_id)
    {
        $query = "DELETE FROM " . $this->panier . " WHERE customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $customer_id);

        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function addToCart()
    {
        // Début de la transaction
        $this->conn->beginTransaction();

        try {
            // Ajouter le produit au panier
            $query = "INSERT INTO " . $this->panier . " (customer_id, product_id, quantity) 
                      VALUES (:customer_id, :product_id, :quantity)
                      ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";

            $stmt = $this->conn->prepare($query);

            $this->customer_id = htmlspecialchars(strip_tags($this->customer_id));
            $this->product_id = htmlspecialchars(strip_tags($this->product_id));
            $this->quantity = htmlspecialchars(strip_tags($this->quantity));

            $stmt->bindParam(':customer_id', $this->customer_id);
            $stmt->bindParam(':product_id', $this->product_id);
            $stmt->bindParam(':quantity', $this->quantity);

            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'ajout au panier : " . implode(", ", $stmt->errorInfo()));
            }

            // Décrémenter la quantité dans la table Produits
            $query = "UPDATE Produits SET quantity = quantity - :quantity WHERE product_id = :product_id AND quantity >= :quantity";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_id', $this->product_id);
            $stmt->bindParam(':quantity', $this->quantity);

            if (!$stmt->execute() || $stmt->rowCount() === 0) {
                throw new Exception("Erreur lors de la mise à jour de la quantité du produit : " . implode(", ", $stmt->errorInfo()));
            }

            // Si tout s'est bien passé, on valide la transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // En cas d'erreur, on annule la transaction
            $this->conn->rollBack();
            error_log("Exception: " . $e->getMessage());
            return false;
        }
    }

    public function generateTicketAndSave($tourneeId)
    {
        // Récupérer les entrées du panier pour le customer_id actuel
        $stmt = $this->ReadByCustomerId($this->customer_id);
        $panierItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($panierItems)) {
            throw new Exception("Le panier est vide.");
        }

        // Générer le ticket de caisse en PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(40, 10, 'Ticket de Caisse');
        $pdf->Ln(20); // Nouvelle ligne
        $pdf->SetFont('Arial', '', 12);

        foreach ($panierItems as $item) {
            $pdf->Cell(0, 10, $item['name'] . ' - Quantité: ' . $item['quantity']);
            $pdf->Ln(10);
        }

        // Nom du fichier PDF
        $fileName = 'ticket_' . $this->customer_id . '_' . time() . '.pdf';
        $filePath = __DIR__ . '/../uploads/' . $fileName;

        // Enregistrer le fichier PDF
        $pdf->Output($filePath, 'F');

        // Stocker le chemin du fichier en base de données dans la table Tournees
        $this->savePdfPathToTournee($tourneeId, $fileName);

        return $filePath;
    }

    private function savePdfPathToTournee($tourneeId, $fileName)
    {
        $query = "UPDATE Tournees 
                  SET pdf_report_path = :filePath 
                  WHERE delivery_id = :tourneeId";
        $stmt = $this->conn->prepare($query);
        $filePath = '/uploads/' . $fileName;
        $stmt->bindParam(':filePath', $filePath);
        $stmt->bindParam(':tourneeId', $tourneeId);
        $stmt->execute();
    }
}
