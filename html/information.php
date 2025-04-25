<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="Information">
    <link rel="stylesheet" href="../style.css" />
    <title>Information</title>
    <link rel="icon" href="../images/logo-transparent-png.png" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
<header>
        <nav>
        
            <ul>
                <li><img class=logo src="../images/logo-transparent-png.png" alt="logo" /></li>
                <li><a href="../index.php">Accueil</a></li>
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
                                echo '<a href="../gestion.php">Gestion</a>';
                            } else {
                                echo '<a href="../moncompte.php">Mon compte</a>';
                            }
                    } catch (PDOException $e) {
                        echo "Erreur de connexion à la base de données : " . $e->getMessage();
                    }
                    } else {
                        echo '<a href="newsletter.php">Newsletter</a>';
                    }
            ?>
            </li>

                <li><a href="information.php">Information</a></li>
                <li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <a href="../deconnexion.php">Déconnexion</a>
                    <?php else: ?>
                        <a href="indentification.php">S'identifier</a>
                    <?php endif; ?>
                </li>
                <li><a href="../monpanier.php" id="panier-bouton">🛒 Panier</a>
                </li>
            </ul> 
        </nav>
    </header>
    <main>
        <h1 id="titre-info">Nos informations</h1>
        <section class="text-info">
            Fondée en 2024, <strong>Click & Deals</strong> est née d'une passion pour l'innovation et le commerce en ligne. Depuis lors, nous avons travaillé sans relâche pour devenir l'une des principales destinations de shopping en ligne pour les consommateurs du monde entier.
            </br></br>
            Depuis nos débuts, nous avons toujours cru à l'importance d'offrir à nos clients une gamme diversifiée de produits, tout en garantissant une expérience d'achat fluide et agréable. Que vous recherchiez des accessoires technologiques, des articles de mode, des équipements pour la maison ou des cadeaux uniques, nous avons tout ce qu'il vous faut, et plus encore.
            </br></br>
            Chez <strong> Click & Deals</strong>, nous croyons que le shopping en ligne doit être simple, rapide et amusant. C'est pourquoi nous nous engageons à :</br>
            
            - Sélectionnez des produits de qualité provenant de fournisseurs de confiance.</br>
            - Proposer des prix compétitifs pour vous permettre de faire de bonnes affaires.</br>
            - Offrir un service client réactif et à l'écoute, prêt à vous accompagner à chaque étape de votre expérience.</br>
            - Notre croissance rapide témoigne de la confiance que nous accordons à nos clients à travers le monde. Aujourd'hui, <strong>Click & Deals</strong> est positionné comme un acteur clé du e-commerce.
            </br></br>
            Nous sommes fiers de collaborer avec des partenaires internationaux pour vous proposer des nouveautés chaque jour, livrées directement à votre porte. Grâce à nos systèmes de paiement sécurisés et à notre logistique optimisée, votre satisfaction est notre priorité
            </br>
            Alors, que vous soyez un acheteur régulier ou un nouveau venu, nous vous invitons à explorer notre boutique et à rejoindre la grande famille Click & Deals .
            </br>
            Votre confiance est notre moteur, et nous avons hâte de continuer à vous surprendre et à vous inspirer, année après année.
            </br></br>
            <strong>Click & Deals</strong>, des prix qui excitent, des offres qui font des deals !
        </section>
        <h1 id="titre-info">Nous contacter</h1>
        <section class="text-info2">
            <source><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6794.446492310675!2d3.046185477119046!3d50.634207474115236!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c2d578da129f7d%3A0xd134d73fb7f4c699!2sJunia%20ISEN%20Lille!5e1!3m2!1sfr!2sfr!4v1732876524806!5m2!1sfr!2sfr"></iframe></source>
            <div>
                <p>Adresse : 41 Boulevard Vauban, 59000 Lille</p>
                <p>Service-client@Click&Deals.com</p>
                <p>+33 06 01 02 03 04</p>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 - Click & Deals</p>
    </footer>
</body>
</html>