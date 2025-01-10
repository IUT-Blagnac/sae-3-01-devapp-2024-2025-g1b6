<?php
session_start();
include("connect.inc.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['idProd']) || !isset($data['idClient'])) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
        exit;
    }

    $idProd = intval($data['idProd']);
    $idClient = intval($data['idClient']);

    try {
        // Vérifier si le produit est déjà dans la liste de souhaits
        $stmt = $pdo->prepare("SELECT * FROM SOUHAITER WHERE IDCLIENT = ? AND IDPROD = ?");
        $stmt->execute([$idClient, $idProd]);
        $existingEntry = $stmt->fetch();

        if ($existingEntry) {
            echo json_encode(['success' => false, 'message' => 'Produit déjà dans la liste de souhaits.']);
        } else {
            // Ajouter le produit à la liste de souhaits
            $stmt = $pdo->prepare("INSERT INTO SOUHAITER (IDCLIENT, IDPROD) VALUES (?, ?)");
            $stmt->execute([$idClient, $idProd]);
            echo json_encode(['success' => true]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'exécution de la requête : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
}
?>