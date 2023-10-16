<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    // Rediriger vers la page de connexion
    header("Location: se_connecter.php");
    exit;
}

// Inclure le fichier de connexion à la base de données
include "connect.php";

// Vérifier si l'ID de l'annonce est fourni dans l'URL
if (!isset($_GET['id'])) {
    // Rediriger vers la page de compte
    header("Location: compte.php");
    exit;
}

$annonce_id = $_GET['id'];

// Vérifier si l'annonce appartient à l'utilisateur connecté
$query = "SELECT * FROM annonces WHERE id = $annonce_id AND utilisateur_id = {$_SESSION['utilisateur_id']}";
$result = mysqli_query($id, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    // L'annonce n'existe pas ou n'appartient pas à l'utilisateur
    // Rediriger vers la page de compte
    header("Location: compte.php");
    exit;
}

$annonce = mysqli_fetch_assoc($result);

// Variable pour stocker le message de réussite ou d'erreur
$message = "";

// Vérification si le formulaire de modification de l'annonce est soumis
if (isset($_POST['modifier_annonce'])) {
    // Récupération des nouvelles données du formulaire
    $nouveau_titre = $_POST['nouveau_titre'];
    $nouvelle_description = $_POST['nouvelle_description'];
    $nouveau_prix = $_POST['nouveau_prix'];

    // Mise à jour de l'annonce dans la base de données
    $query = "UPDATE annonces SET titre = '$nouveau_titre', description = '$nouvelle_description', prix = $nouveau_prix WHERE id = $annonce_id";
    $result = mysqli_query($id, $query);

    if ($result) {
        $message = "L'annonce a été modifiée avec succès.";
        // Rediriger vers la page de compte après la modification
        header("Location: account.php");
        exit;
    } else {
        $message = "Erreur lors de la modification de l'annonce : " . mysqli_error($id);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'annonce - Leboncoin</title>
    <link rel="stylesheet" href="modif.css">
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
        <section>
            <h2>Modifier l'annonce</h2>
            <?php if ($message) : ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
            <div class="annonce-details">
                <div class="annonce-image">
                    <img src="<?php echo $annonce['photo']; ?>" alt="Photo de l'annonce">
                </div>
                <form method="post">
                    <label for="nouveau_titre">Titre :</label>
                    <input type="text" name="nouveau_titre" value="<?php echo $annonce['titre']; ?>" required>
                    <label for="nouvelle_description">Description :</label>
                    <textarea name="nouvelle_description" required><?php echo $annonce['description']; ?></textarea>
                    <label for="nouveau_prix">Prix :</label>
                    <input type="number" name="nouveau_prix" value="<?php echo $annonce['prix']; ?>" required>
                    <button type="submit" name="modifier_annonce">Modifier</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
