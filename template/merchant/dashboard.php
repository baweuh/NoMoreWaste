<?php

include('../../includes/session.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Commerçant</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <header>
        <h1>Bienvenue, Commerçant</h1>
        <nav>
            <ul>
                <?php include('../../includes/menus.php') ?>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <h2>Tableau de Bord</h2>
            <p>Sélectionnez une fonctionnalité dans le menu pour commencer.</p>
        </section>
    </main>
    <script src="../js/dashboard.js"></script>
</body>
</html>
