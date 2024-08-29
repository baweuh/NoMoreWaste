<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Inclure les fichiers nécessaires
require_once '../includes/Database.php';
require_once '../class/VolunteerAssignement.php';
session_start();

$database = new Database();
$db = $database->getConnection();
$volunteerAssignment = new VolunteerAssignment($db);

// Récupérer les données de la requête
$input = json_decode(file_get_contents("php://input"), true);
$volunteer_id = isset($input['volunteer_id']) ? $input['volunteer_id'] : null;
$service_id = isset($input['service_id']) ? $input['service_id'] : null;

if ($volunteer_id === null || $service_id === null) {
    http_response_code(400);
    echo json_encode(array("message" => "Données manquantes."));
    exit();
}

// Enregistrer le service proposé par le bénévole
if (!$volunteerAssignment->registerForService($volunteer_id, $service_id)) {
    http_response_code(500);
    echo json_encode(array("message" => "Erreur lors de l'enregistrement."));
    exit();
}

http_response_code(200);
echo json_encode(array("message" => "Service enregistré avec succès."));
?>
