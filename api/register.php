<?php
header("Content-Type: application/json; charset=UTF-8");

require_once '../includes/Database.php';
require_once '../class/User.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->username) && isset($data->password) && isset($data->role)) {
    $username = $data->username;
    $password = $data->password;
    $role = $data->role;

    $rolesAllowed = ['clients', 'commercants', 'benevoles'];
    if (!in_array($role, $rolesAllowed)) {
        echo json_encode(["success" => false, "message" => "Rôle invalide."]);
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    // Enregistrement de l'utilisateur
    if ($user->register($username, $password, $role)) {
        echo json_encode(["success" => true, "message" => "Inscription réussie."]);
    } else {
        echo json_encode(["success" => false, "message" => "Impossible de créer un compte."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Données d'inscription manquantes."]);
}
?>
