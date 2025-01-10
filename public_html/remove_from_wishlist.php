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
        // Supprimer le produit de la liste de souhaits pour le client
        $stmt = $pdo->prepare("DELETE FROM SOUHAITER WHERE IDCLIENT = ? AND IDPROD = ?");
        $stmt->execute([$idClient, $idProd]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'exécution de la requête : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
}
?>