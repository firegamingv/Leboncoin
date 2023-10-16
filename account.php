<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    // Rediriger vers la page de connexion
    header("Location: se_connecter.php");
    exit;
}

include "connect.php";

// Récupérer les informations de l'utilisateur depuis la session
$utilisateur_id = $_SESSION['utilisateur_id'];
$utilisateur_nom = $_SESSION['utilisateur_nom'];
$utilisateur_prenom = $_SESSION['utilisateur_prenom'];

// Variable pour stocker le message de réussite ou d'erreur
$message = "";

// Vérification si le formulaire de modification des informations est soumis
if (isset($_POST['modifier_informations'])) {
    // Récupération des nouvelles données du formulaire
    $nouveau_nom = $_POST['nouveau_nom'];
    $nouveau_prenom = $_POST['nouveau_prenom'];

    // Mise à jour des informations de l'utilisateur dans la base de données
    $query = "UPDATE utilisateurs SET nom = '$nouveau_nom', prenom = '$nouveau_prenom' WHERE id = $utilisateur_id";
    $result = mysqli_query($id, $query);

    if ($result) {
        // Mise à jour réussie, mettre à jour les informations de la session
        $_SESSION['utilisateur_nom'] = $nouveau_nom;
        $_SESSION['utilisateur_prenom'] = $nouveau_prenom;
        $message = "Les informations ont été modifiées avec succès.";
    } else {
        $message = "Erreur lors de la modification des informations : " . mysqli_error($id);
    }
}

// Vérification si une annonce doit être supprimée
if (isset($_GET['supprimer_annonce'])) {
    $annonce_id = $_GET['supprimer_annonce'];

    // Supprimer l'annonce de la base de données
    $query = "DELETE FROM annonces WHERE id = $annonce_id AND utilisateur_id = $utilisateur_id";
    $result = mysqli_query($id, $query);

    if ($result) {
        $message = "L'annonce a été supprimée avec succès.";
    } else {
        $message = "Erreur lors de la suppression de l'annonce : " . mysqli_error($id);
    }
}

// Récupérer les annonces de l'utilisateur depuis la base de données
$query = "SELECT * FROM annonces WHERE utilisateur_id = $utilisateur_id";
$result = mysqli_query($id, $query);
$annonces = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte - Leboncoin</title>
    <link rel="stylesheet" href="account.css">
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
        <section class="account-section">
            <div class="account-info">
                <h2>Mon compte</h2>
                <h3>Informations personnelles</h3>
                <?php if ($message) : ?>
                    <p class="message"><?php echo $message; ?></p>
                <?php endif; ?>
                <form method="post">
                    <label for="nouveau_nom">Nom :</label>
                    <input type="text" name="nouveau_nom" value="<?php echo $utilisateur_nom; ?>" required>
                    <label for="nouveau_prenom">Prénom :</label>
                    <input type="text" name="nouveau_prenom" value="<?php echo $utilisateur_prenom; ?>" required>
                    <button type="submit" name="modifier_informations">Modifier</button>
                </form>
            </div>

            <div class="annonces-section">
                <h3>Mes annonces</h3>
                <?php if (count($annonces) > 0) : ?>
                    <ul class="annonces-list">
                        <?php foreach ($annonces as $annonce) : ?>
                            <li class="annonce-item">
                                <div class="annonce-details">
                                    <span class="annonce-title"><?php echo $annonce['titre']; ?></span>
                                    <span class="annonce-description"><?php echo $annonce['description']; ?></span>
                                    <span class="annonce-price"><?php echo $annonce['prix']; ?> €</span>
                                </div>
                                <div class="annonce-actions">
                                    <img src="<?php echo $annonce['photo']; ?>" alt="Image de l'annonce" class="annonce-image">
                                    <a href="modifier_annonce.php?id=<?php echo $annonce['id']; ?>">Modifier</a>
                                    <a href="?supprimer_annonce=<?php echo $annonce['id']; ?>">Supprimer</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>Vous n'avez pas encore créé d'annonces.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
