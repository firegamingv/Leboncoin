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

// Requête SELECT pour récupérer les annonces
$sql = "SELECT annonces.id, annonces.titre, annonces.description, annonces.prix, annonces.photo, utilisateurs.nom, utilisateurs.prenom FROM annonces INNER JOIN utilisateurs ON annonces.utilisateur_id = utilisateurs.id";


// Vérification des catégories sélectionnées
if (isset($_GET['categories']) && is_array($_GET['categories']) && count($_GET['categories']) > 0) {
    $categories = implode(",", $_GET['categories']);
    $sql .= " WHERE annonces.categorie_id IN ($categories)";
}

// Vérification si l'annonce a été cliquée depuis la messagerie
if (isset($_GET['annonce_id'])) {
    $annonce_id = $_GET['annonce_id'];
    $sql .= " AND annonces.id = $annonce_id";
}

// Exécution de la requête
$resultat = mysqli_query($connexion, $sql);

// Vérification s'il y a des annonces
if (mysqli_num_rows($resultat) > 0) {
    $annonces = mysqli_fetch_all($resultat, MYSQLI_ASSOC);
} else {
    $annonces = array(); // Aucune annonce trouvée
}

// Récupération des annonces en favoris de l'utilisateur connecté
$favoris = array();
if (isset($_SESSION['utilisateur_id'])) {
    $utilisateur_id = $_SESSION['utilisateur_id'];
    $sql_favoris = "SELECT annonce_id FROM favoris WHERE utilisateur_id = $utilisateur_id";
    $resultat_favoris = mysqli_query($connexion, $sql_favoris);
    if (mysqli_num_rows($resultat_favoris) > 0) {
        $favoris = mysqli_fetch_all($resultat_favoris, MYSQLI_ASSOC);
        $favoris = array_column($favoris, 'annonce_id');
    }
}

// Ajout ou suppression d'une annonce aux favoris
if (isset($_SESSION['utilisateur_id']) && isset($_POST['annonce_id'])) {
    $annonce_id = $_POST['annonce_id'];

    // Vérification si l'annonce existe
    $sql_check_annonce = "SELECT id FROM annonces WHERE id = $annonce_id";
    $resultat_check_annonce = mysqli_query($connexion, $sql_check_annonce);

    if (mysqli_num_rows($resultat_check_annonce) > 0) {
        $sql_check_favoris = "SELECT id FROM favoris WHERE utilisateur_id = $utilisateur_id AND annonce_id = $annonce_id";
        $resultat_check_favoris = mysqli_query($connexion, $sql_check_favoris);

        if (mysqli_num_rows($resultat_check_favoris) > 0) {
            // Supprimer l'annonce des favoris
            $sql_supprimer_favoris = "DELETE FROM favoris WHERE utilisateur_id = $utilisateur_id AND annonce_id = $annonce_id";
            mysqli_query($connexion, $sql_supprimer_favoris);
        } else {
            // Ajouter l'annonce aux favoris
            $sql_ajouter_favoris = "INSERT INTO favoris (utilisateur_id, annonce_id, date_ajout) VALUES ($utilisateur_id, $annonce_id, NOW())";
            mysqli_query($connexion, $sql_ajouter_favoris);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Leboncoin - Petites annonces gratuites</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .favoris-btn {
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #f2f2f2;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .favoris-btn.favoris {
            background-color: yellow;
        }
    </style>
</head>

<body>
    <header>
        <h1><a href="test-1.php" class="logo">Leboncoin</a></h1>
        <nav>
            <ul>
                <li><a href="ad-annonce.php">Déposer une annonce</a></li>
                <li><a href="favoris.php">Favoris</a></li>
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
            <?php if (empty($annonces)) : ?>
                <p>Aucune annonce disponible pour le moment.</p>
            <?php else : ?>
                <?php foreach ($annonces as $annonce) : ?>
                    <div class="annonce-container">
                        <a href="annonce.php?id=<?php echo $annonce['id']; ?>" class="annonce-link">
                            <img src="<?php echo $annonce['photo']; ?>" alt="Image de l'annonce" class="annonce-image">
                            <div class="annonce-details">
                                <h3 class="annonce-title"><?php echo $annonce['titre']; ?></h3>
                                <p class="annonce-price">Prix : <?php echo $annonce['prix']; ?> €</p>
                                <p>Annonce créée par : <?php echo $annonce['prenom'] . ' ' . $annonce['nom']; ?></p>
                            </div>
                        </a>
                        <?php if (isset($_SESSION['utilisateur_id'])) : ?>
                            <?php
                            $annonce_id = $annonce['id'];
                            $is_favori = in_array($annonce_id, $favoris);
                            $favoris_class = $is_favori ? 'favoris' : '';
                            ?>
                            <form method="POST" class="favoris-form">
                                <input type="hidden" name="annonce_id" value="<?php echo $annonce_id; ?>">
                                <button class="favoris-btn <?php echo $favoris_class; ?>">Ajouter aux favoris</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </section>

        <section>
            <h2>Catégories</h2>
            <form method="GET">
                <ul class="checkbox-list">
                    <li>
                        <label>
                            <input type="checkbox" name="categories[]" value="1" <?php if (isset($_GET['categories']) && in_array(1, $_GET['categories'])) echo 'checked'; ?>>
                            Voiture
                            <span class="checkmark"></span>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="categories[]" value="2" <?php if (isset($_GET['categories']) && in_array(2, $_GET['categories'])) echo 'checked'; ?>>
                            Maison
                            <span class="checkmark"></span>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="categories[]" value="3" <?php if (isset($_GET['categories']) && in_array(3, $_GET['categories'])) echo 'checked'; ?>>
                            Vacances
                            <span class="checkmark"></span>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="categories[]" value="4" <?php if (isset($_GET['categories']) && in_array(4, $_GET['categories'])) echo 'checked'; ?>>
                            Loisir
                            <span class="checkmark"></span>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="categories[]" value="5" <?php if (isset($_GET['categories']) && in_array(5, $_GET['categories'])) echo 'checked'; ?>>
                            Jeux
                            <span class="checkmark"></span>
                        </label>
                    </li>
                </ul>
                <button type="submit">Filtrer</button>
            </form>
        </section>
    </main>
</body>

</html>
