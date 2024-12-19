<?php
session_start();

if (!isset($_GET['idCateg']) || empty($_GET['idCateg'])) {  
    header('Location: index.php');
    exit;
}

$idCateg = (int)$_GET['idCateg'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Categorie</title>
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/collection.css">
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


        <div class="collectionContainer">
            <div class="produitsContainer">
                <?php 
                    include("connect.inc.php");

                    $req = $pdo->prepare("SELECT * FROM CATEGORIE WHERE IDCATEG = :idCateg");
                    $req->execute(['idCateg' => $idCateg]);
                    $categ = $req->fetch();

                    echo "<h1 class='titreCollection'>" . $categ['NOMCATEG'] . "</h1>";
                ?>
                <ul id="produitsList">
                <?php 
                    // Configuration de la pagination
                    $limit = 8; // Number of products per page
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    // Requête pour récupérer le nombre total de produits
                    $totalReq = $pdo->prepare("SELECT DISTINCT COUNT(*) as total FROM PRODUIT P 
                                            JOIN APPARTENIRCATEG A ON P.IDPROD = A.IDPROD
                                            JOIN CATEGORIE C ON A.IDCATEG = C.IDCATEG
                                            WHERE C.IDCATEG = :idCateg;");
                    $totalReq->execute(['idCateg' => $idCateg]);
                    $total = $totalReq->fetch()['total'];
                    $totalPages = ceil($total / $limit);

                    // Requête pour récupérer les produits
                    $req = $pdo->prepare("SELECT DISTINCT P.IDPROD, P.NOMPROD, P.PRIXHT
                                        FROM PRODUIT P 
                                        JOIN APPARTENIRCATEG A ON P.IDPROD = A.IDPROD
                                        JOIN CATEGORIE C ON A.IDCATEG = C.IDCATEG
                                        WHERE C.IDCATEG = :idCateg
                                        LIMIT :limit OFFSET :offset;");
                    $req->bindParam(':idCateg', $idCateg, PDO::PARAM_INT);
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
                        <a class="page-number <?php echo ($i == $page) ? 'active' : ''; ?>" href="?idCateg=<?php echo $idCateg; ?>&page=<?php echo $i; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            </div>
            
        </div>
    </div>



    <?php
        include("footer.php");
    ?>



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
                fetch(`grosseCateg.php?idCateg=<?php echo $idCateg; ?>&page=${page}`)
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