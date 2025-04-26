<?php
session_start();

// Gestion du thème via cookies
if (isset($_POST['theme'])) {
    setcookie('theme', $_POST['theme'], time() + (86400 * 30), "/");
    header("Location: index.php");
    exit();
}

$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : null;

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=web;charset=utf8', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Ajout d'un nouvel article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $image = $_POST['image'] ?? '';
    $etoiles = $_POST['etoiles'] ?? 5;

    $stmt = $pdo->prepare('INSERT INTO articles (nom, prix, image, etoiles) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nom, $prix, $image, $etoiles]);
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Récupération des articles
$articles = $pdo->query('SELECT * FROM article')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr" class="<?= $theme ? htmlspecialchars($theme) : '' ?>">
<head>
    <meta charset="utf-8" />
    <title>Click & Deals</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="images/logo-transparent-png.png" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .articles {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .produit {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
            width: 250px;
            text-align: center;
            background: #f9f9f9;
        }
        .image-produit {
            width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: cover;
        }
        form.ajout-article {
            margin-bottom: 30px;
            padding: 20px;
            border: 2px dashed #666;
            border-radius: 10px;
            width: 400px;
        }
        form.ajout-article input, form.ajout-article select {
            margin-bottom: 10px;
            width: 100%;
            padding: 8px;
        }
    </style>
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
                    $stmt = $pdo->prepare("SELECT admin FROM utilisateurs WHERE email = ?");
                    $stmt->execute([$_SESSION['email']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo $user && $user['admin'] ? '<a href="gestion.php">Gestion</a>' : '<a href="moncompte.php">Mon compte</a>';
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

    <!-- Barre de recherche -->
    <div class="barre-de-recherche">
        <input type="text" placeholder="Rechercher un produit" />
        <button>Rechercher</button>
    </div>
    <br><br>

    <!-- Affichage des articles -->
    <div class="articles">
        <?php if (count($articles) > 0): ?>
            <?php foreach ($articles as $article): ?>
                <div class="produit">
                    <img class="image-produit" src="<?= !empty($article['image']) ? htmlspecialchars($article['image']) : 'images/no-image.png' ?>" alt="<?= htmlspecialchars($article['nom']) ?>" />
                    <h2 class="titre"><strong><?= number_format($article['prix'], 2, ',', ' ') ?>€</strong></h2>
                    <h2 class="titre"><?= htmlspecialchars($article['nom']) ?></h2>
                    <button type="button" onclick="location.href='html/article<?= intval($article['id_article']) ?>.html'">
                        <span>Ajout au panier</span>
                        <img src="images/icon-checkmark.png" height="50" width="50" />
                    </button>
                    <br/>
                    <?php
                        $etoiles = isset($article['etoiles']) ? (int)$article['etoiles'] : 5;
                        $etoiles = max(1, min($etoiles, 5));
                        $cheminEtoile = "images/etoile" . $etoiles . ".png";
                    ?>
                    <img src="<?= $cheminEtoile ?>" width="150" />
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <h2 class='nom-gestion' >Aucun article disponible pour le moment.</h2>
        <?php endif; ?>
    </div>

</main>

<hr />

<footer>
    <p>&copy; 2024 - Click & Deals</p>
    <button type="button" onclick="location.href='#'">↑ Retournez en haut ↑</button>
</footer>

</body>
</html>
