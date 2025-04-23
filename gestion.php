<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la base
$pdo = new PDO("mysql:host=localhost;dbname=web;charset=utf8", 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Suppression
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: gestion.php");
    exit();
}

// Modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $stmt = $pdo->prepare("UPDATE utilisateurs SET email = ?, mot_de_passe = ?, admin = ?, nom = ?, prenom = ?, date_naissance = ?, adresse = ? WHERE id = ?");
    $stmt->execute([
        $_POST['email'],
        $_POST['mot_de_passe'],
        $_POST['admin'],
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['date_naissance'],
        $_POST['adresse'],
        $_POST['id']
    ]);
    header("Location: gestion.php");
    exit();
}

// Récupération des utilisateurs
$users = $pdo->query("SELECT * FROM utilisateurs")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        td, th { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f4f4f4; }
        form { margin: 0; }
        .actions a { margin-right: 10px; color: red; }
    </style>
</head>
<body>
<!DOCTYPE html>

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
                        echo '<a href="html/newletter.html">Newsletter</a>';
                    }
            ?>
            </li>

                <li><a href="html/information.html">Information</a></li>
                <li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <a href="deconnexion.php">Déconnexion</a>
                    <?php else: ?>
                        <a href="html/indentification.php">S'identifier</a>
                    <?php endif; ?>
                </li>
                <li><a href="#cart-modal" id="panier-bouton">🛒 Panier</a>
                <div id="cart-modal">
        <div class="cart-content">
            <a href="#" class="close-modal">&times;</a>
            <p>Votre panier est vide.</p>
        </div></li>
            </ul> 
        </nav>
    </header>
<h2>Gestion des utilisateurs</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Mot de passe</th>
        <th>Admin</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Date de naissance</th>
        <th>Adresse</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($users as $user): ?>
        <tr>
            <form method="POST">
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><input type="text" name="email" value="<?= htmlspecialchars($user['email']) ?>"></td>
                <td><input type="text" name="mot_de_passe" value="<?= htmlspecialchars($user['mot_de_passe']) ?>"></td>
                <td><input type="number" name="admin" value="<?= htmlspecialchars($user['admin']) ?>" min="0" max="1"></td>
                <td><input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>"></td>
                <td><input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>"></td>
                <td><input type="date" name="date_naissance" value="<?= htmlspecialchars($user['date_naissance']) ?>"></td>
                <td><input type="text" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>"></td>
                <td class="actions">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <button type="submit" name="modifier">💾</button>
                    <a href="gestion.php?delete=<?= $user['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">🗑️</a>
                </td>
            </form>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
