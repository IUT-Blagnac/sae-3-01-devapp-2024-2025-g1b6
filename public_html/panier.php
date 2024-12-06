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

<header class="header">
        <div class="barreMenu">
            <ul class="menuListe">
                <li> 
                    <label class="burger" for="burgerToggle">
                        <input type="checkbox" id="burgerToggle">
                        <ul class="categories">
                            <?php
                            include ("connect.inc.php");
                            include("categories.php");
                            ?>
                        </ul>
                        <span></span>
                        <span></span>
                        <span></span>
                    </label> 
                </li>
                <li> <a class="lienAccueil" href="index.php"><h1 class="titreLudorama"> Ludorama </h1>  </a></li>
                <li> <input class="barreRecherche" type="text" placeholder="Barre de recherche ..."> </li>
                <li> <div class="imgLoc"></div> </li>
                <li> <a href="panier.php"><div class="imgPanier"></div></a></li>
                <li> <?php
                        // Vérification de la session utilisateur
                        if (isset($_SESSION["user"])) {
                            $id_client = $_SESSION["user"]["IDCLIENT"];
                            // Si l'utilisateur est connecté, on le redirige vers son compte
                            echo '<a href="compte.php?id_client=' . $id_client . '"><div class="imgCompte"></div></a>';
                        } else {
                            // Sinon, on le redirige vers la page de connexion
                            echo '<a href="connexion.php"><div class="imgCompte"></div></a>';
                        }
                    ?> 
                </li>
            </ul>
        </div>
    </header>

    <!-- Breadcrumb and Cart -->
    <main class="container">

        <div class="cartContainer">
        <!-- Product List Section -->
        <div class="productList">
            <div class="cartHeader">
            <h2 class="cartTitle">Panier</h2>
            <button class="clearCartButton">Vider le panier</button>
            </div>
            <div class="product">
            <div class="productImage">
                <img src="images/nerfFortnite.jpg" alt="Nerf Fortnite edition 2024">
            </div>
            <div class="productDetails">
                <h3>Nerf Fortnite edition 2024</h3>
                <p>31779 - Marque : XYZ - Dès 5 ans</p>
                <p>Livraison</p>
            </div>
            <div class="productQuantity">
                <button>-</button>
                1
                <button>+</button>
            </div>
            <div class="productPrice">
            29.99 €
            </div>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="sidebar">
            <h4>Nerf Fortnite edition 2024</h4>
            <p>31779 - Marque : XYZ - Dès 5 ans</p>
            <div class="priceSummary">
            <button class="btn subtotal">Sous-total</button>
            <span class="price">29.99 €</span>
            </div>
            <button class="btn retrait">Retrait magasin</button>
            <button class="btn validate">Valider le panier</button>
            <div class="paymentSecurity">
            <p>Paiement 100% sécurisé</p>
            <div class="paymentIcons">
                <img src="images/visa.png" alt="Visa">
                <img src="images/mastercard.png" alt="MasterCard">
                <img src="images/paypal.png" alt="PayPal">
            </div>
            </div>
        </div>
        </div>
    </main>


  <!-- Footer -->
  <footer class="footer">
    <p>Magasin Environnement Contactez-nous Conditions générales de vente ... Utilisation cookies</p>
  </footer>
</body>
</html>
