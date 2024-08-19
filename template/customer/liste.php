<?php

include('../../includes/session.php');

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des produits</title>
</head>

<body>
    <header>
        <h1>Liste des produits disponibles</h1>
        <nav>
            <?php include("../../includes/menus.php"); ?>
        </nav>
    </header>
    <main>
        <section>
            <h2>Liste des produits disponibles</h2>
            <table id="productsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code Barre</th>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Date d'expiration</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody"></tbody>
            </table>
        </section>
    </main>
    <script src="../../js/liste.js"></script>
</body>

</html>