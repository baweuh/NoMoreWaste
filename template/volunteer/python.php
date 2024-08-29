<?php
include('../../includes/session.php');
include('../../includes/menus.php');

// Chemin vers le script Python
$pythonScript = '../../python/generate_planning.py';  // Chemin relatif au fichier PHP
$uploadDirectory = '../../uploads/plannings.xlsx'; // Chemin vers le fichier généré

// Vérifier l'existence du script Python et du répertoire de sortie
if (!file_exists($pythonScript)) {
    die("Erreur : Le script Python n'existe pas.");
}

if (!is_writable(dirname($uploadDirectory))) {
    die("Erreur : Le répertoire de sortie n'est pas accessible en écriture.");
}

// Exécuter le script Python
$output = shell_exec("python $pythonScript 2>&1");

// Définir l'encodage des caractères pour éviter les problèmes de sortie
header('Content-Type: text/html; charset=utf-8');

// Vérifier si le fichier a été correctement créé
if (!file_exists($uploadDirectory)) {
    echo "Erreur : Le fichier Excel n'a pas été créé.<br><pre>$output</pre>";
    exit();
}

echo "Le fichier Excel a été généré avec succès. <a href='$uploadDirectory'>Télécharger le planning</a>.";
?>
