<?php
session_start();
include("connect.inc.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"]["IDCLIENT"])) {
    header("Location: connexion.php");
    exit();
}

// Récupérer l'ID client depuis la session
$id_client = $_SESSION["user"]["IDCLIENT"];

// Préparer la requête pour récupérer les informations du client
$stmt = $pdo->prepare("SELECT * FROM CLIENT WHERE IDCLIENT = :id_client");
$stmt->execute(['id_client' => $id_client]);
$user = $stmt->fetch();


if (!$user) {
    echo "Erreur : utilisateur introuvable.";
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/compte.css">
    <title>Compte</title>
</head>
<body>
    
<header class="header">
        <div class="barreMenu">
            <ul class="menuListe">
                <li> 
                    <label class="burger" for="burgerToggle">
                        <input type="checkbox" id="burgerToggle">
                        <ul class="categories">
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM CATEGORIE WHERE IDCATEG_CATPERE IS NULL");
                            $stmt->execute();
                            $categories = $stmt->fetchAll();
                            foreach ($categories as $categorie) {
                                echo "<li><a href='categorie.php?id=".$categorie['IDCATEG']."'>".$categorie['NOMCATEG']."</a></li>";
                            }
                            ?>
                        </ul>
                        <span></span>
                        <span></span>
                        <span></span>
                    </label> 
                </li>
                <li> <a class="lienAccueil" href="index.php"><h1 class="titreLudorama"> Ludorama </h1>  </a></li>
                <li> <input class="barreRecherche" type="text" placeholder="Barre de recherche ..."> </li>
                <li> <div class="imgLoc"></div> </li>
                <li> <a href="panier.php"><div class="imgPanier"></div></a></li>
                <li> <?php
                        // Vérification de la session utilisateur
                        if (isset($_SESSION["user"])) {
                            $id_client = $_SESSION["user"]["IDCLIENT"];
                            // Si l'utilisateur est connecté, on le redirige vers son compte
                            echo '<a href="compte.php"><div class="imgCompte"></div></a>';
                        } else {
                            // Sinon, on le redirige vers la page de connexion
                            echo '<a href="connexion.php"><div class="imgCompte"></div></a>';
                        }
                    ?> 
                </li>
            </ul>
        </div>
    </header>



    <main>
        <div class="container">
            <ul class="listeCompte">
                <li>
                <div class="compte">
                    <h1 class="titreCompte">Mon compte</h1>
                    <div class="infosCompte">
                        <div class="infosPerso">
                            <div class="infos">
                                <p class="info">Nom : <?= isset($user['NOMCLIENT']) ? htmlspecialchars($user['NOMCLIENT']) : 'Non défini' ?></p>
                                <p class="info">Prenom : <?= isset($user['PRENOMCLIENT']) ? htmlspecialchars($user['PRENOMCLIENT']) : 'Non défini' ?></p>
                                <p class="info">Adresse : </p>
                                <p class="info">Code postal : </p>
                                <p class="info">Ville : </p>
                                <p class="info">Téléphone : <?= isset($user['NUMTEL']) ? htmlspecialchars($user['NUMTEL']) : 'Non défini' ?></p>
                                <p class="info">email : <?= isset($user['EMAIL']) ? htmlspecialchars($user['EMAIL']) : 'Non défini' ?></p>
                            </div>
                        </div>
                    </div>
                    
                     <a href="updateInfosClient.php"><button class="modifier" type="submit" name="updateInfo">Modifier</button></a>  
                </div>
            </li> 
                    <div class="commandes">
                        <div class="imgCommande"></div>
                        <h1 class="titreCommande">Mes commandes</h1>
                        <div class="infosCommande">
                            <ul class="listeCommandes">
                                <li class="liCommandes">Croque-Carotte</li>
                                <li class="liCommandes">Monopoly</li>
                                <li class="liCommandes">Figurine batman</li>
                                <li class="liCommandes">Voirure</li>
                            </ul>
                        </div>     
                    </div>
                </li>
            </ul>
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