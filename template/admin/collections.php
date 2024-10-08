<!DOCTYPE html>
<html lang="en" data-default-lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Collections</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1 id="ManageCollectionsBackoffice">Manage Collections</h1>
    <button onclick="showCollectionCreateForm()" id="createButtonCollection">Create New Collection</button>
    <table id="collectionsTable">
        <thead>
            <tr>
                <th>Collection ID</th>
                <th>Merchant ID</th>
                <th>Collection Date</th>
                <th>Total Items</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be added here -->
        </tbody>
    </table>

    <div id="collectionFormContainer" style="display: none">
        <h2 id="formTitle"></h2>
        <form id="collectionForm">
            <input type="hidden" id="collectionId" name="collectionId" />
            <label for="merchant_id">Merchant ID:</label>
            <select id="merchant_id" name="merchant_id" required></select>
            <label for="collection_date">Collection Date:</label>
            <input type="date" id="collection_date" name="collection_date" required />
            <label for="total_items">Total Items:</label>
            <input type="number" id="total_items" name="total_items" required />
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="0">En attente de récupération</option>
                <option value="1">En récupération</option>
                <option value="2">Récupéré</option>
                <option value="3">Livré</option>
            </select>
            <button type="submit" id="formSubmitButton">Create</button>
        </form>
    </div>

    <div id="updateStatusContainer" style="display: none">
        <h2>Update Status</h2>
        <form id="updateStatusForm">
            <input type="hidden" id="statusCollectionId" name="statusCollectionId" />
            <label for="statusUpdate">Status:</label>
            <select id="statusUpdate" name="statusUpdate">
                <option value="0">Pending</option>
                <option value="1">Delivered</option>
            </select>
            <button type="submit" id="statusUpdateButton">Update Status</button>
        </form>
    </div>

    <select id="languageSelect">
        <option value="en">English</option>
        <option value="fr">Français</option>
        <option value="de">Deutsch</option>
        <option value="es">Español</option>
        <option value="it">Italiano</option>
    </select>

    <script src="../../js/collections.js"></script>
    <script src="../../js/languages.js"></script>
</body>
</html>
