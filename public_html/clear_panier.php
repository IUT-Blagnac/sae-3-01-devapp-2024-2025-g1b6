<?php 
session_start();
include("connect.inc.php");

// Vérifier que les données ont été envoyées en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['idClient'])) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
        exit;
    }

    $idClient = intval($_POST['idClient']);

    try {
        // Supprimer tous les produits du panier pour le client
        $stmt = $pdo->prepare("DELETE FROM PANIER WHERE IDCLIENT = ?");
        $stmt->execute([$idClient]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'exécution de la requête : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
}
?>