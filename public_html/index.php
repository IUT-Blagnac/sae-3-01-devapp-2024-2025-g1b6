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
                <a href="derniereSortie.php"><button>Dernière sortie</button></a>
                <a href="nosCollection.php"><button>Nos collections</button></a>
                <button>Idées cadeaux</button>
                <button>Promotions !</button>
                <a href="collection.php?idCateg=23"><button>Découvrir la collection</button></a>
            </nav>
        </div>
    
        <section class="hero-section">
            <h1>Tu as rejoint l'aventure de Ludorama !</h1>
            <p>Suivez les dernières aventures de Woody dans Toys Story dans cette dernière collection !</p>
            
            <div class="image-container">
                <img src="images/toyStory.png" alt="Toys Story">
            </div>
        
            <a href="collection.php?idCateg=23"><button class="collection-btn">Découvrir la collection</button></a>
        
            <div class="age-categories">
                <div>
                    <a href="ludizone.php">
                        <img src="images/enfant.jpg" alt="Enfant">
                    </a>
                    <p>Enfant</p>
                </div>
                <div>
                    <a href="grosseCateg.php?idCateg=12">
                        <img src="images/adolescent.jpg" alt="Adolescent">
                    </a>
                    <p>Adolescent</p>
                </div>
                <div>
                    <a href="grosseCateg.php?idCateg=13">
                        <img src="images/jeune_adulte.jpg" alt="Jeune Adulte">
                    </a>
                    <p>Jeune adulte</p>
                </div>
                <div>
                    <a href="grosseCateg.php?idCateg=14">
                        <img src="images/adulte.jpg" alt="Adulte">
                    </a>
                    <p>Adulte</p>
                </div>
            </div>
        </section>
                

        <!-- Coup de coeur -->
             
    
    <section class="coupDeCoeur">
        <h1 class="titreCDC">Coups de coeur</h1>
        
        <ul class="listeCDC">       
            <?php
                include("connect.inc.php");            
                // Requête pour récupérer les 4 produits avec les moyennes des avis les plus hautes
                $sql = "SELECT p.IDPROD, p.NOMPROD, p.PRIXHT, AVG(a.NOTE) AS MOYENNE_NOTE
                        FROM PRODUIT p
                        JOIN AVIS a ON p.IDPROD = a.IDPROD
                        GROUP BY p.IDPROD, p.NOMPROD, p.PRIXHT
                        ORDER BY MOYENNE_NOTE DESC
                        LIMIT 4";
                $req = $pdo->query($sql);
                $produits = $req->fetchAll();
    
                // Affichage des produits
                foreach ($produits as $produit) {
                    echo "<li>";
                        echo "<a href='descProduit.php?idProd=" . $produit['IDPROD'] . "'>";
                            echo "<div class=\"produitCard\">";
                                echo "<div class=\"imageContainer\">";
                                    echo "<img src='./images/prod" . htmlspecialchars($produit['IDPROD']) . ".png' alt='Image du produit'>";
                                echo "</div>";
                                echo "<div class=\"infoContainer\">";
                                    echo "<h2>" . htmlspecialchars($produit['NOMPROD']) . "</h2>";
                                    echo "<p>" . htmlspecialchars($produit['PRIXHT']) . "€</p>";
                                    echo "<p>Note : " . number_format($produit['MOYENNE_NOTE'], 2) . "/5</p>";
                                echo "</div>";
                            echo "</div>";
                        echo "</a>";
                    echo "</li>";
                }
            ?>
        </ul>
    </section>
            
    </section>
            
    <!-- Section des événements -->
    <div class="ludiEvent">
        <h1 class="titreLudiEvent">Ludi'Events</h1>
            
        <ul class="listeEvent">
            <li>
                <div class="Event">
                    <div class="imgEvent1"></div>
                    <a href="ludiEvents.php?idEvent=1"><button class="btnParticipe"> J'y participe </button></a>
                </div>
            </li>
            
            <li>
                <div class="Event">
                    <div class="imgEvent2"></div>
                    <a href="ludiEvents.php?idEvent=2"><button class="btnParticipe"> J'y participe </button></a>
                </div>
            </li>
            
            <li>
                <div class="Event">
                    <div class="imgEvent3"></div>
                    <a href="ludiEvents.php?idEvent=3"><button class="btnParticipe"> J'y participe </button></a>
                </div>
            </li>
            
            <li>
                <div class="Event">
                    <div class="imgEvent4"></div>
                    <a href="ludiEvents.php?idEvent=4"><button class="btnParticipe"> J'y participe </button></a>
                </div>
            </li>
            
            <li>
                <div class="Event">
                    <div class="imgEvent5"></div>
                    <a href="ludiEvents.php?idEvent=5"><button class="btnParticipe"> J'y participe </button></a>
                </div>
            </li>
        </ul>
    </div>
            
    <div class="pack">
        <h1 class="titrePCK">Packs</h1>
            
        <ul class="listePCK">       
        <?php
        // Requête pour récupérer chaque pack avec un seul produit associé
        $req = $pdo->query("
            SELECT P.IDPACK, P.NOMPACK, MIN(PO.IDPROD) AS IDPROD, MIN(PO.NOMPROD) AS NOMPROD, PRIXPACK
            FROM PACK P
            LEFT JOIN ASSOPACK AP ON P.IDPACK = AP.IDPACK
            LEFT JOIN PRODUIT PO ON AP.IDPROD = PO.IDPROD
            GROUP BY P.IDPACK, P.NOMPACK
        ");
            
        $packs = $req->fetchAll();
            
        foreach ($packs as $pack) {
            // Définir le chemin de l'image du produit
            echo "<a href='descPack.php?idPack=".$pack['IDPACK']."'>";
            $imagePath = !empty($pack['IDPROD']) 
                ? "./images/prod" . htmlspecialchars($pack['IDPROD']) . ".png" 
                : "./images/default.png"; // Image par défaut si aucun produit associé
        
            echo "<li class='pack-item'>";
        
            if (!empty($pack['IDPROD'])) {
                echo "<img src='" . $imagePath . "' alt='" . htmlspecialchars($pack['NOMPROD']) . "' class='pack-image'>";
            } else {
                echo "<p>Aucun produit associé à ce pack.</p>";
            }
            echo "<h2>" . htmlspecialchars($pack['NOMPACK']) . "</h2>";
            echo "<p>" . number_format($pack['PRIXPACK'], 2, ',', ' ') . " €</p>";
            echo "</li>";
        }
        ?>
    
        </ul>
        
    </div>
    


</main>

    

    <?php include("footer.php") ?>


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
