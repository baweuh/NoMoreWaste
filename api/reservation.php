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

error_log("HTTP Method: " . $method);

switch($method) {
    case 'PUT':
        parse_str(file_get_contents("php://input"), $data);

        error_log("PUT Data: " . print_r($data, true));

        $errors = [];

        if (empty($data['delivery_id'])) {
            $errors[] = "Delivery ID is required";
        } else {
            $delivery_id = $data['delivery_id'];
            $delivery->delivery_id = $delivery_id;

            $existing_data = $delivery->ReadOne();

            if (!$existing_data) {
                http_response_code(404);
                echo json_encode(array("message" => "Delivery not found"));
                error_log("Delivery ID not found: " . $delivery_id);
                break;
            }
        }
}