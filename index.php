<?php
session_start();
?>

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
                <li><a href="#cart-modal" id="panier-bouton">🛒 Panier</a>
                <div id="cart-modal">
        <div class="cart-content">
            <a href="#" class="close-modal">&times;</a>
            <p>Votre panier est vide.</p>
        </div></li>
            </ul> 
        </nav>
    </header>
    
    </br>
    <?php if (isset($_SESSION['email'])): ?>
        <div class="bienvenue">
        👋 Bienvenue, <?php
            $nomUtilisateur = explode('@', $_SESSION['email'])[0];
            echo htmlspecialchars($nomUtilisateur);
            ?> ! Vous êtes actuellement connecté sur votre compte !
        </div>
    <?php endif; ?>
    <main>
        <div class="barre-de-recherche">
            <input type="text" placeholder="Rechercher un produit" />
            <button>Rechercher</button>
        </div>
        </br>
        </br>
        <div class="articles">
            <div class="produit">
                <img class="image-produit" src="images/produit1.jpg" alt="produit1" />
                <h2 class="titre"><strong>999.99€</strong></h2>
                <h2 class="titre">Canard</h2>
                <button type="button" onclick="location.href='html/article1.html'"><span>Ajout au panier</span><img src="images/icon-checkmark.png" height="50" width="50" /></button>
                </br>
                <img src="images/etoile5.png" width="150" />
            </div>
            <div class="produit">
                <img class="image-produit" src="images/booster-pokemon.jpg" alt="produit1" />
                <h2 class="titre"><strong>499.99€</strong></h2>
                <h2 class="titre">Booster Pokemon Destinées Occultes</h2>
                <button type="button" onclick="location.href='html/article2.html'"><span>Ajout au panier</span><img src="images/icon-checkmark.png" height="50" width="50" /></button>
                </br>
                <img src="images/etoile4.png" width="150" />
        
            </div>
            <div class="produit">
                <img class="image-produit" src="images/joystick.jpg" alt="produit1" />
                <h2 class="titre"><strong>2 490.99€</strong></h2>
                <h2 class="titre">Joystick</h2>
                <button type="button" onclick="location.href='html/article3.html'"><span>Ajout au panier</span><img src="images/icon-checkmark.png" height="50" width="50" /></button>
                </br>
                <img src="images/etoile2.png" width="150" />
            </div>
            <div class="produit">
                <img class="image-produit" src="images/proteges-tibias-et-pieds-basic.jpg" alt="produit1" />
                <h2 class="titre"><strong>2 900.99€</strong></h2>
                <h2 class="titre">Protèges tibias</h2>
                <button type="button" onclick="location.href='html/article4.html'"><span>Ajout au panier</span><img src="images/icon-checkmark.png" height="50" width="50" /></button>
                </br>
                <img src="images/etoile3.png" width="150" />
            </div>
            <div class="produit">
                <img class="image-produit" src="images/selle-velo.jpg" alt="produit1" />
                <h2 class="titre"><strong>11 900.99€</strong></h2>
                <h2 class="titre">Selle de vélo VTT (Extra molle)</h2>
                <button type="button" onclick="location.href='html/article5.html'"><span>Ajout au panier</span><img src="images/icon-checkmark.png" height="50" width="50" /></button>
                </br>
                <img src="images/etoile1.png" width="150" />
            </div>
            <div class="produit">
                <img class="image-produit" src="images/pantoufle-homer.jpg" alt="produit1" />
                <h2 class="titre"><strong>1 000 000€</strong></h2>
                <h2 class="titre">Chausson Homer (Ouh punaise marge)</h2>
                <button type="button" onclick="location.href='html/article6.html'"><span>Ajout au panier</span><img src="images/icon-checkmark.png" height="50" width="50" /></button>
                </br>
                <img src="images/etoile5.png" width="150" />
            </div>
            <div class="produit">
                <img class="image-produit" src="images/deguissement-fortnite.jpg" alt="produit1" />
                <h2 class="titre"><strong>1 900.99€</strong></h2>
                <h2 class="titre">Deguissement Terreur Fluo (Fortnite battle pass)</h2>
                <button type="button" onclick="location.href='html/article7.html'"><span>Ajout au panier</span><img src="images/icon-checkmark.png" height="50" width="50" /></button>
                </br>
                <img src="images/etoile5.png" width="150" />
            </div>
            <div class="produit">
                <img class="image-produit" src="images/masque-cheval.webp" alt="produit1" />
                <h2 class="titre"><strong>75 000€</strong></h2>
                <h2 class="titre">Masque cheval ultra réaliste</h2>
                <button type="button" onclick="location.href='html/article8.html'"><span>Ajout au panier</span><img src="images/icon-checkmark.png" height="50" width="50" /></button>
                </br>
                <img src="images/etoile1.png" width="150" />
            </div>
            <div class="produit">
                <img class="image-produit" src="images/puff.jpg" alt="puff" />
                <h2 class="titre"><strong>9 000€</strong></h2>
                <h2 class="titre">Puff 9k</h2>
                <button type="button" onclick="location.href='html/article9.html'"><span>Ajout au panier</span><img src="images/icon-checkmark.png" height="50" width="50" /></button>
                </br>
                <img src="images/etoile5.png" width="150" />
            </div>
        </div> <!--- fin de la div articles-->
    </main>
    <hr />
    <footer>
        <p>&copy; 2024 - Click & Deals</p>
        <button type="button" onclick="location.href='#'">↑ Retournez en haut ↑</button>
    </footer>
</body>
</html>