<!DOCTYPE html>
<!-- recherche.php-->
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de Recherche - Ludorama</title>
    <link rel="stylesheet" href="Css/accueil.css">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/recherche.css">
</head>
<body>
    <!-- Inclure le header -->
    <?php include 'header.php'; ?>

    <!-- Contenu principal -->
    <main class="recherche-main">
        <!-- Section des filtres -->
        <aside class="recherche-filtres">
            <form method="GET" action="recherche.php">
                <h3>Filtrer les produits :</h3>
                <label>Catégorie :
                    <select name="categorie">
                        <option value="">Toutes</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?= htmlspecialchars($categorie) ?>" <?= isset($_GET['categorie']) && $_GET['categorie'] === $categorie ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categorie) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Marque :
                    <select name="marque">
                        <option value="">Toutes</option>
                        <?php foreach ($marques as $marque): ?>
                            <option value="<?= htmlspecialchars($marque) ?>" <?= isset($_GET['marque']) && $_GET['marque'] === $marque ? 'selected' : '' ?>>
                                <?= htmlspecialchars($marque) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Prix :
                    <input type="number" name="prix_min" placeholder="Min" value="<?= htmlspecialchars($_GET['prix_min'] ?? '') ?>">
                    <input type="number" name="prix_max" placeholder="Max" value="<?= htmlspecialchars($_GET['prix_max'] ?? '') ?>">
                </label>
                <label>
                    <input type="checkbox" name="en_stock" <?= isset($_GET['en_stock']) ? 'checked' : '' ?>> En stock uniquement
                </label>
                <button type="submit">Filtrer</button>
            </form>
        </aside>

        <!-- Section des résultats -->
<section class="recherche-resultats">
    <h1>Résultats de recherche</h1>
    <p><?= count($resultats) ?> produit(s) trouvé(s)</p>
    <div class="produits-grid">
        <?php foreach ($resultats as $produit): ?>
            <div class="produit-item">
                <!-- Vérifier si 'image_url' existe avant d'afficher l'image -->
                <?php if (isset($produit['image_url']) && !empty($produit['image_url'])): ?>
                    <img src="<?= htmlspecialchars($produit['image_url']) ?>" alt="<?= htmlspecialchars($produit['NOMPROD']) ?>">
                <?php else: ?>
                    <img src="default-image.jpg" alt="<?= htmlspecialchars($produit['NOMPROD']) ?>"> <!-- Image par défaut -->
                <?php endif; ?>
                
                <h2><?= htmlspecialchars($produit['NOMPROD']) ?></h2>
                <p><?= htmlspecialchars($produit['DESCPROD']) ?></p>
                <p><?= htmlspecialchars($produit['PRIXHT']) ?> €</p>
                <p class="<?= $produit['QTESTOCK'] > 0 ? 'en-stock' : 'rupture-stock' ?>">
                    <?= $produit['QTESTOCK'] > 0 ? 'En stock' : 'Rupture de stock' ?>
                </p>
                <!-- Lien vers la page de détails du produit -->
                <a href="descProduit.php?idProd=<?= $produit['IDPROD'] ?>" class="produit-lien">Voir le produit</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

    </main>

    <!-- Inclure le footer -->
    <?php include 'footer.php'; ?>
</body>
</html>