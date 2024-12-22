<?php
session_start();
include("connect.inc.php");

// Récupération des marques
$queryMarques = $pdo->query("SELECT IDMARQUE, NOMMARQUE FROM MARQUE ORDER BY NOMMARQUE");
$marques = $queryMarques->fetchAll();

// Récupération des catégories mères (11, 12, 13, 14)
$queryCatMeres = $pdo->prepare("
    SELECT IDCATEG, NOMCATEG 
    FROM CATEGORIE 
    WHERE IDCATEG IN (11, 12, 13, 14)
    ORDER BY NOMCATEG
");
$queryCatMeres->execute();
$categoriesMeres = $queryCatMeres->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/dashboardAdmin.css">
    <link rel="stylesheet" href="Css/ajouterProduit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Ajouter un Produit - Administration</title>
</head>
<body>
    <?php include("header.php"); ?>

    <main>
        <div class="dashboard-container">
            <?php 
                $currentPage = 'ajouter-produit';
                include("includes/adminSidebar.php"); 
            ?>

            <div class="main-content">
                <h1>Ajouter un nouveau produit</h1>
                <div class="required-legend">Champs obligatoires</div>

                <form id="add-product-form" method="post" action="./traitements/saveProduct.php" enctype="multipart/form-data">
                    <!-- Informations principales -->
                    <div class="form-section">
                        <h2>Informations principales</h2>
                        
                        <div class="form-group">
                            <label for="nomProd" class="required">Nom du produit</label>
                            <input type="text" id="nomProd" name="nom" maxlength="30" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="descProd">Description</label>
                            <textarea id="descProd" name="description" maxlength="150"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="marqueProd" class="required">Marque</label>
                            <select id="marqueProd" name="idMarque" required>
                                <option value="">Sélectionnez une marque</option>
                                <?php foreach ($marques as $marque): ?>
                                    <option value="<?= $marque['IDMARQUE'] ?>">
                                        <?= htmlspecialchars($marque['NOMMARQUE']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Catégories -->
                    <div class="form-section">
                        <h3>Catégories</h3>
                        <div class="fields-container">
                            <div class="form-group">
                                <label for="categoriePrincipale" class="required">Catégorie principale :</label>
                                <select id="categoriePrincipale" name="categorie_principale" required>
                                    <option value="">Sélectionnez une catégorie</option>
                                </select>
                                <div id="publicCible" class="public-cible-info">
                                    Public cible : <span>Non défini</span>
                                </div>
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="categoriesSecondaires">Catégories secondaires :</label>
                                <select id="categoriesSecondaires" name="categories_secondaires[]" multiple class="multiple-select">
                                </select>
                                <small class="form-text">Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs catégories</small>
                            </div>
                        </div>
                    </div>

                    <!-- Image du produit -->
                    <div class="form-section">
                        <h2>Image du produit</h2>
                        
                        <div class="form-group">
                            <label for="imageProd" class="required">Image</label>
                            <input type="file" id="imageProd" name="image" accept="image/*" required>
                            <small class="form-text">Formats acceptés : PNG, JPG, JPEG, GIF, WEBP (max 5MB)</small>
                            <div id="image-preview" class="image-preview"></div>
                        </div>
                    </div>

                    <!-- Caractéristiques physiques -->
                    <div class="form-section">
                        <h2>Caractéristiques physiques</h2>
                        
                        <div class="form-group">
                            <label for="couleurProd">Couleur :</label>
                            <input type="text" id="couleurProd" name="couleur" maxlength="30">
                        </div>
                        
                        <div class="form-group">
                            <label for="compositionProd">Composition :</label>
                            <input type="text" id="compositionProd" name="composition" maxlength="150">
                        </div>
                        
                        <div class="form-group">
                            <label for="poidsProd" class="required">Poids (en kg) :</label>
                            <input type="text" id="poidsProd" name="poids" required>
                        </div>
                    </div>

                    <!-- Informations commerciales -->
                    <div class="form-section">
                        <h2>Informations commerciales</h2>
                        
                        <div class="form-group">
                            <label for="prixProd" class="required">Prix HT (€) :</label>
                            <input type="text" id="prixProd" name="prix" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="stockProd" class="required">Stock initial :</label>
                            <input type="number" id="stockProd" name="stock" 
                                   min="0" required>
                        </div>
                                    
                        <div class="form-group">
                            <label for="disponibleProd" class="required">Disponibilité :</label>
                            <select id="disponibleProd" name="disponible" required>
                                <option value="1">Disponible</option>
                                <option value="0">Indisponible</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn">Ajouter le produit</button>
                </form>
            </div>
        </div>
    </main>

    <?php include("footer.php"); ?>

    <div class="toast-container"></div>

    <script src="js/ajouterProduit.js?v=1.2"></script>
</body>
</html> 