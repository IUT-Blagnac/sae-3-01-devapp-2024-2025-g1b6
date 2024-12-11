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
                    WHERE pa.IDCLIENT = ?
                ");
                $stmt->execute([$idClient]);
                $produitsPanier = $stmt->fetchAll();

                if (empty($produitsPanier)) {
                    echo "<p>Votre panier est vide.</p>";
                } else {
                    // Affiche chaque produit du panier
                    foreach ($produitsPanier as $produit) {
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
                        </div>";
                    }
                }
            }
            ?>
        </div>

        <!-- Sidebar Section -->
        <div class="sidebar">
            <?php
            if (!empty($produitsPanier)) {
                $total = 0;
                foreach ($produitsPanier as $produit) {
                    $total += $produit['PRIXHT'] * $produit['QUANTITEPROD'];
                }
                $totalFormatted = number_format($total, 2, ',', ' ') . " €";
                echo "
                <h4>Résumé</h4>
                <div class='priceSummary'>
                    <button class='btn subtotal'>Sous-total</button>
                    <span class='price'>{$totalFormatted}</span>
                </div>
                <button class='btn validate'>Valider le panier</button>
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
</script>
</body>
</html>
