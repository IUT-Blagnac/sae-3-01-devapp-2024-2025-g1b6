<?php
session_start();

include("connect.inc.php");

if (!isset($_GET['idCategorie']) || empty($_GET['idCategorie'])) {
    echo "<h1> Aucune Catégories Séléctionner </h1>";
    exit();
}

$idCategorie = htmlspecialchars($_GET['idCategorie']);

$stmt = $pdo->prepare("SELECT * FROM CATEGORIE WHERE IDCATEG = :idCategorie");
$stmt->execute(['idCategorie' => $idCategorie]);
$categorie = $stmt->fetch();

if (!$categorie) {
    echo "<h1> Catégorie Introuvable </h1>";
    exit();
}

?>

<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories</title>
    <link rel="stylesheet" href="Css/categories.css">
    <link rel="stylesheet" href="Css/all.css">
</head>
<body>
    
    <?php include("header.php"); ?>


    <div class="containerCateg">
        <?php 
            
        ?>
    </div>


    <?php include("footer.php") ?>


</body>