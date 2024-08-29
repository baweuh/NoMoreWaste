<?php

include('../../includes/session.php');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournées du Bénévole</title>
    <script src="../../js/load_volunteer_tournees.js"></script>
</head>
<body>
    <h1>Liste des Tournées Prises en Charge par Vous</h1>
    <nav>
        <ul>
            <?php include('../../includes/menus.php'); ?>
        </ul>
    </nav>
    <table id="tours-table" border="1">
        <thead>
            <tr>
                <th>ID Tournée</th>
                <th>Date</th>
                <th>Heure de Début</th>
                <th>Heure de Fin</th>
                <th>Adresse</th>
                <th>Statut</th>
                <th>Bénévole</th>
                <th>Service</th>
            </tr>
        </thead>
        <tbody>
            <!-- Les données seront insérées ici via JavaScript -->
        </tbody>
    </table>
    <script>
        const userID = <?php echo $_SESSION['user_id']; ?>
    </script>
</body>
</html>
