<?php

include('../../includes/session.php'); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Tournées</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Liste des Tournées</h1>
    <nav>
        <ul>
            <?php include('../../includes/menus.php'); ?>
        </ul>
    </nav>
    <table id="tours-table">
        <thead>
            <tr>
                <th>ID de Livraison</th>
                <th>Date de Livraison</th>
                <th>Service</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Les données seront insérées ici par JavaScript -->
        </tbody>
    </table>

    <script>
        const userID = <?php echo $_SESSION['user_id']; ?>
    </script>

    <script src="../../js/volunteer_tournees.js"></script>
</body>
</html>
