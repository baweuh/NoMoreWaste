<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Customers Management</title>
    <link rel="stylesheet" href="../../styles.css" />
    <script src="../../js/customers.js" defer></script>
</head>
<body>
    <h1>Customers Management</h1>
    <button onclick="showCustomerCreateForm()">Add New Customer</button>

    <!-- Conteneur pour le formulaire de création -->
    <div id="customerFormContainer" style="display: none;">
        <h2>Add New Customer</h2>
        <form id="customerForm">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required />
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" />
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" />
            <button type="submit">Submit</button>
        </form>
    </div>

    <!-- Conteneur pour le formulaire d'édition -->
    <div id="editCustomerFormContainer" style="display: none;">
        <h2>Edit Customer</h2>
        <form id="editCustomerForm">
            <input type="hidden" id="edit_customer_id" name="customer_id" />
            <label for="edit_name">Name:</label>
            <input type="text" id="edit_name" name="name" required />
            <label for="edit_email">Email:</label>
            <input type="email" id="edit_email" name="email" />
            <label for="edit_phone">Phone:</label>
            <input type="text" id="edit_phone" name="phone" />
            <button type="submit">Update</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="customersTableBody">
            <!-- Les lignes de données seront insérées ici par JavaScript -->
        </tbody>
    </table>
</body>
</html>
