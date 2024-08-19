<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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

// Ajouter un journal de débogage pour le type de méthode
error_log("HTTP Method: " . $method);

switch ($method) {
        // Dans la partie 'GET' pour lire toutes les livraisons
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
                    "customer_id" => $result['customer_id'] ?? 'N/A', // Assurez-vous que customer_id n'est pas null
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
                        "customer_id" => $row['customer_id'] ?? 'N/A', // Assurez-vous que customer_id n'est pas null
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
        $errors = [];
        $pdf_report_path = null;

        // Vérifiez les fichiers
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
        }

        // Vérifiez chaque champ et générez des messages d'erreur détaillés
        if (empty($_POST['delivery_date'])) {
            $errors[] = "Delivery date is required.";
        } else {
            $delivery->delivery_date = $_POST['delivery_date'];
        }

        if (empty($_POST['recipient_type'])) {
            $errors[] = "customer type is required.";
        } else {
            $delivery->recipient_type = $_POST['recipient_type'];
        }

        if (empty($_POST['customer_id'])) {
            $errors[] = "customer ID is required.";
        } else {
            $delivery->customer_id = $_POST['customer_id'];
        }

        if (isset($_POST['status'])) {
            $delivery->status = $_POST['status'];
        } else {
            $delivery->status = 0;
        }

        $delivery->pdf_report_path = $pdf_report_path; // Stocker le chemin du fichier PDF

        if (empty($errors)) {
            if ($delivery->create()) {
                http_response_code(200);
                echo json_encode(array("message" => "Delivery created."));
                error_log("Delivery created successfully.");
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Unable to create delivery."));
                error_log("Failed to create delivery.");
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data.", "errors" => $errors));
            error_log("Errors: " . implode(", ", $errors));
        }
        break;

    case 'PUT':
        // Lire les données brutes de la requête PUT
        parse_str(file_get_contents("php://input"), $data);

        // Assurez-vous que les données sont bien reçues
        error_log("PUT Data: " . print_r($data, true));

        // Initialisation des erreurs
        $errors = [];

        // Vérifier si delivery_id est fourni dans les données
        if (empty($data['delivery_id'])) {
            $errors[] = "Delivery ID is required.";
        } else {
            $delivery_id = $data['delivery_id'];
            $delivery->delivery_id = $delivery_id;

            // Récupération des données existantes pour la livraison
            $existing_data = $delivery->readOne();

            // Vérifier si la livraison existe
            if (!$existing_data) {
                http_response_code(404);
                echo json_encode(array("message" => "Delivery not found."));
                error_log("Delivery ID not found: " . $delivery_id);
                break;
            }

            // Mise à jour des données avec les nouvelles valeurs ou garder les anciennes
            $delivery->delivery_date = $data['delivery_date'] ?? $existing_data['delivery_date'];
            $delivery->recipient_type = $data['recipient_type'] ?? $existing_data['recipient_type'];
            $delivery->customer_id = $data['customer_id'] ?? $existing_data['customer_id'];
            $delivery->status = $data['status'] ?? $existing_data['status'];

            // Gestion du fichier PDF
            $pdf_report_path = $existing_data['pdf_report_path']; // Conserver le chemin du fichier existant
            if (isset($data['pdf_report']) && !empty($data['pdf_report'])) {
                // Décoder le fichier PDF reçu en base64
                $pdf_report = base64_decode($data['pdf_report']);
                $pdf_report_name = uniqid() . '.pdf'; // Générer un nom de fichier unique
                $upload_dir = '../uploads/';
                $pdf_report_path = $upload_dir . $pdf_report_name;

                if (file_put_contents($pdf_report_path, $pdf_report) === false) {
                    http_response_code(500);
                    echo json_encode(array("message" => "Failed to save the uploaded PDF file."));
                    error_log("Failed to save PDF file: " . $pdf_report_path);
                    break;
                }
            }

            $delivery->pdf_report_path = $pdf_report_path;

            // Effectuer la mise à jour
            if ($delivery->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Delivery updated."));
                error_log("Delivery updated successfully.");
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update delivery."));
                error_log("Failed to update delivery.");
            }
        }

        // S'il manque des erreurs, renvoyer une réponse d'erreur
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data.", "errors" => $errors));
            error_log("Update errors: " . implode(", ", $errors));
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $delivery->delivery_id = $_GET['id'];
            if ($delivery->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Delivery deleted."));
                error_log("Delivery deleted successfully.");
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete delivery."));
                error_log("Failed to delete delivery.");
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No delivery ID provided."));
            error_log("No delivery ID provided.");
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        error_log("Method not allowed: " . $method);
        break;
}
