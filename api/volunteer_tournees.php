<?php
header("Content-Type: application/json; charset=UTF-8");

// Inclure les fichiers nécessaires
require_once '../includes/Database.php';
require_once '../class/Delivery.php';

// Initialiser la connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Initialiser la classe Delivery
$delivery = new Delivery($db);

// Récupérer l'ID du bénévole depuis les paramètres de la requête
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Vérifier si l'ID du bénévole est valide
if ($user_id > 0) {
    // Récupérer les données
    $data = $delivery->GetTourneesParServiceBenevole($user_id);

    // Vérifier si des données ont été récupérées
    if ($data) {
        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Aucune donnée trouvée.",
            "data" => $data
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "ID du bénévole manquant ou invalide."
    ]);
}
