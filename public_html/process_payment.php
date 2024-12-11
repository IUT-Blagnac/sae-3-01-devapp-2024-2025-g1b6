<?php
session_start();
include("connect.inc.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numCB = $_POST['numCB'];
    $idClient = intval($_SESSION['user']['IDCLIENT']);
    $idAdresse = $_POST['idAdresse'];
    $idLivraison = $_POST['idLivraison'];

    try {
        // Commencer une transaction
        $pdo->beginTransaction();

        // Insérer la commande dans la table Commande
        $stmt = $pdo->prepare("INSERT INTO Commande (IDCLIENT, NUMCB, IDADRESSE, IDLIVRAISON, TYPEREGLEMENT, DATECOMMANDE) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$idClient, $numCB, $idAdresse, $idLivraison, 'CB']);

        // Vider le panier
        $stmt = $pdo->prepare("DELETE FROM PANIER WHERE IDCLIENT = ?");
        $stmt->execute([$idClient]);

        // Valider la transaction
        $pdo->commit();

        // Rediriger vers une page de confirmation
        header("Location: confirmation.php");
        exit();
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        echo "Erreur lors du traitement du paiement : " . $e->getMessage();
    }
} else {
    echo "Requête invalide.";
}
?>