<?php
session_start();
include("./../connect.inc.php");
header('Content-Type: application/json');

if (!isset($_SESSION["admin"])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    if (!isset($_POST['idProd'])) {
        throw new Exception('ID du produit non fourni');
    }

    $idProd = $_POST['idProd'];
    $pdo->beginTransaction();

    // Vérifier si le produit a déjà été commandé
    $queryCommandes = $pdo->prepare("
        SELECT COUNT(*) as nbCommandes 
        FROM PANIER 
        WHERE IDPROD = ? AND IDCOMMANDE > 0
    ");
    $queryCommandes->execute([$idProd]);
    $result = $queryCommandes->fetch();

    if ($result['nbCommandes'] > 0) {
        throw new Exception('Ce produit a déjà été commandé et ne peut pas être supprimé. Vous pouvez le rendre indisponible à la place.');
    }

    // Récupérer les informations sur les relations à supprimer
    $queryPaniers = $pdo->prepare("SELECT COUNT(*) as nb FROM PANIER WHERE IDPROD = ?");
    $queryPaniers->execute([$idProd]);
    $nbPaniers = $queryPaniers->fetch()['nb'];

    $querySouhaits = $pdo->prepare("SELECT COUNT(*) as nb FROM SOUHAITER WHERE IDPROD = ?");
    $querySouhaits->execute([$idProd]);
    $nbSouhaits = $querySouhaits->fetch()['nb'];

    $queryPacks = $pdo->prepare("SELECT COUNT(*) as nb FROM ASSOPACK WHERE IDPROD = ?");
    $queryPacks->execute([$idProd]);
    $nbPacks = $queryPacks->fetch()['nb'];

    // Supprimer toutes les relations
    $pdo->prepare("DELETE FROM PANIER WHERE IDPROD = ?")->execute([$idProd]);
    $pdo->prepare("DELETE FROM APPARTENIRCATEG WHERE IDPROD = ?")->execute([$idProd]);
    $pdo->prepare("DELETE FROM ASSOPACK WHERE IDPROD = ?")->execute([$idProd]);
    $pdo->prepare("DELETE FROM SOUHAITER WHERE IDPROD = ?")->execute([$idProd]);

    // Supprimer le produit
    $pdo->prepare("DELETE FROM PRODUIT WHERE IDPROD = ?")->execute([$idProd]);

    // Supprimer l'image associée
    $imagePath = "../images/prod" . $idProd . ".png";
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    $pdo->commit();

    // Préparer le message de succès avec les détails
    $message = "Produit supprimé avec succès. ";
    $details = [];
    if ($nbPaniers > 0) $details[] = "$nbPaniers panier(s)";
    if ($nbSouhaits > 0) $details[] = "$nbSouhaits liste(s) de souhaits";
    if ($nbPacks > 0) $details[] = "$nbPacks pack(s)";
    
    if (!empty($details)) {
        $message .= "\nLe produit a été retiré de : " . implode(", ", $details) . ".";
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