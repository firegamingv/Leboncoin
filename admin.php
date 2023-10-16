<?php
session_start();

// Vérification de l'authentification de l'utilisateur
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['utilisateur_id'] != 6) {
    header("Location: se_connecter.php");
    exit();
}

// Connexion à la base de données
$mysqli = mysqli_connect("localhost", "root", "", "Leboncoin");

// Vérification de la connexion
if ($mysqli->connect_error) {
    die("Erreur de connexion à la base de données : " . $mysqli->connect_error);
}

// Suppression d'une annonce
if (isset($_POST['supprimer_annonce'])) {
    $annonce_id = $_POST['annonce_id'];

    // Suppression de l'annonce
    $deleteAnnonceQuery = "DELETE FROM annonces WHERE id = '$annonce_id'";
    $mysqli->query($deleteAnnonceQuery);

    // Suppression des messages associés à l'annonce
    $deleteMessagesQuery = "DELETE FROM messages WHERE annonce_id = '$annonce_id'";
    $mysqli->query($deleteMessagesQuery);
}

// Suppression d'un utilisateur
if (isset($_POST['supprimer_utilisateur'])) {
    $utilisateur_id = $_POST['utilisateur_id'];

    if ($utilisateur_id != 6) { 
        $deleteUtilisateurQuery = "DELETE FROM utilisateurs WHERE id = '$utilisateur_id'";
        $mysqli->query($deleteUtilisateurQuery);

        // Suppression des annonces associées à l'utilisateur
        $deleteAnnoncesQuery = "DELETE FROM annonces WHERE utilisateur_id = '$utilisateur_id'";
        $mysqli->query($deleteAnnoncesQuery);

        // Suppression des messages associés à l'utilisateur
        $deleteMessagesQuery = "DELETE FROM messages WHERE utilisateur_id = '$utilisateur_id'";
        $mysqli->query($deleteMessagesQuery);
    } else {
        // Afficher un message d'erreur ou effectuer une autre action appropriée
        echo "Vous ne pouvez pas supprimer le compte administrateur.";
    }
}

// Récupération des annonces
$annoncesQuery = "SELECT * FROM annonces";
$annoncesResult = $mysqli->query($annoncesQuery);

// Récupération des utilisateurs
$utilisateursQuery = "SELECT * FROM utilisateurs";
$utilisateursResult = $mysqli->query($utilisateursQuery);

// Récupération des discussions entre utilisateurs
$discussionsQuery = "SELECT DISTINCT utilisateur_id, destinataire_id FROM messages";
$discussionsResult = $mysqli->query($discussionsQuery);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Leboncoin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h1>Admin - <a href="test-1.php" class="logo">Leboncoin</a></h1>
    </header>
    <main>
        <section>
            <h2>Annonces</h2>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Prix</th>
                            <th>Photo</th>
                            <th>Utilisateur</th>
                            <th>Date de création</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($annonce = $annoncesResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $annonce['id']; ?></td>
                                <td><?php echo $annonce['titre']; ?></td>
                                <td><?php echo $annonce['description']; ?></td>
                                <td><?php echo $annonce['prix']; ?></td>
                                <td><?php echo $annonce['photo']; ?></td>
                                <td><?php echo $annonce['utilisateur_id']; ?></td>
                                <td><?php echo $annonce['date_creation']; ?></td>
                                <td>
                                    <form action="admin.php" method="post">
                                        <input type="hidden" name="annonce_id" value="<?php echo $annonce['id']; ?>">
                                        <button class="button danger" type="submit" name="supprimer_annonce">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section>
            <h2>Utilisateurs</h2>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Date de création</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($utilisateur = $utilisateursResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $utilisateur['id']; ?></td>
                                <td><?php echo $utilisateur['nom']; ?></td>
                                <td><?php echo $utilisateur['prenom']; ?></td>
                                <td><?php echo $utilisateur['email']; ?></td>
                                <td><?php echo $utilisateur['date_creation']; ?></td>
                                <td>
                                    <form action="admin.php" method="post">
                                        <input type="hidden" name="utilisateur_id" value="<?php echo $utilisateur['id']; ?>">
                                        <button class="button danger" type="submit" name="supprimer_utilisateur">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            </section>
        <section>
            <h2>Discussions</h2>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Destinataire</th>
                            <th>Action</th>
                            <th>Supprimer</th> <!-- Nouvelle colonne pour la suppression -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($discussion = $discussionsResult->fetch_assoc()) :
                            $utilisateurId = $discussion['utilisateur_id'];
                            $destinataireId = $discussion['destinataire_id'];

                            $utilisateurQuery = "SELECT nom FROM utilisateurs WHERE id = '$utilisateurId'";
                            $destinataireQuery = "SELECT nom FROM utilisateurs WHERE id = '$destinataireId'";

                            $utilisateurResult = $mysqli->query($utilisateurQuery);
                            $destinataireResult = $mysqli->query($destinataireQuery);

                            // Vérification si les résultats de la requête contiennent des lignes
                            if ($utilisateurResult->num_rows > 0 && $destinataireResult->num_rows > 0) {
                                $utilisateur = $utilisateurResult->fetch_assoc();
                                $destinataire = $destinataireResult->fetch_assoc();
                            ?>
                                <tr>
                                    <td><?php echo $utilisateur['nom']; ?></td>
                                    <td><?php echo $destinataire['nom']; ?></td>
                                    <td>
                                        <a class="button" href="discussion.php?utilisateur_id=<?php echo $utilisateurId; ?>&destinataire_id=<?php echo $destinataireId; ?>">Voir la discussion</a>
                                    </td>
                                    <td>
                                        <form action="admin.php" method="post">
                                            <input type="hidden" name="utilisateur_id" value="<?php echo $utilisateurId; ?>">
                                            <input type="hidden" name="destinataire_id" value="<?php echo $destinataireId; ?>">
                                            <button class="button danger" type="submit" name="supprimer_discussion">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php
                            }
                        endwhile;
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>