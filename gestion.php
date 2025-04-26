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
        $stmt = $pdo->prepare("UPDATE article SET nom = ?, description = ?, prix = ?, etoiles = ?, stock = ? WHERE id_article = ?");
        $stmt->execute([
            $_POST['nom'],
            $_POST['description'],
            $_POST['prix'],
            $_POST['etoiles'],
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
        $imagePath = null;

        if (isset($_FILES['image_nouveau']) && $_FILES['image_nouveau']['error'] === UPLOAD_ERR_OK) {
            $uploadsDir = 'uploads/';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0777, true);
            }

            $filename = basename($_FILES['image_nouveau']['name']);
            $targetFile = $uploadsDir . uniqid() . '_' . $filename;
            move_uploaded_file($_FILES['image_nouveau']['tmp_name'], $targetFile);

            $imagePath = $targetFile;
        }

        $stmt = $pdo->prepare("INSERT INTO article (nom, description, prix, etoiles, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nom_nouveau'],
            $_POST['description_nouveau'],
            $_POST['prix_nouveau'],
            $_POST['etoiles_nouveau'],
            $_POST['stock_nouveau'],
            $imagePath
        ]);

        header("Location: gestion.php?status=article_added");
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de l'ajout d'un article : " . $e->getMessage());
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
    <link rel="icon" href="images/logo-transparent-png.png" type="image/x-icon" />
</head>
<body>
<header>
    <nav>
        <ul>
            <li><img class="logo" src="images/logo-transparent-png.png" alt="logo"></li>
            <li><a href="index.php">Accueil</a></li>
            <li>
            <?php
                if (isset($_SESSION['email'])) {
                    try {
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
    <thead>
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
    </thead>

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
<table class='gestion-table'>
    <thead>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Prix</th>
        <th>Étoiles</th>
        <th>Stock</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>
    </thead>
    <form method="POST" enctype="multipart/form-data">
    <tr>
        <td>Auto</td>
        <td><input name="nom_nouveau" required></td>
        <td><input name="description_nouveau" required></td>
        <td><input name="prix_nouveau" type="number" step="0.01" required></td>
        <td><input name="etoiles_nouveau" type="number" min="0" max="5" required></td>
        <td><input name="stock_nouveau" type="number" required></td>
        <td><input name="image_nouveau" type="file" accept="image/*" required></td>
        <td><button type="submit" name="ajouter_article">➕</button></td>
    </tr>
    </form>

    <?php foreach ($articles as $article): ?>
    <tr>
        <form method="POST">
            <td><?= $article['id_article'] ?></td>
            <td><input name="nom" value="<?= htmlspecialchars($article['nom']) ?>"></td>
            <td><input name="description" value="<?= htmlspecialchars($article['description']) ?>"></td>
            <td><input name="prix" type="number" step="0.01" value="<?= htmlspecialchars($article['prix']) ?>"></td>
            <td><input name="etoiles" type="number" value="<?= htmlspecialchars($article['etoiles']) ?>" min="0" max="5"></td>
            <td><input name="stock" type="number" value="<?= htmlspecialchars($article['stock']) ?>"></td>
            <td>
                <?php if (!empty($article['image'])): ?>
                    <img src="<?= htmlspecialchars($article['image']) ?>" alt="Image" style="max-width: 100px; max-height: 100px;">
                <?php else: ?>
                    Pas d'image
                <?php endif; ?>
            </td>
            <td>
                <input type="hidden" name="id_article" value="<?= $article['id_article'] ?>">
                <button type="submit" name="modifier_article">💾</button>
                <a href="gestion.php?delete_article=<?= $article['id_article'] ?>">🗑️</a>
            </td>
        </form>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
