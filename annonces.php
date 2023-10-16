<?php
session_start();
if (isset($_SESSION['utilisateur_id'])) {
    $nom_utilisateur = $_SESSION['utilisateur_nom'];
    $prenom_utilisateur = $_SESSION['utilisateur_prenom'];
}

// Connexion à la base de données
$connexion = mysqli_connect("localhost", "root", "", "Leboncoin");

// Vérification de la connexion
if (!$connexion) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

// Vérifier si le paramètre de catégorie est passé en URL
if (isset($_GET['categorie'])) {
    $categorie_id = $_GET['categorie'];

    // Requête SELECT pour récupérer les annonces de la catégorie spécifiée
    $sql = "SELECT annonces.id, annonces.titre, annonces.description, annonces.prix, annonces.photo, utilisateurs.nom, utilisateurs.prenom FROM annonces INNER JOIN utilisateurs ON annonces.utilisateur_id = utilisateurs.id WHERE annonces.categorie_id = $categorie_id";
} else {
    // Requête SELECT pour récupérer toutes les annonces
    $sql = "SELECT annonces.id, annonces.titre, annonces.description, annonces.prix, annonces.photo, utilisateurs.nom, utilisateurs.prenom FROM annonces INNER JOIN utilisateurs ON annonces.utilisateur_id = utilisateurs.id";
}

// Exécution de la requête
$resultat = mysqli_query($connexion, $sql);

// Vérification s'il y a des annonces
if (mysqli_num_rows($resultat) > 0) {
    $annonces = mysqli_fetch_all($resultat, MYSQLI_ASSOC);
} else {
    $annonces = array(); // Aucune annonce trouvée
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Annonces - Leboncoin</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1><a href="test-1.php" class="logo">Leboncoin</a></h1>
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
    </header>
    <main>
        <?php if (isset($_SESSION['utilisateur_id'])) : ?>
            <div class="welcome-message">Bienvenue, <?php echo $prenom_utilisateur . ' ' . $nom_utilisateur; ?> !</div>
        <?php endif; ?>
        <section>
            <h2>Annonces</h2>
            <?php foreach ($annonces as $annonce) : ?>
                <a href="annonce.php?id=<?php echo $annonce['id']; ?>" class="annonce-link">
                    <div class="annonce-container">
                        <img src="<?php echo $annonce['photo']; ?>" alt="Image de l'annonce" class="annonce-image">
                        <div class="annonce-details">
                            <h3 class="annonce-title"><?php echo $annonce['titre']; ?></h3>
                            <p><?php echo $annonce['description']; ?></p>
                            <p class="annonce-price">Prix : <?php echo $annonce['prix']; ?> €</p>
                            <p>Annonce créée par : <?php echo $annonce['prenom'] . ' ' . $annonce['nom']; ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>

            <a href="#">Voir toutes les annonces</a>
        </section>
        <section>
            <h2>Catégories</h2>
            <ul>
                <li><a href="annonces.php?categorie=1">Véhicules</a></li>
                <li><a href="annonces.php?categorie=2">Immobilier</a></li>
                <li><a href="annonces.php?categorie=3">Emploi</a></li>
                <li><a href="annonces.php?categorie=4">Vacances</a></li>
                <li><a href="annonces.php?categorie=5">Maison</a></li>
            </ul>
        </section>

    </main>
</body>

</html>
