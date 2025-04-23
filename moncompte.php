<?php

session_start();
$pdo = new PDO("mysql:host=localhost;dbname=web", "root", "root");

// 👇 Protection propre
$id = $_SESSION['user_id'] ?? null;
if (!$id) {
    header("Location: connexion.php");
    exit;
}

// Récupération des données utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement formulaire
if (isset($_POST['update'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $date_naissance = $_POST['date_naissance'];
    $adresse = $_POST['adresse'];
    $mot_de_passe = $_POST['mot_de_passe'];

    if (!empty($mot_de_passe)) {
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $sql = "UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, date_naissance = ?, adresse = ?, mot_de_passe = ? WHERE id = ?";
        $params = [$nom, $prenom, $email, $date_naissance, $adresse, $mot_de_passe_hash, $id];
    } else {
        $sql = "UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, date_naissance = ?, adresse = ? WHERE id = ?";
        $params = [$nom, $prenom, $email, $date_naissance, $adresse, $id];
    }

    $update = $pdo->prepare($sql);
    $update->execute($params);

    echo "<p>✅ Informations mises à jour avec succès.</p>";

    // Rafraîchir l'utilisateur
    $stmt->execute([$id]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>


<form method="POST" action="mon_compte.php">
  <label>Nom :</label>
  <input type="text" name="nom" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required><br>

  <label>Prénom :</label>
  <input type="text" name="prenom" value="<?= htmlspecialchars($utilisateur['prenom']) ?>" required><br>

  <label>Email :</label>
  <input type="email" name="email" value="<?= htmlspecialchars($utilisateur['email']) ?>" required><br>

  <label>Date de naissance :</label>
  <input type="date" name="date_naissance" value="<?= htmlspecialchars($utilisateur['date_naissance']) ?>"><br>

  <label>Adresse :</label>
  <textarea name="adresse" required><?= htmlspecialchars($utilisateur['adresse']) ?></textarea><br>

  <label>Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
  <input type="password" name="mot_de_passe"><br>

  <button type="submit" name="update">Mettre à jour</button>
</form>

