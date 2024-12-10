<?php 
session_start();
include("connect.inc.php");

// Vérifier que les données ont été envoyées en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['idProd']) || !isset($data['idClient'])) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
        exit;
    }

    $idProd = intval($data['idProd']);
    $idClient = intval($data['idClient']);

    try {
        // Vérifier si le produit est déjà dans le panier
        $stmt = $pdo->prepare("SELECT * FROM PANIER WHERE IDCLIENT = ? AND IDPROD = ?");
        $stmt->execute([$idClient, $idProd]);
        $existingEntry = $stmt->fetch();

        if ($existingEntry) {
            // Mettre à jour la quantité si le produit existe déjà
            $stmt = $pdo->prepare("UPDATE PANIER SET QUANTITEPROD = QUANTITEPROD + 1 WHERE IDCLIENT = ? AND IDPROD = ?");
            $stmt->execute([$idClient, $idProd]);
        } else {
            // Ajouter une nouvelle entrée dans le panier
            $stmt = $pdo->prepare("INSERT INTO PANIER (IDCLIENT, IDPROD, QUANTITEPROD) VALUES (?, ?, 1)");
            $stmt->execute([$idClient, $idProd]);
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
}
?>