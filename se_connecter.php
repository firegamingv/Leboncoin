<?php
session_start();

$message_html = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['mot_de_passe'];

    if ($email && $password) {
        // Connexion à la base de données
        $connexion = mysqli_connect("localhost", "root", "", "Leboncoin");

        // Vérification de la connexion
        if (!$connexion) {
            die("Erreur de connexion à la base de données : " . mysqli_connect_error());
        }

        // Préparation de la requête pour récupérer l'utilisateur avec l'e-mail fourni (en utilisant des instructions préparées)
        $query = "SELECT * FROM utilisateurs WHERE email = ?";
        $stmt = mysqli_prepare($connexion, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            // L'utilisateur existe, on vérifie le mot de passe haché
            $row = mysqli_fetch_assoc($result);
            $hashedPassword = $row['mot_de_passe'];

            // Vérification du mot de passe haché
            if (password_verify($password, $hashedPassword)) {
                // L'utilisateur est authentifié, on démarre la session
                $_SESSION['utilisateur_id'] = $row['id'];
                $_SESSION['utilisateur_nom'] = $row['nom'];
                $_SESSION['utilisateur_prenom'] = $row['prenom'];

                // Message de connexion réussie
                $message = "Vous êtes connecté en tant que " . $row['prenom'] . " " . $row['nom'];
                $message_html = "<div class=\"popup show\">";
                $message_html .= "<h2>Connexion réussie</h2>";
                $message_html .= "<p>" . $message . "</p>";
                $message_html .= "</div>";
            } else {
                // Le mot de passe est incorrect
                $erreur = "Adresse e-mail ou mot de passe incorrect";
            }
        } else {
            // L'utilisateur n'existe pas
            $erreur = "Adresse e-mail ou mot de passe incorrect";
        }

     
    } else {
        // Les données fournies sont invalides
        $erreur = "Veuillez fournir une adresse e-mail valide et un mot de passe";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Leboncoin</title>
    <link rel="stylesheet" href="se_connecter.css">
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 9999;
        }

        .popup.show {
            display: block;
        }

        .popup h2 {
            margin-top: 0;
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
            <h2>Se connecter</h2>

            <?php if (isset($erreur)): ?>
                <div class="erreur">
                    <?php echo htmlspecialchars($erreur); ?>
                </div>
            <?php endif; ?>

            <form action="se_connecter.php" method="post">
                <label for="email">Adresse e-mail</label>
                <input type="email" name="email" id="email" required>
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" required>
                <button type="submit">Se connecter</button>
                <input type="hidden" name="utilisateur_id" value="id">
            </form>
            <p>Pas encore inscrit ? <a href="inscription.php">S'inscrire</a></p>
        </section>

        <?php echo $message_html; ?>
    </main>
</body>
</html>
