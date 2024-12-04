<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/connexion.css">
    <link rel="stylesheet" href="Css/all.css">
    <title>Connexion</title>
</head>
<body>

    <header class="header">
        <div class="barreMenu">
            <ul class="menuListe">
                <li> <a class="lienAccueil" href="index.php"><h1 class="titreLudorama"> Ludorama </h1>  </a></li>
                <li> <div class="imgLoc"></div> </li>
                <li> <a href="panier.php"><div class="imgPanier"></div></a></li>
            </ul>
        </div>
    </header>




    <?php
    // Vérifiez si des cookies existent
    $email = isset($_COOKIE['email']) ? $_COOKIE['email'] : '';
    $password = isset($_COOKIE['password']) ? $_COOKIE['password'] : '';
    ?>
    <main>
        <div class="container">
            <div class="compte">
                <h1 class="titreCompte">Se connecter</h1>
                <div class="infos">
                    <form class="formConnexion" action="traitConnexion.php" method="post">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                        
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" value="<?= htmlspecialchars($password) ?>" required>
                        
                        <div class="checkbox_container">
                            <input type="checkbox" id="remember_me" name="remember_me" <?= $email ? 'checked' : '' ?>>
                            <label for="remember_me">Se souvenir de moi</label>
                        </div>
                        
                        <button type="submit">Se connecter</button>
                    </form>
                </div>
                <div class="creerCompte">
                    <p>Vous n'avez pas de compte ?</p>
                    <a href="inscription.php">Créer un compte</a>
                </div>
            </div>
        </div>
    </main>



<!-- Pied de page -->
<footer class="footer">
    <div class="footer-column">
        <h3>Qui sommes-nous ?</h3>
        <ul>
            <li><a href="#">Ludorama.com</a></li>
            <li><a href="#">Nos magasins</a></li>
            <li><a href="#">Cartes cadeaux</a></li>
        </ul>
    </div>
    <div class="footer-column">
        <h3>En ce moment</h3>
        <ul>
            <li><a href="#">Ambiance de Noël</a></li>
            <li><a href="#">Nouveautés</a></li>
            <li><a href="#">Rejoignez LudiSphere !</a></li>
        </ul>
    </div>
    <div class="footer-column">
        <h3>Marques</h3>
        <ul>
            <li><a href="#">Lego</a></li>
            <li><a href="#">Playmobil</a></li>
            <li><a href="#">Jurassic Park</a></li>
        </ul>
    </div>
    <div class="footer-column">
        <h3>Personnages jouets</h3>
        <ul>
            <li><a href="#">Pokemon</a></li>
            <li><a href="#">Tous les personnages</a></li>
        </ul>
    </div>
    <div class="footer-column">
        <h3>Nos sites</h3>
        <ul>
            <li><a href="#">France</a></li>
            <li><a href="#">Allemagne</a></li>
            <li><a href="#">Tous nos sites</a></li>
        </ul>
    </div>
</footer>


    
</body>
</html>