<?php
session_start();
require_once('../connexion.php'); 

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
    <link rel="icon" href="../images/logo-transparent-png.png" type="image/x-icon" />
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

            <a href="newsletter.php">Creer un compte</a><br> 
            <a href="mdp_oublier.html">Mot de passe oublié</a><br>
            <a href="../index.php">Retour à l'accueil</a>
        </form>
    <div style="text-align: center; margin-top: 20px;">
        <form class="theme" method="POST" style="display: inline-block; text-align: center; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px;">
            <h3 style="color: black; margin-bottom: 15px;">Changer le thème</h3>
            <label for="theme" style="color: black; font-weight: bold;">Choisissez un thème :</label>
            <select name="theme" id="theme" style="color: black; padding: 5px; border-radius: 5px; margin-left: 10px;">
                <option value="clair" <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'clair') ? 'selected' : '' ?>>Clair</option>
                <option value="sombre" <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'sombre') ? 'selected' : '' ?>>Sombre</option>
            </select>
            <button type="submit" style="margin-left: 10px; padding: 5px 10px; border-radius: 5px; background-color: #4CAF50; color: white; border: none; cursor: pointer; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#45a049';" onmouseout="this.style.backgroundColor='#4CAF50';">Appliquer</button>
        </form>
    </div>

    <?php
    // Gestion du thème via cookies
    if (isset($_POST['theme'])) {
        setcookie('theme', $_POST['theme'], time() + (86400 * 30), "/");
        header("Location: indentification.php");
        exit();
    }
    ?>
</body>
</html>
