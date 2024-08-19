<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gérer les Produits</title>
    <link rel="stylesheet" href="css/manage_products.css">
</head>

<body>
    <header>
        <h1>Gérer les Produits</h1>
        <nav>
            <ul>
                <?php include("../../includes/session.php");
                include("../../includes/menus.php") ?>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <h2>Liste des Produits</h2>
            <table id="productsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code-barres</th>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Date d'expiration</th>
                        <th>Collection</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    <!-- Les produits seront chargés ici dynamiquement -->
                </tbody>
            </table>
        </section>
        <section>
            <h2>Ajouter un Produit</h2>
            <form id="addProductForm">
                <label for="addBarcode">Code-barres:</label>
                <input type="text" id="addBarcode" name="barcode" required>

                <label for="addProductName">Nom du Produit:</label>
                <input type="text" id="addProductName" name="name" required>

                <label for="addQuantity">Quantité:</label>
                <input type="number" id="addQuantity" name="quantity" required>

                <label for="addExpiryDate">Date d'expiration:</label>
                <input type="date" id="addExpiryDate" name="expiry_date">

                <label for="addCollectionId">Collection:</label>
                <select id="addCollectionId" name="collection_id" required>
                    <!-- Les options de collection seront chargées ici dynamiquement -->
                </select>

                <button type="submit" id="addProductButton">Ajouter</button>
            </form>
        </section>
        <section>
            <h2>Modifier un Produit</h2>
            <form id="editProductForm" style="display:none;">
                <input type="hidden" id="editProductId" name="product_id">

                <label for="editBarcode">Code-barres:</label>
                <input type="text" id="editBarcode" name="barcode" required>

                <label for="editProductName">Nom du Produit:</label>
                <input type="text" id="editProductName" name="name" required>

                <label for="editQuantity">Quantité:</label>
                <input type="number" id="editQuantity" name="quantity" required>

                <label for="editExpiryDate">Date d'expiration:</label>
                <input type="date" id="editExpiryDate" name="expiry_date">

                <label for="editCollectionId">Collection:</label>
                <select id="editCollectionId" name="collection_id" required>
                    <!-- Les options de collection seront chargées ici dynamiquement -->
                </select>

                <button type="submit" id="editProductButton">Modifier</button>
            </form>
        </section>
    </main>
    <script src="../../js/manage_product.js"></script>
</body>

</html>