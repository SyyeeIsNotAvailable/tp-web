<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="Newsletter">
    <link rel="stylesheet" href="../style.css" />
    <title>Newsletter</title>
    <link rel="icon" href="../images/logo-transparent-png.png" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body id="backcolor">
    <header>
    </header>
    <main>
        <div class="newletters">
            <!-- Formulaire avec envoi POST vers le script PHP -->
            <form class="form2" method="POST" action="ajouter_utilisateur.php">
                <p class="titre">Bienvenue</p>
                <p class="titre2">Pour créer un compte</p>
                <p class="titre2">veuillez compléter les informations suivantes</p>

                <input type="text" name="nom" placeholder="Nom" required><br>
                <input type="text" name="prenom" placeholder="Prénom" required><br>
                <input type="date" name="date_naissance" placeholder="Date de naissance" required><br>
                <input type="text" name="adresse" placeholder="Adresse de livraison" required><br>
                <input type="email" name="email" placeholder="Email" required><br>
                <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br>

                <button type="submit" class="buttonConnexion">
                    <span>Créer le compte</span>
                    <img src="../images/icon-checkmark.png" height="50" width="50" />
                </button><br>

                <a href="../index.php">Retour à l'accueil</a>
            </form>
        </div>
    </main>
</body>
</html>
