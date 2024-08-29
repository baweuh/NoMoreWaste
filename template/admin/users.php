<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
</head>
<body>
    <h1>Gestion des Utilisateurs</h1>
    <button onclick="showUserCreateForm()">Ajouter un utilisateur</button>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom d'utilisateur</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            <!-- Les utilisateurs seront injectés ici par JavaScript -->
        </tbody>
    </table>

    <div id="userFormContainer" style="display: none;">
        <h2 id="formTitle">Ajouter un utilisateur</h2>
        <form id="userForm">
            <input type="hidden" id="userId" name="user_id">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required><br>

            <label for="role">Rôle:</label>
            <select id="role" name="role" required>
                <option value="commercants">Commerçant</option>
                <option value="clients">Client</option>
                <option value="benevoles">Bénévole</option>
            </select><br>

            <label for="statut">Statut:</label>
            <select id="statut" name="statut" required>
                <option value="0">En attente</option>
                <option value="1">Validé</option>
                <option value="2">Réfusé</option>
            </select><br>

            <button type="submit" id="formSubmitButton">Créer</button>
        </form>
    </div>

    <script src="../../js/users.js"></script>
</body>
</html>
