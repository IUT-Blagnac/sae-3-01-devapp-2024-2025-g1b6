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

// Récupérer les produits du panier pour l'utilisateur connecté
$idClient = intval($_SESSION['user']['IDCLIENT']);
$stmt = $pdo->prepare("
    SELECT p.NOMPROD, p.IDPROD, p.PRIXHT, p.DESCPROD, pa.QUANTITEPROD
    FROM PANIER pa
    JOIN PRODUIT p ON pa.IDPROD = p.IDPROD
    WHERE pa.IDCLIENT = ? AND pa.IDCOMMANDE = ?
");
$stmt->execute([$idClient, $numCommande]);
$produitsPanier = $stmt->fetchAll();

// Récupérer les informations des packs et leurs produits
$stmtPacks = $pdo->query("
    SELECT p.IDPACK, p.NOMPACK, p.PRIXPACK, GROUP_CONCAT(ap.IDPROD) AS PRODUITS
    FROM PACK p
    JOIN ASSOPACK ap ON p.IDPACK = ap.IDPACK
    GROUP BY p.IDPACK
");
$packs = $stmtPacks->fetchAll(PDO::FETCH_ASSOC);

// Convertir les produits du panier en un tableau associatif pour un accès rapide
$produitsPanierAssoc = [];
foreach ($produitsPanier as $produit) {
    $produitsPanierAssoc[$produit['IDPROD']] = $produit;
}

// Vérifie si tous les produits d'un pack sont présents dans le panier
$packsAppliques = [];
$produitsInclusDansPacks = [];
foreach ($packs as $pack) {
    $produitsPack = explode(',', $pack['PRODUITS']);
    $tousProduitsPresents = true;
    foreach ($produitsPack as $idProd) {
        if (!isset($produitsPanierAssoc[$idProd]) || $produitsPanierAssoc[$idProd]['QUANTITEPROD'] < 1) {
            $tousProduitsPresents = false;
            break;
        }
    }
    if ($tousProduitsPresents) {
        $packsAppliques[] = $pack;
        // Marquer les produits du pack comme inclus dans un pack appliqué et ajuster les quantités
        foreach ($produitsPack as $idProd) {
            $produitsPanierAssoc[$idProd]['QUANTITEPROD'] -= 1;
            if ($produitsPanierAssoc[$idProd]['QUANTITEPROD'] == 0) {
                unset($produitsPanierAssoc[$idProd]);
            }
            $produitsInclusDansPacks[$idProd] = true;
        }
    }
}

$totalHT = 0;
foreach ($produitsPanierAssoc as $produit) {
    $totalHT += $produit['PRIXHT'] * $produit['QUANTITEPROD'];
}
foreach ($packsAppliques as $pack) {
    $totalHT += $pack['PRIXPACK'];
}
$totalTTC = $totalHT * 1.2 + $commande['FRAISEXP']; // Ajout de la TVA (20%)

?>

<h1>Merci pour votre commande !</h1>
<p>Votre paiement a été traité avec succès. Vous recevrez bientôt un email de confirmation.</p>

<h2>Détails de la commande</h2>
<p><strong>Prix de la commande :</strong> <?php echo number_format($totalTTC, 2, ',', ' '); ?> €</p>
<p><strong>Numéro de commande :</strong> <?php echo htmlspecialchars($commande['NUMCOMMANDE']); ?></p>
<p><strong>Date de commande :</strong> <?php echo htmlspecialchars($commande['DATECOMMANDE']); ?></p>
<p><strong>Statut de livraison :</strong> <?php echo htmlspecialchars($commande['STATUTLIVRAISON']); ?></p>
<p><strong>Code de suivi :</strong> <?php echo htmlspecialchars($commande['CODESUIVI']); ?></p>

<h2>Adresse de livraison</h2>
<p><?php echo htmlspecialchars($commande['NUMRUE']) . ' ' . htmlspecialchars($commande['NOMRUE']); ?></p>
<p><?php echo htmlspecialchars($commande['COMPLEMENTADR']); ?></p>
<p><?php echo htmlspecialchars($commande['NOMVILLE']) . ', ' . htmlspecialchars($commande['CODEPOSTAL']) . ', ' . htmlspecialchars($commande['PAYS']); ?></p>

<h2>Type d'expédition</h2>
<p><?php echo htmlspecialchars($commande['TYPEEXP']); ?></p>

<?php include("footer.php"); ?>
</body>
</html>