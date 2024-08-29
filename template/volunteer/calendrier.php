<?php
include('../../includes/session.php');

// Récupérer les paramètres de l'URL
$delivery_id = isset($_GET['delivery_id']) ? $_GET['delivery_id'] : 'undefined';
$service_id = isset($_GET['service_id']) ? $_GET['service_id'] : 'undefined';
$volunteer_id = isset($_GET['volunteerId']) ? $_GET['volunteerId'] : 'undefined';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de Créneau</title>
    <style>
        /* Styles ici */
    </style>
</head>

<body>

    <h1>Réserver un Créneau</h1>
    <nav>
        <ul>
            <?php include("../../includes/menus.php"); ?>
        </ul>
    </nav>

    <form id="reservationForm">
        <label for="dateTime">Choisissez une date et une heure :</label>
        <input type="datetime-local" id="dateTime" name="dateTime" required>

        <!-- Champs cachés pour les IDs -->
        <input type="hidden" id="deliveryId" value="<?php echo htmlspecialchars($delivery_id); ?>">
        <input type="hidden" id="serviceId" value="<?php echo htmlspecialchars($service_id); ?>">
        <input type="hidden" id="volunteerId" value="<?php echo htmlspecialchars($volunteer_id) ?>">

        <input type="submit" value="Réserver">
    </form>

    <script src="../../js/calendar.js"></script>
</body>

</html>
