<?php
require_once 'rechercheAvancee.php'; // Inclure la fonction rechercheAvancee
require_once 'connect.inc.php'; // Connexion PDO

// Récupérer les termes de recherche depuis la barre de recherche
$criteres = [
    'mot_cle' => $_GET['mot_cle'] ?? '',
    'categorie' => $_GET['categorie'] ?? '',
    'marque' => $_GET['marque'] ?? '',
    'prix_min' => $_GET['prix_min'] ?? '',
    'prix_max' => $_GET['prix_max'] ?? '',
    'en_stock' => isset($_GET['en_stock']) ? 1 : 0
];

// Charger les catégories dynamiquement
$stmt = $pdo->query("SELECT IDCATEG, NOMCATEG FROM CATEGORIE ORDER BY NOMCATEG ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Charger les marques dynamiquement
$stmt = $pdo->query("SELECT DISTINCT NOMMARQUE FROM MARQUE ORDER BY NOMMARQUE ASC");
$marques = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les produits via la fonction rechercheAvancee
$resultats = rechercheAvancee($criteres, $pdo);

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche Produits</title>
    <link rel="stylesheet" href="Css/recherche.css">
    <link rel="stylesheet" href="Css/all.css">
</head>

<body>
    <?php include("header.php") ?>

    <div class="recherche-main">
        <!-- Section des filtres -->
        <div class="recherche-filtres">
            <h3>Filtres</h3>
            <form action="recherche.php" method="get">
                <label for="mot_cle">Mot-clé :</label>
                <input type="text" name="mot_cle" id="mot_cle" value="<?= htmlspecialchars($criteres['mot_cle']) ?>"> <br>

                <label for="categorie">Catégorie :</label>
                <select name="categorie" id="categorie">
                    <option value="">-- Toutes les catégories --</option>
                    <?php foreach ($categories as $categorie): ?>
                        <option value="<?= htmlspecialchars($categorie['IDCATEG']) ?>"
                            <?= $criteres['categorie'] == $categorie['IDCATEG'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categorie['NOMCATEG']) ?>
                        </option>
                    <?php endforeach; ?>


                </select><br>

                <label for="marque">Marque :</label>
                <select name="marque" id="marque">
                    <option value="">-- Toutes les marques --</option>
                    <?php foreach ($marques as $marque): ?>
                        <option value="<?= htmlspecialchars($marque) ?>"
                            <?= $criteres['marque'] == $marque ? 'selected' : '' ?>>
                            <?= htmlspecialchars($marque) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="prix_min">Prix minimum :</label>
                <input type="number" step="0.01" name="prix_min" id="prix_min" value="<?= htmlspecialchars($criteres['prix_min']) ?>"> <br>

                <label for="prix_max">Prix maximum :</label>
                <input type="number" step="0.01" name="prix_max" id="prix_max" value="<?= htmlspecialchars($criteres['prix_max']) ?>"><br>

                <label for="en_stock">
                    En stock uniquement
                    <input type="checkbox" name="en_stock" id="en_stock" <?= $criteres['en_stock'] ? 'checked' : '' ?>>
                </label><br>

                <button type="submit">Rechercher</button>
            </form>
        </div>


        <!-- Section des résultats -->
        <div class="recherche-resultats">
            <h3>Résultats</h3>
            <div class="produits-grid">
                <?php if (empty($resultats)): ?>
                    <p>Aucun produit trouvé.</p>
                <?php else: ?>
                    <?php foreach ($resultats as $produit): ?>
                        <div class="produit-item">
                            <h4><?= htmlspecialchars($produit['NOMPROD']) ?></h4>
                            <p><?= htmlspecialchars($produit['DESCPROD']) ?></p>
                            <p>Prix : <?= number_format($produit['PRIXHT'], 2) ?> €</p>
                            <p class="<?= $produit['QTESTOCK'] > 0 ? 'en-stock' : 'rupture-stock' ?>">
                                <?= $produit['QTESTOCK'] > 0 ? 'En stock' : 'Rupture de stock' ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include("footer.php") ?>

</body>

</html>