<?php
session_start();
include("connect.inc.php");

if (!isset($_SESSION["admin"])) {
    header("Location: connexion.php");
    exit();
}

// Récupération de tous les produits disponibles
$queryProduits = $pdo->prepare("
    SELECT IDPROD, NOMPROD, PRIXHT 
    FROM PRODUIT 
    WHERE DISPONIBLE = 1 
    ORDER BY NOMPROD
");
$queryProduits->execute();
$produits = $queryProduits->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/dashboardAdmin.css">
    <link rel="stylesheet" href="Css/ajouterProduit.css">
    <link rel="stylesheet" href="Css/toasts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Ajouter un Pack - Administration</title>
</head>
<body>
    <?php include("header.php"); ?>

    <main>
        <div class="dashboard-container">
            <?php 
                $currentPage = 'ajouter-pack';
                include("includes/adminSidebar.php"); 
            ?>

            <div class="main-content">
                <h1>Ajouter un nouveau pack</h1>
                <div class="required-legend">Champs obligatoires</div>

                <form id="add-pack-form" method="post" action="./traitements/savePack.php">
                    <!-- Informations principales -->
                    <div class="form-section">
                        <h2>Informations du pack</h2>
                        
                        <div class="form-group">
                            <label for="nomPack" class="required">Nom du pack</label>
                            <input type="text" id="nomPack" name="nom" maxlength="30" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="descPack">Description</label>
                            <textarea id="descPack" name="description" maxlength="150"></textarea>
                        </div>
                    </div>

                    <!-- Sélection des produits -->
                    <div class="form-section">
                        <h2>Produits du pack</h2>
                        <div class="form-group">
                            <div class="search-container">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" id="searchProduits" placeholder="Rechercher un produit..." class="search-input">
                            </div>
                            
                            <label class="required">Sélectionner les produits :</label>
                            <div id="produitsList" class="products-list">
                                <?php foreach ($produits as $produit): ?>
                                <div class="product-item" data-price="<?= $produit['PRIXHT'] ?>">
                                    <input type="checkbox" 
                                           id="product-<?= $produit['IDPROD'] ?>" 
                                           name="produits[]" 
                                           value="<?= $produit['IDPROD'] ?>"
                                           onchange="updatePackPrice()">
                                    <label for="product-<?= $produit['IDPROD'] ?>">
                                        <?= htmlspecialchars($produit['NOMPROD']) ?>
                                    </label>
                                    <span class="product-price"><?= number_format($produit['PRIXHT'], 2) ?> €</span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="pack-price-info">
                                <div class="price-details">
                                    <div class="subtotal">
                                        <span>Prix total :</span>
                                        <span id="subtotalPrice">0.00 €</span>
                                    </div>
                                    <div class="discount">
                                        <span>Réduction (15%) :</span>
                                        <span id="discountAmount">-0.00 €</span>
                                    </div>
                                    <div class="final-price">
                                        <span>Prix final du pack :</span>
                                        <span id="finalPrice">En attente de 2 produits minimum</span>
                                    </div>
                                </div>
                                <small class="form-text" id="minProductsWarning">Sélectionnez au moins 2 produits pour créer un pack</small>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn">Ajouter le pack</button>
                </form>
            </div>
        </div>
    </main>

    <div class="toast-container"></div>
    
    <?php include("footer.php"); ?>

    <style>
        .search-container {
            position: relative;
            margin-bottom: 15px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .search-input {
            padding-left: 35px !important;
        }

        .products-list {
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid var(--light-gray);
            transition: all 0.2s ease;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-item:hover {
            background-color: var(--clair-purple);
        }

        .product-item input[type="checkbox"] {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .product-item label {
            flex-grow: 1;
            cursor: pointer;
            font-weight: 500;
            margin-right: 15px;
        }

        .product-price {
            color: var(--dark-purple);
            font-weight: 600;
            min-width: 80px;
            text-align: right;
        }

        .pack-price-info {
            background: var(--white);
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .price-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .price-details > div {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }

        .subtotal {
            color: var(--text-dark);
        }

        .discount {
            color: #28a745;
            font-size: 0.95em;
        }

        .final-price {
            border-top: 2px solid var(--light-gray);
            padding-top: 15px !important;
            margin-top: 5px;
            font-weight: 600;
            font-size: 1.1em;
            color: var(--dark-purple);
        }

        .pack-price-info small {
            display: block;
            margin-top: 10px;
            color: #dc3545;
            text-align: center;
        }

        #submitBtn:disabled {
            background-color: var(--light-gray);
            cursor: not-allowed;
        }

        .final-price.waiting {
            color: #dc3545;
            font-style: italic;
        }

        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchProduits');
        const productItems = document.querySelectorAll('.product-item');
        const submitBtn = document.getElementById('submitBtn');
        
        // Désactiver le bouton au chargement
        submitBtn.disabled = true;
        
        // Fonction de recherche en temps réel
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            productItems.forEach(item => {
                const label = item.querySelector('label').textContent.toLowerCase();
                item.style.display = label.includes(searchTerm) ? '' : 'none';
            });
        });

        // Validation du formulaire
        document.getElementById('add-pack-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const nom = document.getElementById('nomPack').value.trim();
            const produits = Array.from(document.querySelectorAll('input[name="produits[]"]:checked'));
            
            if (nom.length === 0 || nom.length > 30) {
                showToast('Le nom doit contenir entre 1 et 30 caractères', 'error');
                return;
            }
            
            if (produits.length < 2) {
                showToast('Veuillez sélectionner au moins deux produits', 'error');
                return;
            }

            try {
                const formData = new FormData(this);
                const response = await fetch('traitements/savePack.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Pack ajouté avec succès', 'success', 'Succès');
                    setTimeout(() => {
                        window.location.href = 'listePacks.php';
                    }, 1500);
                } else {
                    showToast(result.message || 'Erreur lors de l\'ajout du pack', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showToast('Erreur lors de la communication avec le serveur', 'error');
            }
        });

        // Ajouter l'écouteur d'événements pour les checkboxes
        document.querySelectorAll('input[name="produits[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updatePackPrice);
        });
    });

    function updatePackPrice() {
        const checkedProducts = document.querySelectorAll('input[name="produits[]"]:checked');
        const submitBtn = document.getElementById('submitBtn');
        const finalPriceElement = document.querySelector('.final-price');
        const warningElement = document.getElementById('minProductsWarning');
        let totalPrice = 0;

        // Calculer le prix total
        checkedProducts.forEach(checkbox => {
            const price = parseFloat(checkbox.closest('.product-item').dataset.price);
            if (!isNaN(price)) {
                totalPrice += price;
            }
        });

        // Mettre à jour l'affichage
        document.getElementById('subtotalPrice').textContent = `${totalPrice.toFixed(2)} €`;

        if (checkedProducts.length >= 2) {
            const discount = totalPrice * 0.15;
            const finalPrice = totalPrice - discount;
            
            document.getElementById('discountAmount').textContent = `-${discount.toFixed(2)} €`;
            document.getElementById('finalPrice').textContent = `${finalPrice.toFixed(2)} €`;
            finalPriceElement.classList.remove('waiting');
            warningElement.style.display = 'none';
            submitBtn.disabled = false;
        } else {
            document.getElementById('discountAmount').textContent = `-0.00 €`;
            document.getElementById('finalPrice').textContent = 'En attente de 2 produits minimum';
            finalPriceElement.classList.add('waiting');
            warningElement.style.display = 'block';
            submitBtn.disabled = true;
        }
    }

    function showToast(message, type = 'error', title = 'Erreur') {
        const toastContainer = document.querySelector('.toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="toast-icon">${type === 'success' ? '✓' : '✕'}</div>
            ${message}
        `;
        
        toast.addEventListener('click', () => toast.remove());
        toastContainer.appendChild(toast);
        // Le toast se fermera automatiquement grâce à l'animation CSS
    }
    </script>
</body>
</html> 