<?php
session_start();
include("connect.inc.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idClient = intval($_SESSION['user']['IDCLIENT']);
    $idProd = intval($_POST['idProd']);
    $descAvis = trim($_POST['descAvis']);

    // Vérifier la longueur de l'avis
    if (strlen($descAvis) > 750) {
        echo "Votre avis ne doit pas dépasser 750 caractères.";
        exit();
    }

    // Insérer l'avis dans la base de données
    $stmt = $pdo->prepare("INSERT INTO AVIS (IDCLIENT, IDPROD, DESCAVIS) VALUES (?, ?, ?)");
    $stmt->execute([$idClient, $idProd, $descAvis]);

    // Rediriger vers la page du produit
    header("Location: descProduit.php?idProd=" . $idProd);
    exit();
} else {
    echo "Requête invalide.";
}
?>