<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$dbname = 'web';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Suppression utilisateur
if (isset($_GET['delete']) && !isset($_GET['delete_article'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: gestion.php?status=user_deleted");
        exit();
    } catch (PDOException $e) {
        die("Erreur suppression utilisateur : " . $e->getMessage());
    }
}

// Modification utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET email = ?, admin = ?, nom = ?, prenom = ?, date_naissance = ?, adresse = ? WHERE id = ?");
        $stmt->execute([
            $_POST['email'],
            $_POST['admin'],
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['date_naissance'],
            $_POST['adresse'],
            $_POST['id']
        ]);
        header("Location: gestion.php?status=user_updated");
        exit();
    } catch (PDOException $e) {
        die("Erreur modification utilisateur : " . $e->getMessage());
    }
}

// Suppression article
if (isset($_GET['delete_article'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM article WHERE id_article = ?");
        $stmt->execute([$_GET['delete_article']]);
        header("Location: gestion.php?status=article_deleted");
        exit();
    } catch (PDOException $e) {
        die("Erreur suppression article : " . $e->getMessage());
    }
}

// Modification article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_article'])) {
    try {
        $stmt = $pdo->prepare("UPDATE article SET nom = ?, description = ?, prix = ?, etoiles = ?, id_avis = ?, stock = ? WHERE id_article = ?");
        $id_avis_value = !empty($_POST['id_avis']) ? $_POST['id_avis'] : null;
        $stmt->execute([
            $_POST['nom'],
            $_POST['description'],
            $_POST['prix'],
            $_POST['etoiles'],
            $id_avis_value,
            $_POST['stock'],
            $_POST['id_article']
        ]);
        header("Location: gestion.php?status=article_updated");
        exit();
    } catch (PDOException $e) {
        die("Erreur modification article : " . $e->getMessage());
    }
}

// Ajout d'article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_article'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO article (nom, description, prix, etoiles, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nom_nouveau'],
            $_POST['description_nouveau'],
            $_POST['prix_nouveau'],
            $_POST['etoiles_nouveau'],
            $_POST['stock_nouveau']
        ]);
        header("Location: gestion.php?status=article_added");
        exit();
    } catch (PDOException $e) {
        die("Erreur ajout article : " . $e->getMessage());
    }
}

$users = $pdo->query("SELECT * FROM utilisateurs ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$articles = $pdo->query("SELECT * FROM article ORDER BY id_article ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
<h2 class='nom-gestion'>Gestion des utilisateurs</h2>
<table class='gestion-table'>
    <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Admin</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Naissance</th>
        <th>Adresse</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <form method="POST">
            <td><?= $user['id'] ?></td>
            <td><input name="email" value="<?= $user['email'] ?>"></td>
            <td><input name="admin" type="number" value="<?= $user['admin'] ?>" min="0" max="1"></td>
            <td><input name="nom" value="<?= $user['nom'] ?>"></td>
            <td><input name="prenom" value="<?= $user['prenom'] ?>"></td>
            <td><input name="date_naissance" type="date" value="<?= $user['date_naissance'] ?>"></td>
            <td><input name="adresse" value="<?= $user['adresse'] ?>"></td>
            <td>
                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                <button name="modifier">💾</button>
                <a href="gestion.php?delete=<?= $user['id'] ?>">🗑️</a>
            </td>
        </form>
    </tr>
    <?php endforeach; ?>
</table>

<h2 class='nom-gestion'>Gestion des articles</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Prix</th>
        <th>Étoiles</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <tr>
        <form method="POST">
            <td>Auto</td>
            <td><input name="nom_nouveau" required></td>
            <td><input name="description_nouveau" required></td>
            <td><input name="prix_nouveau" type="number" step="0.01" required></td>
            <td><input name="etoiles_nouveau" type="number" min="0" max="5" required></td>
            <td><input name="stock_nouveau" type="number" required></td>
            <td><button name="ajouter_article">➕</button></td>
        </form>
    </tr>
    <?php foreach ($articles as $article): ?>
    
    <tr>
        <form method="POST">
            <td><?= $article['id_article'] ?></td>
            <td><input name="nom" value="<?= $article['nom'] ?>"></td>
            <td><input name="description" value="<?= $article['description'] ?>"></td>
            <td><input name="prix" type="number" step="0.01" value="<?= $article['prix'] ?>"></td>
            <td><input name="etoiles" type="number" value="<?= $article['etoiles'] ?>" min="0" max="5"></td>
            <td><input name="stock" type="number" value="<?= $article['stock'] ?>"></td>
            <td>
                <input type="hidden" name="id_article" value="<?= $article['id_article'] ?>">
                <button name="modifier_article">💾</button>
                <a href="gestion.php?delete_article=<?= $article['id_article'] ?>">🗑️</a>
            </td>
        </form>
    </tr>
    <?php endforeach; ?>
    
</table>
</body>
</html>
