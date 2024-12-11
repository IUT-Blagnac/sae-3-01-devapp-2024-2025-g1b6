<?php
session_start();
include("connect.inc.php");

// Récupérer les produits du panier pour l'utilisateur connecté
$idClient = intval($_SESSION['user']['IDCLIENT']);
$stmt = $pdo->prepare("
    SELECT p.NOMPROD, p.IDPROD, p.PRIXHT, p.DESCPROD, pa.QUANTITEPROD
    FROM PANIER pa
    JOIN PRODUIT p ON pa.IDPROD = p.IDPROD
    WHERE pa.IDCLIENT = ?
");
$stmt->execute([$idClient]);
$produitsPanier = $stmt->fetchAll();

// Récupérer les informations de paiement pour l'utilisateur connecté
$stmt = $pdo->prepare("
    SELECT ip.NUMCB, ip.NOMCOMPLETCB, ip.DATEEXP
    FROM INFORMATIONPAIEMENT ip
    JOIN POSSEDERIP pi ON ip.NUMCB = pi.NUMCB
    WHERE pi.IDCLIENT = ?
");
$stmt->execute([$idClient]);
$infosPaiement = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande - Ludorama</title>
    <link rel="stylesheet" href="Css/commande.css">
    <link rel="stylesheet" href="Css/all.css">
</head>
<body>

<h1>Page du détail d'une Commande</h1>

<div class="container">
    <div class="commande-container">
        <div class="produits">
            <?php
            if (empty($produitsPanier)) {
                echo "<p>Votre panier est vide.</p>";
            } else {
                foreach ($produitsPanier as $produit) {
                    $imagePath = "./images/prod" . htmlspecialchars($produit['IDPROD']) . ".png"; // Chemin d'image
                    $prix = number_format($produit['PRIXHT'], 2, ',', ' ') . " €";
                    $quantite = intval($produit['QUANTITEPROD']);
                    echo "
                    <div class='product'>
                        <img src='$imagePath' alt='Image du produit'>
                        <div class='productDetails'>
                            <h3>" . htmlspecialchars($produit['NOMPROD']) . "</h3>
                            <p>" . htmlspecialchars($produit['DESCPROD']) . "</p>
                            <p>Prix : $prix</p>
                            <p>Quantité : $quantite</p>
                        </div>
                    </div>";
                }
            }
            ?>
        </div>

        <div class="payment-section">
            <h2>Choisir une Information de Paiement</h2>
            <form action="process_payment.php" method="post">
                <?php
                if (empty($infosPaiement)) {
                    echo "<p>Aucune information de paiement disponible. <a href='info_paiement.php'>Ajouter une information de paiement</a></p>";
                } else {
                    foreach ($infosPaiement as $info) {
                        echo "
                        <div class='payment-method'>
                            <input type='radio' id='payment_{$info['NUMCB']}' name='numCB' value='{$info['NUMCB']}' required>
                            <label for='payment_{$info['NUMCB']}'>Carte se terminant par " . substr($info['NUMCB'], -4) . " - Expire le {$info['DATEEXP']}</label>
                        </div>";
                    }
                }
                ?>
                <div class="payment-method">
                    <label for="idAdresse">Adresse de livraison</label>
                    <input type="text" id="idAdresse" name="idAdresse" required>
                </div>
                <div class="payment-method">
                    <label for="idLivraison">Type de livraison</label>
                    <input type="text" id="idLivraison" name="idLivraison" required>
                </div>
                <button type="submit" class="btn validate">Payer</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>