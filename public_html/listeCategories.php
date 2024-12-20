<?php
session_start();
include("connect.inc.php");

// Gestion des messages toast
if (isset($_GET['toast']) && isset($_GET['message'])) {
    $toastType = htmlspecialchars($_GET['toast']);
    $toastMessage = htmlspecialchars($_GET['message']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('$toastMessage', '$toastType', '$toastType' === 'success' ? 'Succès' : 'Erreur');
        });
    </script>";
}

// Récupération des catégories avec leur niveau hiérarchique et le bon comptage des produits
$query = $pdo->query("
    WITH RECURSIVE 
    CategoriesHierarchie AS (
        SELECT 
            C.IDCATEG,
            C.NOMCATEG,
            C.DESCCATEG,
            CASE 
                WHEN C.IDCATEG IN (11, 12, 13, 14) THEN 'Mère'
                WHEN EXISTS (
                    SELECT 1 
                    FROM CATPERE CP 
                    WHERE CP.IDCATEG = C.IDCATEG 
                    AND CP.IDCATEG_PERE IN (11, 12, 13, 14)
                ) THEN 'Principale'
                ELSE 'Secondaire'
            END as NIVEAU,
            COALESCE(
                (
                    SELECT GROUP_CONCAT(C2.NOMCATEG SEPARATOR ', ')
                    FROM CATPERE CP
                    JOIN CATEGORIE C2 ON CP.IDCATEG_PERE = C2.IDCATEG
                    WHERE CP.IDCATEG = C.IDCATEG
                ),
                '-'
            ) as CATEGORIES_PARENTES
        FROM CATEGORIE C
    ),
    ProduitsSecondaires AS (
        -- Compte des produits pour les catégories secondaires
        SELECT 
            CH.IDCATEG,
            COUNT(DISTINCT AC.IDPROD) as NB_PRODUITS
        FROM CategoriesHierarchie CH
        LEFT JOIN APPARTENIRCATEG AC ON CH.IDCATEG = AC.IDCATEG
        WHERE CH.NIVEAU = 'Secondaire'
        GROUP BY CH.IDCATEG
    ),
    ProduitsPrincipaux AS (
        -- Somme des produits uniques des catégories secondaires pour chaque catégorie principale
        SELECT 
            CP.IDCATEG_PERE as IDCATEG,
            COUNT(DISTINCT AC.IDPROD) as NB_PRODUITS
        FROM CATPERE CP
        JOIN CategoriesHierarchie CH ON CP.IDCATEG = CH.IDCATEG
        JOIN APPARTENIRCATEG AC ON CH.IDCATEG = AC.IDCATEG
        WHERE CH.NIVEAU = 'Secondaire'
        GROUP BY CP.IDCATEG_PERE
    ),
    ProduitsMeres AS (
        -- Somme des produits uniques des catégories principales pour chaque catégorie mère
        SELECT 
            CP.IDCATEG_PERE as IDCATEG,
            COUNT(DISTINCT AC.IDPROD) as NB_PRODUITS
        FROM CATPERE CP
        JOIN CategoriesHierarchie CH ON CP.IDCATEG = CH.IDCATEG
        JOIN APPARTENIRCATEG AC ON CH.IDCATEG = AC.IDCATEG
        WHERE CH.NIVEAU IN ('Principale', 'Secondaire')
        GROUP BY CP.IDCATEG_PERE
    )
    SELECT 
        CH.*,
        CASE CH.NIVEAU
            WHEN 'Secondaire' THEN COALESCE(PS.NB_PRODUITS, 0)
            WHEN 'Principale' THEN COALESCE(PP.NB_PRODUITS, 0)
            ELSE COALESCE(PM.NB_PRODUITS, 0)
        END as NB_PRODUITS
    FROM CategoriesHierarchie CH
    LEFT JOIN ProduitsSecondaires PS ON CH.IDCATEG = PS.IDCATEG AND CH.NIVEAU = 'Secondaire'
    LEFT JOIN ProduitsPrincipaux PP ON CH.IDCATEG = PP.IDCATEG AND CH.NIVEAU = 'Principale'
    LEFT JOIN ProduitsMeres PM ON CH.IDCATEG = PM.IDCATEG AND CH.NIVEAU = 'Mère'
    ORDER BY 
        CASE CH.NIVEAU
            WHEN 'Mère' THEN 1
            WHEN 'Principale' THEN 2
            ELSE 3
        END,
        CH.NOMCATEG
");

$categoriesList = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/dashboardAdmin.css">
    <link rel="stylesheet" href="Css/toasts.css?v=1.0">
    <link rel="stylesheet" href="Css/listeCategories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Liste des Catégories - Administration</title>
</head>
<body>
    <?php include("header.php"); ?>

    <main>
        <div class="dashboard-container">
            <?php 
                $currentPage = 'liste-categories';
                include("includes/adminSidebar.php"); 
            ?>

            <div class="main-content">
                <h1>Liste des Catégories</h1>
                <!-- Filtres -->
                <div class="filters-container">
                    <div class="search-container">
                        <input type="text" class="search-bar" placeholder="Rechercher une catégorie...">
                    </div>
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label>Niveau</label>
                            <select id="filter-niveau">
                                <option value="">Tous les niveaux</option>
                                <option value="Mère">Catégories mères</option>
                                <option value="Principale">Catégories principales</option>
                                <option value="Secondaire">Catégories secondaires</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Catégorie Mère</label>
                            <select id="filter-parent">
                                <option value="">Toutes les catégories parentes</option>
                                <?php foreach ($categoriesList as $categorie): 
                                    if (isset($categorie['NIVEAU']) && $categorie['NIVEAU'] === 'Mère'): ?>
                                    <option value="<?= htmlspecialchars($categorie['NOMCATEG']) ?>">
                                        <?= htmlspecialchars($categorie['NOMCATEG']) ?>
                                    </option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Catégorie principale</label>
                            <select id="filter-principale">
                                <option value="">Toutes les catégories principales</option>
                                <?php foreach ($categoriesList as $categorie): 
                                    if (isset($categorie['NIVEAU']) && $categorie['NIVEAU'] === 'Principale'): ?>
                                    <option value="<?= htmlspecialchars($categorie['NOMCATEG']) ?>">
                                        <?= htmlspecialchars($categorie['NOMCATEG']) ?>
                                    </option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Nombre minimum de produits</label>
                            <input type="number" id="filter-produits-min" min="0">
                        </div>
                        <div class="filter-group">
                            <label>Nombre maximum de produits</label>
                            <input type="number" id="filter-produits-max" min="0">
                        </div>
                    </div>
                </div>

                <!-- Table des catégories -->
                <table class="products-table categories-table">
                    <thead>
                        <tr>
                            <th data-sort="id">ID <i class="fas fa-sort sort-icon"></i></th>
                            <th data-sort="nom">Nom <i class="fas fa-sort sort-icon"></i></th>
                            <th data-sort="niveau">Niveau <i class="fas fa-sort sort-icon"></i></th>
                            <th>Catégories parentes</th>
                            <th data-sort="produits">Nombre de produits <i class="fas fa-sort sort-icon"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categoriesList as $categorie): ?>
                            <tr>
                                <td><?= $categorie['IDCATEG'] ?></td>
                                <td><?= htmlspecialchars($categorie['NOMCATEG']) ?></td>
                                <td>
                                    <span class="niveau-badge niveau-<?= strtolower(isset($categorie['NIVEAU']) ? $categorie['NIVEAU'] : 'non-defini') ?>">
                                        <?= isset($categorie['NIVEAU']) ? $categorie['NIVEAU'] : 'Non défini' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($categorie['CATEGORIES_PARENTES']) ?></td>
                                <td><?= $categorie['NB_PRODUITS'] ?></td>
                                <td class="actions">
                                    <button class="btn-edit" onclick="editCategory(<?= $categorie['IDCATEG'] ?>)" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-delete" onclick="deleteCategory(<?= $categorie['IDCATEG'] ?>)" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="no-results" style="display: none;">
                    <i class="fas fa-search"></i>
                    <p>Aucune catégorie trouvée</p>
                </div>
            </div>
        </div>
    </main>

    <?php include("footer.php"); ?>

    <div class="toast-container"></div>

    <script src="js/listeCategories.js?v=1.2"></script>

    <!-- Modal Modifier Catégorie -->
    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Modifier la catégorie</h2>
                <span class="close">&times;</span>
            </div>
            <form id="edit-category-form" class="admin-form">
                <input type="hidden" id="edit-id" name="id">
                
                <div class="form-section">
                    <h3>Informations générales</h3>
                    <div class="form-group">
                        <label for="edit-nom">Nom de la catégorie *</label>
                        <input type="text" id="edit-nom" name="nom" required>
                    </div>

                    <div class="form-group">
                        <label for="edit-description">Description</label>
                        <textarea id="edit-description" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit-niveau">Niveau de la catégorie</label>
                        <input type="text" id="edit-niveau" name="niveau" readonly>
                    </div>
                </div>

                <!-- Section pour les catégories Mères -->
                <div class="form-section" id="edit-section-categories-meres" style="display: none;">
                    <h3>Catégories Mères</h3>
                    <div class="categories-grid" id="edit-meres-grid">
                        <!-- Rempli dynamiquement -->
                    </div>
                </div>

                <!-- Section pour les catégories Principales -->
                <div class="form-section" id="edit-section-categories-principales" style="display: none;">
                    <h3>Catégories Principales</h3>
                    <div class="categories-grid" id="edit-principales-grid">
                        <!-- Rempli dynamiquement -->
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 