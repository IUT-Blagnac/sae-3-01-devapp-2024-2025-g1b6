<?php
// Recherche.php - Partie de traitement des résultats
require_once 'rechercheAvancee.php'; // Inclure la fonction rechercheAvancee
require_once 'connect.inc.php'; // Connexion PDO

// Variables de pagination et de critères
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$produitsParPage = 10; // Nombre de produits par page
$offset = ($page - 1) * $produitsParPage;

$criteres = [
    'mot_cle' => $_GET['mot_cle'] ?? '',
    'categorie' => $_GET['categorie'] ?? NULL,
    'marque' => $_GET['marque'] ?? NULL,
    'prix_min' => $_GET['prix_min'] ?? NULL,
    'prix_max' => $_GET['prix_max'] ?? NULL,
    'en_stock' => isset($_GET['en_stock']) ? 1 : NULL,
    'tri' => $_GET['tri'] ?? 'nom_asc'
];

// Charger les catégories et marques
$stmt = $pdo->query("SELECT IDCATEG, NOMCATEG FROM CATEGORIE ORDER BY NOMCATEG ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT DISTINCT NOMMARQUE FROM MARQUE ORDER BY NOMMARQUE ASC");
$marques = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Appel à la fonction rechercheAvancee pour récupérer les produits
$resultats = rechercheAvancee($criteres, $pdo, $produitsParPage, $offset);

// Compter le total des produits pour la pagination (avant suppression des doublons)
$totalProduits = countProduits($criteres, $pdo);
$totalPages = ceil($totalProduits / $produitsParPage);
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
                <input type="number" step="1.00" name="prix_min" id="prix_min" value="<?= htmlspecialchars($criteres['prix_min']) ?>"> <br>

                <label for="prix_max">Prix maximum :</label>
                <input type="number" step="1.00" name="prix_max" id="prix_max" value="<?= htmlspecialchars($criteres['prix_max']) ?>"><br>

                <label for="en_stock">
                    En stock uniquement
                    <input type="checkbox" name="en_stock" id="en_stock" <?= $criteres['en_stock'] ? 'checked' : '' ?>>
                </label><br>

                <label for="tri">Trier par :</label>
                <select name="tri" id="tri">
                    <option value="nom_asc" <?= $criteres['tri'] == 'nom_asc' ? 'selected' : '' ?>>Nom croissant</option>
                    <option value="nom_desc" <?= $criteres['tri'] == 'nom_desc' ? 'selected' : '' ?>>Nom décroissant</option>
                    <option value="prix_asc" <?= $criteres['tri'] == 'prix_asc' ? 'selected' : '' ?>>Prix croissant</option>
                    <option value="prix_desc" <?= $criteres['tri'] == 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                </select>
                <br>
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <!-- Section des résultats -->
        <div class="recherche-resultats">
            <h3>Résultats</h3>
            <div class="produits-grid">
                <?php
                // Si des résultats existent
                if (empty($resultats)): ?>
                    <p>Aucun produit trouvé.</p>
                <?php else: ?>
                    <?php
                    // Séparer les packs des produits
                    $packs = array_filter($resultats, fn($resultat) => !empty($resultat['IDPACK']));
                    $produits = array_filter($resultats, fn($resultat) => empty($resultat['IDPACK']));

                    // Supprimer les doublons dans les packs (en utilisant l'IDPACK)
                    $packsUnique = [];
                    foreach ($packs as $pack) {
                        $packsUnique[$pack['IDPACK']] = $pack; // Utilisation de l'IDPACK comme clé pour garantir l'unicité
                    }
                    $packsUnique = array_values($packsUnique); // Réindexer le tableau après avoir supprimé les doublons

                    // Affichage des packs uniques
                    foreach ($packsUnique as $pack): ?>
                        <div class="produit-item">
                            <img src="./images/prod<?= htmlspecialchars($pack['IDPROD']) ?>.png" class="produit-image" width="100">
                            <h4><?= htmlspecialchars($pack['NOMPACK']) ?></h4>
                            <p><?= htmlspecialchars($pack['DESCPACK']) ?></p>
                            <p>Prix du pack : <?= number_format($pack['PRIXPACK'], 2) ?> €</p>
                            <a href="descPack.php?idPack=<?= htmlspecialchars($pack['IDPACK']) ?>" class="btn-details">Voir les détails</a>
                        </div>
                    <?php endforeach;

                    // Affichage de tous les produits, y compris ceux appartenant à un pack
                    foreach ($resultats as $produit): ?>
                        <div class="produit-item">
                            <img src="./images/prod<?= htmlspecialchars($produit['IDPROD']) ?>.png" class="produit-image" width="100">
                            <h4><?= htmlspecialchars($produit['NOMPROD']) ?></h4>
                            <p><?= htmlspecialchars($produit['DESCPROD']) ?></p>
                            <p>Prix : <?= number_format($produit['PRIXHT'], 2) ?> €</p>
                            <a href="descProduit.php?idProd=<?= htmlspecialchars($produit['IDPROD']) ?>" class="btn-details">Voir les détails</a>
                        </div>
                <?php endforeach;

                endif; ?>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($totalPages > 1): ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= htmlspecialchars("recherche.php?page=$i&" . http_build_query(array_filter([
    'mot_cle' => $criteres['mot_cle'] ?? NULL,
    'categorie' => !empty($criteres['categorie']) ? $criteres['categorie'] : NULL,
    'marque' => $criteres['marque'] ?? NULL,
    'prix_min' => $criteres['prix_min'] ?? NULL,
    'prix_max' => $criteres['prix_max'] ?? NULL,
    'en_stock' => $criteres['en_stock'] === 1 ? 1 : NULL,
    'tri' => $criteres['tri'] ?? 'nom_asc'
]))) ?>"
   class="<?= $i == $page ? 'active' : '' ?>">
    <?= $i ?>
</a>

            <?php endfor; ?>
        <?php else: ?>
            <p>Aucune autre page.</p>
        <?php endif; ?>
    </div>

    <?php include("footer.php") ?>
</body>

</html>