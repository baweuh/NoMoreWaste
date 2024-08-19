<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gérer le Profil</title>
    <link rel="stylesheet" href="css/manage_profile.css">
</head> 

<body>
    <header>
        <h1>Gérer le Profil</h1>
        <nav>
            <ul>
                <?php
                include("../../includes/session.php");
                include("../../includes/menus.php");
                ?>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <h2>Informations du Profil</h2>
            <form id="profileForm">
                <label for="username">Nom d'utilisateur:</label>
                <input type="text" id="username" name="username" required>

                <label for="role">Rôle:</label>
                <input type="text" id="role" name="role" required>

                <button type="submit" id="updateButton">Mettre à jour</button>
            </form>
        </section>
    </main>
    <script src="../../js/manage_signin.js"></script>
</body>

</html>