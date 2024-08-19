<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../includes/Database.php';
require_once '../class/Delivery.php';
require_once '../class/Panier.php';
require('../fpdf/fpdf.php');
session_start();

$database = new Database();
$db = $database->getConnection();
$delivery = new Delivery($db);
$panier = new Panier($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(array("message" => "Invalid JSON data."));
        exit();
    }

    $errors = [];
    
    // Valider et assigner les données
    if (empty($data['customer_id'])) {
        $errors[] = "Customer ID is required";
    } else {
        $delivery->customer_id = $data['customer_id'];
    }

    if (empty($data['recipient_type'])) {
        $errors[] = "Recipient type is required";
    } else {
        $delivery->recipient_type = $data['recipient_type'];
    }

    if (empty($data['address'])) {
        $errors[] = "Address is required";
    } else {
        $delivery->address = $data['address'];
    }

    if (empty($data['city'])) {
        $errors[] = "City is required";
    } else {
        $delivery->city = $data['city'];
    }

    if (empty($data['zipcode'])) {
        $errors[] = "Postal code is required";
    } else {
        $delivery->zipcode = $data['zipcode'];
    }

    // Si pas d'erreurs, procéder à l'ajout
    if (empty($errors)) {
        // Ajouter la tournée
        if ($delivery->AddTournee()) {
            // Récupérer l'ID de la tournée récemment ajoutée
            $delivery->delivery_id = $db->lastInsertId();

            // Ajouter ou mettre à jour l'adresse pour la tournée
            if ($delivery->addAddress()) {
                // Générer le PDF du panier
                $stmt = $panier->ReadByCustomerId($delivery->customer_id);

                if ($stmt->rowCount() > 0) {
                    // Créer une instance de FPDF
                    $pdf = new FPDF();
                    $pdf->AddPage();

                    // Titre
                    $pdf->SetFont('Arial', 'B', 16);
                    $pdf->Cell(0, 10, 'Votre Panier', 0, 1, 'C');

                    // Espacement
                    $pdf->Ln(10);

                    // Entête du tableau
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->Cell(30, 10, 'Produit ID', 1);
                    $pdf->Cell(80, 10, 'Nom du produit', 1);
                    $pdf->Cell(20, 10, 'Quantite', 1);
                    $pdf->Cell(30, 10, 'Date d\'expiration', 1);
                    $pdf->Ln();

                    // Remplir le tableau avec les données du panier
                    $pdf->SetFont('Arial', '', 12);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $pdf->Cell(30, 10, $row['product_id'], 1);
                        $pdf->Cell(80, 10, $row['name'], 1);
                        $pdf->Cell(20, 10, $row['quantity'], 1);
                        $pdf->Cell(30, 10, $row['expiry_date'], 1);
                        $pdf->Ln();
                    }

                    // Sauvegarde du fichier PDF dans le dossier /uploads
                    $uploads_dir = '/nomorewaste/NoMoreWaste/uploads';
                    $fileName = 'panier_' . $delivery->customer_id . '_' . time() . '.pdf';
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . $uploads_dir . '/' . $fileName;
                    $pdf->Output('F', $filePath); // Sauvegarde le PDF

                    // Mettre à jour le chemin du PDF dans la base de données
                    if ($delivery->UpdateTourneePDF($fileName)) {
                        if ($panier->emptyCart($delivery->customer_id)) {
                            http_response_code(200);
                            echo json_encode(array("message" => "Delivery created, address updated, and PDF path updated successfully."));
                        } else {
                            http_response_code(500);
                            echo json_encode(array("message" => "Failed to empty cart."));
                        }
                    } else {
                        http_response_code(500);
                        echo json_encode(array("message" => "Delivery and address updated but failed to update PDF path."));
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Failed to retrieve cart data."));
                }
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Failed to update address after creating delivery."));
            }
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Unable to create delivery"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data.", "errors" => $errors));
    }
}
?>
