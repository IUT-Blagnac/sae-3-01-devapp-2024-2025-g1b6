<?php
session_start();
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
    
<?php 
include("header.php"); 

$idClient = intval($_SESSION['user']['IDCLIENT']);
// Récupérer les informations de paiement pour l'utilisateur connecté
$stmt = $pdo->prepare("
    SELECT ip.NUMCB, ip.NOMCOMPLETCB, ip.DATEEXP
    FROM INFORMATIONPAIEMENT ip
    JOIN POSSEDERIP pi ON ip.NUMCB = pi.NUMCB
    WHERE pi.IDCLIENT = ?
");
$stmt->execute([$idClient]);
$infosPaiement = $stmt->fetchAll();

// Récupérer les adresses pour l'utilisateur connecté
$stmt = $pdo->prepare("
    SELECT a.IDADRESSE, a.NUMRUE, a.NOMRUE, a.COMPLEMENTADR, a.NOMVILLE, a.CODEPOSTAL, a.PAYS
    FROM ADRESSE a
    JOIN POSSEDERADR pa ON a.IDADRESSE = pa.IDADRESSE
    WHERE pa.IDCLIENT = ?
");
$stmt->execute([$idClient]);
$adresses = $stmt->fetchAll();

// Récupérer les transporteurs
$stmt = $pdo->prepare("
    SELECT IDTRANSPORTEUR, TYPEEXP, FRAISEXP
    FROM TRANSPORTEUR
    ORDER BY FRAISEXP ASC
");
$stmt->execute();
$transporteurs = $stmt->fetchAll();

// Récupérer les produits du panier pour l'utilisateur connecté
$idClient = intval($_SESSION['user']['IDCLIENT']);
$stmt = $pdo->prepare("
    SELECT p.NOMPROD, p.IDPROD, p.PRIXHT, p.DESCPROD, pa.QUANTITEPROD
    FROM PANIER pa
    JOIN PRODUIT p ON pa.IDPROD = p.IDPROD
    WHERE pa.IDCLIENT = ? AND pa.IDCOMMANDE = 0
");
$stmt->execute([$idClient]);
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
?>

<div class="container">
    <div class="commande-container">
        <div class="produits">
            <?php
            if (empty($produitsPanier) && empty($packsAppliques)) {
                echo "<p>Votre panier est vide.</p>";
            } else {
                $totalHT = 0;
                // Affiche chaque produit du panier qui n'est pas inclus dans un pack appliqué
                foreach ($produitsPanierAssoc as $produit) {
                    $imagePath = "./images/prod" . htmlspecialchars($produit['IDPROD']) . ".png"; // Chemin d'image
                    $prix = number_format($produit['PRIXHT'], 2, ',', ' ') . " €";
                    $quantite = intval($produit['QUANTITEPROD']);
                    $totalHT += $produit['PRIXHT'] * $quantite;
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

                // Affiche les packs appliqués
                foreach ($packsAppliques as $pack) {
                    // Utiliser l'image du premier produit du pack comme image du pack
                    $produitsPack = explode(',', $pack['PRODUITS']);
                    $imagePath = "./images/prod" . htmlspecialchars($produitsPack[0]) . ".png";
                    $totalHT += $pack['PRIXPACK'];
                    echo "
                    <div class='product'>
                        <img src='$imagePath' alt='Image du pack'>
                        <div class='productDetails'>
                            <h3>" . htmlspecialchars($pack['NOMPACK']) . "</h3>
                            <p>Pack promotionnel</p>
                            <p>Prix : " . number_format($pack['PRIXPACK'], 2, ',', ' ') . " €</p>
                            <p>Quantité : 1</p>
                        </div>
                    </div>";
                }

                $totalTTC = $totalHT * 1.2; // Ajout de la TVA (20%)
            }
            ?>
        </div>

        <div class="payment-section">
            <h2>Choisir une Information de Paiement</h2>
            <form action="process_payment.php" method="post" onsubmit="return confirmPayment()">
                <?php
                if (empty($infosPaiement)) {
                    echo "<p>Aucune information de paiement disponible. <a href='info_paiement.php'>Ajouter une information de paiement</a></p>";
                } else {
                    echo "<p><a href='info_paiement.php'>Ajouter une information de paiement</a></p>";
                    foreach ($infosPaiement as $info) {
                        echo "
                        <div class='payment-method'>
                            <input type='radio' id='payment_{$info['NUMCB']}' name='numCB' value='{$info['NUMCB']}' required>
                            <label for='payment_{$info['NUMCB']}'>Carte se terminant par <span>" . substr($info['NUMCB'], -4) . "</span> - Expire le {$info['DATEEXP']}</label>
                        </div>";
                    }
                }
                ?>
                <h2>Choisir une Adresse de Livraison</h2>
                <?php
                    foreach ($adresses as $adresse) {
                        echo "
                        <div class='address-method'>
                            <input type='radio' id='address_{$adresse['IDADRESSE']}' name='idAdresse' value='{$adresse['IDADRESSE']}' required>
                            <label for='address_{$adresse['IDADRESSE']}'>
                                {$adresse['NUMRUE']} {$adresse['NOMRUE']}, {$adresse['COMPLEMENTADR']}, {$adresse['NOMVILLE']}, {$adresse['CODEPOSTAL']}, {$adresse['PAYS']}
                            </label>
                        </div>";
                    }
                ?>
                <div class="address-method">
                    <input type="radio" id="newAddress" name="idAdresse" value="new" required>
                    <label for="newAddress">Entrer une nouvelle adresse</label>
                </div>
                <div id="newAddressFields" style="display: none;">
                    <div class="payment-method">
                        <label for="numRue">Numéro de rue</label>
                        <input type="text" id="numRue" name="numRue">
                    </div>
                    <div class="payment-method">
                        <label for="nomRue">Nom de la rue</label>
                        <input type="text" id="nomRue" name="nomRue">
                    </div>
                    <div class="payment-method">
                        <label for="complementAdr">Complément d'adresse</label>
                        <input type="text" id="complementAdr" name="complementAdr">
                    </div>
                    <div class="payment-method">
                        <label for="nomVille">Ville</label>
                        <input type="text" id="nomVille" name="nomVille">
                    </div>
                    <div class="payment-method">
                        <label for="codePostal">Code postal</label>
                        <input type="text" id="codePostal" name="codePostal">
                    </div>
                    <div class="payment-method">
                        <label for="pays">Pays</label>
                        <input type="text" id="pays" name="pays">
                    </div>
                </div>
                <h2>Choisir un Transporteur</h2>
                <?php
                if (empty($transporteurs)) {
                    echo "<p>Aucun transporteur disponible.</p>";
                } else {
                    foreach ($transporteurs as $transporteur) {
                        echo "
                        <div class='transport-method'>
                            <input type='radio' id='transport_{$transporteur['IDTRANSPORTEUR']}' name='idTransporteur' value='{$transporteur['IDTRANSPORTEUR']}' data-frais='{$transporteur['FRAISEXP']}' required>
                            <label for='transport_{$transporteur['IDTRANSPORTEUR']}'>
                                {$transporteur['TYPEEXP']} - Frais : " . number_format($transporteur['FRAISEXP'], 2, ',', ' ') . " €
                            </label>
                        </div>";
                    }
                }
                ?>
                <h2>Prix Total</h2>
                <p id="totalPrice"><?php echo number_format($totalTTC, 2, ',', ' '); ?> €</p>
                <input type="hidden" id="totalPriceInput" name="totalPrice" value="<?php echo $totalTTC; ?>">
                <button type="submit" class="btn validate">Payer</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('newAddress').addEventListener('change', function() {
    document.getElementById('newAddressFields').style.display = 'block';
});

document.querySelectorAll('input[name="idAdresse"]').forEach(function(input) {
    if (input.id !== 'newAddress') {
        input.addEventListener('change', function() {
            document.getElementById('newAddressFields').style.display = 'none';
        });
    }
});

document.querySelectorAll('input[name="idTransporteur"]').forEach(function(input) {
    input.addEventListener('change', function() {
        const frais = parseFloat(this.getAttribute('data-frais'));
        const totalHT = <?php echo $totalHT; ?>;
        const totalTTC = totalHT * 1.2; // Ajout de la TVA (20%)
        const total = totalTTC + frais;
        document.getElementById('totalPrice').textContent = total.toFixed(2).replace('.', ',') + ' €';
        document.getElementById('totalPriceInput').value = total;
    });
});

function confirmPayment() {
    return confirm("Voulez-vous confirmer le paiement ?");
}
</script>

</body>
</html>