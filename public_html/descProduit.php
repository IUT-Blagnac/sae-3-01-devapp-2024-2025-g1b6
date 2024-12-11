<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Description du Produit - Ludorama</title>
    <link rel="stylesheet" href="Css/descProd.css">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <style>
        /* Style minimal pour gérer l'affichage des onglets */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
<?php
include("header.php");

// Vérification de la présence et de la validité de l'identifiant du produit
if (!isset($_GET['idProd']) || empty($_GET['idProd'])) {
    echo "Produit non spécifié.";
    exit();
}

$idProd = intval($_GET['idProd']); // Sécurisation de l'entrée

// Récupération des informations du produit depuis la base de données
$stmt = $pdo->prepare("
    SELECT p.*, 
           m.NOMMARQUE, 
           GROUP_CONCAT(c.NOMCATEG SEPARATOR ', ') AS CATEGORIES
    FROM PRODUIT p
    LEFT JOIN MARQUE m ON p.IDMARQUE = m.IDMARQUE
    LEFT JOIN APPARTENIRCATEG ac ON p.IDPROD = ac.IDPROD
    LEFT JOIN CATEGORIE c ON ac.IDCATEG = c.IDCATEG
    WHERE p.IDPROD = ?
    GROUP BY p.IDPROD
");
$stmt->execute([$idProd]);
$produit = $stmt->fetch();

if (!$produit) {
    echo "Produit introuvable.";
    exit();
}

// Définition de l'image principale du produit (colonne IMAGE dans PRODUIT)
$imagePath = "./images/prod". htmlspecialchars($produit['IDPROD'])  .".png" ; // Image par défaut si aucune image n'est trouvée
?>

<main class="main-content">
    <!-- Partie galerie du produit -->
    <div class="product-top">
        <div class="product-gallery">
            <div class="main-image">
                <?php
                // Affichage de l'image principale
                echo "<img src='$imagePath' width='100' alt='image principale du produit' id='main-product-image'>";
                ?>
                <span class="favorite">❤️</span>
            </div>
        </div>

        <!-- Partie détails du produit -->
        <div class="product-details">
            <h1><?php echo htmlspecialchars($produit['NOMPROD']); ?></h1>
            <p class="product-id">Réference : <?php echo $produit['IDPROD']; ?> | Marque : <?php echo htmlspecialchars($produit['NOMMARQUE']); ?> | Catégories : <?php echo htmlspecialchars($produit['CATEGORIES']); ?></p>
            <p class="price"><?php echo number_format($produit['PRIXHT'], 2, ',', ' '); ?> €</p>
            <button class="btn-cart" onclick="confirmAddToCart()">Ajouter au panier</button>
            <p class="shipping-info">Livraison gratuite dès 50 € | Livraison prévue pour le <?php echo date('d/m/Y', strtotime('+5 days')); ?></p>
        </div>
    </div>

    <!-- Description et onglets -->
    <div class="tabs">
        <button class="tab-link active" onclick="openTab(event, 'description')">Description</button>
        <button class="tab-link" onclick="openTab(event, 'features')">Caractéristiques</button>
        <button class="tab-link" onclick="openTab(event, 'shipping')">Livraison</button>
        <button class="tab-link" onclick="openTab(event, 'reviews')">Avis</button>
    </div>

    <div id="description" class="tab-content active">
        <p><?php echo nl2br(htmlspecialchars($produit['DESCPROD'])); ?></p>
    </div>

    <div id="features" class="tab-content">
        <ul>
            <li>Marque : <?php echo htmlspecialchars($produit['NOMMARQUE']); ?></li>
            <li>Couleur : <?php echo htmlspecialchars($produit['COULEUR']); ?></li>
            <li>Composition : <?php echo htmlspecialchars($produit['COMPOSITION']); ?></li>
            <li>Poids : <?php echo htmlspecialchars($produit['POIDSPRODUIT']); ?> kg</li>
            <li>Stock disponible : <?php echo htmlspecialchars($produit['QTESTOCK']); ?></li>
        </ul>
    </div>
    <div id="shipping" class="tab-content">
        <p>Livraison standard : 3.90 €</p>
        <p>Livraison express : 9.90 €</p>
    </div>
    <div id="reviews" class="tab-content">
        <?php
        // Récupération des avis associés au produit
        $stmtAvis = $pdo->prepare("SELECT * FROM AVIS A JOIN CLIENT C ON A.IDCLIENT=C.IDCLIENT WHERE IDPROD = ?");
        $stmtAvis->execute([$idProd]);
        $avis = $stmtAvis->fetchAll();
        if ($avis) {
            foreach ($avis as $a) {
                echo '<p><strong>' . htmlspecialchars($a['NOMCLIENT']) . " " .htmlspecialchars($a['PRENOMCLIENT']). ' :</strong> ' . htmlspecialchars($a['DESCAVIS']) . '</p>';
            }
        } else {
            echo '<p>Aucun avis pour l\'instant.</p>';
        }
        ?>
    </div>

</main>

<?php
include("footer.php");
?>

<script>
    function confirmAddToCart() {
        const confirmation = confirm("Voulez-vous ajouter ce produit au panier ?");
        if (confirmation) {
            addToCart();
        }
    }

    function addToCart() {
        const productId = <?php echo $idProd; ?>;
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
            } else {
                alert("Erreur lors de l'ajout du produit au panier : " + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert("Erreur lors de l'ajout du produit au panier.");
        });
    }

    function openTab(evt, tabName) {
        // Récupérer tous les éléments avec la classe "tab-content" et les cacher
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => content.classList.remove('active'));

        // Supprimer la classe "active" de tous les boutons d'onglets
        const tabLinks = document.querySelectorAll('.tab-link');
        tabLinks.forEach(link => link.classList.remove('active'));

        // Afficher le contenu de l'onglet actuel et ajouter la classe "active" au bouton sélectionné
        document.getElementById(tabName).classList.add('active');
        evt.currentTarget.classList.add('active');
    }
</script>
</body>
</html>
