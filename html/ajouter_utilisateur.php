<?php
$host = 'localhost';
$dbname = 'web'; 
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupération des données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $adresse = $_POST['adresse'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $admin = 0;

    // Vérifie si l'email existe déjà
    $verif = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
    $verif->execute([':email' => $email]);
    
    if ($verif->fetch()) {
        echo "❌ Cet e-mail est déjà utilisé. <a href='javascript:history.back()'>Retour</a>";
        exit;
    }

    try {
        // Requête d'insertion
        $sql = "INSERT INTO utilisateurs (email, mot_de_passe, admin, nom, prenom, date_naissance, adresse)
                VALUES (:email, :mot_de_passe, :admin, :nom, :prenom, :date_naissance, :adresse)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':mot_de_passe' => $mot_de_passe,
            ':admin' => $admin,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':date_naissance' => $date_naissance,
            ':adresse' => $adresse
        ]);

        // Redirection après succès
        header("Location: ../index.php");
        exit;
    } catch (PDOException $e) {
        echo "❌ Erreur lors de l'insertion : " . $e->getMessage();
        exit;
    }
} else {
    echo "❌ Méthode HTTP non autorisée.";
    exit;
}
?>
