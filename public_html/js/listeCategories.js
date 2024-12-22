document.addEventListener('DOMContentLoaded', function() {
    // Fonction de filtrage
    function filterCategories() {
        const searchTerm = document.querySelector('.search-bar').value.toLowerCase();
        const niveauFilter = document.getElementById('filter-niveau').value;
        const parentFilter = document.getElementById('filter-parent').value;
        const principaleFilter = document.getElementById('filter-principale').value;
        const produitsMin = parseInt(document.getElementById('filter-produits-min').value) || 0;
        const produitsMax = parseInt(document.getElementById('filter-produits-max').value) || Infinity;

        const rows = document.querySelectorAll('.categories-table tbody tr');
        const table = document.querySelector('.categories-table');
        const noResults = document.querySelector('.no-results');
        let visibleRows = 0;

        // Trouver d'abord les catégories principales directement liées à la catégorie mère
        let categoriesPrincipales = new Set();
        if (parentFilter !== '') {
            rows.forEach(row => {
                const niveau = row.querySelector('td:nth-child(3)').textContent.trim();
                const parentsText = row.querySelector('td:nth-child(4)').textContent;
                const nom = row.querySelector('td:nth-child(2)').textContent;
                
                const parentsArray = parentsText.split(',').map(p => p.trim());
                
                if (niveau === 'Principale' && parentsArray.includes(parentFilter)) {
                    categoriesPrincipales.add(nom);
                }
            });
        }

        // Filtrage des lignes
        rows.forEach(row => {
            const nom = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const niveau = row.querySelector('td:nth-child(3)').textContent.trim();
            const parentsText = row.querySelector('td:nth-child(4)').textContent;
            const parentsArray = parentsText.split(',').map(p => p.trim());
            const nbProduits = parseInt(row.querySelector('td:nth-child(5)').textContent);

            const matchesSearch = nom.includes(searchTerm);
            const matchesNiveau = niveauFilter === '' || niveau === niveauFilter;
            const matchesParent = parentFilter === '' || 
                                (niveau === 'Principale' && parentsArray.includes(parentFilter)) ||
                                (niveau === 'Secondaire' && Array.from(categoriesPrincipales).some(cat => parentsArray.includes(cat)));
            const matchesPrincipale = principaleFilter === '' || parentsArray.includes(principaleFilter);
            const matchesProduits = nbProduits >= produitsMin && 
                                  (produitsMax === Infinity || nbProduits <= produitsMax);

            const isVisible = matchesSearch && matchesNiveau && 
                            matchesParent && matchesPrincipale && matchesProduits;
            
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleRows++;
        });

        table.style.display = visibleRows > 0 ? '' : 'none';
        noResults.style.display = visibleRows > 0 ? 'none' : 'block';
    }

    // Fonction de tri
    function sortTable(column) {
        const table = document.querySelector('.categories-table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const headers = table.querySelectorAll('th[data-sort]');
        const currentHeader = table.querySelector(`th[data-sort="${column}"]`);
        const isAscending = currentHeader.classList.contains('asc');

        headers.forEach(header => {
            header.classList.remove('asc', 'desc');
        });

        currentHeader.classList.toggle(isAscending ? 'desc' : 'asc');

        rows.sort((a, b) => {
            let aValue, bValue;

            switch(column) {
                case 'produits':
                    aValue = parseInt(a.querySelector('td:nth-child(5)').textContent);
                    bValue = parseInt(b.querySelector('td:nth-child(5)').textContent);
                    break;
                case 'id':
                    aValue = parseInt(a.querySelector('td:nth-child(1)').textContent);
                    bValue = parseInt(b.querySelector('td:nth-child(1)').textContent);
                    break;
                default:
                    aValue = a.querySelector(`td:nth-child(${getColumnIndex(column)})`).textContent;
                    bValue = b.querySelector(`td:nth-child(${getColumnIndex(column)})`).textContent;
            }

            if (column === 'produits' || column === 'id') {
                return isAscending ? bValue - aValue : aValue - bValue;
            } else {
                return isAscending ? 
                    bValue.localeCompare(aValue) : 
                    aValue.localeCompare(bValue);
            }
        });

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

    // Écouteurs d'événements
    document.querySelector('.search-bar').addEventListener('input', filterCategories);
    document.getElementById('filter-niveau').addEventListener('change', filterCategories);
    document.getElementById('filter-parent').addEventListener('change', filterCategories);
    document.getElementById('filter-principale').addEventListener('change', filterCategories);
    document.getElementById('filter-produits-min').addEventListener('input', filterCategories);
    document.getElementById('filter-produits-max').addEventListener('input', filterCategories);

    document.querySelectorAll('th[data-sort]').forEach(header => {
        header.addEventListener('click', () => {
            sortTable(header.dataset.sort);
        });
    });

    // Appliquer les filtres au chargement initial
    filterCategories();

    // Gérer la soumission du formulaire d'édition
    document.getElementById('edit-category-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            
            // Vérifier si au moins une catégorie est sélectionnée selon le niveau
            const niveau = document.getElementById('edit-niveau').value;
            if (niveau === 'Principale') {
                const selectedMeres = formData.getAll('categories_meres[]');
                if (selectedMeres.length === 0) {
                    return false;
                }
            } else if (niveau === 'Secondaire') {
                const selectedPrincipales = formData.getAll('categories_principales[]');
                if (selectedPrincipales.length === 0) {
                    return false;
                }
            }

            const response = await fetch('traitements/modifierCategorie.php', {
                method: 'POST',
                body: formData
            });

            // Vérifier si la réponse est bien du JSON
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("La réponse n'est pas au format JSON");
            }

            const data = await response.json();

            if (data.success) {
                window.location.href = 'listeCategories.php?toast=success&message=La catégorie a été modifiée avec succès';
            } else {
                showToast(data.message || 'Une erreur est survenue lors de la modification', 'error');
            }
        } catch (error) {
            console.error('Erreur complète:', error);
            showToast('Erreur lors de la communication avec le serveur', 'error');
        }
    });

    // Gérer les changements de checkbox en dehors du submit
    document.addEventListener('change', function(e) {
        if (e.target.matches('.category-checkbox input[type="checkbox"]')) {
            const checkboxType = e.target.name;
            const selectedBoxes = document.querySelectorAll(`input[name="${checkboxType}"]:checked`).length;
            
            if (selectedBoxes === 0) {
                e.target.checked = true;
            }
        }
    });

    // Gérer la fermeture du modal
    document.querySelectorAll('.close, .close-modal').forEach(element => {
        element.addEventListener('click', () => {
            const modal = document.getElementById('editCategoryModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        });
    });

    // Modifier la fermeture en cliquant en dehors
    window.addEventListener('click', (e) => {
        const modal = document.getElementById('editCategoryModal');
        if (e.target === modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    });
});

// Fonction pour supprimer une catégorie
async function deleteCategory(categoryId) {
    // Créer et ajouter la modal de confirmation
    const modal = document.createElement('div');
    modal.className = 'confirm-modal';
    modal.innerHTML = `
        <div class="confirm-modal-content">
            <h2>Confirmer la suppression</h2>
            <p>Êtes-vous sûr de vouloir supprimer cette catégorie ?</p>
            <div class="confirm-modal-buttons">
                <button class="confirm-btn">Supprimer</button>
                <button class="cancel-btn">Annuler</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    return new Promise((resolve) => {
        const confirmBtn = modal.querySelector('.confirm-btn');
        const cancelBtn = modal.querySelector('.cancel-btn');
        const modalContent = modal.querySelector('.confirm-modal-content');

        // Empêcher la propagation du clic
        modalContent.addEventListener('click', (e) => e.stopPropagation());

        // Fermer sur clic en dehors de la modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
                modal.remove();
                resolve(false);
            }
        });

        // Action du bouton Annuler
        cancelBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            modal.remove();
            resolve(false);
        });

        // Action du bouton Supprimer
        confirmBtn.addEventListener('click', async () => {
            try {
                const response = await fetch('traitements/deleteCategory.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `idCateg=${categoryId}`
                });

                const result = await response.json();
                modal.style.display = 'none';
                modal.remove();

                if (result.success) {
                    showToast(result.message, 'success', 'Succès');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                modal.style.display = 'none';
                modal.remove();
                showToast('Erreur lors de la communication avec le serveur', 'error');
            }
            resolve(true);
        });

        // Afficher la modal
        modal.style.display = 'flex';
    });
}

// Fonction pour afficher les toasts
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

// Fonction pour éditer une catégorie
function editCategory(id) {
    const modal = document.getElementById('editCategoryModal');
    const form = document.getElementById('edit-category-form');
    const sectionMeres = document.getElementById('edit-section-categories-meres');
    const sectionPrincipales = document.getElementById('edit-section-categories-principales');

    try {
        fetch(`traitements/getCategorieDetails.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const categorie = data.categorie;
                    
                    // Remplir le formulaire
                    document.getElementById('edit-id').value = categorie.IDCATEG;
                    document.getElementById('edit-nom').value = categorie.NOMCATEG;
                    document.getElementById('edit-description').value = categorie.DESCCATEG || '';
                    document.getElementById('edit-niveau').value = categorie.NIVEAU;

                    // Gérer l'affichage des sections selon le niveau
                    sectionMeres.style.display = 'none';
                    sectionPrincipales.style.display = 'none';

                    if (categorie.NIVEAU === 'Principale') {
                        sectionMeres.style.display = 'block';
                        const meresGrid = document.getElementById('edit-meres-grid');
                        meresGrid.innerHTML = data.categoriesMeres.map(mere => `
                            <div class="category-checkbox">
                                <input type="checkbox" 
                                       id="edit-mere-${mere.IDCATEG}" 
                                       name="categories_meres[]" 
                                       value="${mere.IDCATEG}"
                                       ${categorie.parents.includes(parseInt(mere.IDCATEG)) ? 'checked' : ''}>
                                <label for="edit-mere-${mere.IDCATEG}">
                                    ${mere.NOMCATEG}
                                </label>
                            </div>
                        `).join('');
                    } else if (categorie.NIVEAU === 'Secondaire') {
                        sectionPrincipales.style.display = 'block';
                        const principalesGrid = document.getElementById('edit-principales-grid');
                        principalesGrid.innerHTML = data.categoriesPrincipales.map(principale => `
                            <div class="category-checkbox">
                                <input type="checkbox" 
                                       id="edit-principale-${principale.IDCATEG}" 
                                       name="categories_principales[]" 
                                       value="${principale.IDCATEG}"
                                       ${categorie.parents.includes(parseInt(principale.IDCATEG)) ? 'checked' : ''}>
                                <label for="edit-principale-${principale.IDCATEG}">
                                    ${principale.NOMCATEG}
                                </label>
                            </div>
                        `).join('');
                    }

                    // Afficher le modal avec animation
                    modal.style.display = 'block';
                    // Force un reflow pour permettre l'animation
                    modal.offsetHeight;
                    modal.classList.add('show');
                } else {
                    showToast(data.message || 'Erreur lors de la récupération des données', 'error');
                }
            })
            .catch(error => {
                showToast('Erreur lors de la communication avec le serveur', 'error');
                console.error('Erreur:', error);
            });
    } catch (error) {
        showToast('Erreur lors de la communication avec le serveur', 'error');
        console.error('Erreur:', error);
    }
} 