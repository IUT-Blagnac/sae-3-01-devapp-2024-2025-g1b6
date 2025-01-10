<?php
session_start();
include("connect.inc.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numCB = $_POST['numCB'];
    $idClient = intval($_SESSION['user']['IDCLIENT']);
    $numRue = $_POST['numRue'];
    $nomRue = $_POST['nomRue'];
    $complementAdr = $_POST['complementAdr'];
    $nomVille = $_POST['nomVille'];
    $codePostal = $_POST['codePostal'];
    $pays = $_POST['pays'];
    $idAdresse = $_POST['idAdresse'];
    $idTransporteur = $_POST['idTransporteur'];
    $statutLivraison = 'En cours'; // Par défaut, le statut de livraison est "En cours"

    // Générer le numéro de suivi (3 lettres suivies de 9 chiffres)
    $lettres = strtoupper(substr(md5(rand()), 0, 3));  // 3 lettres aléatoires
    $chiffres = str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);  // 9 chiffres
    $codeSuivi = $lettres . $chiffres;

    

    // Récuperer la date de commande
    $dateCommande = date("Y-m-d");


    try {
        // Commencer une transaction
        $pdo->beginTransaction();

        if($idAdresse === 'new'){
            // Insérer l'adresse dans la table Adresse
            $stmt = $pdo->prepare("INSERT INTO ADRESSE ( NUMRUE, NOMRUE, COMPLEMENTADR, NOMVILLE, CODEPOSTAL, PAYS) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([ $numRue, $nomRue, $complementAdr, $nomVille, $codePostal, $pays]);

            // Récupérer l'ID de l'adresse généré
            $idAdresse = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO POSSEDERADR (IDADRESSE, IDCLIENT) VALUES (?, ?)");
            $stmt->execute([$idAdresse, $idClient]);
        }
        // Insérer la commande dans la table Commande
        $stmt = $pdo->prepare("INSERT INTO COMMANDE (IDCLIENT, IDTRANSPORTEUR, NUMCB, IDADRESSE, TYPEREGLEMENT, DATECOMMANDE, STATUTLIVRAISON, CODESUIVI) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$idClient, $idTransporteur, $numCB, $idAdresse, 'CB', $dateCommande, $statutLivraison, $codeSuivi]);

        // Récupérer le numéro de commande généré
        $numCommande = $pdo->lastInsertId();

        // Mettre à jour la date de commande dans le panier
        $stmt = $pdo->prepare("UPDATE PANIER SET IDCOMMANDE = ? WHERE IDCLIENT = ? AND IDCOMMANDE = 0");
        $stmt->execute([$numCommande, $idClient]);

        // Mettre à jour le stock des produits
        $stmt = $pdo->prepare("
            UPDATE PRODUIT p
            JOIN PANIER pa ON p.IDPROD = pa.IDPROD
            SET p.QTESTOCK = p.QTESTOCK - pa.QUANTITEPROD
            WHERE pa.IDCLIENT = ? AND pa.IDCOMMANDE = ?
        ");
        $stmt->execute([$idClient, $numCommande]);

        // Valider la transaction
        $pdo->commit();

        // Rediriger vers une page de confirmation
        header("Location: confirmation.php?commande=" . $numCommande);
        exit();
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        echo "Erreur lors du traitement du paiement : " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "Requête invalide.";
}
?>