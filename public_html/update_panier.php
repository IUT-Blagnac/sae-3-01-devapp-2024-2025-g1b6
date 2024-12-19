<?php 
session_start();
include("connect.inc.php");

// Vérifier que les données ont été envoyées en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['idProd']) || !isset($_POST['delta']) || !isset($_POST['idClient'])) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
        exit;
    }

    $idProd = intval($_POST['idProd']);
    $delta = intval($_POST['delta']);
    $idClient = intval($_POST['idClient']);

    try {
        // Vérifier si le produit est déjà dans le panier
        $stmt = $pdo->prepare("SELECT * FROM PANIER WHERE IDCLIENT = ? AND IDPROD = ?");
        $stmt->execute([$idClient, $idProd]);
        $existingEntry = $stmt->fetch();

        if ($existingEntry) {
            
            $newQuantity = $existingEntry['QUANTITEPROD'] + $delta;
            if ($newQuantity > 0) {
                $stmt = $pdo->prepare("UPDATE PANIER SET QUANTITEPROD = ? WHERE IDCLIENT = ? AND IDPROD = ?");
                $stmt->execute([$newQuantity, $idClient, $idProd]);
            } else {
                // Supprimer le produit si la quantité devient 0 ou moins
                $stmt = $pdo->prepare("DELETE FROM PANIER WHERE IDCLIENT = ? AND IDPROD = ?");
                $stmt->execute([$idClient, $idProd]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Produit non trouvé dans le panier.']);
            exit;
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'exécution de la requête : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
}
?>