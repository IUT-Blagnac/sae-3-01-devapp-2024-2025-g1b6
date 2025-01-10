<?php
session_start();
include("connect.inc.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idClient = intval($_SESSION['user']['IDCLIENT']);
    $idProd = intval($_POST['idProd']);
    $descAvis = trim($_POST['descAvis']);
    $note = intval($_POST['note']);

    // Vérifier la longueur de l'avis
    if (strlen($descAvis) > 750) {
        echo "Votre avis ne doit pas dépasser 750 caractères.";
        exit();
    }

    if (!in_array($note, [1, 2, 3, 4, 5])) {
        echo "La note doit être comprise entre 1 et 5.";
        exit();
    }
    
    // Vérifier si l'avis existe déjà
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM AVIS WHERE IDCLIENT = ? AND IDPROD = ?");
    $stmtCheck->execute([$idClient, $idProd]);
    if ($stmtCheck->fetchColumn() > 0) {
        die("Vous avez déjà laissé un avis pour ce produit.");
    }

    // Insérer l'avis dans la base de données
    $stmt = $pdo->prepare("INSERT INTO AVIS (IDCLIENT, IDPROD, DESCAVIS, NOTE) VALUES (?, ?, ?, ?)");
    $stmt->execute([$idClient, $idProd, $descAvis, $note]);

    // Rediriger vers la page du produit
    header("Location: descProduit.php?idProd=" . $idProd);
    exit();
} else {
    echo "Requête invalide.";
}
?>