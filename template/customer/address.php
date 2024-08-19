<?php
// address.php

header("Content-Type: text/html; charset=UTF-8");

include('../../includes/session.php');

$customer_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adresse de Livraison</title>
    <link rel="stylesheet" href="styles.css"> <!-- Inclure votre CSS ici -->
</head>
<body>
    <h1>Adresse de Livraison</h1>

    <!-- Formulaire pour l'adresse de livraison -->
    <form id="addressForm">
        <input type="hidden" id="customerId" name="customer_id" value="<?php echo htmlspecialchars($customer_id); ?>">
        
        <label for="recipient_name">Nom du destinataire :</label>
        <input type="text" id="recipient_name" name="recipient_name" required>
        
        <label for="address">Adresse :</label>
        <input type="text" id="address" name="address" required>
        
        <label for="city">Ville :</label>
        <input type="text" id="city" name="city" required>
        
        <label for="zipcode">Code Postal :</label>
        <input type="text" id="zipcode" name="zipcode" required>
        
        <label for="recipient_type">Type de destinataire :</label>
        <select id="recipient_type" name="recipient_type" required>
            <option value="home">Domicile</option>
            <option value="work">Travail</option>
            <option value="other">Autre</option>
        </select>
        
        <button type="submit" id="submitButton">Valider la Commande</button>
        <button type="button" id="cancelButton">Annuler</button>
    </form>

    <script src="../../js/address.js"></script> <!-- Inclure le script address.js ici -->
</body>
</html>
