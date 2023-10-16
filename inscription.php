<?php
// Variable pour stocker le message de réussite ou d'erreur
$message = "";

// Vérification si le formulaire est soumis
if (isset($_POST['submit'])) {
    // Récupération des données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérification de la correspondance entre le mot de passe et sa confirmation
    if ($password !== $confirm_password) {
        $message = "Le mot de passe et sa confirmation ne correspondent pas.";
    } elseif (strlen($password) < 10) {
        $message = "Le mot de passe doit contenir au moins 10 caractères.";
    } else {
        // Connexion à la base de données
        $mysqli = mysqli_connect("localhost", "root", "", "Leboncoin");

        // Vérification de la connexion
        if ($mysqli->connect_error) {
            die("Erreur de connexion à la base de données : " . $mysqli->connect_error);
        }

        // Vérification si l'adresse e-mail existe déjà en utilisant des instructions préparées pour éviter les injections SQL
        $checkQuery = "SELECT * FROM utilisateurs WHERE email = ?";
        $checkStmt = $mysqli->prepare($checkQuery);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $message = "Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.";
        } else {
            // Hachage du mot de passe en utilisant la fonction password_hash()
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Requête d'insertion des données avec des instructions préparées
            $insertQuery = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)";
            $insertStmt = $mysqli->prepare($insertQuery);
            $insertStmt->bind_param("ssss", $nom, $prenom, $email, $hashedPassword);

            // Exécution de la requête
            if ($insertStmt->execute()) {
                $message = "Inscription réussie !";
            } else {
                $message = "Erreur lors de l'inscription : " . $insertStmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>S'inscrire - Leboncoin</title>
    <link rel="stylesheet" href="inscription.css">
    <style>
        .message-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            display: <?php echo ($message) ? 'block' : 'none'; ?>;
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
        <section>
            <h2>S'inscrire</h2>
            <form method="post">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" required>
                <label for="prenom">Prénom :</label>
                <input type="text" name="prenom" required>
                <label for="email">Email :</label>
                <input type="email" name="email" <?php if (isset($message)) echo 'class="erreur"'; ?> required>
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" <?php if (isset($message)) echo 'class="erreur"'; ?> required>
                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" name="confirm_password" <?php if (isset($message)) echo 'class="erreur"'; ?> required>
                <?php if ($message) : ?>
                    <p class="message-popup"><?php echo $message; ?></p>
                <?php endif; ?>
                <button type="submit" name="submit">S'inscrire</button>
            </form>
        </section>
    </main>
</body>
</html>
