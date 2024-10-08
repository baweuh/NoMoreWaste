<!DOCTYPE html>
<html lang="en" data-default-lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Manage Products</h1>
    <button onclick="showProductCreateForm()">Create New Product</button>
    <table id="productsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Barcode</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Expiry Date</th>
                <th>Collection ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="productsTableBody">
            <!-- Product rows will be inserted here by JavaScript -->
        </tbody>
    </table>

    <div id="productFormContainer" style="display:none;">
        <h2 id="formTitle">Create Product</h2>
        <form id="productForm">
            <input type="hidden" id="productId">
            <label for="barcode">Barcode:</label>
            <input type="text" id="barcode" required>
            <label for="name">Name:</label>
            <input type="text" id="name" required>
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity">
            <label for="expiry_date">Expiry Date:</label>
            <input type="date" id="expiry_date">
            <label for="collection_id">Collection ID:</label>
            <select id="collection_id" required>
                <!-- Options will be populated by JavaScript -->
            </select>
            <button type="submit" id="formSubmitButton">Create</button>
        </form>
    </div>

    <script src="../../js/products.js"></script>
</body>
</html>
