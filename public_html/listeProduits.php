<?php
session_start();
include("connect.inc.php");

// Vérification de la déconnexion
if (isset($_GET['disconnect']) && $_GET['disconnect'] === 'true') {
    session_destroy();
    header("Location: connexion.php");
    exit();
}

header("Cache-Control: no-cache, must-revalidate");

// Récupération des catégories mères (11, 12, 13, 14)
$queryCatMeres = $pdo->prepare("
    SELECT IDCATEG, NOMCATEG 
    FROM CATEGORIE 
    WHERE IDCATEG IN (11, 12, 13, 14)
    ORDER BY NOMCATEG
");
$queryCatMeres->execute();
$categoriesMeres = $queryCatMeres->fetchAll();

// Récupération des produits avec leurs informations
$query = $pdo->prepare("
    SELECT 
        P.IDPROD,
        P.NOMPROD,
        P.DESCPROD,
        P.PRIXHT,
        P.COULEUR,
        P.COMPOSITION,
        P.POIDSPRODUIT,
        P.QTESTOCK,
        P.DISPONIBLE,
        M.NOMMARQUE,
        GROUP_CONCAT(C.NOMCATEG SEPARATOR ', ') as CATEGORIES
    FROM PRODUIT P
    LEFT JOIN MARQUE M ON P.IDMARQUE = M.IDMARQUE
    LEFT JOIN APPARTENIRCATEG AC ON P.IDPROD = AC.IDPROD
    LEFT JOIN CATEGORIE C ON AC.IDCATEG = C.IDCATEG
    GROUP BY P.IDPROD
    ORDER BY P.IDPROD ASC
");
$query->execute();
$produits = $query->fetchAll();

// Récupération des marques pour le filtre
$queryMarques = $pdo->query("SELECT IDMARQUE, NOMMARQUE FROM MARQUE ORDER BY NOMMARQUE");
$marques = $queryMarques->fetchAll();

// Récupération des catégories pour le filtre
$queryCategories = $pdo->query("SELECT IDCATEG, NOMCATEG FROM CATEGORIE ORDER BY NOMCATEG");
$categories = $queryCategories->fetchAll();

// Récupération des catégories mères pour le filtre
$queryCategoriesMeres = $pdo->query("
    SELECT DISTINCT C.IDCATEG, C.NOMCATEG 
    FROM CATEGORIE C
    WHERE C.IDCATEG IN (11, 12, 13, 14)
    ORDER BY C.NOMCATEG
");
$categoriesMeres = $queryCategoriesMeres->fetchAll();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/dashboardAdmin.css">
    <link rel="stylesheet" href="Css/modals.css?v=1.1">
    <link rel="stylesheet" href="Css/toasts.css?v=1.0">
    <link rel="stylesheet" href="Css/listeProduits.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Liste des Produits - Administration</title>
</head>
<body>
    <?php include("header.php"); ?>

    <main>
        <div class="dashboard-container">
            <?php 
                $currentPage = 'liste-produits';
                include("includes/adminSidebar.php"); 
            ?>

            <!-- Contenu principal -->
            <div class="main-content">
                <h1>Liste des Produits</h1>

                <!-- Filtres -->
                <div class="filters-container">
                    <div class="search-container">
                        <input type="text" class="search-bar" placeholder="Rechercher un produit...">
                    </div>
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label>Marque</label>
                            <select id="filter-marque">
                                <option value="">Toutes les marques</option>
                                <?php foreach ($marques as $marque): ?>
                                    <option value="<?= htmlspecialchars($marque['NOMMARQUE']) ?>">
                                        <?= htmlspecialchars($marque['NOMMARQUE']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Catégorie</label>
                            <select id="filter-categorie">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $categorie): ?>
                                    <option value="<?= htmlspecialchars($categorie['NOMCATEG']) ?>">
                                        <?= htmlspecialchars($categorie['NOMCATEG']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Prix minimum (€)</label>
                            <input type="number" id="filter-prix-min" min="0" step="0.01">
                        </div>
                        <div class="filter-group">
                            <label>Prix maximum (€)</label>
                            <input type="number" id="filter-prix-max" min="0" step="0.01">
                        </div>
                        <div class="filter-group">
                            <label>Stock minimum</label>
                            <input type="number" id="filter-stock-min" min="0">
                        </div>
                        <div class="filter-group">
                            <label>Stock maximum</label>
                            <input type="number" id="filter-stock-max" min="0">
                        </div>
                        <div class="filter-group">
                            <label>Disponibilité</label>
                            <select id="filter-disponible">
                                <option value="">Tous</option>
                                <option value="1">Disponible</option>
                                <option value="0">Indisponible</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table des produits -->
                <table class="products-table">
                    <thead>
                        <tr>
                            <th data-sort="id">ID <i class="fas fa-sort sort-icon"></i></th>
                            <th data-sort="nom">Nom <i class="fas fa-sort sort-icon"></i></th>
                            <th data-sort="marque">Marque <i class="fas fa-sort sort-icon"></i></th>
                            <th data-sort="prix">Prix HT <i class="fas fa-sort sort-icon"></i></th>
                            <th data-sort="stock">Stock <i class="fas fa-sort sort-icon"></i></th>
                            <th data-sort="disponible">Disponible</th>
                            <th>Catégories</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $produit): ?>
                            <tr>
                                <td><?= $produit['IDPROD'] ?></td>
                                <td><?= htmlspecialchars($produit['NOMPROD']) ?></td>
                                <td><?= htmlspecialchars($produit['NOMMARQUE']) ?></td>
                                <td><?= number_format($produit['PRIXHT'], 2) ?> €</td>
                                <td><?= $produit['QTESTOCK'] ?></td>
                                <td>
                                    <span class="disponible-badge disponible-<?= $produit['DISPONIBLE'] ? 'true' : 'false' ?>">
                                        <?= $produit['DISPONIBLE'] ? 'Oui' : 'Non' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($produit['CATEGORIES']) ?></td>
                                <td class="actions">
                                    <button class="btn-edit" onclick="editProduct(<?= $produit['IDPROD'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-image" onclick="editImage(<?= $produit['IDPROD'] ?>)">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <button class="btn-delete" onclick="deleteProduct(<?= $produit['IDPROD'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="no-results" style="display: none;">
                    <i class="fas fa-search"></i>
                    <p>Aucun produit trouvé</p>
                </div>
            </div>
        </div>

        <button class="disconnect-btn">Se déconnecter</button>
    </main>

    <?php include("footer.php"); ?>

    <!-- Modal de modification -->
    <div id="modal-edit-product" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier le produit</h2>
            <form id="edit-product-form" method="post" action="updateProduct.php?action=edit">
                <input type="hidden" name="idProd">
                
                <!-- Informations principales -->
                <div class="form-section">
                    <h3>Informations principales</h3>
                    <div class="fields-container">
                        <div class="form-group">
                            <label for="nomProd">Nom du produit :</label>
                            <input type="text" id="nomProd" name="nom" maxlength="30" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="marqueProd">Marque :</label>
                            <select id="marqueProd" name="idMarque" required>
                                <?php foreach ($marques as $marque): ?>
                                    <option value="<?= $marque['IDMARQUE'] ?>"><?= htmlspecialchars($marque['NOMMARQUE']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label for="descProd">Description :</label>
                            <textarea id="descProd" name="description" maxlength="150" required></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Catégories -->
                <div class="form-section">
                    <h3>Catégories</h3>
                    <div class="fields-container">
                        <div class="form-group full-width">
                            <label class="required">Catégories mères :</label>
                            <div class="categories-meres">
                                <?php foreach ($categoriesMeres as $catMere): ?>
                                    <label class="checkbox-container">
                                        <input type="checkbox" name="categories_meres[]" 
                                               value="<?= $catMere['IDCATEG'] ?>">
                                        <?= htmlspecialchars($catMere['NOMCATEG']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="categoriePrincipale">Catégorie principale :</label>
                            <select id="categoriePrincipale" name="categorie_principale" required>
                                <option value="">Sélectionnez une catégorie</option>
                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="categoriesSecondaires">Catégories secondaires :</label>
                            <select id="categoriesSecondaires" name="categories_secondaires[]" multiple class="multiple-select">
                            </select>
                            <small class="form-text">Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs catégories</small>
                        </div>
                    </div>
                </div>
                
                <!-- Caractéristiques physiques -->
                <div class="form-section">
                    <h3>Caractéristiques physiques</h3>
                    <div class="fields-container">
                        <div class="form-group">
                            <label for="couleurProd">Couleur :</label>
                            <input type="text" id="couleurProd" name="couleur" maxlength="30">
                        </div>
                        
                        <div class="form-group">
                            <label for="poidsProd">Poids (en kg) :</label>
                            <input type="number" id="poidsProd" name="poids" step="0.01" min="0" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="compositionProd">Composition :</label>
                            <input type="text" id="compositionProd" name="composition" maxlength="150">
                        </div>
                    </div>
                </div>

                <!-- Informations commerciales -->
                <div class="form-section">
                    <h3>Informations commerciales</h3>
                    <div class="fields-container">
                        <div class="form-group">
                            <label for="prixProd">Prix HT (€) :</label>
                            <input type="number" id="prixProd" name="prix" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="stockProd">Stock :</label>
                            <input type="number" id="stockProd" name="stock" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="disponibleProd">Disponibilité :</label>
                            <select id="disponibleProd" name="disponible" required>
                                <option value="1">Disponible</option>
                                <option value="0">Indisponible</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" id="submitBtn-edit-product">Modifier</button>
            </form>
        </div>
    </div>

    <!-- Ajouter la nouvelle modal pour l'image -->
    <div id="modal-edit-image" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier l'image du produit</h2>
            <form id="edit-image-form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="idProd">
                
                <div class="image-comparison">
                    <div class="current-image">
                        <h3>Image actuelle</h3>
                        <div class="image-container"></div>
                    </div>
                    
                    <div class="new-image">
                        <h3>Nouvelle image</h3>
                        <input type="file" id="newImage" name="image" accept="image/*" required>
                        <div id="image-preview" class="image-preview"></div>
                        <small class="form-text">Formats acceptés : PNG, JPG, JPEG, GIF, WEBP (max 5MB)</small>
                    </div>
                </div>

                <button type="submit" id="submitBtn-edit-image">Mettre à jour l'image</button>
            </form>
        </div>
    </div>

    <div class="toast-container"></div>

    <script src="js/listeProduits.js"></script>
</body>
</html> 