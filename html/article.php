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
    $stmt = $pdo->prepare("SELECT id_avis FROM avis WHERE id_article = :id_article");
    $stmt->execute(['id_article' => $id_article]);
    $avis = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (count($avis)>3){
        shuffle($avis);
        $avis=array_slice($avis,0,3);
    }
    if (!empty($avis)){
        $nom=[];
        $placeholders = implode(',', array_fill(0, count($avis), '?'));
        $stmt = $pdo->prepare("SELECT id_utilisateurs FROM avis WHERE id_avis IN ($placeholders)");
        $stmt->execute($avis);
        $id_utilisateurs = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($id_utilisateurs)) {
            $placeholders = implode(',', array_fill(0, count($id_utilisateurs), '?'));
            $stmt = $pdo->prepare("SELECT id, prenom FROM utilisateurs WHERE id IN ($placeholders)");
            $stmt->execute($id_utilisateurs);
            $utilisateurs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
            foreach ($id_utilisateurs as $id) {
                if (isset($utilisateurs[$id])) {
                    $nom[] = $utilisateurs[$id];
                } else {
                    $nom[] = "Inconnu";
                }
            }
        }
        $avis_description = [];
        $placeholders = implode(',', array_fill(0, count($avis), '?'));
        $stmt = $pdo->prepare("SELECT id_avis, commentaire FROM avis WHERE id_avis IN ($placeholders)");
        $stmt->execute($avis);
        $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($commentaires as $row) {
            $avis_description[$row['id_avis']] = $row['commentaire'];
        }
        
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
            <?php if (count($avis)>0): ?>
            <h3>Avis :</h3><?php endif ?>
            <?php
            for ($i = 0; $i < count($avis); $i++) {
                $id_avis = $avis[$i]; 
                if (isset($avis_description[$id_avis])) {
                    echo "<p><u>" . htmlspecialchars($nom[$i]) . " </u> : ". htmlspecialchars($avis_description[$id_avis]) . "</p>";
                }
            }
            ?>
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