<?php
//recherche.php
require_once 'rechercheAvancee.php'; // Inclure la fonction rechercheAvancee
require_once 'connect.inc.php'; // Connexion PDO

// Récupérer la page actuelle (par défaut 1 si non définie)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$produitsParPage = 12; // Nombre de produits par page

// Calculer l'offset
$offset = ($page - 1) * $produitsParPage;

// Récupérer les termes de recherche depuis la barre de recherche
<<<<<<< HEAD
=======
// Récupérer les critères de recherche depuis la barre de recherche
>>>>>>> 58492d3d674473bca65841a379863d3695ffa395
$criteres = [
    'mot_cle' => $_GET['mot_cle'] ?? '',
    'categorie' => $_GET['categorie'] ?? NULL,
    'marque' => $_GET['marque'] ?? NULL,
    'prix_min' => $_GET['prix_min'] ?? NULL,
    'prix_max' => $_GET['prix_max'] ?? NULL,
    'en_stock' => isset($_GET['en_stock']) ? 1 : NULL,  // Si en_stock est défini, on le met à 1, sinon NULL
<<<<<<< HEAD
];


=======
    'tri' => $_GET['tri'] ?? 'nom_asc' // Tri par défaut si non fourni
];



>>>>>>> 58492d3d674473bca65841a379863d3695ffa395
// Charger les catégories dynamiquement
$stmt = $pdo->query("SELECT IDCATEG, NOMCATEG FROM CATEGORIE ORDER BY NOMCATEG ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Charger les marques dynamiquement
$stmt = $pdo->query("SELECT DISTINCT NOMMARQUE FROM MARQUE ORDER BY NOMMARQUE ASC");
$marques = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les produits via la fonction rechercheAvancee
$resultats = rechercheAvancee($criteres, $pdo, $produitsParPage, $offset);

// Compter le total des produits pour générer la pagination
$totalProduits = countProduits($criteres, $pdo);
$totalPages = ceil($totalProduits / $produitsParPage);
<<<<<<< HEAD
=======

>>>>>>> 58492d3d674473bca65841a379863d3695ffa395
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

<<<<<<< HEAD

=======
>>>>>>> 58492d3d674473bca65841a379863d3695ffa395
<body>
    <?php include("header.php") ?>


    <div class="recherche-main">
        <!-- Section des filtres -->
        <div class="recherche-filtres">
            <h3>Filtres</h3>
            <form action="recherche.php" method="get">
                <label for="mot_cle">Mot-clé :</label>
                <input type="text" name="mot_cle" id="mot_cle" value="<?= htmlspecialchars($criteres['mot_cle']) ?>"> <br>

<<<<<<< HEAD

=======
>>>>>>> 58492d3d674473bca65841a379863d3695ffa395
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
<<<<<<< HEAD
                <input type="number" step="0.01" name="prix_min" id="prix_min" value="<?= htmlspecialchars($criteres['prix_min']) ?>"> <br>
                <label for="prix_max">Prix maximum :</label>
                <input type="number" step="0.01" name="prix_max" id="prix_max" value="<?= htmlspecialchars($criteres['prix_max']) ?>"><br>
=======
                <input type="number" step="1.00" name="prix_min" id="prix_min" value="<?= htmlspecialchars($criteres['prix_min']) ?>"> <br>
                <label for="prix_max">Prix maximum :</label>
                <input type="number" step="1.00" name="prix_max" id="prix_max" value="<?= htmlspecialchars($criteres['prix_max']) ?>"><br>
>>>>>>> 58492d3d674473bca65841a379863d3695ffa395
                <label for="en_stock">
                    En stock uniquement
                    <input type="checkbox" name="en_stock" id="en_stock" <?= $criteres['en_stock'] ? 'checked' : '' ?>>
                </label><br>
<<<<<<< HEAD
                <button type="submit">Rechercher</button>
            </form>
        </div>
=======
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

       

>>>>>>> 58492d3d674473bca65841a379863d3695ffa395
        <!-- Section des résultats -->
        <div class="recherche-resultats">
            <h3>Résultats</h3>
            <div class="produits-grid">
                <?php if (empty($resultats)): ?>
                    <p>Aucun produit trouvé.</p>
                <?php else: ?>
                    <?php foreach ($resultats as $produit): ?>
                        <div class="produit-item">
                            <!-- Générer dynamiquement l'image en fonction de l'ID du produit -->
                            <img src="./images/prod<?= htmlspecialchars($produit['IDPROD']) ?>.png"
                                alt="Image de <?= htmlspecialchars($produit['NOMPROD']) ?>"
                                class="produit-image"
                                width="100">

                            <!-- Afficher les informations du produit -->
                            <h4><?= htmlspecialchars($produit['NOMPROD']) ?></h4>
                            <p><?= htmlspecialchars($produit['DESCPROD']) ?></p>
                            <p>Prix : <?= number_format($produit['PRIXHT'], 2) ?> €</p>
                            <p class="<?= $produit['QTESTOCK'] > 0 ? 'en-stock' : 'rupture-stock' ?>">
                                <?= $produit['QTESTOCK'] > 0 ? 'En stock' : 'Rupture de stock' ?>
                            </p>

                            <!-- Ajouter un bouton pour accéder à la description détaillée -->
                            <a href="descProduit.php?idProd=<?= htmlspecialchars($produit['IDPROD']) ?>" class="btn-details">Voir les détails</a>
                        </div>

<<<<<<< HEAD

=======
>>>>>>> 58492d3d674473bca65841a379863d3695ffa395
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="pagination">
        <?php if ($totalPages > 1): ?>
<<<<<<< HEAD


=======
>>>>>>> 58492d3d674473bca65841a379863d3695ffa395
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= htmlspecialchars("recherche.php?page=$i&" . http_build_query([
                                'mot_cle' => $criteres['mot_cle'] ?? NULL,
                                'categorie' => isset($criteres['categorie']) && $criteres['categorie'] !== '' ? $criteres['categorie'] : NULL,
                                'marque' => $criteres['marque'] ?? NULL,
                                'prix_min' => $criteres['prix_min'] ?? NULL,
                                'prix_max' => $criteres['prix_max'] ?? NULL,
<<<<<<< HEAD
                                'en_stock' => $criteres['en_stock'] !== NULL ? 1 : ''
=======
                                'en_stock' => $criteres['en_stock'] !== NULL ? 1 : '',
                                'tri' => $criteres['tri'] ?? 'nom_asc' // Tri par défaut si non fourni
>>>>>>> 58492d3d674473bca65841a379863d3695ffa395
                            ])) ?>"
                    class="<?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        <?php else: ?>
            <p>Aucune autre page.</p>
        <?php endif; ?>
    </div>
<<<<<<< HEAD



    <?php include("footer.php") ?>
</body>

</html>
=======
    <?php include("footer.php") ?>
</body>

</html>
>>>>>>> 58492d3d674473bca65841a379863d3695ffa395
