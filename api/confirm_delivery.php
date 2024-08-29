<?php
header("Content-Type: application/json; charset=UTF-8");

// Inclure les fichiers nécessaires
require_once '../includes/Database.php';
require_once '../class/Delivery.php';

// Initialiser la connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Récupérer les données JSON envoyées
$data = json_decode(file_get_contents("php://input"));

// Vérifier si les données nécessaires sont présentes
if (!isset($data->delivery_id) || !isset($data->user_id) || !isset($data->action)) {
    echo json_encode([
        "status" => "error",
        "message" => "ID de livraison, ID utilisateur ou action manquante."
    ]);
    exit;
}

// Initialiser la classe Delivery
$delivery = new Delivery($db);

// Gérer les différentes actions avec un switch case
switch ($data->action) {
    case 'confirm_recovery':
        $result = $delivery->confirmRecovery($data->delivery_id, $data->user_id);
        if ($result) {
            echo json_encode([
                "status" => "success",
                "message" => "Récupération confirmée."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Échec de la confirmation de la récupération."
            ]);
        }
        break;

    case 'confirm_delivery':
        $result = $delivery->confirmDelivery($data->delivery_id, $data->user_id);
        if ($result) {
            echo json_encode([
                "status" => "success",
                "message" => "Livraison confirmée."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Échec de la confirmation de la livraison."
            ]);
        }
        break;

    default:
        echo json_encode([
            "status" => "error",
            "message" => "Action non reconnue."
        ]);
        break;
}
?>
