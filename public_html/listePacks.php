<?php
session_start();
include("connect.inc.php");

if (!isset($_SESSION["admin"])) {
    header("Location: connexion.php");
    exit();
}

// Vérification de la déconnexion
if (isset($_GET['disconnect']) && $_GET['disconnect'] === 'true') {
    session_destroy();
    header("Location: connexion.php");
    exit();
}

header("Cache-Control: no-cache, must-revalidate");

// Récupération des packs avec le nombre de produits associés et le prix total
$query = $pdo->prepare("
    SELECT 
        P.IDPACK,
        P.NOMPACK,
        P.DESCPACK,
        COUNT(AP.IDPROD) as NB_PRODUITS,
        ROUND(COALESCE(SUM(PR.PRIXHT), 0) * 0.85, 2) as PRIX_PACK
    FROM PACK P
    LEFT JOIN ASSOPACK AP ON P.IDPACK = AP.IDPACK
    LEFT JOIN PRODUIT PR ON AP.IDPROD = PR.IDPROD
    GROUP BY P.IDPACK, P.NOMPACK, P.DESCPACK
    ORDER BY P.IDPACK ASC
");
$query->execute();
$packs = $query->fetchAll();
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
    <title>Liste des Packs - Administration</title>
    <style>
        .search-input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid var(--light-gray);
            border-radius: 4px;
            font-size: 1rem;
        }
 
        .products-list {
            border: 1px solid var(--light-gray);
            border-radius: 4px;
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid var(--light-gray);
            transition: background-color 0.2s;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-item:hover {
            background-color: var(--clair-purple);
        }

        .product-item input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .product-item label {
            flex-grow: 1;
            cursor: pointer;
            user-select: none;
        }

        .tooltip {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .tooltip .info-icon {
            margin-left: 5px;
            color: var(--primary-color);
            cursor: help;
            font-size: 0.9em;
        }

        .tooltip .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px;
            position: fixed;
            z-index: 9999;
            margin-bottom: 10px;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.9em;
            font-weight: normal;
            pointer-events: none;
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        .tooltip .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <main>
        <div class="dashboard-container">
            <?php 
                $currentPage = 'liste-lots';
                include("includes/adminSidebar.php"); 
            ?>

            <!-- Contenu principal -->
            <div class="main-content">
                <h1>Liste des Packs</h1>

                <!-- Filtres -->
                <div class="filters-container">
                    <div class="search-container">
                        <input type="text" class="search-bar" placeholder="Rechercher un pack...">
                    </div>
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label>Prix minimum (€)</label>
                            <input type="number" id="filter-prix-min" min="0" step="0.01">
                        </div>
                        <div class="filter-group">
                            <label>Prix maximum (€)</label>
                            <input type="number" id="filter-prix-max" min="0" step="0.01">
                        </div>
                        <div class="filter-group">
                            <label>Nombre de produits minimum</label>
                            <input type="number" id="filter-produits-min" min="0">
                        </div>
                        <div class="filter-group">
                            <label>Nombre de produits maximum</label>
                            <input type="number" id="filter-produits-max" min="0">
                        </div>
                    </div>
                </div>

                <!-- Table des packs -->
                <table class="products-table">
                    <thead>
                        <tr>
                            <th data-sort="id">ID <i class="fas fa-sort sort-icon"></i></th>
                            <th data-sort="nom">Nom <i class="fas fa-sort sort-icon"></i></th>
                            <th data-sort="description">Description</th>
                            <th data-sort="nb_produits">Nombre de produits <i class="fas fa-sort sort-icon"></i></th>
                            <th data-sort="prix">
                                <div class="tooltip">
                                    Prix du pack
                                    <i class="fas fa-info-circle info-icon"></i>
                                    <span class="tooltip-text">Prix calculé comme la somme des prix de tous les produits du pack avec une réduction de 15%</span>
                                    <i class="fas fa-sort sort-icon"></i>
                                </div>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($packs as $pack): ?>
                            <tr>
                                <td><?= $pack['IDPACK'] ?></td>
                                <td><?= htmlspecialchars($pack['NOMPACK']) ?></td>
                                <td><?= htmlspecialchars($pack['DESCPACK']) ?></td>
                                <td><?= $pack['NB_PRODUITS'] ?></td>
                                <td><?= number_format($pack['PRIX_PACK'], 2) ?> €</td>
                                <td class="actions">
                                    <button class="btn-edit" onclick="editPack(<?= $pack['IDPACK'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-delete" onclick="deletePack(<?= $pack['IDPACK'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="no-results" style="display: none;">
                    <i class="fas fa-search"></i>
                    <p>Aucun pack trouvé</p>
                </div>
            </div>
        </div>

        <button class="disconnect-btn">Se déconnecter</button>
    </main>

    <?php include("footer.php"); ?>

    <!-- Modal de modification -->
    <div id="modal-edit-pack" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier le pack</h2>
            <form id="edit-pack-form" method="post">
                <input type="hidden" name="idPack">
                
                <!-- Informations principales -->
                <div class="form-section">
                    <h3>Informations du pack</h3>
                    <div class="fields-container">
                        <div class="form-group">
                            <label for="nomPack">Nom du pack :</label>
                            <input type="text" id="nomPack" name="nom" maxlength="30" required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="descPack">Description :</label>
                            <textarea id="descPack" name="description" maxlength="150"></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Produits du pack -->
                <div class="form-section">
                    <h3>Produits du pack</h3>
                    <div class="fields-container">
                        <div class="form-group full-width">
                            <label for="searchProduits">Rechercher des produits :</label>
                            <input type="text" id="searchProduits" placeholder="Rechercher un produit..." class="search-input">
                            
                            <label>Sélectionner les produits :</label>
                            <div id="produitsList" class="products-list">
                                <!-- Les produits seront chargés dynamiquement -->
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" id="submitBtn-edit-pack">Modifier</button>
            </form>
        </div>
    </div>

    <div class="toast-container"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Écouteurs pour la recherche et les filtres
        document.querySelector('.search-bar').addEventListener('input', filterPacks);
        document.getElementById('filter-prix-min').addEventListener('input', filterPacks);
        document.getElementById('filter-prix-max').addEventListener('input', filterPacks);
        document.getElementById('filter-produits-min').addEventListener('input', filterPacks);
        document.getElementById('filter-produits-max').addEventListener('input', filterPacks);

        // Appliquer les filtres au chargement initial
        filterPacks();

        // Ajoutez ce code pour positionner le tooltip
        const tooltips = document.querySelectorAll('.tooltip');
        
        tooltips.forEach(tooltip => {
            const tooltipText = tooltip.querySelector('.tooltip-text');
            
            tooltip.addEventListener('mouseenter', (e) => {
                const rect = tooltip.getBoundingClientRect();
                tooltipText.style.left = rect.left + (rect.width / 2) + 'px';
                tooltipText.style.top = (rect.top - tooltipText.offsetHeight - 10) + 'px';
            });
        });
    });

    function filterPacks() {
        const searchTerm = document.querySelector('.search-bar').value.toLowerCase();
        const prixMin = parseFloat(document.getElementById('filter-prix-min').value) || 0;
        const prixMax = parseFloat(document.getElementById('filter-prix-max').value) || Infinity;
        const produitsMin = parseInt(document.getElementById('filter-produits-min').value) || 0;
        const produitsMax = parseInt(document.getElementById('filter-produits-max').value) || Infinity;

        const rows = document.querySelectorAll('.products-table tbody tr');
        const table = document.querySelector('.products-table');
        const noResults = document.querySelector('.no-results');
        let visibleRows = 0;

        rows.forEach(row => {
            const nom = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const description = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const nbProduits = parseInt(row.querySelector('td:nth-child(4)').textContent);
            const prix = parseFloat(row.querySelector('td:nth-child(5)').textContent.replace('€', '').trim());

            const matchesSearch = nom.includes(searchTerm) || description.includes(searchTerm);
            const matchesPrix = prix >= prixMin && (prixMax === Infinity || prix <= prixMax);
            const matchesProduits = nbProduits >= produitsMin && (produitsMax === Infinity || nbProduits <= produitsMax);

            const isVisible = matchesSearch && matchesPrix && matchesProduits;
                
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleRows++;
        });

        // Afficher/masquer le tableau ou le message "Aucun résultat"
        table.style.display = visibleRows > 0 ? '' : 'none';
        noResults.style.display = visibleRows > 0 ? 'none' : 'block';
    }

    // Fonction pour trier les packs
    function sortTable(column) {
        const table = document.querySelector('.products-table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const headers = table.querySelectorAll('th[data-sort]');
        const currentHeader = table.querySelector(`th[data-sort="${column}"]`);
        const isAscending = currentHeader.classList.contains('asc');

        // Réinitialiser les classes de tri sur tous les en-têtes
        headers.forEach(header => {
            header.classList.remove('asc', 'desc');
        });

        // Mettre à jour la classe de tri sur l'en-tête actuel
        currentHeader.classList.toggle(isAscending ? 'desc' : 'asc');

        // Trier les lignes
        rows.sort((a, b) => {
            let aValue = a.querySelector(`td:nth-child(${getColumnIndex(column)})`).textContent;
            let bValue = b.querySelector(`td:nth-child(${getColumnIndex(column)})`).textContent;

            // Convertir en nombre si nécessaire
            if (column === 'prix' || column === 'nb_produits' || column === 'id') {
                aValue = parseFloat(aValue.replace('€', '').trim()) || 0;
                bValue = parseFloat(bValue.replace('€', '').trim()) || 0;
            }

            if (isAscending) {
                return aValue > bValue ? -1 : 1;
            } else {
                return aValue < bValue ? -1 : 1;
            }
        });

        // Réinsérer les lignes triées
        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));
    }

    // Fonction pour obtenir l'index de la colonne
    function getColumnIndex(columnName) {
        const headers = document.querySelectorAll('th[data-sort]');
        let index = 1;
        for (const header of headers) {
            if (header.dataset.sort === columnName) {
                return index;
            }
            index++;
        }
        return 1;
    }

    // Écouteurs d'événements pour le tri
    document.querySelectorAll('th[data-sort]').forEach(header => {
        header.addEventListener('click', () => {
            sortTable(header.dataset.sort);
        });
    });

    function showToast(message, type = 'error', title = 'Erreur') {
        const toastContainer = document.querySelector('.toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="toast-icon">${type === 'success' ? '✓' : '✕'}</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
        `;
        
        toast.addEventListener('click', () => toast.remove());
        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    function editPack(packId) {
        const modal = document.getElementById('modal-edit-pack');
        const form = document.getElementById('edit-pack-form');
        const searchInput = document.getElementById('searchProduits');
        
        // Récupérer les données du pack
        fetch(`traitements/getPack.php?id=${packId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showToast(data.message, 'error');
                    return;
                }

                // Remplir le formulaire
                form.querySelector('[name="idPack"]').value = data.pack.IDPACK;
                form.querySelector('[name="nom"]').value = data.pack.NOMPACK;
                form.querySelector('[name="description"]').value = data.pack.DESCPACK || '';

                // Remplir la liste des produits
                const selectProduits = form.querySelector('#produitsPack');
                const allProducts = data.produits_disponibles;
                const selectedProducts = data.pack.PRODUITS ? data.pack.PRODUITS.split(',') : [];

                // Fonction pour remplir la liste des produits avec filtre
                function fillProductsList(filter = '') {
                    const produitsList = document.getElementById('produitsList');
                    produitsList.innerHTML = '';
                    
                    // Filtrer les produits selon la recherche
                    const filteredProducts = allProducts.filter(produit => 
                        produit.NOMPROD.toLowerCase().includes(filter.toLowerCase()) ||
                        produit.IDPROD.toString().includes(filter)
                    );

                    // Trier les produits : sélectionnés en premier, puis par ordre alphabétique
                    const sortedProducts = filteredProducts.sort((a, b) => {
                        const isSelectedA = selectedProducts.includes(a.IDPROD.toString());
                        const isSelectedB = selectedProducts.includes(b.IDPROD.toString());
                        
                        if (isSelectedA && !isSelectedB) return -1;
                        if (!isSelectedA && isSelectedB) return 1;
                        
                        return a.NOMPROD.localeCompare(b.NOMPROD);
                    });

                    // Créer les éléments de la liste
                    sortedProducts.forEach(produit => {
                        const isSelected = selectedProducts.includes(produit.IDPROD.toString());
                        const div = document.createElement('div');
                        div.className = 'product-item';
                        
                        const id = `product-${produit.IDPROD}`;
                        div.innerHTML = `
                            <input type="checkbox" 
                                   id="${id}" 
                                   name="produits[]" 
                                   value="${produit.IDPROD}" 
                                   ${isSelected ? 'checked' : ''}>
                            <label for="${id}">
                                ${isSelected ? '✓ ' : ''}${produit.NOMPROD} (${produit.PRIXHT} €)
                            </label>
                        `;
                        
                        produitsList.appendChild(div);
                    });
                }

                // Remplir initialement la liste
                fillProductsList();

                // Ajouter l'écouteur pour la recherche en temps réel
                searchInput.value = ''; // Réinitialiser la recherche
                searchInput.addEventListener('input', (e) => {
                    fillProductsList(e.target.value);
                });

                modal.style.display = 'block';
            })
            .catch(error => {
                showToast('Erreur lors de la récupération des données', 'error');
            });
    }

    function deletePack(packId) {
        // Créer la modal de confirmation
        const modal = document.createElement('div');
        modal.className = 'confirm-modal';
        modal.innerHTML = `
            <div class="confirm-modal-content">
                <h3>Confirmer la suppression</h3>
                <p>Êtes-vous sûr de vouloir supprimer ce pack ? Cette action est irréversible.</p>
                <div class="confirm-buttons">
                    <button class="cancel-btn">Annuler</button>
                    <button class="confirm-btn">Supprimer</button>
                </div>
            </div>
        `;

        // Ajouter la modal au document
        document.body.appendChild(modal);
        modal.style.display = 'block';

        // Gérer les actions des boutons
        return new Promise((resolve) => {
            const confirmBtn = modal.querySelector('.confirm-btn');
            const cancelBtn = modal.querySelector('.cancel-btn');
            const modalContent = modal.querySelector('.confirm-modal-content');

            // Empêcher la propagation du clic
            modalContent.addEventListener('click', (e) => e.stopPropagation());

            // Fermer sur clic en dehors de la modal
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                    resolve(false);
                }
            });

            // Action du bouton Annuler
            cancelBtn.addEventListener('click', () => {
                modal.remove();
                resolve(false);
            });

            // Action du bouton Supprimer
            confirmBtn.addEventListener('click', async () => {
                try {
                    const response = await fetch('traitements/deletePack.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `idPack=${packId}`
                    });

                    const result = await response.json();
                    modal.remove();

                    if (result.success) {
                        showToast(result.message, 'success', 'Succès');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(result.message, 'error');
                    }
                } catch (error) {
                    modal.remove();
                    showToast('Erreur lors de la communication avec le serveur', 'error');
                }
                resolve(true);
            });
        });
    }

    // Ajouter l'écouteur pour le formulaire d'édition
    document.getElementById('edit-pack-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            const response = await fetch('traitements/updatePack.php?action=edit', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
             
            const result = await response.json();
            
            if (result.success) {
                showToast('Pack modifié avec succès', 'success', "Succès");
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(result.message || 'Erreur lors de la modification', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur lors de la communication avec le serveur: ' + error.message, 'error');
        }
    });

    // Gestionnaire pour la fermeture des modals
    document.querySelectorAll('.modal .close').forEach(closeBtn => {
        closeBtn.onclick = function() {
            this.closest('.modal').style.display = 'none';
        }
    });

    // Fermeture des modals en cliquant à l'extérieur
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
    </script>
</body>
</html> 