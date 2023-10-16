<?php
session_start();

// Vérification si l'ID de l'annonce est présent dans l'URL
if (isset($_GET['id'])) {
    $annonce_id = $_GET['id'];

    // Connexion à la base de données
    $connexion = mysqli_connect("localhost", "root", "", "Leboncoin");

    // Vérification de la connexion
    if (!$connexion) {
        die("Erreur de connexion à la base de données : " . mysqli_connect_error());
    }

    // Requête SELECT pour récupérer les détails de l'annonce spécifique
    $sql = "SELECT annonces.titre, annonces.description, annonces.prix, annonces.photo, utilisateurs.id as utilisateur_id, utilisateurs.nom, utilisateurs.prenom 
            FROM annonces 
            INNER JOIN utilisateurs ON annonces.utilisateur_id = utilisateurs.id
            WHERE annonces.id = '$annonce_id'";

    // Exécution de la requête
    $resultat = mysqli_query($connexion, $sql);

    // Vérification si l'annonce existe
    if (mysqli_num_rows($resultat) > 0) {
        $annonce = mysqli_fetch_assoc($resultat);
    } else {
        // Redirection vers une page d'erreur si l'annonce n'existe pas
        header("Location: erreur.php");
        exit();
    }
} 
// Traitement du formulaire d'envoi de message
if (isset($_POST['envoyer'])) {
    // Récupération des données du formulaire
    $destinataire_id = $_POST['destinataire_id'];
    $message = $_POST['message'];

    

    // Connexion à la base de données
    $connexion = mysqli_connect("localhost", "root", "", "Leboncoin");

    // Vérification de la connexion
    if (!$connexion) {
        die("Erreur de connexion à la base de données : " . mysqli_connect_error());
    }

    // Échappement des caractères spéciaux dans le message pour éviter les injections SQL
    $message = mysqli_real_escape_string($connexion, $message);

    // Requête INSERT pour ajouter le message à la base de données
    $sql = "INSERT INTO messages (utilisateur_id, destinataire_id, message, annonce_id) VALUES ('{$_SESSION['utilisateur_id']}', '$destinataire_id', '$message', '$annonce_id')";

    // Exécution de la requête
    if (mysqli_query($connexion, $sql)) {
        // Redirection vers la page de messagerie avec un message de succès
        header("Location: messagerie.php?success=1");
        exit();
    } 
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Détails de l'annonce - Leboncoin</title>
    <link rel="stylesheet" href="annonce.css">
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
        <section>
            <h2>Détails de l'annonce</h2>
            <div class="annonce-container">
                <img src="<?php echo $annonce['photo']; ?>" alt="Image de l'annonce" class="annonce-image">
                <div class="annonce-details">
                    <h3 class="annonce-title"><?php echo $annonce['titre']; ?></h3>
                    <p><?php echo $annonce['description']; ?></p>
                    <p class="annonce-price">Prix : <?php echo $annonce['prix']; ?> €</p>
                    <p>Annonce créée par :
                        <a href="messagerie.php?destinataire=<?php echo $annonce['utilisateur_id']; ?>">
                            <?php echo $annonce['prenom'] . ' ' . $annonce['nom']; ?>
                        </a>
                    </p>

                    <h3>Envoyer un message</h3>
                    <form action="" method="post">
                        <input type="hidden" name="destinataire_id" value="<?php echo $annonce['utilisateur_id']; ?>">
                        <textarea name="message" rows="4" cols="50" placeholder="Votre message"></textarea>
                        <br>
                        <input type="submit" name="envoyer" value="Envoyer">
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
