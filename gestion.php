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
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if (isset($_GET['delete']) && !isset($_GET['delete_article'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: gestion.php?status=user_deleted");
        exit();
    } catch (PDOException $e) {
         die("Erreur lors de la suppression de l'utilisateur : " . $e->getMessage());
    }
}

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
         die("Erreur lors de la modification de l'utilisateur : " . $e->getMessage());
     }
}

if (isset($_GET['delete_article'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM article WHERE id_article = ?");
        $stmt->execute([$_GET['delete_article']]);
        header("Location: gestion.php?status=article_deleted");
        exit();
    } catch (PDOException $e) {
         die("Erreur lors de la suppression de l'article : " . $e->getMessage());
    }
}

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
         die("Erreur lors de la modification de l'article : " . $e->getMessage());
     }
}

try {
    $users = $pdo->query("SELECT id, email, admin, nom, prenom, date_naissance, adresse FROM utilisateurs ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Erreur lors de la récupération des utilisateurs : " . $e->getMessage() . "</p>";
    $users = [];
}

try {
    $stmt_articles = $pdo->query("SELECT id_article, nom, description, prix, etoiles, id_avis, stock FROM article ORDER BY id_article ASC");
    $articles = $stmt_articles->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Erreur lors de la récupération des articles : " . $e->getMessage() . "</p>";
    $articles = [];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
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

    <main>

        <?php if (isset($_GET['status'])): ?>
            <div class="status-message <?= (strpos($_GET['status'], 'error') === false) ? 'status-success' : 'status-error' ?>">
                <?php
                switch ($_GET['status']) {
                    case 'user_deleted': echo "Utilisateur supprimé avec succès !"; break;
                    case 'user_updated': echo "Utilisateur mis à jour avec succès !"; break;
                    case 'article_deleted': echo "Article supprimé avec succès !"; break;
                    case 'article_updated': echo "Article mis à jour avec succès !"; break;
                }
                ?>
            </div>
        <?php endif; ?>


        <h2 class='nom-gestion'>Gestion des utilisateurs</h2>
        <table class="gestion-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Date naissance</th>
                    <th>Adresse</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <form method="POST" action="gestion.php">
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"></td>
                            <td><input type="number" name="admin" value="<?= htmlspecialchars($user['admin'] ?? 0) ?>" min="0" max="1"></td>
                            <td><input type="text" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>"></td>
                            <td><input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>"></td>
                            <td><input type="date" name="date_naissance" value="<?= htmlspecialchars($user['date_naissance'] ?? '') ?>"></td>
                            <td><input type="text" name="adresse" value="<?= htmlspecialchars($user['adresse'] ?? '') ?>"></td>
                            <td class="actions">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" name="modifier" title="Enregistrer Utilisateur">💾</button>
                                <a href="gestion.php?delete=<?= $user['id'] ?>" onclick="return confirm('Supprimer cet utilisateur \'<?= htmlspecialchars(addslashes($user['email'] ?? ''), ENT_QUOTES) ?>\' ?')" title="Supprimer Utilisateur">🗑️</a>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Aucun utilisateur trouvé.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>


        <h2 class='nom-gestion'>Gestion des Articles</h2>
        <table class="gestion-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Prix (€)</th>
                    <th>Étoiles</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($articles)): ?>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <form method="POST" action="gestion.php">
                                <td><?= htmlspecialchars($article['id_article']) ?></td>
                                <td><input type="text" name="nom" value="<?= htmlspecialchars($article['nom'] ?? '') ?>"></td>
                                <td><input type="text" name="description" value="<?= htmlspecialchars($article['description'] ?? '') ?>"></td>
                                <td><input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($article['prix'] ?? 0.00) ?>" min="0"></td>
                                <td><input type="number" name="etoiles" value="<?= htmlspecialchars($article['etoiles'] ?? 0) ?>" min="0" max="5"></td>
                                <td><input type="number" name="stock" value="<?= htmlspecialchars($article['stock'] ?? 0) ?>" min="0"></td>
                                <td class="actions">
                                    <input type="hidden" name="id_article" value="<?= $article['id_article'] ?>">
                                    <button type="submit" name="modifier_article" title="Enregistrer Article">💾</button>
                                    <a href="gestion.php?delete_article=<?= $article['id_article'] ?>"
                                       onclick="return confirm('Supprimer cet article \'<?= htmlspecialchars(addslashes($article['nom'] ?? ''), ENT_QUOTES) ?>\' ?')"
                                       title="Supprimer Article">🗑️</a>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Aucun article trouvé dans la base de données.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </main>

    <footer>
        <!-- Pied de page -->
    </footer>

</body>
</html>