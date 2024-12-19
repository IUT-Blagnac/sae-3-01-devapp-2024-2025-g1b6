<?php
session_start();
?>

<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LudiGames</title>
    <link rel="stylesheet" href="Css/categorieEnfants.css">
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
                $stmt = $pdo->prepare("SELECT DISTINCT P.IDPROD, P.NOMPROD, P.DESCPROD, P.PRIXHT, P.COULEUR, P.COMPOSITION, P.POIDSPRODUIT, P.QTESTOCK
                                       FROM PRODUIT P 
                                       Where P.IDMARQUE = 21");
                $stmt->execute();
                $produits = $stmt->fetchAll();

                echo "<div class=\"sousCategorieContainer\">";
                    echo "<h1 class=\"titreSousCateg\">Ludi Games </h1>";
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