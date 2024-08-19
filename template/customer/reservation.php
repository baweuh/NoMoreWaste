<?php include('../../includes/session.php'); ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation du produit</title>
</head>

<body>
    <header>
        <h1>Réservation du produit</h1>
        <nav>
            <?php include('../../includes/menus.php'); ?>
        </nav>
    </header>
    <main>
        <section>
            <h2>Saisissez votre adresse</h2>
            <form id="completeAddress">
                <label for="address">Adresse : </label><br>
                <input type="text" id="address" name="address" required><br>
<br>
                <label for="zipcode">Code Postal : </label><br>
                <input type="text" id="zipcode" name="zipcode" required><br>
<br>
                <label for="city">Ville : </label><br>
                <input type="text" id="city" name="city" required><br>
<br>
                <button type="submit" id="confirm">Confirmer l'adresse</button>
            </form>
        </section>
    </main>
    <script src="../../js/reservation.js"></script>
</body>

</html>