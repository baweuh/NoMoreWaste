<?php
header("Content-Type: application/json; charset=UTF-8");

require_once '../includes/Database.php';
require_once '../class/TourneesBenevoles.php';

// Initialize Database
$database = new Database();
$db = $database->getConnection();

// Initialize TourneesBenevoles Object
$tournee = new TourneesBenevoles($db);

// Get the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'POST':
        if (
            !empty($input['delivery_id']) &&
            !empty($input['volunteer_id']) &&
            !empty($input['service_id']) &&
            !empty($input['date'])
        ) {
            $tournee->delivery_id = $input['delivery_id'];
            $tournee->volunteer_id = $input['volunteer_id'];
            $tournee->service_id = $input['service_id'];
            $tournee->date = $input['date'];

            if ($tournee->create()) {
                echo json_encode(["message" => "Tournée ajoutée avec succès."]);
            } else {
                echo json_encode(["message" => "Impossible d'ajouter la tournée."]);
            }
        } else {
            echo json_encode(["message" => "Données manquantes."]);
        }
        break;

    case 'GET':
        if (
            !empty($_GET['delivery_id']) &&
            !empty($_GET['volunteer_id']) &&
            !empty($_GET['service_id'])
        ) {
            $tournee->delivery_id = $_GET['delivery_id'];
            $tournee->volunteer_id = $_GET['volunteer_id'];
            $tournee->service_id = $_GET['service_id'];

            $result = $tournee->readOne();
            echo json_encode($result);
        } else {
            $result = $tournee->read();
            echo json_encode($result);
        }
        break;

    case 'PUT':
        if (
            !empty($input['delivery_id']) &&
            !empty($input['volunteer_id']) &&
            !empty($input['service_id']) &&
            !empty($input['date'])
        ) {
            $tournee->delivery_id = $input['delivery_id'];
            $tournee->volunteer_id = $input['volunteer_id'];
            $tournee->service_id = $input['service_id'];
            $tournee->date = $input['date'];

            if ($tournee->update()) {
                echo json_encode(["message" => "Tournée mise à jour avec succès."]);
            } else {
                echo json_encode(["message" => "Impossible de mettre à jour la tournée."]);
            }
        } else {
            echo json_encode(["message" => "Données manquantes."]);
        }
        break;

    case 'DELETE':
        if (
            !empty($input['delivery_id']) &&
            !empty($input['volunteer_id']) &&
            !empty($input['service_id'])
        ) {
            $tournee->delivery_id = $input['delivery_id'];
            $tournee->volunteer_id = $input['volunteer_id'];
            $tournee->service_id = $input['service_id'];

            if ($tournee->delete()) {
                echo json_encode(["message" => "Tournée supprimée avec succès."]);
            } else {
                echo json_encode(["message" => "Impossible de supprimer la tournée."]);
            }
        } else {
            echo json_encode(["message" => "Données manquantes."]);
        }
        break;

    default:
        echo json_encode(["message" => "Méthode non autorisée."]);
        break;
}
