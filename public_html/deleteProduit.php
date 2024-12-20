<?php
session_start();
include("connect.inc.php");

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION["admin"])) {
    header("Location: connexion.php");
    exit();
}

if (isset($_GET['id'])) {
    try {
        $pdo->beginTransaction();

        $idProd = $_GET['id'];

        // Supprimer les associations avec les catégories
        $stmt = $pdo->prepare("DELETE FROM APPARTENIRCATEG WHERE IDPROD = :idProd");
        $stmt->execute([':idProd' => $idProd]);

        // Supprimer les associations avec les packs
        $stmt = $pdo->prepare("DELETE FROM ASSOPACK WHERE IDPROD = :idProd");
        $stmt->execute([':idProd' => $idProd]);

        // Supprimer les avis liés au produit
        $stmt = $pdo->prepare("DELETE FROM AVIS WHERE IDPROD = :idProd");
        $stmt->execute([':idProd' => $idProd]);

        // Supprimer les entrées du panier liées au produit
        $stmt = $pdo->prepare("DELETE FROM PANIER WHERE IDPROD = :idProd");
        $stmt->execute([':idProd' => $idProd]);

        // Supprimer les souhaits liés au produit
        $stmt = $pdo->prepare("DELETE FROM SOUHAITER WHERE IDPROD = :idProd");
        $stmt->execute([':idProd' => $idProd]);

        // Enfin, supprimer le produit
        $stmt = $pdo->prepare("DELETE FROM PRODUIT WHERE IDPROD = :idProd");
        $stmt->execute([':idProd' => $idProd]);

        $pdo->commit();
        header("Location: listeProduits.php?success=Le produit a été supprimé avec succès");

    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: listeProduits.php?error=" . urlencode("Erreur lors de la suppression du produit : " . $e->getMessage()));
    }
} else {
    header("Location: listeProduits.php?error=ID du produit non spécifié");
}
exit();