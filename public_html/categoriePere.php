<?php
session_start();

if (!isset($_GET['idCategorie']) || empty($_GET['idCategorie'])) {
    echo "<h1> Aucune Catégories Séléctionner </h1>";
    exit();
}

$idCategorie = htmlspecialchars($_GET['idCategorie']);


?>

<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories</title>
    <link rel="stylesheet" href="Css/categoriePere.css">
    <link rel="stylesheet" href="Css/all.css">
</head>
<body>
    
    <?php include("header.php"); ?>


    <div class="containerCategPere">
        <div class="filtres">
            <h1>Filtres</h1>
            <ul>
                <li>Age</li>
                <li>Prix</li>
            </ul>
        </div>

        <div class="containerProduit">
            <?php
                include("connect.inc.php");
                $stmt = $pdo->prepare("SELECT DISTINCT C.IDCATEG, C.NOMCATEG
                                       FROM CATEGORIE C JOIN CATPERE CP ON C.IDCATEG = CP.IDCATEG 
                                       WHERE CP.IDCATEG_PERE = :IDCATPERE;");
                $stmt->execute(["IDCATPERE" => $idCategorie]);
                $sous_categories = $stmt->fetchAll();

                foreach ($sous_categories as $sous_categorie) {
                    echo "<div class=\"sousCategorieContainer\">";
                    echo "<h1 class=\"titreSousCateg\">".$sous_categorie['NOMCATEG']."</h1>";
                    echo "<ul>";
                    try {
                            $stmt = $pdo->prepare("SELECT DISTINCT P.IDPROD, P.NOMPROD, P.DESCPROD, P.PRIXHT, P.COULEUR, P.COMPOSITION, P.POIDSPRODUIT, P.QTESTOCK
                                                FROM PRODUIT P JOIN APPARTENIRCATEG A ON P.IDPROD = A.IDPROD
                                                        JOIN CATEGORIE C ON A.IDCATEG = C.IDCATEG
                                                WHERE C.IDCATEG = :IDSOUSCATEG;");
                        $stmt->execute(["IDSOUSCATEG" => $sous_categorie['IDCATEG']]);
                        $produits = $stmt->fetchAll();
                        foreach ($produits as $produit) {
                            echo "<li>";
                            echo "<a href='descProduit.php?idProd=".$produit['IDPROD']."'>";
                            echo "<div class\"produitCard\">";
                                echo "<div class=\"imageContainer\">";
                                    echo "<img src='Images/".$produit['IDPROD'].".jpg' alt='Image du produit'>";
                                echo "</div>";
                                echo "<div class=\"infoContainer\">";
                                    echo "<h2>".$produit['NOMPROD']."</h2>";
                                    echo "<p>".$produit['PRIXHT']."€</p>";
                                echo "</div>";
                            echo "</div>";
                            echo "</a>";
                            echo "</li>";
                        }
                    } catch (PDOException $e) {
                        echo "Erreur lors de la récupération des produits : " . $e->getMessage();
                        exit();
                    }
                    echo "</ul>";
                    echo "</div>";
                } 
               

            ?>


        </div>
    </div>


    <?php include("footer.php") ?>


</body>