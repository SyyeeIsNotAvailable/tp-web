<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la BDD
$pdo = new PDO("mysql:host=localhost;dbname=web;charset=utf8", "root", "root");

// Récupérer l'utilisateur connecté
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    echo "Utilisateur non trouvé.";
    exit();
}

$id_utilisateur = $utilisateur['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_article'])) {
    $id_article = $_POST['id_article'] ?? null;

    if ($id_article) {
        // Vérifier la quantité actuelle
        $checkStmt = $pdo->prepare("SELECT quantité FROM panier WHERE id_utilisateur = ? AND id_article = ?");
        $checkStmt->execute([$id_utilisateur, $id_article]);
        $article = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($article && $article['quantité'] > 1) {
            // Décrémenter la quantité
            $updateStmt = $pdo->prepare("UPDATE panier SET quantité = quantité - 1 WHERE id_utilisateur = ? AND id_article = ?");
            $updateStmt->execute([$id_utilisateur, $id_article]);
        } else {
            // Supprimer l'article si la quantité est 1
            $deleteStmt = $pdo->prepare("DELETE FROM panier WHERE id_utilisateur = ? AND id_article = ?");
            $deleteStmt->execute([$id_utilisateur, $id_article]);
        }
    }
}

$sql = "SELECT a.nom, p.prix, p.id_article, p.quantité 
        FROM panier p
        JOIN article a ON p.id_article = a.id_article
        WHERE p.id_utilisateur = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_utilisateur]);
$articles = $stmt->fetchAll();
?>

<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Click & Deals</title>
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
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    if (isset($_SESSION['email'])) {
                        // Connexion à la base de données (MAMP par défaut : user root, mdp root)
                        $host = 'localhost';
                        $dbname = 'web';
                        $username = 'root';
                        $password = 'root';

                        try {
                            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            // Requête pour récupérer l'attribut admin de l'utilisateur connecté
                            $stmt = $pdo->prepare("SELECT admin FROM utilisateurs WHERE email = ?");
                            $stmt->execute([$_SESSION['email']]);
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($user && $user['admin']) {
                                echo '<a href="gestion.php">Gestion</a>';
                            } else {
                                echo '<a href="moncompte.php">Mon compte</a>';
                            }
                    } catch (PDOException $e) {
                        echo "Erreur de connexion à la base de données : " . $e->getMessage();
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
                <li><a href="monpanier.php" id="panier-bouton">🛒 Panier</a>
                </li>
            </ul> 
        </nav>
    </header>
</body>
<h2 class='nom-gestion'>Votre panier</h2>

<?php if (empty($articles)): ?>
    <p>Il n'y a aucun article dans votre panier pour le moment</p>
<?php else: ?>
    <table class='gestion-table'>
        <thead>
            <tr>
                <th>Nom de l'article</th>
                <th>Prix unitaire</th>
                <th>Quantité</th>
                <th>Prix total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($articles as $article):
                $prix_total_article = $article['prix'] * $article['quantité'];
                $total += $prix_total_article;
            ?>
                <tr>
                    <td><?= htmlspecialchars($article['nom']) ?></td>
                    <td><?= number_format($article['prix'], 2) ?> €</td>
                    <td><?= $article['quantité'] ?></td>
                    <td><?= number_format($prix_total_article, 2) ?> €</td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_article" value="<?= $article['id_article'] ?>">
                            <button type="submit" name="supprimer_article" onclick="return confirm('Supprimer un exemplaire de cet article ?')">🗑 Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td><strong>Total</strong></td>
                <td></td>
                <td></td>
                <td><strong><?= number_format($total, 2) ?> €</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>
