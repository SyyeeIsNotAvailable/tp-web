<?php
session_start();
require_once('../connexion.php'); 

if (isset($_GET['id'])) {
    $id_article = (int)$_GET['id']; 


    $stmt = $pdo->prepare("SELECT * FROM article WHERE id_article = :id_article");
    $stmt->execute(['id_article' => $id_article]);
    $article = $stmt->fetch();



    if (!$article) {
        echo "L'article demandé n'existe pas.";
        exit();
    }
} else {
    echo "Aucun article sélectionné.";
    exit();
}
?>


<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Click & Deals</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="icon" href="../images/logo-transparent-png.png" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><img class=logo src="../images/logo-transparent-png.png" alt="logo" /></li>
                <li><a id=accueil href="../index.php">Accueil</a></li>
                <li><a id=newsletter href="../html/newsletter.html">Newsletter</a></li>
                <li><a id=information href="../html/information.html">Information</a></li>
                <li><a id=identification href="../html/indentification.php">S'identifier</a></li>
                <li><?php if (isset($_SESSION['email'])): ?>
                    <a href="../monpanier.php" id="panier-bouton">🛒 Panier</a>
                <?php endif ?></li>

            </ul> 
        </nav>
    </header>
    <main class="open_article">
        <div>
            <img src="../<?= !empty($article['image']) ? htmlspecialchars($article['image']) : 'images/no-image.png' ?>" alt="<?= htmlspecialchars($article['nom']) ?>" />
        </div>
        <div class="textDescription">   
            <h1><?= $article['nom'] ?></h1>
            <p><?= $article['description'] ?></p>
            <h3>Avis :</h3>
            <p><u>Mumu le player</u> : </br>Bon produit, vraiment conforme à la description</p>
            <p><u>Lord Maximous</u> : </br>Super pour faire une blague à mes beaux-parents</p>
            <p><u>LeRacketteur</u> : </br>Très efficace pour faire peur au pseudo-judoka</p>
        </div>
        <div class="text">
            <h4>Neuf :</h4>
            <h1><?= $article['prix'] ?>€</h1>
            <p><?= $article['etoiles'] ?> étoiles</p>
            <p>Livraison gratuite sous 10 jours</p>
            <button type="button" class="buttonConnexion"><span>Ajout au panier</span><img src="../images/icon-checkmark.png" height="50" width="50" /></button>
            <p>Stock : <?= $article['stock'] ?></p>
        </div>
    </main>
    <hr />
    <footer>
        <p>&copy; 2024 - Click & Deals</p>
    </footer>
</body>
</html>