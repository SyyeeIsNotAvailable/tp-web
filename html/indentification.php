<?php
session_start();
require_once('../connexion.php'); // Assure-toi que ce fichier contient la connexion PDO

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Requête pour vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email AND mot_de_passe = :password");
    $stmt->execute(['email' => $email, 'password' => $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['email'] = $user['email'];
        header("Location: ../index.php");
        exit();
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Identification</title>
    <link rel="stylesheet" href="../style.css" />
</head>
<body id="backcolor">
    <div class="container">
        <form class="form1" method="POST">
            <p class="titre">Bienvenue</p>

            <?php if (isset($erreur)) echo "<p style='color:red;'>$erreur</p>"; ?>

            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Mot de passe" required><br>

            <button type="submit" class="buttonConnexion">
                <span>Connexion</span>
                <img src="../images/icon-checkmark.png" height="50" width="50" />
            </button><br>

            <a href="mdp_oublier.html">Mot de passe oublié</a><br>
            <a href="../index.php">Retour à l'accueil</a>
        </form>
    </div>
</body>
</html>
