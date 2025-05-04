<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: connexion.php");
    exit();
}

// Connexion
$pdo = new PDO("mysql:host=localhost;dbname=web;charset=utf8", "root", "root");

// Récupérer l'utilisateur connecté
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

// Mise à jour des infos
if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE utilisateurs SET email = ?, mot_de_passe = ?, nom = ?, prenom = ?, date_naissance = ?, adresse = ? WHERE id = ?");
    $stmt->execute([
        $_POST['email'],
        $_POST['mot_de_passe'],
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['date_naissance'],
        $_POST['adresse'],
        $utilisateur['id']
    ]);
    $_SESSION['email'] = $_POST['email']; // Mise à jour de l'email en session
    header("Location: moncompte.php");
    exit();
}

// Suppression du compte
if (isset($_POST['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->execute([$utilisateur['id']]);
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte - Click & Deals</title>
    <meta charset="utf-8" />
    <title>Gestion - Click & Deals</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="images/logo-transparent-png.png" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<header>
        <nav>
            <ul>
                <li><img class=logo src="images/logo-transparent-png.png" alt="logo" /></li>
                <li><a href="index.php">Accueil</a></li>
                <li>
                <?php
                    if (isset($_SESSION['email'])) {
                        try {
                            $stmt_nav = $pdo->prepare("SELECT admin FROM utilisateurs WHERE email = ?");
                            $stmt_nav->execute([$_SESSION['email']]);
                            $user_nav = $stmt_nav->fetch(PDO::FETCH_ASSOC);

                            if ($user_nav && $user_nav['admin']) {
                                echo '<a href="gestion.php">Gestion</a>';
                            } else {
                                echo '<a href="moncompte.php">Mon compte</a>';
                            }
                        } catch (PDOException $e) {
                            error_log("Erreur nav header: " . $e->getMessage());
                            echo '<a href="moncompte.php">Mon compte</a>';
                        }
                    } else {
                        echo '<a href="html/newletter.html">Newsletter</a>';
                    }
                 ?>
                </li>
                <li><a href="html/information.php">Information</a></li>
                <li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <a href="deconnexion.php">Déconnexion</a>
                    <?php else: ?>
                        <a href="html/indentification.php">S'identifier</a>
                    <?php endif; ?>
                </li>
                <li><a href="#cart-modal" id="panier-bouton">🛒 Panier</a>
                    <div id="cart-modal" style="display:none;">
                        <div class="cart-content">
                            <a href="#" class="close-modal">×</a>
                            <p>Votre panier est vide.</p>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
 
    <h2 style="text-align: center; color: white;">Mon compte</h2>

    <form class="compte" method="POST">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($utilisateur['email']) ?>" required style="color: white;">

        <label>Mot de passe </label>
        <input type="text" name="mot_de_passe" value="<?= htmlspecialchars($utilisateur['mot_de_passe']) ?>" required style="color: white;">

        <label>Nom </label>
        <input type="text" name="nom" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required style="color: white;">

        <label>Prénom </label>
        <input type="text" name="prenom" value="<?= htmlspecialchars($utilisateur['prenom']) ?>" required style="color: white;">

        <label>Date de naissance </label>
        <input type="date" name="date_naissance" value="<?= htmlspecialchars($utilisateur['date_naissance']) ?>" required style="color: white;">

        <label>Adresse </label>
        <textarea name="adresse" required><?= htmlspecialchars($utilisateur['adresse']) ?></textarea>
        </br>

        <div class="boutoncompte">
            <button class="boutoncompte" type="submit" name="update" style="transition: background-color 0.3s; text-align: center; display: flex; justify-content: center; align-items: center;" onmouseover="this.style.backgroundColor='green';" onmouseout="this.style.backgroundColor='';">Enregistrer les modifications</button>
            <a href="deconnexion.php"><button type="button">Se déconnecter</button></a>
            <button type="submit" name="delete" class="danger" onclick="return confirm('Supprimer votre compte ?')" style="transition: background-color 0.3s; text-align: center; display: flex; justify-content: center; align-items: center;" onmouseover="this.style.backgroundColor='red';" onmouseout="this.style.backgroundColor='';">Supprimer mon compte</button>
        </div>
    </form>
    <form class="theme" method="POST" style="margin-top: 20px; text-align: center;">
        <h3 style="color: white;">Changer le thème</h3>
        <label for="theme" style="color: white;">Choisissez un thème :</label>
        <select name="theme" id="theme" style="color: black; padding: 5px; border-radius: 5px;">
            <option value="clair" <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'clair') ? 'selected' : '' ?>>Clair</option>
            <option value="sombre" <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'sombre') ? 'selected' : '' ?>>Sombre</option>
        </select>
        <button type="submit" style="margin-left: 10px; padding: 5px 10px; border-radius: 5px; background-color: #4CAF50; color: white; border: none; cursor: pointer; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#45a049';" onmouseout="this.style.backgroundColor='#4CAF50';">Appliquer</button>
    </form>

    <?php
    // Gestion du thème via cookies
    if (isset($_POST['theme'])) {
        setcookie('theme', $_POST['theme'], time() + (86400 * 30), "/");
        header("Location: moncompte.php");
        exit();
    }
    ?>
</body>
</html>
