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

        <div class="containerProduit">
            <?php 
                include("connect.inc.php");
                $stmt = $pdo->prepare("SELECT DISTINCT P.IDPROD, P.NOMPROD, P.DESCPROD, P.PRIXHT, P.COULEUR, P.COMPOSITION, P.POIDSPRODUIT, P.QTESTOCK
                                       FROM PRODUIT P JOIN APPARTENIRCATEG A1 ON P.IDPROD = A1.IDPROD
                                       JOIN CATEGORIE C1 ON A1.IDCATEG = C1.IDCATEG 
                                       JOIN CATPERE CP ON C1.IDCATEG = CP.IDCATEG
                                       JOIN APPARTENIRCATEG A2 ON P.IDPROD = A2.IDPROD
                                       WHERE C1.IDCATEG = :IDSOUSCATEG
                                       AND CP.IDCATEG_PERE = :IDCATPERE
                                       AND A2.IDCATEG = CP.IDCATEG_PERE;");
                $stmt->execute(["IDCATPERE" => $idCategoriePere, "IDSOUSCATEG" => $idCategorie]);
                $produits = $stmt->fetchAll();

                $stmt2 = $pdo->prepare("SELECT * FROM CATEGORIE WHERE IDCATEG = :IDCATEG;");
                $stmt2->execute(["IDCATEG" => $idCategorie]);
                $categorie = $stmt2->fetch();

                echo "<div class=\"sousCategorieContainer\">";
                    echo "<h1 class=\"titreSousCateg\">".$categorie["NOMCATEG"]."</h1>";
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
                                echo "</div>";
                            echo "</div>";
                            echo "</a>";
                        echo "</li>";
                    }
                echo "</ul>";
                echo "</div>";
            ?>

        </div>
    </div>

    <?php include("footer.php") ?>

</body>