<?php include('../../includes/session.php') ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Collectes</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/manage_collects.css">
</head>
<body>
    <header>
        <h1>Gérer les Collectes</h1>
        <nav>
            <ul>
                <?php include('../../includes/menus.php') ?>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <h2>Liste des Collectes</h2>
            <table id="collectsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom du Produit</th>
                        <th>Date de Collecte</th>
                        <th>Quantité</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="collectsTableBody">
                    <!-- Les collectes seront chargées ici dynamiquement -->
                </tbody>
            </table>
        </section>
        <section>
            <h2>Ajouter une Collecte</h2>
            <form id="addCollectForm">
                <label for="addProductName">Nom du Produit:</label>
                <input type="text" id="addProductName" name="productName" required>
                
                <label for="addCollectDate">Date de Collecte:</label>
                <input type="date" id="addCollectDate" name="collectDate" required>
                
                <label for="addQuantity">Quantité:</label>
                <input type="number" id="addQuantity" name="quantity" required>
                
                <button type="submit" id="addFormSubmitButton">Ajouter</button>
            </form>
        </section>
        <section id="editSection" style="display: none;">
            <h2>Modifier une Collecte</h2>
            <form id="editCollectForm">
                <input type="hidden" id="editCollectId" name="collectId">
                <label for="editProductName">Nom du Produit:</label>
                <input type="text" id="editProductName" name="productName" required>
                
                <label for="editCollectDate">Date de Collecte:</label>
                <input type="date" id="editCollectDate" name="collectDate" required>
                
                <label for="editQuantity">Quantité:</label>
                <input type="number" id="editQuantity" name="quantity" required>
                
                <button type="submit" id="editFormSubmitButton">Modifier</button>
            </form>
        </section>
    </main>
    <script src="../../js/manage_collects.js"></script>
</body>
</html>
