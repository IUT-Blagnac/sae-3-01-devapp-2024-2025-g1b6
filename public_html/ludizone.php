<?php
session_start();
include("connect.inc.php");
?>
<!DOCTYPE html>
<html lang="fr">
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

<?php
include("header.php");
?>

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
        <section class="coupDeCoeur">
            <h1 class="titreCDC">Coups de coeur</h1>
            <ul class="listeCDC">       
                    <?php
                        include("connect.inc.php");            
                        // Requête pour récupérer l'ID maximum des produits
                        $maxIdReq = $pdo->query("SELECT MAX(IDPROD) as maxId FROM PRODUIT");
                        $maxId = $maxIdReq->fetch()['maxId'];

                        $randomIds = [];
                        while (count($randomIds) < 4) {
                            $randomId = rand(1, $maxId);
                            if (!in_array($randomId, $randomIds)) {
                                $randomIds[] = $randomId;
                            }
                        }

                            $placeholders = implode(',', array_fill(0, count($randomIds), '?'));
                            $req = $pdo->prepare("SELECT * FROM PRODUIT WHERE IdPROD IN ($placeholders)");
                            $req->execute($randomIds);
                            $produits = $req->fetchAll();

                            foreach ($produits as $produit) {
                                echo "<li>";
                                    echo "<a href='descProduit.php?idProd=".$produit['IDPROD']."'>";
                                    echo "<div class\"produitCard\">";
                                        echo "<div class=\"imageContainer\">";
                                            echo "<img src='./images/prod". htmlspecialchars($produit['IDPROD']).".png' alt='Image du produit'>";
                                        echo "</div>";
                                        echo "<div class=\"infoContainer\">";
                                            echo "<h2>".$produit['NOMPROD']."</h2>";
                                            echo "<p>".$produit['PRIXHT']."€</p>";
                                        echo "</div>";
                                echo "</div>";
                                echo "</a>";
                                echo "</li>";
                            }
                    ?>
            </ul>
        </section>

        <section class="ludiGames">
                <h1 class="titreLudiGame">Ludi'Games</h1>
                <a href="ludiGame.php"><div class="imgLudiGame"></div></a>
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
    </div>


    <div class="enfantsProduits">

    </div>
        

        
</div>  

<?php include("footer.php"); ?>

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
