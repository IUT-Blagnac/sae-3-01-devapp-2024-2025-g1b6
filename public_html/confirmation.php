<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - Ludorama</title>
    <link rel="stylesheet" href="Css/confirmation.css">
    <link rel="stylesheet" href="Css/all.css">
</head>
<body>

<?php 
include("header.php"); 

if (!isset($_GET['commande'])) {
    echo "Numéro de commande manquant.";
    exit();
}

$numCommande = intval($_GET['commande']);

// Récupérer les informations de la commande
$stmt = $pdo->prepare("
    SELECT c.NUMCOMMANDE, c.DATECOMMANDE, c.STATUTLIVRAISON, c.CODESUIVI,
           a.NUMRUE, a.NOMRUE, a.COMPLEMENTADR, a.NOMVILLE, a.CODEPOSTAL, a.PAYS,
           t.TYPEEXP, t.FRAISEXP
    FROM COMMANDE c
    JOIN ADRESSE a ON c.IDADRESSE = a.IDADRESSE
    JOIN TRANSPORTEUR t ON c.IDTRANSPORTEUR = t.IDTRANSPORTEUR
    WHERE c.NUMCOMMANDE = ?
");
$stmt->execute([$numCommande]);
$commande = $stmt->fetch();

if (!$commande) {
    echo "Commande non trouvée.";
    exit();
}

// Récupérer les produits de la commande
$stmt = $pdo->prepare("
    SELECT p.NOMPROD, p.PRIXHT, pa.QUANTITEPROD
    FROM PANIER pa
    JOIN PRODUIT p ON pa.IDPROD = p.IDPROD
    WHERE pa.IDCLIENT = ? AND pa.DATECOMMANDE = ?
");
$stmt->execute([$_SESSION['user']['IDCLIENT'], $commande['DATECOMMANDE']]);
$produitsPanier = $stmt->fetchAll();

// Calculer le prix total de la commande
$totalHT = 0;
foreach ($produitsPanier as $produit) {
    $totalHT += $produit['PRIXHT'] * $produit['QUANTITEPROD'];
}
$totalTTC = $totalHT * 1.2; // Ajout de la TVA (20%)
$total = $totalTTC + $commande['FRAISEXP'];

?>

<h1>Merci pour votre commande !</h1>
<p>Votre paiement a été traité avec succès. Vous recevrez bientôt un email de confirmation.</p>

<h2>Détails de la commande</h2>
<p><strong>Prix de la commande :</strong> <?php echo number_format($total, 2, ',', ' '); ?> €</p>
<p><strong>Numéro de commande :</strong> <?php echo htmlspecialchars($commande['NUMCOMMANDE']); ?></p>
<p><strong>Date de commande :</strong> <?php echo htmlspecialchars($commande['DATECOMMANDE']); ?></p>
<p><strong>Statut de livraison :</strong> <?php echo htmlspecialchars($commande['STATUTLIVRAISON']); ?></p>
<p><strong>Code de suivi :</strong> <?php echo htmlspecialchars($commande['CODESUIVI']); ?></p>

<h2>Adresse de livraison</h2>
<p><?php echo htmlspecialchars($commande['NUMRUE']) . ' ' . htmlspecialchars($commande['NOMRUE']); ?></p>
<p><?php echo htmlspecialchars($commande['COMPLEMENTADR']); ?></p>
<p><?php echo htmlspecialchars($commande['NOMVILLE']) . ', ' . htmlspecialchars($commande['CODEPOSTAL']) . ', ' . htmlspecialchars($commande['PAYS']); ?></p>

<h2>Transporteur</h2>
<p><?php echo htmlspecialchars($commande['TYPEEXP']); ?></p>

<?php include("footer.php"); ?>
</body>
</html>