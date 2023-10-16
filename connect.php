<?php
$servername = "localhost"; // Remplacez par l'adresse du serveur de la base de données
$username = "root"; // Remplacez par le nom d'utilisateur de la base de données
$password = ""; // Remplacez par le mot de passe de la base de données
$dbname = "Leboncoin"; // Remplacez par le nom de la base de données

// Créer une connexion à la base de données
$id = mysqli_connect($servername, $username, $password, $dbname);

// Vérifier la connexion
if (!$id) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}
?>
