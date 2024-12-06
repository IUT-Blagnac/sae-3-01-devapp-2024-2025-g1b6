<?php
include("connect.inc.php");
session_start();

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
$imagePrincipale = $produit['IMAGE'] ?? 'images/default.png'; // Image par défaut si aucune image n'est trouvée

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    echo '<script>alert("Vous devez être connecté pour ajouter un produit au panier.");</script>';
    header("Location: connexion.php");
    exit();
}

$id_client = $_SESSION["user"]["IDCLIENT"]; // ID du client connecté

// Ajouter au panier (si le formulaire est soumis après confirmation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $quantite = intval($_POST['quantite']);
    
    if ($quantite > 0) {
        // Vérifier si le produit existe déjà dans le panier
        $stmtCheck = $pdo->prepare("SELECT * FROM PANIER WHERE IDCLIENT = ? AND IDPROD = ?");
        $stmtCheck->execute([$id_client, $idProd]);
        $existingItem = $stmtCheck->fetch();

        if ($existingItem) {
            // Si le produit est déjà dans le panier, on met à jour la quantité
            $newQuantity = $existingItem['QUANTITEPROD'] + $quantite;
            $stmtUpdate = $pdo->prepare("UPDATE PANIER SET QUANTITEPROD = ? WHERE IDCLIENT = ? AND IDPROD = ?");
            $stmtUpdate->execute([$newQuantity, $id_client, $idProd]);
        } else {
            // Si le produit n'est pas dans le panier, on l'ajoute
            $stmtInsert = $pdo->prepare("INSERT INTO PANIER (IDCLIENT, IDPROD, QUANTITEPROD) VALUES (?, ?, ?)");
            $stmtInsert->execute([$id_client, $idProd, $quantite]);
        }

        // Message de confirmation et redirection vers le panier
        echo '<script>alert("Produit ajouté au panier !");</script>';
        header("Location: panier.php");
        exit();
    } else {
        echo '<script>alert("Quantité invalide.");</script>';
    }
}

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
                        include("categories.php");
                        ?>
                    </ul>
                    <span></span>
                    <span></span>
                    <span></span>
                </label>
            </li>
            <li><a class="lienAccueil" href="index.php"><h1 class="titreLudorama">Ludorama</h1></a></li>
            <li><input class="barreRecherche" type="text" placeholder="Barre de recherche ..."></li>
            <li><div class="imgLoc"></div></li>
            <li><a href="panier.php"><div class="imgPanier"></div></a></li>
            <li>
                <?php
                if (isset($_SESSION["user"])) {
                    $id_client = $_SESSION["user"]["IDCLIENT"];
                    echo '<a href="compte.php?id_client=' . $id_client . '"><div class="imgCompte"></div></a>';
                } else {
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
            <div class="main-image">
                <?php
                echo '<img src="' . htmlspecialchars($imagePrincipale) . '" alt="Image principale du produit" id="main-product-image">';
                ?>
                <span class="favorite">❤️</span>
            </div>
        </div>

        <div class="product-details">
            <h1><?php echo htmlspecialchars($produit['NOMPROD']); ?></h1>
            <p class="product-id">ID : <?php echo $produit['IDPROD']; ?> | Marque : <?php echo htmlspecialchars($produit['NOMMARQUE']); ?> | Catégories : <?php echo htmlspecialchars($produit['CATEGORIES']); ?></p>
            <p class="price"><?php echo number_format($produit['PRIXHT'], 2, ',', ' '); ?> €</p>
            <button class="btn-store">Choisir un magasin</button>
            
            <!-- Formulaire pour ajouter au panier avec confirmation -->
            <form method="POST" action="">
                <label for="quantite">Quantité :</label>
                <input type="number" name="quantite" id="quantite" value="1" min="1" required>
                <button type="button" id="confirm-add" class="btn-cart">Ajouter au panier</button>
            </form>

            <p class="shipping-info">Livraison gratuite dès 50 € | Livraison prévue pour le <?php echo date('d/m/Y', strtotime('+5 days')); ?></p>
        </div>
    </div>

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
</main>

<script>
// JavaScript pour la confirmation avant l'ajout au panier
document.getElementById("confirm-add").addEventListener("click", function() {
    var quantite = document.getElementById("quantite").value;
    var confirmAction = confirm("Voulez-vous vraiment ajouter ce produit au panier avec une quantité de " + quantite + " ?");
    if (confirmAction) {
        document.querySelector("form").submit(); // Soumettre le formulaire si confirmé
    }
});
</script>

</body>
</html>
