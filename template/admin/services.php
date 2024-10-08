<!DOCTYPE html>
<html lang="en" data-default-lang="">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Services</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <h1>Manage Services</h1>
    <button onclick="showServiceCreateForm()">Create New Service</button>
    <table id="servicesTable">
        <thead>
            <tr>
                <th>Service ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="servicesTableBody">
            <!-- Les lignes des services seront ajoutées ici -->
        </tbody>
    </table>

    <div id="serviceFormContainer" style="display: none">
        <h2 id="formTitle"></h2>
        <form id="serviceForm">
            <input type="hidden" id="serviceId" name="serviceId" />
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required />

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>

            <button type="submit" id="formSubmitButton">Create</button>
        </form>
    </div>

    <script src="../../js/services.js"></script>
</body>
</html>
