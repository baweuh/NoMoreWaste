<?php

include('../../includes/session.php');

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
</head>

<body>
    <header>
        <h1>Votre Panier</h1>
        <nav>
            <?php include("../../includes/menus.php"); ?>
        </nav>
    </header>
    <main>
        <section>
            <h2>Liste de votre panier</h2>
            <table id="paniersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Date d'expiration</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody id="paniersTableBody"></tbody>
            </table>
            <div id="validateCartButton"></div>
        </section>
    </main>
    <script src="../../js/liste_panier.js"></script>
</body>

</html>