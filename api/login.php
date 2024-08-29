<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once '../includes/Database.php';
require_once '../class/User.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->username) && isset($data->password)) {
    $username = $data->username;
    $password = $data->password;

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    if ($user->login($username, $password)) {
        $_SESSION['role'] = $user->getRole();
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['username'] = $user->username;
        $_SESSION['statut'] = $user->statut;

        if ($_SESSION['statut'] == 0) {
            echo json_encode([
                "success" => false,
                "message" => "Compte en cours de vérification"
            ]);
        } else if ($_SESSION['statut'] == 2) {
            echo json_encode([
                "success" => false,
                "message" => "Compte refusé ou banni par l'admin"
            ]);
        } else {
            echo json_encode([
                "success" => true, 
                "message" => "Connexion réussie.", 
                "role" => $_SESSION['role'],
                "user_id" => $_SESSION['user_id'],
                "username" => $_SESSION['username']
            ]);
        }
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "Nom d'utilisateur ou mot de passe incorrect."
        ]);
    }
} else {
    echo json_encode([
        "success" => false, 
        "message" => "Données de connexion manquantes."
    ]);
}
?>
