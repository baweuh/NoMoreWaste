<!DOCTYPE html>
<html lang="en" data-default-lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Languages</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1 id="ManageLanguagesBackoffice">Manage Languages</h1>
    <button onclick="showLanguageCreateForm()" id="createButton">Create New Language</button>
    <table id="languagesTable">
        <thead>
            <tr>
                <th id="languageCodeHeader">Language Code</th>
                <th id="languageTitleHeader">Language Name</th>
                <th id="actionsHeader">Actions</th>
            </tr>
        </thead>
        <tbody id="languagesTableBody">
            <!-- Rows will be added here -->
        </tbody>
    </table>

    <div id="languageFormContainer" style="display: none">
        <h2 id="formTitle"></h2>
        <form id="languageForm">
            <input type="hidden" id="languageCode" name="languageCode" />
            <label for="languageName" id="languageNameLabel">Language Name:</label>
            <input type="text" id="languageName" name="languageName" required />
            <label for="languageContent" id="languageContentLabel">Content (JSON):</label>
            <textarea id="languageContent" name="languageContent" rows="10" required></textarea>
            <button type="submit" id="formSubmitButton">Create</button>
        </form>
    </div>

    <!-- Language Select Dropdown -->
    <select id="languageSelect">
        <option value="en">English</option>
        <option value="fr">Français</option>
        <option value="de">Deutsch</option>
        <option value="es">Español</option>
        <option value="it">Italiano</option>
    </select>

    <script src="../../js/languages.js"></script>
</body>
</html>
