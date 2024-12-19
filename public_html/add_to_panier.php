<?php
session_start();
include("connect.inc.php");

// Vérifier que les données ont été envoyées en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['idClient']) || !isset($data['produits']) || !is_array($data['produits'])) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants ou invalides.']);
        exit;
    }

    $idClient = intval($data['idClient']);
    $produits = $data['produits'];

    try {
        $pdo->beginTransaction();

        foreach ($produits as $idProd) {
            $idProd = intval($idProd);

            // Vérifier si le produit est déjà dans le panier
            $stmt = $pdo->prepare("SELECT * FROM PANIER WHERE IDCLIENT = ? AND IDPROD = ? AND IDCOMMANDE = 0");
            $stmt->execute([$idClient, $idProd]);
            $existingEntry = $stmt->fetch();

            if ($existingEntry) {
                // Mettre à jour la quantité si le produit existe déjà
                $stmt = $pdo->prepare("UPDATE PANIER SET QUANTITEPROD = QUANTITEPROD + 1 WHERE IDCLIENT = ? AND IDPROD = ? AND IDCOMMANDE = 0");
                $stmt->execute([$idClient, $idProd]);
            } else {
                // Ajouter une nouvelle entrée dans le panier
                $stmt = $pdo->prepare("INSERT INTO PANIER (IDCLIENT, IDPROD, QUANTITEPROD) VALUES (?, ?, 1)");
                $stmt->execute([$idClient, $idProd]);
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'exécution de la requête : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
}
?>