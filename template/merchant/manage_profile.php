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
                <input type="hidden" id="merchantId" name="merchant_id">
                
                <label for="name">Nom:</label>
                <input type="text" id="name" name="name" required>
                
                <label for="address">Adresse:</label>
                <input type="text" id="address" name="address">
                
                <label for="phone">Téléphone:</label>
                <input type="tel" id="phone" name="phone">
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="membershipStartDate">Date de début d'adhésion:</label>
                <input type="date" id="membershipStartDate" name="membership_start_date" required>
                
                <label for="membershipEndDate">Date de fin d'adhésion:</label>
                <input type="date" id="membershipEndDate" name="membership_end_date" required>
                
                <button type="submit">Mettre à jour</button>
            </form>
        </section>
    </main>
    <script src="../../js/manage_profile.js"></script>
</body>
</html>
