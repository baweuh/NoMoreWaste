<?php

if ($_SESSION['role'] == 'admin') {
    echo "<li><a href='volunteers.php'>Bénévoles</a></li>";
    echo "<li><a href=volunteers_assignements.php>Bénévoles_services</a></li>";
    echo "<li><a href=customers.php>Clients</a></li>";
    echo "<li><a href=collections.php>Collectes</a></li>";
    echo "<li><a href=merchants.php>Commerçants</a></li>";
    echo "<li><a href=paniers.php>Paniers</a></li>";
    echo "<li><a href=products.php>Produits</a></li>";
    echo "<li><a href=services.php>Services</a></li>";
    echo "<li><a href=deliveries.php>Tournées</a></li>";
    echo "<li><a href=deliveries_volunteers.php>Tournées_bénévoles</a></li>";
    echo "<li><a href=users.php>Utilisateurs</a></li>";
} else if ($_SESSION['role'] == 'commercants') {
    echo "<li><a href=dashboard.php>Accueil</a></li>";
    echo "<li><a href=manage_collects.php>Gérer les Collectes</a></li>";
    echo "<li><a href=manage_distribution.php>Gérer les Tournées de Distribution</a></li>";
    echo "<li><a href=manage_profile.php>Gérer le Profil</a></li>";
    echo "<li><a href=manage_signin.php>Gérer les Identifiants de connexion</a></li>";
    echo "<li><a href=../../includes/logout.php>Se Déconnecter</a></li>";
} else if ($_SESSION['role'] == 'clients') {
    echo "<li><a href=dashboard.php>Accueil</a></li>";
    echo "<li><a href=liste.php>Liste des produits disponibles</a></li>";
    echo "<li><a href=liste_panier.php>Panier</a></li>";
    echo "<li><a href=deliveries.php>Voir les commandes</a></li>";
    echo "<li><a href=manage_profile.php>Gérer le Profil</a></li>";
    echo "<li><a href=manage_signin.php>Gérer les Identifiants de connexion</a></li>";
    echo "<li><a href=../../includes/logout.php>Se Déconnecter</a></li>";
} else if ($_SESSION['role'] == 'benevoles') {
    echo "<li><a href=dashboard.php>Accueil</a></li>";
    echo "<li><a href=planning_services.php>S'inscrire aux Services</a></li>";
    echo "<li><a href=liste_tournees.php>Liste des tournées</a></li>";
    echo "<li><a href=mes_tournees.php>Mes tournées</a></li>";
    echo "<li><a href=python.php>Voir le Planning</a></li>";
    echo "<li><a href=manage_profile.php>Gérer le Profil</a></li>";
    echo "<li><a href=manage_signin.php>Gérer les Identifiants de connexion</a></li>";
    echo "<li><a href=../../includes/logout.php>Se Déconnecter</a></li>";
}
