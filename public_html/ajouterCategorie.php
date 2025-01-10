<?php
session_start();
include("connect.inc.php");

if (!isset($_SESSION["admin"])) {
    header("Location: connexion.php");
    exit();
}

// Récupérer les catégories mères
$queryMeres = $pdo->prepare("
    SELECT IDCATEG, NOMCATEG 
    FROM CATEGORIE 
    WHERE IDCATEG IN (11, 12, 13, 14)
    ORDER BY NOMCATEG
");
$queryMeres->execute();
$categoriesMeres = $queryMeres->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les catégories principales
$queryPrincipales = $pdo->prepare("
    SELECT DISTINCT C.IDCATEG, C.NOMCATEG
    FROM CATEGORIE C
    JOIN CATPERE CP ON C.IDCATEG = CP.IDCATEG
    WHERE CP.IDCATEG_PERE IN (11, 12, 13, 14)
    ORDER BY C.NOMCATEG
");
$queryPrincipales->execute();
$categoriesPrincipales = $queryPrincipales->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Catégorie - Administration</title>
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/dashboardAdmin.css">
    <link rel="stylesheet" href="Css/ajouterCategorie.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include("header.php"); ?>

    <main>
        <div class="dashboard-container">
            <?php 
                $currentPage = 'ajouter-categorie';
                include("includes/adminSidebar.php"); 
            ?>

            <div class="main-content">
                <h1>Ajouter une Catégorie</h1>

                <form id="add-category-form" class="admin-form">
                    <div class="form-section">
                        <h2>Informations générales</h2>
                        <div class="form-group">
                            <label for="nom">Nom de la catégorie *</label>
                            <input type="text" id="nom" name="nom" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="niveau">Niveau de la catégorie *</label>
                            <select id="niveau" name="niveau" required>
                                <option value="">Sélectionnez un niveau</option>
                                <option value="Principale">Principale</option>
                                <option value="Secondaire">Secondaire</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-section" id="section-categories-meres" style="display: none;">
                        <h2>Catégories Mères</h2>
                        <p class="section-description">Sélectionnez au moins une catégorie mère</p>
                        <div class="categories-grid">
                            <?php foreach ($categoriesMeres as $categorie): ?>
                                <div class="category-checkbox">
                                    <input type="checkbox" 
                                           id="mere-<?= $categorie['IDCATEG'] ?>" 
                                           name="categories_meres[]" 
                                           value="<?= $categorie['IDCATEG'] ?>">
                                    <label for="mere-<?= $categorie['IDCATEG'] ?>">
                                        <?= htmlspecialchars($categorie['NOMCATEG']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-section" id="section-categories-principales" style="display: none;">
                        <h2>Catégories Principales</h2>
                        <p class="section-description">Sélectionnez au moins une catégorie principale</p>
                        <div class="categories-grid">
                            <?php foreach ($categoriesPrincipales as $categorie): ?>
                                <div class="category-checkbox">
                                    <input type="checkbox" 
                                           id="principale-<?= $categorie['IDCATEG'] ?>" 
                                           name="categories_principales[]" 
                                           value="<?= $categorie['IDCATEG'] ?>">
                                    <label for="principale-<?= $categorie['IDCATEG'] ?>">
                                        <?= htmlspecialchars($categorie['NOMCATEG']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit">
                            Ajouter la catégorie </i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include("footer.php"); ?>

    <div class="toast-container"></div>

    <script src="js/ajouterCategorie.js"></script>
</body>
</html> 