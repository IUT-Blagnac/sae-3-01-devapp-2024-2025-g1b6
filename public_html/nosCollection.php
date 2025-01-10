<?php
session_start();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos collection</title>
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/nosCollection.css">
</head>
<body>

    <!-- Barre de navigation -->
    <?php include("header.php") ?>
    

    <!-- Section principale -->
     <div class="nosCollecContainer">
        <ul>
            <?php 
                include("connect.inc.php");
                $req = $pdo->prepare("SELECT * FROM CATEGORIE C 
                                      WHERE C.NOMCATEG LIKE 'Collection%'");
                $req->execute();
                $collection = $req->fetchAll();

                foreach($collection as $collec){
                    $req2 = $pdo->prepare("SELECT P.IDPROD FROM PRODUIT P JOIN APPARTENIRCATEG A ON P.IDPROD = A.IDPROD
                                           WHERE A.IDCATEG = :idCateg
                                           LIMIT 1");
                    $req2->execute(array(':idCateg' => $collec['IDCATEG']));
                    $idProd = $req2->fetch();

                    echo "<li>";
                    echo "<a href='collection.php?idCateg=".$collec['IDCATEG']."'>";
                    echo "<img src='./images/prod". $idProd['IDPROD'] .".png' alt='collection'>";
                    echo "<p>".$collec['NOMCATEG']."</p>";
                    echo "</a>";
                    echo "</li>";
                }

            ?>
        </ul>
     </div>
   
    


    <?php include("footer.php") ?>
    
</body>
</html>