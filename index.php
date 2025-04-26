<?php
session_start();

// Gestion du thème via cookies
if (isset($_POST['theme'])) {
    setcookie('theme', $_POST['theme'], time() + (86400 * 30), "/"); // valide 30 jours
    header("Location: index.php");
    exit();
}

$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : null;


$pdo = new PDO('mysql:host=localhost;dbname=ta_base;charset=utf8', 'utilisateurs', 'motdepasse');
$articles = $pdo->query("SELECT * FROM articles")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr" class="<?= $theme ? htmlspecialchars($theme) : '' ?>">
<head>
    <meta charset="utf-8" />
    <title>Click & Deals</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="images/logo-transparent-png.png" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php if (!$theme): ?>
    <div class="choix-theme">
        <form method="post">
            <p>Choisissez un thème :</p>
            <button class="boutoncookie1" type="submit" name="theme" value="clair">🌞 Thème clair</button>
            <button class="boutoncookie2" type="submit" name="theme" value="sombre">🌙 Thème sombre</button>
        </form>
    </div>
<?php endif; ?>

<header>
    <nav>
        <ul>
            <li><img class="logo" src="images/logo-transparent-png.png" alt="logo" /></li>
            <li><a href="index.php">Accueil</a></li>
            <li>
            <?php
                if (isset($_SESSION['email'])) {
                    try {
                        $pdo = new PDO("mysql:host=localhost;dbname=web;charset=utf8", "root", "root");
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $stmt = $pdo->prepare("SELECT admin FROM utilisateurs WHERE email = ?");
                        $stmt->execute([$_SESSION['email']]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo $user && $user['admin'] ? '<a href="gestion.php">Gestion</a>' : '<a href="moncompte.php">Mon compte</a>';
                    } catch (PDOException $e) {
                        echo "Erreur BDD : " . $e->getMessage();
                    }
                } else {
                    echo '<a href="html/newsletter.php">Newsletter</a>';
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
            <li><a href="monpanier.php" id="panier-bouton">🛒 Panier</a></li>
        </ul>
    </nav>
</header>

<br>
<?php if (isset($_SESSION['email'])): ?>
    <div class="bienvenue">
        Bienvenue, <?= htmlspecialchars(explode('@', $_SESSION['email'])[0]) ?> ! Vous êtes actuellement connecté sur votre compte !
    </div>
<?php endif; ?>

<main>
    <div class="barre-de-recherche">
        <input type="text" placeholder="Rechercher un produit" />
        <button>Rechercher</button>
    </div>
    <br><br>
    <div class="articles">
    

    <?php foreach ($articles as $article): ?>
        <div class="produit">
            <!-- Image du produit -->
            <img 
                class="image-produit" 
                src="<?= !empty($article['image']) ? htmlspecialchars($article['image']) : 'images/no-image.png' ?>" 
                alt="<?= htmlspecialchars($article['nom']) ?>" 
            />

            <!-- Prix -->
            <h2 class="titre">
                <strong><?= number_format($article['prix'], 2, ',', ' ') ?>€</strong>
            </h2>

            <!-- Nom du produit -->
            <h2 class="titre"><?= htmlspecialchars($article['nom']) ?></h2>

            <!-- Bouton d'ajout au panier -->
            <button type="button" onclick="location.href='html/article<?= intval($article['id_article']) ?>.html'">
                <span>Ajout au panier</span>
                <img src="images/icon-checkmark.png" height="50" width="50" />
            </button>
            <br/>

            <!-- Affichage des étoiles -->
            <?php
                $etoiles = isset($article['etoiles']) ? (int)$article['etoiles'] : 0;
                // Vérifier que l'image existe pour éviter les erreurs
                $etoiles = max(1, min($etoiles, 5)); // Entre 1 et 5 étoiles
                $cheminEtoile = "images/etoile" . $etoiles . ".png";
            ?>
            <img src="<?= $cheminEtoile ?>" width="150" />
        </div>
    <?php endforeach; ?>
</div>
 <!--- fin de la div articles-->
        </main>

<hr />
<footer>
    <p>&copy; 2024 - Click & Deals</p>
    <button type="button" onclick="location.href='#'">↑ Retournez en haut ↑</button>
</footer>
</body>
</html>