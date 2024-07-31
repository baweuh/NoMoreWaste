<?php
// Inclure les fichiers nécessaires
require_once '../includes/Database.php';
require_once '../class/Delivery.php';

// Initialisation de la base de données et de la connexion
$database = new Database();
$db = $database->getConnection();

// Création de l'objet Delivery avec la connexion
$delivery = new Delivery($db);

// Détermination de la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Lecture d'une seule livraison par ID
            $delivery->delivery_id = $_GET['id'];
            $result = $delivery->readOne();

            if ($result) {
                http_response_code(200);
                echo json_encode(array(
                    "delivery_id" => $result['delivery_id'],
                    "delivery_date" => $result['delivery_date'],
                    "recipient_type" => $result['recipient_type'],
                    "recipient_id" => $result['recipient_id'],
                    "status" => $result['status'],
                    "pdf_report_path" => $result['pdf_report_path'] // Chemin vers le PDF
                ));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Delivery not found."));
            }
        } else {
            // Lecture de toutes les livraisons
            $stmt = $delivery->read();
            $num = $stmt->rowCount(); // Utiliser rowCount() pour vérifier le nombre de lignes

            if ($num > 0) {
                $deliveries_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $delivery_item = array(
                        "delivery_id" => $row['delivery_id'],
                        "delivery_date" => $row['delivery_date'],
                        "recipient_type" => $row['recipient_type'],
                        "recipient_id" => $row['recipient_id'],
                        "status" => $row['status'],
                        "pdf_report_path" => $row['pdf_report_path'] // Inclure le chemin du fichier PDF
                    );
                    array_push($deliveries_arr, $delivery_item);
                }
                http_response_code(200);
                echo json_encode($deliveries_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No deliveries found."));
            }
        }
        break;

    case 'POST':
        // Vérifiez les fichiers
        $pdf_report_path = null;
        if (isset($_FILES['pdf_report']) && $_FILES['pdf_report']['error'] === UPLOAD_ERR_OK) {
            $pdf_report_name = basename($_FILES['pdf_report']['name']);
            $upload_dir = '../uploads/'; // Répertoire de destination des fichiers téléchargés
            $pdf_report_path = $upload_dir . $pdf_report_name;

            // Déplacer le fichier vers le répertoire de destination
            if (move_uploaded_file($_FILES['pdf_report']['tmp_name'], $pdf_report_path)) {
                // Succès du téléversement
            } else {
                $pdf_report_path = null;
                $errors[] = "Failed to move uploaded file.";
            }
        } else {
            $errors[] = "No file uploaded or upload error.";
        }

        // Vérifiez chaque champ et générez des messages d'erreur détaillés
        $errors = [];

        if (empty($_POST['delivery_date'])) {
            $errors[] = "Delivery date is required.";
        } else {
            $delivery_date = $_POST['delivery_date'];
        }

        if (empty($_POST['recipient_type'])) {
            $errors[] = "Recipient type is required.";
        } else {
            $recipient_type = $_POST['recipient_type'];
        }

        if (empty($_POST['recipient_id'])) {
            $errors[] = "Recipient ID is required.";
        } else {
            $recipient_id = $_POST['recipient_id'];
        }

        if (empty($errors)) {
            // Assigner les valeurs aux propriétés de l'objet $delivery
            $delivery->delivery_date = $delivery_date;
            $delivery->recipient_type = $recipient_type;
            $delivery->recipient_id = $recipient_id;
            $delivery->status = isset($status) ? $status : 0;
            $delivery->pdf_report_path = $pdf_report_path; // Stocker le chemin du fichier PDF

            if ($delivery->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Delivery created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create delivery."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data.", "errors" => $errors));
        }
        break;

    case 'PUT':
        // Lire les données brutes
        parse_str(file_get_contents("php://input"), $data);

        // Initialisation des erreurs
        $errors = [];

        // Vérification de la présence de l'ID de livraison
        if (empty($data['delivery_id'])) {
            $errors[] = "Delivery ID is required.";
        } else {
            $delivery_id = $data['delivery_id'];

            // Récupération des données existantes pour la livraison
            $delivery->delivery_id = $delivery_id;
            $existing_data = $delivery->readOne();

            // Vérification si la livraison existe
            if (!$existing_data) {
                http_response_code(404);
                echo json_encode(array("message" => "Delivery not found."));
                break;
            }
        }

        // Validation des champs à mettre à jour
        if (empty($data['delivery_date']) && empty($data['recipient_type']) && empty($data['recipient_id']) && empty($data['status']) && !isset($data['pdf_report'])) {
            $errors[] = "At least one field (delivery_date, recipient_type, recipient_id, status) or pdf_report must be provided.";
        }

        // Si des erreurs sont présentes, renvoyer une réponse d'erreur
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data.", "errors" => $errors));
            break;
        }

        // Mise à jour des données avec les nouvelles valeurs ou garder les anciennes
        $delivery_date = $data['delivery_date'] ?? $existing_data['delivery_date'];
        $recipient_type = $data['recipient_type'] ?? $existing_data['recipient_type'];
        $recipient_id = $data['recipient_id'] ?? $existing_data['recipient_id'];
        $status = $data['status'] ?? $existing_data['status'];

        // Gestion du fichier PDF
        $pdf_report_path = $existing_data['pdf_report_path']; // Conserver le chemin du fichier existant
        if (isset($data['pdf_report']) && !empty($data['pdf_report'])) {
            $pdf_report = base64_decode($data['pdf_report']);
            $pdf_report_name = uniqid() . '.pdf'; // Générer un nom de fichier unique
            $upload_dir = '../uploads/';
            $pdf_report_path = $upload_dir . $pdf_report_name;

            if (file_put_contents($pdf_report_path, $pdf_report) === false) {
                http_response_code(500);
                echo json_encode(array("message" => "Failed to save the uploaded PDF file."));
                break;
            }
        }

        // Assigner les valeurs aux propriétés de l'objet Delivery
        $delivery->delivery_date = $delivery_date;
        $delivery->recipient_type = $recipient_type;
        $delivery->recipient_id = $recipient_id;
        $delivery->status = $status;
        $delivery->pdf_report_path = $pdf_report_path;

        // Effectuer la mise à jour
        if ($delivery->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Delivery updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update delivery."));
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $delivery->delivery_id = $_GET['id'];
            if ($delivery->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Delivery deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete delivery."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No delivery ID provided."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
