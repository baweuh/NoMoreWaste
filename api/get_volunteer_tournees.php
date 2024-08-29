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

// Obtenir l'ID de l'utilisateur connecté
$user_id = $_GET['user_id'] ?? null;

// Vérifier si l'ID de l'utilisateur est présent
if (!$user_id) {
    echo json_encode([
        "status" => "error",
        "message" => "ID du bénévole manquant ou invalide."
    ]);
    exit;
}

// Récupérer les tournées prises en charge par le bénévole
$data = $delivery->GetTourneesParServiceBenevole($user_id);

// Vérifier si des données ont été récupérées
if ($data) {
    // Renvoie les données au format JSON
    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);
} else {
    // Renvoie une erreur si aucune donnée n'a été trouvée
    echo json_encode([
        "status" => "error",
        "message" => "Aucune tournée trouvée."
    ]);
}
