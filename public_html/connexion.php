<?php
    session_start();
    if(isset($_SESSION['admin'])){
        header("Location: dashboardAdmin.php");
        exit();
    }
?>
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

    <!-- En-tête -->
     <?php include 'header.php'; ?>

    <?php
    // Vérifiez si des cookies existent
    $EMAIL = isset($_COOKIE['EMAIL']) ? $_COOKIE['EMAIL'] : '';
    $PASSWORD = isset($_COOKIE['PASSWORD']) ? $_COOKIE['PASSWORD'] : '';

    // Récupérer les erreurs si présentes
    $errorMessage = "";
    if (isset($_GET['error'])) {
        switch ($_GET['error']) {
            case 1:
                $errorMessage = "email ou mot de passe incorrect.";
                break;
            case 2:
                $errorMessage = "Une erreur est survenue, veuillez réessayer plus tard.";
                break;
            default:
                $errorMessage = "";
        }
    }
    ?>

    <main>
        <div class="container">
            <div class="compte">
                <h1 class="titreCompte">Se connecter</h1>
                <div class="infos">
                    <form class="formConnexion" action="traitConnexion.php" method="post">
                        <!-- Affichage de l'erreur si elle existe -->
                        <?php if (!empty($errorMessage)): ?>
                            <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
                        <?php endif; ?>

                        <label for="EMAIL">email</label>
                        <input type="EMAIL" id="EMAIL" name="EMAIL" value="<?= htmlspecialchars($EMAIL) ?>" required>
                        
                        <label for="PASSWORD">Mot de passe</label>
                        <input type="PASSWORD" id="PASSWORD" name="PASSWORD" value="<?= htmlspecialchars($PASSWORD) ?>" required>
                        
                        <div class="checkbox_container">
                            <input type="checkbox" id="remember_me" name="remember_me" <?= $EMAIL ? 'checked' : '' ?>>
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
