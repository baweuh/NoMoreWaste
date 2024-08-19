<?php
require_once 'session.php'; // Inclure le fichier de session

// Détruire la session
session_unset();
session_destroy();

// Rediriger vers la page de connexion ou d'accueil
header("Location: ../index.php");
exit();
