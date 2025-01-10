<?php
session_start();
include("connect.inc.php");

// Vérifier que les données ont été envoyées en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['idClient']) || !isset($data['idProd'])) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
        exit;
    }

    $idClient = intval($data['idClient']);
    $idProd = intval($data['idProd']);

    try {
        // Vérifier si le produit fait partie d'un pack
        $stmt = $pdo->prepare("
            SELECT p.IDPACK
            FROM ASSOPACK ap
            JOIN PACK p ON ap.IDPACK = p.IDPACK
            WHERE ap.IDPROD = ?
        ");
        $stmt->execute([$idProd]);
        $pack = $stmt->fetch();

        if ($pack) {
            // Le produit fait partie d'un pack, retirer tous les produits du pack du panier
            $stmt = $pdo->prepare("
                DELETE pa
                FROM PANIER pa
                JOIN ASSOPACK ap ON pa.IDPROD = ap.IDPROD
                WHERE pa.IDCLIENT = ? AND ap.IDPACK = ? AND pa.IDCOMMANDE = 0
            ");
            $stmt->execute([$idClient, $pack['IDPACK']]);
        } else {
            // Le produit ne fait pas partie d'un pack, retirer le produit du panier
            $stmt = $pdo->prepare("DELETE FROM PANIER WHERE IDCLIENT = ? AND IDPROD = ? AND IDCOMMANDE = 0");
            $stmt->execute([$idClient, $idProd]);
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'exécution de la requête : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
}
?>