<?php

if ($_SESSION['role'] == 'admin') {
    echo "<li><a href='admin/volunteers.php'>Bénévoles</a></li>";
    echo "<li><a href=admin/volunteers_assignements.php>Bénévoles_services</a></li>";
    echo "<li><a href=admin/customers.php>Clients</a></li>";
    echo "<li><a href=admin/collections.php>Collectes</a></li>";
    echo "<li><a href=admin/merchants.php>Commerçants</a></li>";
    echo "<li><a href=admin/products.php>Produits</a></li>";
    echo "<li><a href=admin/services.php>Services</a></li>";
    echo "<li><a href=admin/deliveries.php>Tournées</a></li>";
    echo "<li><a href=admin/users.php>Utilisateurs</a></li>";
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
    echo "<li><a href=manage_profile.php>Gérer le Profil</a></li>";
    echo "<li><a href=manage_signin.php>Gérer les Identifiants de connexion</a></li>";
    echo "<li><a href=../../includes/logout.php>Se Déconnecter</a></li>";
}
