<?php
session_start();

if (!isset($_SESSION['utilisateur_id'])) {
    // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
    header("Location: se_connecter.php");
    exit();
}

$utilisateur_id = $_SESSION['utilisateur_id'];

// Vérifier si le destinataire_id est passé en paramètre d'URL
if (isset($_GET['destinataire_id'])) {
    $destinataire_id = $_GET['destinataire_id'];
} else {
    // Rediriger l'utilisateur vers la page de messagerie s'il n'y a pas de destinataire_id spécifié
    header("Location: messagerie.php");
    exit();
}

// Connexion à la base de données
$connexion = mysqli_connect("localhost", "root", "", "Leboncoin");

// Vérification de la connexion
if (!$connexion) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

// Récupérer les messages de la conversation entre l'utilisateur et le destinataire
$query = "SELECT * FROM messages WHERE (utilisateur_id = $utilisateur_id AND destinataire_id = $destinataire_id) OR (utilisateur_id = $destinataire_id AND destinataire_id = $utilisateur_id) ORDER BY date_envoi ASC LIMIT 50";
$result = mysqli_query($connexion, $query);

if (!$result) {
    echo "Erreur lors de la récupération des messages : " . mysqli_error($connexion);
    exit();
}

// Envoyer un nouveau message
if (isset($_POST['envoyer'])) {
    $message = $_POST['message'];
    $date_envoi = date("Y-m-d H:i:s");

    // Requête d'insertion du nouveau message dans la base de données
    $insertQuery = "INSERT INTO messages (utilisateur_id, destinataire_id, message, date_envoi) VALUES ($utilisateur_id, $destinataire_id, '$message', '$date_envoi')";
    $insertResult = mysqli_query($connexion, $insertQuery);

    if ($insertResult) {
        // Actualiser la page après l'envoi du message
        header("Location: conversation.php?destinataire_id=$destinataire_id");
        exit();
    } else {
        echo "Erreur lors de l'envoi du message : " . mysqli_error($connexion);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Conversation - LeBonCoin</title>
    <link rel="stylesheet" href="conversation.css">
    <style>
    

        .messages {
            display: flex;
            flex-direction: column;
        }

        .message {
            margin-bottom: 10px;
        }

        .message p {
            padding: 5px;
            border-radius: 5px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .message-utilisateur p {
            background-color: #3366cc;
            color: white;
        }

        .message span {
            font-size: 0.8em;
            color: #888;
        }
    </style>
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
        <h1>Conversation</h1>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            $message = $row['message'];
            $date_envoi = $row['date_envoi'];
            $est_utilisateur_courant = $row['utilisateur_id'] == $utilisateur_id;

            // Détermine la classe CSS en fonction de l'utilisateur courant
            $classe_message = $est_utilisateur_courant ? 'message message-utilisateur' : 'message';

            // Afficher les messages avec la classe CSS appropriée
            echo "<div class='$classe_message'>";
            echo "<p>$message</p>";
            echo "<span class='date'>$date_envoi</span>";
            echo "</div>";
        }
        ?>

        </div>
        <form action="" method="post">
            <label for="message">Nouveau message :</label>
            <textarea name="message" id="message" rows="4" required></textarea>
            <input type="submit" name="envoyer" value="Envoyer">
        </form>
    </main>
</body>
</html>
