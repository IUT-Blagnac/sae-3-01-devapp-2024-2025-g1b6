<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dernière Sorties</title>
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/derniereSortie.css">
</head>
<body>

    <!-- Barre de navigation -->
    <?php include("header.php") ?>
    


    <!-- Section principale -->
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


        <div class="dernierProdContainer">
            <h1 class="titreDernierProd">Voici nos 20 dernières sorties !</h1>
            <ul>
                <?php 
                    include("connect.inc.php");
                    $req = $pdo->prepare("SELECT P.IDPROD, P.NOMPROD, P.PRIXHT, A.NOTE
                                        FROM PRODUIT P LEFT JOIN AVIS A ON P.IDPROD = A.IDPROD
                                        ORDER BY P.IDPROD DESC
                                        LIMIT 20");
                    $req->execute();
                    $produits = $req->fetchAll();

                    foreach($produits as $prod){
                        echo "<li>";
                        echo "<a href='produit.php?idProd=".$prod['IDPROD']."'>";
                        echo "<img src='./images/prod". $prod['IDPROD'] .".png' alt='collection'>";
                        echo "<p>".$prod['NOMPROD']."</p>";
                        echo "<p>".$prod['PRIXHT']."€</p>";
                        echo "<p>".$prod['NOTE']."/5</p>";
                        echo "</a>";
                        echo "</li>";
                    }

                ?>
            </ul>
        </div>
    </div>
   
    


    <?php include("footer.php") ?>
    
</body>
</html>