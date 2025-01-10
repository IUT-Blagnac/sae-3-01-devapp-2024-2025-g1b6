<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Description du Pack - Ludorama</title>
    <link rel="stylesheet" href="Css/descPack.css">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
</head>
<body>
<?php
include("header.php");

// Vérification de la présence et de la validité de l'identifiant du pack
if (!isset($_GET['idPack']) || empty($_GET['idPack'])) {
    echo "Pack non spécifié.";
    exit();
}

$idPack = intval($_GET['idPack']); // Sécurisation de l'entrée

// Récupération des informations du pack depuis la base de données
$stmt = $pdo->prepare("
    SELECT p.*, 
           GROUP_CONCAT(pr.IDPROD SEPARATOR ',') AS PRODUITS
    FROM PACK p
    LEFT JOIN ASSOPACK ap ON p.IDPACK = ap.IDPACK
    LEFT JOIN PRODUIT pr ON ap.IDPROD = pr.IDPROD
    WHERE p.IDPACK = ?
    GROUP BY p.IDPACK
");
$stmt->execute([$idPack]);
$pack = $stmt->fetch();

if (!$pack) {
    echo "Pack introuvable.";
    exit();
}

// Récupération des produits du pack
$produits = explode(',', $pack['PRODUITS']);


// Définition de l'image principale du pack (utilisation de l'image du premier produit du pack)
$imagePath = "./images/prod" . htmlspecialchars($produits[0]) . ".png"; // Image par défaut si aucune image n'est trouvée
?>

<main class="main-content">
    <!-- Partie galerie du pack -->
    <div class="product-top">
        <div class="product-gallery">
            <div class="main-image">
                <?php
                // Affichage de l'image principale
                echo "<img src='$imagePath' width='100' alt='image principale du pack' id='main-product-image'>";
                ?>
                <span class="favorite">❤️</span>
            </div>
            <div class="product-thumbnails">
                <?php
                // Affichage des images des produits du pack
                foreach ($produits as $idProd) {
                    $imagePath = "./images/prod" . htmlspecialchars($idProd) . ".png";
                    echo "<img src='$imagePath' width='50' alt='image du produit' class='thumbnail' onclick='changeMainImage(\"$imagePath\")'>";
                }
                ?>
            </div>
        </div>

        <!-- Partie détails du pack -->
        <div class="product-details">
            <h1><?php echo htmlspecialchars($pack['NOMPACK']); ?></h1>
            <p class="product-id">Réference : <?php echo $pack['IDPACK']; ?></p>
            <p class="price">
                <span class="discounted-price"><?php echo number_format($pack['PRIXPACK'], 2, ',', ' '); ?> €</span>
            </p>
            <button class="btn-cart" onclick="confirmAddToCart()">Ajouter au panier</button>
            <p class="shipping-info">Livraison gratuite dès 50 € | Livraison prévue pour le <?php echo date('d/m/Y', strtotime('+5 days')); ?></p>
        </div>
    </div>

    <!-- Liste des produits du pack -->
    <div class="pack-products">
        <h2>Produits inclus dans le pack</h2>
        <ul>
            <?php
            foreach ($produits as $idProd) {
                $stmtProd = $pdo->prepare("SELECT NOMPROD FROM PRODUIT WHERE IDPROD = ?");
                $stmtProd->execute([$idProd]);
                $produit = $stmtProd->fetch();
                if ($produit) {
                    echo "<li><a href='descProduit.php?idProd=" . htmlspecialchars($idProd) . "'>" . htmlspecialchars($produit['NOMPROD']) . "</a></li>";
                }
            }
            ?>
        </ul>
    </div>

    <!-- Description du pack -->
    <div id="description" class="tab-content active">
        <h2>Description du Pack</h2>
        <p><?php echo nl2br(htmlspecialchars($pack['DESCPACK'])); ?></p>
    </div>
</main>

<?php
include("footer.php");
?>

<script>
    function changeMainImage(imagePath) {
        document.getElementById('main-product-image').src = imagePath;
    }

    function confirmAddToCart() {
        const confirmation = confirm("Voulez-vous ajouter ce produit au panier ?");
        if (confirmation) {
            addToCart();
        }
    }

    function addToCart() {
        const packId = <?php echo $idPack; ?>;
        const clientId = <?php echo isset($_SESSION['user']['IDCLIENT']) ? $_SESSION['user']['IDCLIENT'] : 'null'; ?>;
        const produits = <?php echo json_encode($produits); ?>;

        if (!clientId) {
            alert("Vous devez être connecté pour ajouter un pack au panier.");
            return;
        }

        fetch('add_pack.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ idPack: packId, idClient: clientId, produits: produits })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Produits du pack ajoutés au panier avec succès !");
            } else {
                alert("Erreur lors de l'ajout des produits du pack au panier : " + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert("Erreur lors de l'ajout des produits du pack au panier.");
        });
    }
</script>
</body>
</html>