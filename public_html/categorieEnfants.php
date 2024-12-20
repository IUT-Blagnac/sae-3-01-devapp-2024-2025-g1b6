<?php
session_start();

if (!isset($_GET['idCategorie']) || empty($_GET['idCategorie'])) {
    echo "<h1> Aucune Catégories Séléctionner </h1>";
    exit();
}

$idCategorie = htmlspecialchars($_GET['idCategorie']);
$idCategoriePere = htmlspecialchars($_GET['idCategoriePere']);

?>

<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sous-Catégories</title>
    <link rel="stylesheet" href="Css/categorieEnfants.css">
    <link rel="stylesheet" href="Css/all.css">
</head>
<body>
    
    <?php include("header.php"); ?>

    <div class="containerCategPere">
        <div class="filtres">
            <h1>Filtres</h1>
            <form method="GET" action="categorieEnfants.php">
                <input type="hidden" name="idCategorie" value="<?php echo $idCategorie; ?>">
                <input type="hidden" name="idCategoriePere" value="<?php echo $idCategoriePere; ?>">
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
                            $marquesReq = $pdo->query("SELECT DISTINCT NOMMARQUE FROM MARQUE");
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

        <div class="containerProduit">
            <?php 
                include("connect.inc.php");

                // Configuration de la pagination
                $limit = 8; // Number of products per page
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $limit;

                // Initialisation des filtres
                $prixMin = isset($_GET['prixMin']) && $_GET['prixMin'] !== '' ? (int)$_GET['prixMin'] : null;
                $prixMax = isset($_GET['prixMax']) && $_GET['prixMax'] !== '' ? (int)$_GET['prixMax'] : null;
                $marque = isset($_GET['marque']) ? $_GET['marque'] : '';
                $avis = isset($_GET['avis']) ? strtolower($_GET['avis']) : '';
                $validAvis = ['asc', 'desc'];

                if (!in_array($avis, $validAvis)) {
                    $avis = '';
                }

                // Requête pour récupérer le nombre total de produits
                $totalQuery = "SELECT DISTINCT COUNT(*) as total FROM PRODUIT P 
                               JOIN APPARTENIRCATEG A1 ON P.IDPROD = A1.IDPROD
                               JOIN CATEGORIE C1 ON A1.IDCATEG = C1.IDCATEG 
                               JOIN CATPERE CP ON C1.IDCATEG = CP.IDCATEG
                               JOIN APPARTENIRCATEG A2 ON P.IDPROD = A2.IDPROD
                               WHERE C1.IDCATEG = :IDSOUSCATEG
                               AND CP.IDCATEG_PERE = :IDCATPERE
                               AND A2.IDCATEG = CP.IDCATEG_PERE";

                if (!is_null($prixMin)) {
                    $totalQuery .= " AND P.PRIXHT >= :prixMin";
                }
                if (!is_null($prixMax)) {
                    $totalQuery .= " AND P.PRIXHT <= :prixMax";
                }
                if ($marque) {
                    $totalQuery .= " AND P.IDMARQUE = (SELECT IDMARQUE FROM MARQUE WHERE NOMMARQUE = :marque)";
                }

                $totalStmt = $pdo->prepare($totalQuery);
                $totalStmt->bindParam(':IDSOUSCATEG', $idCategorie, PDO::PARAM_INT);
                $totalStmt->bindParam(':IDCATPERE', $idCategoriePere, PDO::PARAM_INT);
                if (!is_null($prixMin)) {
                    $totalStmt->bindParam(':prixMin', $prixMin, PDO::PARAM_INT);
                }
                if (!is_null($prixMax)) {
                    $totalStmt->bindParam(':prixMax', $prixMax, PDO::PARAM_INT);
                }
                if ($marque) {
                    $totalStmt->bindParam(':marque', $marque, PDO::PARAM_STR);
                }

                $totalStmt->execute();
                $total = $totalStmt->fetch()['total'];
                $totalPages = ceil($total / $limit);

                // Requête pour récupérer les produits
                $query = "SELECT DISTINCT P.IDPROD, P.NOMPROD, P.DESCPROD, P.PRIXHT, P.COULEUR, P.COMPOSITION, P.POIDSPRODUIT, P.QTESTOCK, AVG(AV.NOTE) AS MOYENNE_NOTE
                          FROM PRODUIT P 
                          JOIN APPARTENIRCATEG A1 ON P.IDPROD = A1.IDPROD
                          JOIN CATEGORIE C1 ON A1.IDCATEG = C1.IDCATEG 
                          JOIN CATPERE CP ON C1.IDCATEG = CP.IDCATEG
                          JOIN APPARTENIRCATEG A2 ON P.IDPROD = A2.IDPROD
                          LEFT JOIN AVIS AV ON P.IDPROD = AV.IDPROD
                          WHERE C1.IDCATEG = :IDSOUSCATEG
                          AND CP.IDCATEG_PERE = :IDCATPERE
                          AND A2.IDCATEG = CP.IDCATEG_PERE";

                if (!is_null($prixMin)) {
                    $query .= " AND P.PRIXHT >= :prixMin";
                }
                if (!is_null($prixMax)) {
                    $query .= " AND P.PRIXHT <= :prixMax";
                }
                if ($marque) {
                    $query .= " AND P.IDMARQUE = (SELECT IDMARQUE FROM MARQUE WHERE NOMMARQUE = :marque)";
                }
                $query .= " GROUP BY P.IDPROD";
                if ($avis) {
                    $query .= " ORDER BY MOYENNE_NOTE $avis";
                }
                $query .= " LIMIT :limit OFFSET :offset";

                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':IDSOUSCATEG', $idCategorie, PDO::PARAM_INT);
                $stmt->bindParam(':IDCATPERE', $idCategoriePere, PDO::PARAM_INT);
                if (!is_null($prixMin)) {
                    $stmt->bindParam(':prixMin', $prixMin, PDO::PARAM_INT);
                }
                if (!is_null($prixMax)) {
                    $stmt->bindParam(':prixMax', $prixMax, PDO::PARAM_INT);
                }
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

                if ($marque) {
                    $stmt->bindParam(':marque', $marque, PDO::PARAM_STR);
                }

                $stmt->execute();
                $produits = $stmt->fetchAll();

                $stmt2 = $pdo->prepare("SELECT * FROM CATEGORIE WHERE IDCATEG = :IDCATEG;");
                $stmt2->execute(["IDCATEG" => $idCategorie]);
                $categorie = $stmt2->fetch();

                echo "<div class=\"sousCategorieContainer\">";
                    echo "<h1 class=\"titreSousCateg\">".$categorie["NOMCATEG"]."</h1>";
                    if (count($produits) > 0) {
                        echo "<ul>";
                        foreach ($produits as $produit) {
                            $imagePath = "./images/prod". htmlspecialchars($produit['IDPROD'])  .".png" ;
                            echo "<li>";
                                echo "<a href='descProduit.php?idProd=".$produit['IDPROD']."'>";
                                echo "<div class\"produitCard\">";
                                    echo "<div class=\"imageContainer\">";
                                        echo "<img src='$imagePath' alt='image principale du produit' id='main-product-image'>";
                                    echo "</div>";
                                    echo "<div class=\"infoContainer\">";
                                        echo "<h2>".$produit['NOMPROD']."</h2>";                                    
                                        echo "<p>".$produit['PRIXHT']."€</p>";
                                        echo "<p>Note moyenne: ".round($produit['MOYENNE_NOTE'], 1)."/5</p>";
                                    echo "</div>";
                                echo "</div>";
                                echo "</a>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>Aucun produit ne correspond à vos critères.</p>";
                    }
                echo "</div>";
            ?>

        </div>
    </div>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="page-number <?php echo ($i == $page) ? 'active' : ''; ?>" href="?idCategorie=<?php echo $idCategorie; ?>&idCategoriePere=<?php echo $idCategoriePere; ?>&page=<?php echo $i; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>

    <?php include("footer.php") ?>


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