<?php

include('../../includes/session.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Livraisons</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        button {
            margin: 5px;
            padding: 10px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <h1>Mes Livraisons</h1>
    <nav>
        <ul>
            <?php include('../../includes/menus.php') ?>
        </ul>
    </nav>

    <!-- Boutons pour filtrer les livraisons -->
    <button onclick="loadTournees(0)">En cours de préparation</button>
    <button onclick="loadTournees(1)">En livraison</button>
    <button onclick="loadTournees(2)">Livré</button>
    <button onclick="loadTournees(3)">Annulé</button>
    <button onclick="loadTournees()">Tout afficher</button>
    
    <table id="tourneesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Type de Récepteur</th>
                <th>Statut</th>
                <th>PDF Rapport</th>
                <th>Annuler</th>
            </tr>
        </thead>
        <tbody id="tourneesTableBody">
            <!-- Les lignes seront ajoutées dynamiquement par JavaScript -->
        </tbody>
    </table>

    <script src="../../js/customer_delivery.js"></script>
</body>

</html>
