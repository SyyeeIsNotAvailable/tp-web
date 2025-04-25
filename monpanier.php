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

// 🔄 Requête avec le vrai prix de l'article
$sql = "SELECT a.nom, a.prix 
        FROM panier p
        JOIN article a ON p.id_article = a.id_article
        WHERE p.id_utilisateur = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_utilisateur]);
$articles = $stmt->fetchAll();
?>


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
    <h2 class='nom-gestion'>Votre panier</h2>
<?php if (empty($articles)): ?>
    <p>Votre panier est vide.</p>
<?php else: ?>
    <table class="gestion-table">
        <thead>
            <tr>
                <th>Nom de l'article</th>
                <th>Prix</th>
                <th>Supprimer</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($articles as $article):
                $total += $article['prix'];
            ?>
                <tr>
                    <td><?= htmlspecialchars($article['nom']) ?></td>
                    <td><?= number_format($article['prix'], 2) ?> €</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong><?= number_format($total, 2) ?> €</strong></td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>
