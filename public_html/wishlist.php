<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Liste de Souhaits</title>
  <link rel="stylesheet" href="Css/wishlist.css">
  <link rel="stylesheet" href="Css/all.css">
</head>
<body>

<!-- En-tête -->
<?php include("header.php"); ?>

<main class="container">
    <div class="wishlistContainer">
        <!-- Product List Section -->
        <div class="productList">
            <div class="wishlistHeader">
                <h2 class="wishlistTitle">Liste de Souhaits</h2>
            </div>
            <?php

            // Vérifie si un utilisateur est connecté
            if (!isset($_SESSION['user']['IDCLIENT'])) {
                echo "<p>Vous devez être connecté pour voir votre liste de souhaits.</p>";
            } else {
                $idClient = intval($_SESSION['user']['IDCLIENT']);

                // Récupère les produits de la liste de souhaits pour l'utilisateur connecté
                $stmt = $pdo->prepare("
                    SELECT p.NOMPROD, p.IDPROD, p.PRIXHT, p.DESCPROD
                    FROM SOUHAITER s
                    JOIN PRODUIT p ON s.IDPROD = p.IDPROD
                    WHERE s.IDCLIENT = ?
                ");
                $stmt->execute([$idClient]);
                $produitsWishlist = $stmt->fetchAll();

                if (empty($produitsWishlist)) {
                    echo "<p>Votre liste de souhaits est vide.</p>";
                } else {
                    // Affiche chaque produit de la liste de souhaits
                    foreach ($produitsWishlist as $produit) {
                        $imagePath = "./images/prod" . htmlspecialchars($produit['IDPROD']) . ".png"; // Chemin d'image
                        $prix = number_format($produit['PRIXHT'], 2, ',', ' ') . " €";
                        echo "
                        <div class='product'>
                            <div class='productImage'>
                                <img src='{$imagePath}' alt='" . htmlspecialchars($produit['NOMPROD']) . "'>
                            </div>
                            <div class='productDetails'>
                                <h3><a href='descProduit.php?idProd=" . htmlspecialchars($produit['IDPROD']) . "'>" . htmlspecialchars($produit['NOMPROD']) . "</a></h3>
                                <p>" . htmlspecialchars($produit['DESCPROD']) . "</p>
                                <p class='productPrice'>{$prix}</p>
                            </div>
                            <div class='productActions'>
                                <button class='removeFromWishlistButton' onclick='confirmRemoveFromWishlist(" . htmlspecialchars($produit['IDPROD']) . ")'>Retirer de la liste</button>
                                <button onclick='confirmAddToCart(" . htmlspecialchars($produit['IDPROD']) . ")'>Ajouter au panier</button>
                            </div>
                        </div>";
                    }
                }
            }
            ?>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include("footer.php"); ?>

<script>
    function confirmAddToCart(productId) {
        const confirmation = confirm("Voulez-vous ajouter ce produit au panier ?");
        if (confirmation) {
            addToCart(productId);
        }
    }

    function confirmRemoveFromWishlist(productId) {
        const confirmation = confirm("Voulez-vous retirer ce produit de votre liste de souhaits ?");
        if (confirmation) {
            removeFromWishlist(productId);
        }
    }

    function addToCart(productId) {
        const clientId = <?php echo isset($_SESSION['user']['IDCLIENT']) ? $_SESSION['user']['IDCLIENT'] : 'null'; ?>;

        if (!clientId) {
            alert("Vous devez être connecté pour ajouter un produit au panier.");
            return;
        }

        fetch('add_to_panier.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ idProd: productId, idClient: clientId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Produit ajouté au panier avec succès !");
                location.reload(); // Recharger la page pour mettre à jour la liste de souhaits
            } else {
                alert("Erreur lors de l'ajout du produit au panier : " + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert("Erreur lors de l'ajout du produit au panier.");
        });
    }

    function removeFromWishlist(productId) {
        const clientId = <?php echo isset($_SESSION['user']['IDCLIENT']) ? $_SESSION['user']['IDCLIENT'] : 'null'; ?>;

        if (!clientId) {
            alert("Vous devez être connecté pour retirer un produit de votre liste de souhaits.");
            return;
        }

        fetch('remove_from_wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ idProd: productId, idClient: clientId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Produit retiré de votre liste de souhaits avec succès !");
                location.reload(); // Recharger la page pour mettre à jour la liste de souhaits
            } else {
                alert("Erreur lors du retrait du produit de la liste de souhaits : " + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert("Erreur lors du retrait du produit de la liste de souhaits.");
        });
    }
</script>

</body>
</html>