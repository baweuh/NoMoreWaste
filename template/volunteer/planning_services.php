<?php 

include('../../includes/session.php');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription aux Services</title>
    <link rel="stylesheet" href="styles.css"> <!-- Inclure votre CSS ici -->
    <script>
        // Définir une variable globale pour l'ID du bénévole
        var volunteerId = <?php echo $_SESSION['user_id']; ?>;
    </script>
</head>
<body>
    <h1>Inscription aux Services</h1>
    <nav>
        <ul>
            <?php include('../../includes/menus.php'); ?>
        </ul>
    </nav>

    <form id="serviceForm">
        <label for="service">Choisissez les services que vous pouvez fournir :</label>
        <select id="service" name="service_id" required>
            <!-- Options de services ajoutées dynamiquement -->
        </select>

        <button type="submit">S'inscrire</button>
    </form>

    <script src="../../js/volunteer_services.js"></script> <!-- Inclure le script pour gérer la soumission -->
</body>
</html>
