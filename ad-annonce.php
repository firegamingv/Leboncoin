<?php
session_start();

if (isset($_SESSION['utilisateur_id'])) {
    $nom_utilisateur = $_SESSION['utilisateur_nom'];
    $prenom_utilisateur = $_SESSION['utilisateur_prenom'];
    echo '<div class="bienvenue">Bienvenue, ' . $prenom_utilisateur . ' ' . $nom_utilisateur . ' !</div>';
} else {
    header("Location: se_connecter.php");
}

// Vérification si le formulaire est soumis
if (isset($_POST['submit'])) {
    // Récupération des données du formulaire
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $utilisateur_id = $_SESSION['utilisateur_id']; // Récupérer l'identifiant de l'utilisateur à partir de la session
    $categorie_id = $_POST['categorie']; // Récupérer la catégorie sélectionnée

    // Connexion à la base de données
    $connexion = mysqli_connect("localhost", "root", "", "Leboncoin");

    // Vérification de la connexion
    if (!$connexion) {
        die("Erreur de connexion à la base de données : " . mysqli_connect_error());
    }

    // Vérification si un fichier a été uploadé
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name']; // Nom du fichier image
        $image_tmp = $_FILES['image']['tmp_name']; // Emplacement temporaire du fichier image sur le serveur

        // Générer un nom de fichier unique
        $extension = pathinfo($image, PATHINFO_EXTENSION);
        $nom_fichier = uniqid() . '.' . $extension;

        // Définir l'URL complète de l'image
        $url_image = 'http://localhost/Leboncoin/images/' . $nom_fichier;

        // Définir le chemin de destination pour enregistrer l'image
        $dossier_images = 'C:/wamp64/www/Leboncoin/images/';
        $chemin_image = $dossier_images . $nom_fichier;

        // Déplacer le fichier vers un emplacement permanent
        move_uploaded_file($image_tmp, $chemin_image);
    } else {
        $url_image = ''; // Aucune image sélectionnée
    }

    // Requête d'insertion des données avec une requête préparée
    $sql = "INSERT INTO annonces (titre, description, prix, photo, categorie_id, utilisateur_id, date_creation) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, "ssdssd", $titre, $description, $prix, $url_image, $categorie_id, $utilisateur_id);

    // Exécution de la requête préparée
    if (mysqli_stmt_execute($stmt)) {
        echo "Annonce ajoutée avec succès";
    } else {
        echo "Erreur lors de l'ajout de l'annonce : " . mysqli_stmt_error($stmt);
    }

    // Fermeture de la requête préparée
    mysqli_stmt_close($stmt);
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déposer une annonce - LeBonCoin</title>
    <link rel="stylesheet" href="ad_annonce.css">
</head>
<body>
    <header>
        <nav>
            <ul>
            <li><a href="ad-annonce.php">Déposer une annonce</a></li>
                <li><a href="account.php">Mon compte</a></li>
                <li><a href="messagerie.php">Ma messagerie</a></li>
                <li><a href="se_connecter.php">Se connecter</a></li>
                <li><a href="inscription.php">S'inscrire</a></li>
                <li><a href="deconnexion.php" class="deconnexion">Se déconnecter</a></li>
            </ul>
        </nav>
        <a href="test-1.php" class="logo">LeBonCoin</a>
    </header>
    <main>
        <h1>Déposer une annonce</h1>
        <form method="post" action="" enctype="multipart/form-data"> 
            <label for="titre">Titre de l'annonce :</label>
            <input type="text" id="titre" name="titre" placeholder="Titre de l'annonce">

            <label for="description">Description de l'annonce :</label>
            <textarea id="description" name="description" placeholder="Description de l'annonce"></textarea>

            <label for="prix">Prix :</label>
            <input type="number" id="prix" name="prix" placeholder="Prix">

            <label for="categorie">Catégorie :</label>
            <select id="categorie" name="categorie">
                <option value="1">Voiture</option>
                <option value="2">Maison</option>
                <option value="3">Vacances</option>
                <option value="4">Loisir</option>
                <option value="5">Jeux</option>
            </select>

            <label for="image">Image :</label>
            <input type="file" id="image" name="image">

            <input type="submit" name="submit" value="Déposer l'annonce">
        </form>
    </main>
</body>
</html>
