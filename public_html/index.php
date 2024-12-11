<?php
    session_start();
?>

<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ludorama</title>
    <link rel="stylesheet" href="Css/accueil.css">
    <link rel="stylesheet" href="Css/all.css">
</head>
<body>

    <!-- Barre de navigation -->
    <?php include("header.php") ?>
    
    

    <!-- Section principale -->
    <main>
        
        <div class="navigation-container">
            <nav>
                <button>Dernière sortie</button>
                <button>Nos collections</button>
                <button>Idées cadeaux</button>
                <button>Promotions !</button>
                <button>Découvrir la collection</button>
            </nav>
        </div>
    
        <section class="hero-section">
            <h1>Tu as rejoint l'aventure de Ludorama !</h1>
            <p>Suivez les dernières aventures de Woody dans Toys Story dans cette dernière collection !</p>
            
            <div class="image-container">
                <img src="images/toyStory.png" alt="Toys Story">
            </div>
        
            <button class="collection-btn">Découvrir la collection</button>
        
            <div class="age-categories">
                <div>
                    <a href="ludizone.php">
                        <img src="images/enfant.jpg" alt="Enfant">
                    </a>
                    <p>Enfant</p>
                </div>
                <div>
                    <a href="#">
                        <img src="images/adolescent.jpg" alt="Adolescent">
                    </a>
                    <p>Adolescent</p>
                </div>
                <div>
                    <a href="#">
                        <img src="images/jeune_adulte.jpg" alt="Jeune Adulte">
                    </a>
                    <p>Jeune adulte</p>
                </div>
                <div>
                    <a href="#">
                        <img src="images/adulte.jpg" alt="Adulte">
                    </a>
                    <p>Adulte</p>
                </div>
            </div>
        </section>
                

        <!-- Coup de coeur -->
         

<section class="coupDeCoeur">
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
            <a href="descProduit.php">
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
    
</section>


<!-- Section des événements -->
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


    <!-- Script menu déroulant -->
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
