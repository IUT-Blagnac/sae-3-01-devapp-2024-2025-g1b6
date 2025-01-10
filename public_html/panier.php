<?php
session_start();
?>    
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panier Page</title>
  <link rel="stylesheet" href="Css/paiement.css">
  <link rel="stylesheet" href="Css/all.css">
</head>
<body>

<!-- En-tête -->
<?php include("header.php"); ?>

<main class="container">
    <div class="cartContainer">
        <!-- Product List Section -->
        <div class="productList">
            <div class="cartHeader">
                <h2 class="cartTitle">Panier</h2>
                <button class="clearCartButton" onclick="clearCart()">Vider le panier</button>
            </div>
            <?php

            // Vérifie si un utilisateur est connecté
            if (!isset($_SESSION['user']['IDCLIENT'])) {
                echo "<p>Vous devez être connecté pour voir votre panier.</p>";
            } else {
                $idClient = intval($_SESSION['user']['IDCLIENT']);

                // Récupère les produits du panier pour l'utilisateur connecté
                $stmt = $pdo->prepare("
                    SELECT p.NOMPROD, p.IDPROD, p.PRIXHT, p.DESCPROD, pa.QUANTITEPROD
                    FROM PANIER pa
                    JOIN PRODUIT p ON pa.IDPROD = p.IDPROD
                    WHERE pa.IDCLIENT = ? AND pa.IDCOMMANDE = 0
                ");
                $stmt->execute([$idClient]);
                $produitsPanier = $stmt->fetchAll();

                if (empty($produitsPanier)) {
                    echo "<p>Votre panier est vide.</p>";
                } else {
                    // Récupère les informations des packs et leurs produits
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

                    // Affiche chaque produit du panier qui n'est pas inclus dans un pack appliqué
                    foreach ($produitsPanierAssoc as $produit) {
                        $imagePath = "./images/prod" . htmlspecialchars($produit['IDPROD']) . ".png"; // Chemin d'image
                        $prix = number_format($produit['PRIXHT'], 2, ',', ' ') . " €";
                        echo "
                        <div class='product'>
                            <div class='productImage'>
                                <img src='{$imagePath}' alt='" . htmlspecialchars($produit['NOMPROD']) . "'>
                            </div>
                            <div class='productDetails'>
                                <h3>" . htmlspecialchars($produit['NOMPROD']) . "</h3>
                                <p>" . htmlspecialchars($produit['DESCPROD']) . "</p>
                            </div>
                            <div class='productQuantity'>
                                <button onclick='updateQuantity(" . $produit['IDPROD'] . ", -1)' " . ($produit['QUANTITEPROD'] <= 1 ? "disabled" : "") . ">-</button>
                                " . htmlspecialchars($produit['QUANTITEPROD']) . "
                                <button onclick='updateQuantity({$produit['IDPROD']}, 1)'>+</button>
                            </div>
                            <div class='productPrice'>
                                {$prix}
                            </div>
                            <div class='productActions'>
                                <button class='removeFromCartButton' onclick='confirmRemoveFromCart(" . htmlspecialchars($produit['IDPROD']) . ")'>Retirer</button>
                            </div>
                        </div>";
                    }

                    // Affiche les packs appliqués
                    foreach ($packsAppliques as $pack) {
                        // Utiliser l'image du premier produit du pack comme image du pack
                        $produitsPack = explode(',', $pack['PRODUITS']);
                        $imagePath = "./images/prod" . htmlspecialchars($produitsPack[0]) . ".png";

                        echo "
                        <div class='product'>
                            <div class='productImage'>
                                <img src='{$imagePath}' alt='" . htmlspecialchars($pack['NOMPACK']) . "'>
                            </div>
                            <div class='productDetails'>
                                <h3>" . htmlspecialchars($pack['NOMPACK']) . "</h3>
                                <p>Pack promotionnel</p>
                            </div>
                            <div class='productQuantity'>
                                1
                            </div>
                            <div class='productPrice'>
                                " . number_format($pack['PRIXPACK'], 2, ',', ' ') . " €
                            </div>
                            <div class='productActions'>
                                <button class='removeFromCartButton' onclick='confirmRemoveFromCart(" . htmlspecialchars($produit['IDPROD']) . ")'>Retirer</button>
                            </div>
                        </div>";
                    }
                }
            }
            ?>
        </div>

        <!-- Sidebar Section -->
        <div class="sidebar">
            <?php
            if (!empty($produitsPanier) || !empty($packsAppliques)) {
                $total = 0;
                foreach ($produitsPanierAssoc as $produit) {
                    $total += $produit['PRIXHT'] * $produit['QUANTITEPROD'];
                }
                foreach ($packsAppliques as $pack) {
                    $total += $pack['PRIXPACK'];
                }
                $totalFormatted = number_format($total, 2, ',', ' ') . " €";
                echo "
                <h4>Résumé</h4>
                <div class='priceSummary'>
                    <span>Total</span>
                    <span class='price'>{$totalFormatted}</span>
                </div>
                <form action='commande.php' method='post'>
                    <button type='submit' class='btn validate'>Valider le panier</button>
                </form>
                <div class='paymentSecurity'>
                    <p>Paiement 100% sécurisé</p>
                    <div class='paymentIcons'>
                        <img src='images/visa.png' alt='Visa'>
                        <img src='images/mastercard.png' alt='MasterCard'>
                        <img src='images/paypal.png' alt='PayPal'>
                    </div>
                </div>";
            }
            ?>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include("footer.php"); ?>

<script>
    function updateQuantity(productId, delta) {
        const formData = new URLSearchParams();
        formData.append('idProd', productId);
        formData.append('delta', delta);
        formData.append('idClient', <?php echo $_SESSION['user']['IDCLIENT']; ?>);

        fetch('update_panier.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la mise à jour de la quantité : ' + data.message);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function clearCart() {
        if (confirm("Voulez-vous vraiment vider votre panier ?")) {
            const formData = new URLSearchParams();
            formData.append('idClient', <?php echo $_SESSION['user']['IDCLIENT']; ?>);

            fetch('clear_panier.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la suppression du panier : ' + data.message);
                }
            })
            .catch(error => console.error('Erreur:', error));
        }
    }

    function confirmRemoveFromCart(productId) {
        const confirmation = confirm("Voulez-vous retirer ce produit du panier ?");
        if (confirmation) {
            removeFromCart(productId);
        }
    }

    function removeFromCart(productId) {
        const clientId = <?php echo isset($_SESSION['user']['IDCLIENT']) ? $_SESSION['user']['IDCLIENT'] : 'null'; ?>;

        if (!clientId) {
            alert("Vous devez être connecté pour retirer un produit du panier.");
            return;
        }

        fetch('remove_from_panier.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ idProd: productId, idClient: clientId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Produit retiré du panier avec succès !");
                location.reload(); // Recharger la page pour mettre à jour le panier
            } else {
                alert("Erreur lors du retrait du produit du panier : " + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert("Erreur lors du retrait du produit du panier.");
        });
    }
</script>
</body>
</html>