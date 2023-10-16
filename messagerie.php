<?php
session_start();
            
if (!isset($_SESSION['utilisateur_id'])) {
    // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
    header("Location: se_connecter.php");
    exit();
}

$utilisateur_id = $_SESSION['utilisateur_id'];

// Connexion à la base de données
$connexion = mysqli_connect("localhost", "root", "", "Leboncoin");

// Vérification de la connexion
if (!$connexion) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}


// Récupérer les conversations de l'utilisateur avec les derniers messages
$query = "SELECT DISTINCT utilisateur_id, destinataire_id FROM messages WHERE utilisateur_id = $utilisateur_id OR destinataire_id = $utilisateur_id ORDER BY utilisateur_id, destinataire_id";

$result = mysqli_query($connexion, $query);

if (!$result) {
    echo "Erreur lors de la récupération des conversations : " . mysqli_error($connexion);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messagerie - LeBonCoin</title>
    <link rel="stylesheet" href="messagerie.css">
    <style>
        main {
            padding: 20px;
        }

        .conversations {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .conversation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        .conversation:hover {
            background-color: #ebebeb;
        }

        .conversation-info {
            flex: 1;
        }

        .conversation-info h2 {
            margin: 0;
            font-size: 1.2em;
            font-weight: bold;
        }

        .last-message {
            margin: 0;
            font-size: 0.9em;
        }

        .last-message-date {
            font-size: 0.8em;
            color: #888;
        }

        .annonce-link {
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <header>
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
        <a href="test-1.php" class="logo">LeBonCoin</a>
    </header>
    <main>
        <h1>Messagerie</h1>
        <div class="conversations">
            <?php
            if (mysqli_num_rows($result) > 0) {
                // Afficher les conversations avec les derniers messages
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($row['utilisateur_id'] == $utilisateur_id) {
                        $conversation_destinataire_id = $row['destinataire_id'];
                    } else {
                        $conversation_destinataire_id = $row['utilisateur_id'];
                    }

                    // Récupérer les détails de l'utilisateur avec qui l'utilisateur actuel a une conversation
                    $query_utilisateur = "SELECT nom, prenom FROM utilisateurs WHERE id = $conversation_destinataire_id";
                    $result_utilisateur = mysqli_query($connexion, $query_utilisateur);

                    if (!$result_utilisateur) {
                        echo "Erreur lors de la récupération des informations de l'utilisateur : " . mysqli_error($connexion);
                        exit();
                    }

                    $row_utilisateur = mysqli_fetch_assoc($result_utilisateur);
                    $nom_utilisateur = $row_utilisateur['nom'];
                    $prenom_utilisateur = $row_utilisateur['prenom'];

                    // Récupérer le dernier message de la conversation
                    $query_dernier_message = "SELECT message, date_envoi, annonce_id FROM messages WHERE (utilisateur_id = $utilisateur_id AND destinataire_id = $conversation_destinataire_id) OR (utilisateur_id = $conversation_destinataire_id AND destinataire_id = $utilisateur_id) ORDER BY date_envoi DESC LIMIT 1";
                    $result_dernier_message = mysqli_query($connexion, $query_dernier_message);

                    if (!$result_dernier_message) {
                        echo "Erreur lors de la récupération du dernier message : " . mysqli_error($connexion);
                        exit();
                    }

                    if (mysqli_num_rows($result_dernier_message) > 0) {
                        $row_dernier_message = mysqli_fetch_assoc($result_dernier_message);
                        $dernier_message = $row_dernier_message['message'];
                        $date_dernier_message = $row_dernier_message['date_envoi'];
                        $annonce_id = $row_dernier_message['annonce_id'];
                    } else {
                        $dernier_message = "Aucun message";
                        $date_dernier_message = "";
                        $annonce_id = "";
                    }

                    // Afficher les informations de l'utilisateur avec qui l'utilisateur actuel a une conversation
                    echo "<div class='conversation'>";
                    echo "<div class='conversation-info'>";
                    echo "<h2>$prenom_utilisateur $nom_utilisateur</h2>";
                    echo "<p class='last-message'>$dernier_message</p>";
                    echo "<p class='last-message-date'>$date_dernier_message</p>";
                    echo "</div>";
                    echo "<div class='annonce-link'>";
                    echo "<a href='conversation.php?destinataire_id=$conversation_destinataire_id'>Voir la discussion</a>";
                    if (!empty($annonce_id)) {
                        echo " | ";
                        echo "<a href='test-1.php?annonce_id=$annonce_id'>Voir l'annonce</a>";
                    }
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                // Afficher l'interface pour l'absence de messages
                echo "<p>Aucun message disponible pour le moment.</p>";
            }

            mysqli_close($connexion);
            ?>
        </div>
    </main>
</body>
</html>
