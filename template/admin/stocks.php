<!DOCTYPE html>
<html lang="en" data-default-lang="">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Stocks</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <h1>Manage Stocks</h1>
    <button onclick="showStockCreateForm()">Create New Stock</button>
    <!-- Exemple d'élément HTML -->
    <table id="stocksTable">
      <thead>
        <tr>
          <th>Stock ID</th>
          <th>Product ID</th>
          <th>Quantity</th>
          <th>Location</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="stocksTableBody">
        <!-- Les lignes des stocks seront ajoutées ici -->
      </tbody>
    </table>

    <div id="stockFormContainer" style="display: none">
      <h2 id="formTitle"></h2>
      <form id="stockForm">
        <input type="hidden" id="stockId" name="stockId" />
        <label for="product_id">Product ID:</label>
        <select id="product_id" name="product_id" required>
          <!-- Options seront ajoutées par JavaScript -->
        </select>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required />

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" required />

        <button type="submit" id="formSubmitButton">Create</button>
      </form>
    </div>

    <script src="../../js/stocks.js"></script>
  </body>
</html>
