<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Deliveries Management</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <h1>Deliveries Management</h1>
    <button id="showCreateFormButton" onclick="showDeliveryCreateForm()">Add New Delivery</button>

    <!-- Conteneur pour le formulaire de création -->
    <div id="deliveryFormContainer" style="display: none">
      <h2 id="formTitle">Add New Delivery</h2>
      <form id="deliveryForm">
        <label for="delivery_date">Delivery Date:</label>
        <input type="date" id="delivery_date" name="delivery_date" required />
        <label for="recipient_type">Recipient Type:</label>
        <input type="text" id="recipient_type" name="recipient_type" required />
        <label for="customer_id">Recipient ID:</label>
        <select id="customer_id" name="customer_id" required>
          <!-- Options seront insérées ici par JavaScript -->
        </select>
        <label for="status">Status:</label>
        <input type="number" id="status" name="status" required />
        <label for="pdf_report">PDF Report:</label>
        <input type="file" id="pdf_report" name="pdf_report" />
        <input type="hidden" id="deliveryId" name="deliveryId" />
        <button type="submit">Submit</button>
      </form>
    </div>

    <table>
      <thead>
        <tr>
          <th>Delivery ID</th>
          <th>Delivery Date</th>
          <th>Recipient Type</th>
          <th>Recipient ID</th>
          <th>Status</th>
          <th>PDF Report</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="deliveriesTableBody">
        <!-- Les lignes de données seront insérées ici par JavaScript -->
      </tbody>
    </table>

    <!-- Conteneur pour le formulaire d'édition -->
    <div id="editDeliveryFormContainer" style="display: none">
      <h2 id="editFormTitle">Edit Delivery</h2>
      <form id="editDeliveryForm">
        <input type="hidden" id="editDeliveryId" name="delivery_id" />
        <label for="edit_delivery_date">Delivery Date:</label>
        <input type="date" id="edit_delivery_date" name="delivery_date" />

        <label for="edit_recipient_type">Recipient Type:</label>
        <input type="text" id="edit_recipient_type" name="recipient_type" />

        <label for="edit_customer_id">Customer ID:</label>
        <select id="edit_customer_id" name="customer_id"></select>

        <label for="edit_status">Status:</label>
        <input type="text" id="edit_status" name="status" />

        <label for="edit_pdf_report">PDF Report:</label>
        <input type="file" id="edit_pdf_report" name="pdf_report" />

        <button type="submit">Save</button>
      </form>

      <div id="edit_pdf_report_link"></div>
    </div>

    <script src="../../js/deliveries.js"></script>
  </body>
</html>
