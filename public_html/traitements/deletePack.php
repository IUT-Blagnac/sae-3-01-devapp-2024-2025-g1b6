<?php
session_start();
include("./../connect.inc.php");

if (!isset($_SESSION["admin"])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

header('Content-Type: application/json');

try {
    if (!isset($_POST['idPack'])) {
        throw new Exception('ID du pack non fourni');
    }

    $idPack = $_POST['idPack'];
    $pdo->beginTransaction();

    // Vérifier si le pack a des produits associés
    $queryProduits = $pdo->prepare("SELECT COUNT(*) as nbProduits FROM ASSOPACK WHERE IDPACK = ?");
    $queryProduits->execute([$idPack]);
    $nbProduits = $queryProduits->fetch()['nbProduits'];

    // Supprimer les associations avec les produits
    $pdo->prepare("DELETE FROM ASSOPACK WHERE IDPACK = ?")->execute([$idPack]);

    // Supprimer le pack
    $pdo->prepare("DELETE FROM PACK WHERE IDPACK = ?")->execute([$idPack]);

    $pdo->commit();

    // Préparer le message de succès
    $message = "Pack supprimé avec succès. ";
    if ($nbProduits > 0) {
        $message .= "$nbProduits produit(s) ont été retirés du pack.";
    }

    echo json_encode([
        'success' => true,
        'message' => $message
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 