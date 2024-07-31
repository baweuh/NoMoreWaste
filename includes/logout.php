<?php
require_once 'session.php'; // Inclure le fichier de session
require_once 'bdd.php';    // Inclure le fichier de connexion à la base de données

session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Préparation de la requête SQL pour supprimer le jeton
    $sql = "UPDATE users SET remember_token = NULL WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $userId);

    try {
        $stmt->execute();
    } catch (PDOException $e) {
        // Gérer les erreurs éventuelles
        error_log("Erreur lors de la suppression du token : " . $e->getMessage());
    }
}

// Détruire la session
session_unset();
session_destroy();

// Rediriger vers la page de connexion ou d'accueil
header("Location: ../index.html"); // Modifier cette ligne pour pointer vers votre page de connexion
exit();
?>
