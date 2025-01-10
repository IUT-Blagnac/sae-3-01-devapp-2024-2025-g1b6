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
    <link rel="stylesheet" href="Css/grosseCateg.css">
</head>
<body>

    <?php
        include("header.php");
    ?>

    <div class="principale">
        <div class="filtres">
            <h1>Filtres</h1>
            <form method="GET" action="grosseCateg.php">
                <input type="hidden" name="idCateg" value="<?php echo $idCateg; ?>">
                <div class="filtreSection">
                    <label for="prixMin">Prix Min:</label>
                    <input type="number" name="prixMin" id="prixMin" min="0" value="<?php echo isset($_GET['prixMin']) ? htmlspecialchars($_GET['prixMin']) : ''; ?>">
                </div>
                <div class="filtreSection">
                    <label for="prixMax">Prix Max:</label>
                    <input type="number" name="prixMax" id="prixMax" min="0" value="<?php echo isset($_GET['prixMax']) ? htmlspecialchars($_GET['prixMax']) : ''; ?>">
                </div>
                <div class="filtreSection">
                    <label for="marque">Marque:</label>
                    <select name="marque" id="marque">
                        <option value="">Toutes</option>
                        <?php
                            include("connect.inc.php");
                            $marquesReq = $pdo->prepare("SELECT DISTINCT NOMMARQUE FROM MARQUE");
                            $marquesReq->execute();
                            while ($marque = $marquesReq->fetch()) {
                                $selected = (isset($_GET['marque']) && $_GET['marque'] == $marque['NOMMARQUE']) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($marque['NOMMARQUE']) . "' $selected>" . htmlspecialchars($marque['NOMMARQUE']) . "</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="filtreSection">
                    <label for="avis">Avis:</label>
                    <select name="avis" id="avis">
                        <option value="">Tous</option>
                        <option value="desc" <?php echo (isset($_GET['avis']) && $_GET['avis'] == 'desc') ? 'selected' : ''; ?>>Meilleures notes</option>
                        <option value="asc" <?php echo (isset($_GET['avis']) && $_GET['avis'] == 'asc') ? 'selected' : ''; ?>>Pires notes</option>
                    </select>
                </div>
                <button type="submit">Appliquer</button>
            </form>
        </div>

        <div class="grosseCategContainer">
            <div class="produitsContainer">
                <?php 
                    include("connect.inc.php");

                    $req = $pdo->prepare("SELECT * FROM CATEGORIE WHERE IDCATEG = :idCateg");
                    $req->execute(['idCateg' => $idCateg]);
                    $categ = $req->fetch();

                    echo "<h1 class='titreGrosseCateg'>" . $categ['NOMCATEG'] . "</h1>";
                ?>
                <ul id="produitsList">
                <?php 
                    // Configuration de la pagination
                    $limit = 11; // Number of products per page
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    // Filtrage
                    $prixMin = isset($_GET['prixMin']) ? (int)$_GET['prixMin'] : 0;
                    $prixMax = isset($_GET['prixMax']) ? (int)$_GET['prixMax'] : PHP_INT_MAX;
                    $marque = isset($_GET['marque']) ? $_GET['marque'] : '';
                    $categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';
                    $avis = isset($_GET['avis']) ? $_GET['avis'] : '';

                    // Requête pour récupérer le nombre total de produits
                    $query = "SELECT DISTINCT COUNT(*) as total FROM PRODUIT P 
                                            JOIN APPARTENIRCATEG A ON P.IDPROD = A.IDPROD
                                            JOIN CATEGORIE C ON A.IDCATEG = C.IDCATEG
                                            JOIN CATPERE CA ON C.IDCATEG = CA.IDCATEG
                                            LEFT JOIN AVIS AV ON P.IDPROD = AV.IDPROD
                                            WHERE CA.IDCATEG_PERE = :idCateg
                                            AND P.PRIXHT BETWEEN :prixMin AND :prixMax";
                    if ($marque) $query .= " AND P.IDMARQUE = (SELECT IDMARQUE FROM MARQUE WHERE NOMMARQUE = :marque)";
                    if ($avis) $query .= " ORDER BY AV.NOTE " . strtoupper($avis);
                    
                    $totalReq = $pdo->prepare($query);
                    $totalReq->bindParam(':idCateg', $idCateg, PDO::PARAM_INT);
                    $totalReq->bindParam(':prixMin', $prixMin, PDO::PARAM_INT);
                    $totalReq->bindParam(':prixMax', $prixMax, PDO::PARAM_INT);
                    if ($marque) $totalReq->bindParam(':marque', $marque, PDO::PARAM_STR);
                    $totalReq->execute();
                    $total = $totalReq->fetch()['total'];
                    $totalPages = ceil($total / $limit);

                    // Requête pour récupérer les produits
                    $query = "SELECT DISTINCT P.IDPROD, P.NOMPROD, P.PRIXHT, AVG(AV.NOTE) AS MOYENNE_NOTE
                                        FROM PRODUIT P 
                                        JOIN APPARTENIRCATEG A ON P.IDPROD = A.IDPROD
                                        JOIN CATEGORIE C ON A.IDCATEG = C.IDCATEG
                                        JOIN CATPERE CA ON C.IDCATEG = CA.IDCATEG
                                        LEFT JOIN AVIS AV ON P.IDPROD = AV.IDPROD
                                        WHERE CA.IDCATEG_PERE = :idCateg
                                        AND P.PRIXHT BETWEEN :prixMin AND :prixMax";
                    if ($marque) $query .= " AND P.IDMARQUE = (SELECT IDMARQUE FROM MARQUE WHERE NOMMARQUE = :marque)";
                    $query .= " GROUP BY P.IDPROD, P.NOMPROD, P.PRIXHT";
                    if ($avis) $query .= " ORDER BY MOYENNE_NOTE " . strtoupper($avis);
                    $query .= " LIMIT :limit OFFSET :offset";
                    
                    $req = $pdo->prepare($query);
                    $req->bindParam(':idCateg', $idCateg, PDO::PARAM_INT);
                    $req->bindParam(':prixMin', $prixMin, PDO::PARAM_INT);
                    $req->bindParam(':prixMax', $prixMax, PDO::PARAM_INT);
                    if ($marque) $req->bindParam(':marque', $marque, PDO::PARAM_STR);
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
                        <a class="page-number <?php echo ($i == $page) ? 'active' : ''; ?>" href="?idCateg=<?php echo $idCateg; ?>&page=<?php echo $i; ?>&prixMin=<?php echo $prixMin; ?>&prixMax=<?php echo $prixMax; ?>&marque=<?php echo $marque; ?>&avis=<?php echo $avis; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
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