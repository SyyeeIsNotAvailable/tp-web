<?php
$host = 'localhost';
$dbname = 'web'; // Remplace par le nom de ta base si différent
$username = 'root';
$password = 'root'; // mot de passe MAMP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>