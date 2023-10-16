<?php
session_start();

// Vérification de l'authentification de l'utilisateur
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: se_connecter.php");
    exit();
}

// Récupération des paramètres utilisateur_id et destinataire_id depuis l'URL
if (!isset($_GET['utilisateur_id']) || !isset($_GET['destinataire_id'])) {
    // Rediriger vers une page d'erreur ou une autre action appropriée
  
    exit();
}

$utilisateurId = $_GET['utilisateur_id'];
$destinataireId = $_GET['destinataire_id'];

// Connexion à la base de données
$mysqli = mysqli_connect("localhost", "root", "", "Leboncoin");

// Vérification de la connexion
if ($mysqli->connect_error) {
    die("Erreur de connexion à la base de données : " . $mysqli->connect_error);
}

// Vérification si l'utilisateur et le destinataire existent dans la base de données
$utilisateurQuery = "SELECT * FROM utilisateurs WHERE id = '$utilisateurId'";
$destinataireQuery = "SELECT * FROM utilisateurs WHERE id = '$destinataireId'";

$utilisateurResult = $mysqli->query($utilisateurQuery);
$destinataireResult = $mysqli->query($destinataireQuery);

if ($utilisateurResult->num_rows == 0 || $destinataireResult->num_rows == 0) {
    // Rediriger vers une page d'erreur ou une autre action appropriée
    header("Location: erreur.php");
    exit();
}

// Récupération des messages de la discussion entre l'utilisateur et le destinataire
$messagesQuery = "SELECT * FROM messages WHERE (utilisateur_id = '$utilisateurId' AND destinataire_id = '$destinataireId') OR (utilisateur_id = '$destinataireId' AND destinataire_id = '$utilisateurId') ORDER BY date_envoi ASC";
$messagesResult = $mysqli->query($messagesQuery);

// Récupération du nom de l'utilisateur et du destinataire
$utilisateur = $utilisateurResult->fetch_assoc();
$destinataire = $destinataireResult->fetch_assoc();

// Fermeture de la connexion à la base de données
$mysqli->close();
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Discussion - Leboncoin</title>
    <link rel="stylesheet" href="discussion.css">
</head>
<body>
    <header>
        <h1>Discussion - Leboncoin</h1>
    </header>
    <main>
        <section>
            <h2>Discussion entre l'utilisateur <?php echo $utilisateur['nom']; ?> et le destinataire <?php echo $destinataire['nom']; ?></h2>
            <div class="messages-container">
                <?php while ($message = $messagesResult->fetch_assoc()) : ?>
                    <div class="message">
                        <p class="message-sender">De: <?php echo $utilisateur['nom']; ?></p>
                        <p class="message-content"><?php echo $message['message']; ?></p>
                        <p class="message-date">Envoyé le <?php echo $message['date_envoi']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
        <section>
            <h2>Nouveau message</h2>
            <form action="envoyer_message.php" method="post">
                <input type="hidden" name="utilisateur_id" value="<?php echo $utilisateurId; ?>">
                <input type="hidden" name="destinataire_id" value="<?php echo $destinataireId; ?>">
                <textarea name="contenu" placeholder="Entrez votre message"></textarea>
                <button class="button" type="submit">Envoyer</button>
            </form>
        </section>
        <a href="admin.php">Retour à la page admin</a>
    </main>
</body>
</html>
