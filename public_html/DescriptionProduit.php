<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Description du Produit - Ludorama</title>
    <link rel="stylesheet" href="Css/descProd.css">
    <link rel="stylesheet" href="Css/all.css">
    <!-- Ajout de la bibliothèque de polices de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
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
                            $stmt = $pdo->prepare("SELECT * FROM CATEGORIE WHERE IDCATEG_CATPERE IS NULL");
                            $stmt->execute();
                            $categories = $stmt->fetchAll();
                            foreach ($categories as $categorie) {
                                echo "<li><a href='categorie.php?id=".$categorie['IDCATEG']."'>".$categorie['NOMCATEG']."</a></li>";
                            }
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

    <main class="main-content">

        <div class="product-top">
            <div class="product-gallery">
                <div class="thumbnail-images">
                    <img src="images/nerf2.png" alt="Vignette 1" class="thumbnail">
                    <img src="images/nerf3.png" alt="Vignette 2" class="thumbnail">
                    <img src="images/nerf4.png" alt="Vignette 3" class="thumbnail">
                </div>
                <div class="main-image">
                    <img src="images/nerf1.png" alt="Image principale du produit" id="main-product-image">
                    <span class="favorite">❤️</span>
                </div>
            </div>

            <div class="product-details">
                <h1>Nerf Fortnite Édition 2024</h1>
                <p class="product-id">ID : 37770 | Marque : Nerf | Âge : 8+</p>
                <p class="price">29.99 €</p>
                <button class="btn-store">Choisir un magasin</button>
                <button class="btn-cart">Ajouter au panier</button>
                <p class="shipping-info">Livraison gratuite dès 50 € | Livraison prévue pour le 10/11/2024</p>
            </div>

        </div>
        

        <div class="tabs">
            <button class="tab-link active" onclick="openTab(event, 'description')">Description</button>
            <button class="tab-link" onclick="openTab(event, 'features')">Caractéristiques</button>
            <button class="tab-link" onclick="openTab(event, 'shipping')">Livraison</button>
            <button class="tab-link" onclick="openTab(event, 'reviews')">Avis</button>
        </div>

        <div id="description" class="tab-content active">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Elitarn ullamcorper luctus et ullamcorper. Sed facilisis sed lorem porta tincidunt. Fusce efficitur efficitur eros.</p>
        </div>

        <div id="features" class="tab-content">
            <ul>
                <li>Référence : 0000</li>
                <li>Marque : Nerf</li>
                <li>Âge : 8+</li>
                <li>Dimensions : 20x20x20 cm</li>
                <li>Poids : 2kg</li>
            </ul>
        </div>
        <div id="shipping" class="tab-content">
            <div class="shipping-option">
                <p>Retrait en magasin : Gratuit</p>
            </div>
            <div class="shipping-option">
                <p>Point relais : 3.90 €</p>
            </div>
            <div class="shipping-option">
                <p>Livraison à domicile : 7.90 €</p>
            </div>
            <div class="shipping-option">
                <p>Livraison express : 9.90 €</p>
            </div>
        </div>
        <div id="reviews" class="tab-content">
            <p>Aucun avis pour l'instant.</p>
        </div>

    </main>



    <footer class="footer">
        <div class="footer-column">
            <h3>Qui sommes-nous ?</h3>
            <ul>
                <li><a href="#">Ludorama.com</a></li>
                <li><a href="#">Nos magasins</a></li>
                <li><a href="#">Cartes cadeaux</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>En ce moment</h3>
            <ul>
                <li><a href="#">Ambiance de Noël</a></li>
                <li><a href="#">Nouveautés</a></li>
                <li><a href="#">Rejoignez LudiSphere !</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Marques</h3>
            <ul>
                <li><a href="#">Lego</a></li>
                <li><a href="#">Playmobil</a></li>
                <li><a href="#">Jurassic Park</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Personnages jouets</h3>
            <ul>
                <li><a href="#">Pokemon</a></li>
                <li><a href="#">Tous les personnages</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Nos sites</h3>
            <ul>
                <li><a href="#">France</a></li>
                <li><a href="#">Allemagne</a></li>
                <li><a href="#">Tous nos sites</a></li>
            </ul>
        </div>
    </footer>


    <!-- Script changement d'informations produit -->
    <script>
        // Fonction pour ouvrir les onglets
        function openTab(event, tabName) {
            var i, tabcontent, tablinks;
            // Récupérer tous les éléments avec la classe "tab-content" et les cacher
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove('active');
            }
            // Récupérer tous les éléments avec la classe "tab-link" et retirer la classe "active"
            tablinks = document.getElementsByClassName("tab-link");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            // Afficher l'onglet actuel, et ajouter une classe "active" au bouton qui a ouvert l'onglet
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.className += " active";
        }

        // Ajouter un événement pour ouvrir le premier onglet par défaut
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelector('.tab-link').click();
        });

        // Changer l'image principale au clic sur une vignette
        document.querySelectorAll('.thumbnail').forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                document.getElementById('main-product-image').src = this.src;
            });
        });
    </script>
</body>
</html>