<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/ludizone.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>LudiZone</title>
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
                            include ("connect.inc.php");
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
                            echo '<a href="compte.php?id_client=' . $id_client . '"><div class="imgCompte"></div></a>';
                        } else {
                            // Sinon, on le redirige vers la page de connexion
                            echo '<a href="connexion.php"><div class="imgCompte"></div></a>';
                        }
                    ?> 
                </li>
            </ul>
        </div>
    </header>



<div class="principale">

    <div class="filtres">
        <h1>Filtres</h1>
        <ul class="listFiltres">
            <li class="liFiltres">Prix</li>
            <li class="liFiltres">Marques</li>
            <li class="liFiltres">Catégorie</li>
            <li class="liFiltres">Avis</li>
        </ul>
    </div>

    <div class="ludizone">
        <h1 class="titreLudizone">Bienvenue dans la LudiZone !</h1>
        <div class="coupDeCoeur">
            <h1 class="titreCDC">Coup de coeur</h1>
            
            <ul class="listeCDC">
                
                <li class="jeuCDC"> 
                    <div class="imgJeuCDC1"></div> 
                    <div class="nomJeuCDC">Croque-Carotte</div> 
                    <div class="prixCDC">35€</div> 
                    <div class="dsiponibiliteCDC"> 
                        <ul class="listeDisponibilites">
                            <li class="elementDispo">Web Magasin</li>
                            <li class="elementDispo">disponible disponible</li>
                            <li> <img class="elementDispoImg" src="images/pointVert.png" alt="ptVert"> <img class="elementDispoImg" src="images/pointVert.png" alt="ptVert"></li>
                        </ul>
                    </div>
                </li>
                
                <li class="jeuCDC">
                    <a href="DescriptionProduit.php">
                    <div class="imgJeuCDC2"></div> 
                    </a>
                    <div class="nomJeuCDC">Nerf </div> 
                    <div class="prixCDC">29.99€</div> 
                    <div class="dsiponibiliteCDC"> 
                        <ul class="listeDisponibilites">
                            <li class="elementDispo">Web Magasin</li>
                            <li class="elementDispo">disponible disponible</li>
                            <li><img class="elementDispoImg" src="images/pointVert.png" alt="ptVert"> <img class="elementDispoImg" src="images/pointVert.png" alt="ptVert"></li>
                        </ul>
                    </div>
                </li>

                <li class="jeuCDC"> 
                    <div class="imgJeuCDC3"></div> 
                    <div class="nomJeuCDC">Docteur Maboul</div> 
                    <div class="prixCDC">46€</div> 
                    <div class="dsiponibiliteCDC"> 
                        <ul class="listeDisponibilites">
                            <li class="elementDispo">Web Magasin</li>
                            <li class="elementDispo">disponible disponible</li>
                            <li> <img class="elementDispoImg" src="images/pointVert.png" alt="ptVert"> <img class="elementDispoImg" src="images/pointVert.png" alt="ptVert"></li>
                        </ul>
                    </div>
                </li>

                <li class="jeuCDC"> 
                    <div class="imgJeuCDC4"></div> 
                    <div class="nomJeuCDC">Monoopoly</div> 
                    <div class="prixCDC">30€</div> 
                    <div class="dsiponibiliteCDC"> 
                        <ul class="listeDisponibilites">
                            <li class="elementDispo">Web Magasin</li>
                            <li class="elementDispo">disponible disponible</li>
                            <li> <img class="elementDispoImg" src="images/pointVert.png" alt="ptVert"> <img class="elementDispoImg" src="images/pointVert.png" alt="ptVert"></li>
                        </ul>
                    </div>
                </li>
            </ul>

        </div>

        <div class="ludiGames">
            <h1 class="titreLudiGame">Ludi'Games</h1>
            <div class="imgLudiGame"></div>
        </div>


        <div class="ludiEvent">
            <h1 class="titreLudiEvent">Ludi'Events</h1>
            
            <ul class="listeEvent">
                <li>
                    <div class="Event">
                        <div class="imgEvent1"></div>
                        <button class="btnParticipe"> J'y participe </button>
                    </div>
                </li>

                <li>
                    <div class="Event">
                        <div class="imgEvent2"></div>
                        <button class="btnParticipe"> J'y participe </button>
                    </div>
                </li>

                <li>
                    <div class="Event">
                        <div class="imgEvent3"></div>
                        <button class="btnParticipe"> J'y participe </button>
                    </div>
                </li>

                <li>
                    <div class="Event">
                        <div class="imgEvent4"></div>
                        <button class="btnParticipe"> J'y participe </button>
                    </div>
                </li>

                <li>
                    <div class="Event">
                        <div class="imgEvent5"></div>
                        <button class="btnParticipe"> J'y participe </button>
                    </div>
                </li>
            </ul>
        </div>

        <div class="vide">

        </div>

    </div>
    
</div>  


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


<script>
    document.addEventListener('DOMContentLoaded', () => {
    const burgerToggle = document.getElementById('burgerToggle');
    const categoriesMenu = document.querySelector('.categories');

    // Fermer le menu déroulant si on clique ailleurs
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.burger')) {
            burgerToggle.checked = false; // Décocher la checkbox pour fermer le menu
        }
    });

    // Empêcher le clic sur le menu burger de se propager
    burgerToggle.addEventListener('click', (event) => {
        event.stopPropagation();
    });
});
</script>



</body>


</html>