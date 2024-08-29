<?php
header("Content-Type: application/json; charset=UTF-8");

require_once '../includes/Database.php';
require_once '../class/VolunteerService.php';
require_once '../class/Volunteer.php';

// Initialize Database
$database = new Database();
$db = $database->getConnection();

// Initialize TourneesBenevoles Object
$tournee_benevole = new TourneesBenevoles($db);
$benevole = new Volunteer($db);

// Start the session
session_start();

// Get the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Récupère les paramètres de la chaîne de requête
        $delivery_id = isset($_GET['delivery_id']) ? $_GET['delivery_id'] : null;
        $service_id = isset($_GET['service_id']) ? $_GET['service_id'] : null;

        // Vérifie si les deux paramètres sont présents
        if ($delivery_id && $service_id) {
            // Instancie la classe TourneesBenevoles
            $deliveryDate = $tournee_benevole->GetTourneeDate($delivery_id, $service_id);
            $volunteerId = $benevole->getVolunteerId($_SESSION['user_id'], $db);

            // Vérifie si la date est null ou vide
            if ($deliveryDate === null || $deliveryDate === '') {
                echo json_encode(["message" => "La date de livraison est null ou vide."]);
            } else {
                // Renvoie la date de livraison sous forme de JSON
                echo json_encode(["delivery_date" => $deliveryDate, "volunteerId" => $volunteerId]);
            }
        } else {
            echo json_encode(["message" => "Paramètres de livraison manquants."]);
        }
        break;

    case 'PATCH':
        $input = json_decode(file_get_contents("php://input"), true);
        if (!empty($input['dateTime']) && !empty($input['delivery_id']) && !empty($input['service_id'])) {
            $selectedDateTime = $input['dateTime'];
            $delivery_id = $input['delivery_id'];
            $service_id = $input['service_id'];

            // Retrieve delivery date using the new parameters
            $deliveryDate = $tournee_benevole->GetTourneeDate($delivery_id, $service_id);

            if (empty($deliveryDate)) {
                echo json_encode(["message" => "La date de livraison est null ou vide."]);
                exit;
            }

            $selectedDateTimeObj = new DateTime($selectedDateTime);

            // Validate selected datetime
            if (strtotime($selectedDateTime) < strtotime('now')) {
                echo json_encode(["message" => "La date et l'heure doivent être dans le futur."]);
                return;
            }

            if (strtotime($selectedDateTime) > strtotime($deliveryDate)) {
                echo json_encode(["message" => "La date et l'heure doivent être avant la date de la tournée."]);
                return;
            }

            $volunteerId = $benevole->getVolunteerId($_SESSION['user_id'], $db);
            // Set the properties and register the delivery
            $tournee_benevole->delivery_id = $delivery_id;
            $tournee_benevole->volunteer_id = $volunteerId;
            $tournee_benevole->service_id = $service_id;
            $tournee_benevole->date = $selectedDateTimeObj->format('Y-m-d H:i:s');

            if ($tournee_benevole->RegisterForDelivery()) {
                echo json_encode(["message" => "Réservation de créneau réussie."]);
            } else {
                echo json_encode(["message" => "Impossible de réserver le créneau."]);
            }
        } else {
            echo json_encode(["message" => "Données manquantes."]);
        }
        break;

    default:
        echo json_encode(["message" => "Méthode non autorisée."]);
        break;
}
