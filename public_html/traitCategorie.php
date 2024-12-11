<?php 
session_start();
$idCateg = isset($_GET['idCateg']) ? $_GET['idCateg'] : null;
$idCategPere = isset($_GET['idCategPere']) ? $_GET['idCategPere'] : null;


if ($idCategPere !== null && $idCateg == null) {    // Si on a uniquement une catégorie parente
    header("Location: categoriePere.php?idCategorie=".$idCategPere);
    exit();

} else if ($idCateg !== null && $idCategPere !== null) {    // Si on a une catégorie enfant et une catégorie parente
    header("Location: categorieEnfants.php?idCategorie=".$idCateg); 
    exit();
} else {
    echo "Aucune catégorie sélectionnée";
    header("Location: index.php");
    exit();
}

?>