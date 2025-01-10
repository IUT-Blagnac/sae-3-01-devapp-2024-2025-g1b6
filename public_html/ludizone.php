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


    <div class="ludizone">
        <h1 class="titreLudizone">Bienvenue dans la LudiZone !</h1>
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
        <h1>Nos produits pour enfants !</h1>
        <ul id="produitsList">
        <?php 
            // Configuration de la pagination
            $limit = 11; // Number of products per page
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Requête pour récupérer le nombre total de produits
            $totalReq = $pdo->query("SELECT COUNT(*) as total FROM PRODUIT P 
                                     JOIN APPARTENIRCATEG A ON P.IDPROD = A.IDPROD
                                     JOIN CATEGORIE C ON A.IDCATEG = C.IDCATEG
                                     JOIN CATPERE CA ON C.IDCATEG = CA.IDCATEG
                                     WHERE CA.IDCATEG_PERE = 11;");
            $total = $totalReq->fetch()['total'];
            $totalPages = ceil($total / $limit);

            // Requête pour récupérer les produits
            $req = $pdo->prepare("SELECT P.IDPROD, P.NOMPROD, P.PRIXHT
                                  FROM PRODUIT P 
                                  JOIN APPARTENIRCATEG A ON P.IDPROD = A.IDPROD
                                  JOIN CATEGORIE C ON A.IDCATEG = C.IDCATEG
                                  JOIN CATPERE CA ON C.IDCATEG = CA.IDCATEG
                                  WHERE CA.IDCATEG_PERE = 11
                                  LIMIT :limit OFFSET :offset;");
            $req->bindParam(':limit', $limit, PDO::PARAM_INT);
            $req->bindParam(':offset', $offset, PDO::PARAM_INT);
            $req->execute();
            $produits = $req->fetchAll();

            foreach ($produits as $produit) {
                echo "<li>";
                    echo "<a href='descProduit.php?idProd=".$produit['IDPROD']."'>";
                    echo "<div class=\"produitCard\">";
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
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="page-number <?php echo ($i == $page) ? 'active' : ''; ?>" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
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

        // Pagination script
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const page = event.target.getAttribute('data-page');
                fetch(`ludizone.php?page=${page}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newList = doc.querySelector('#produitsList').innerHTML;
                        document.querySelector('#produitsList').innerHTML = newList;
                        window.history.pushState({}, '', `?page=${page}`);

                        // Update numéro de page actif
                        document.querySelector('.pagination .active').classList.remove('active');
                        event.target.classList.add('active');
                    });
            });
        });
    });
</script>

</body>
</html>
