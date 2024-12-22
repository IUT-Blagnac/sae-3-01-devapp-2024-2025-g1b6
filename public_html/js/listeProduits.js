document.addEventListener('DOMContentLoaded', function() {
    // Vérifier s'il y a un message dans l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const toastMessage = urlParams.get('message');
    const toastType = urlParams.get('toast');
    
    if (toastMessage && toastType) {
        showToast(decodeURIComponent(toastMessage), toastType, toastType === 'success' ? 'Succès' : 'Erreur');
    }

    const modalEdit = document.getElementById('modal-edit-product');
    const form = document.getElementById('edit-product-form');
    
    // Modifier l'écouteur d'événements pour la catégorie principale
    document.getElementById('categoriePrincipale').addEventListener('change', async function() {
        const publicCibleSpan = document.querySelector('#publicCible span');
        
        if (this.value) {
            try {
                // Récupérer les catégories parentes
                const response = await fetch(`getCategories.php?get_parents=${this.value}`);
                const data = await response.json();
                
                if (data.parent_categories && data.parent_categories.length > 0) {
                    // Mapper les IDs aux noms des publics cibles
                    const publicsCibles = data.parent_categories
                        .filter(cat => ['11', '12', '13', '14'].includes(cat.IDCATEG.toString()))
                        .map(cat => {
                            switch(cat.IDCATEG.toString()) {
                                case '11': return 'Enfants';
                                case '12': return 'Adolescents';
                                case '13': return 'Adultes';
                                case '14': return 'Famille';
                                default: return '';
                            }
                        })
                        .filter(name => name !== '');

                    publicCibleSpan.textContent = publicsCibles.length > 0 ? publicsCibles.join(', ') : 'Non défini';
                } else {
                    publicCibleSpan.textContent = 'Non défini';
                }
                
                await loadSecondaryCategories(this.value);
            } catch (error) {
                console.error('Erreur:', error);
                publicCibleSpan.textContent = 'Non défini';
            }
        } else {
            publicCibleSpan.textContent = 'Non défini';
            document.getElementById('categoriesSecondaires').innerHTML = '';
        }
    });
    
    // Gestion des boutons d'édition
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const productId = this.dataset.id;
            try {
                const response = await fetch(`traitements/getProduit.php?id=${productId}`);
                const product = await response.json();
                
                if (product.error) {
                    showToast(product.error, 'error');
                    return;
                }
                
                // Remplir le formulaire
                fillEditForm(product);
                modalEdit.style.display = 'block';
                
            } catch (error) {
                showToast('Erreur lors de la récupération des données', 'error');
            }
        });
    });
    
    // Fermeture de la modale
    document.querySelectorAll('.close').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });
    
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
    
    // Validation du formulaire
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validateProductForm()) {
            return;
        }
        
        try {
            const formData = new FormData(this);
            const response = await fetch('traitements/updateProduct.php?action=edit', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
             
            const result = await response.json();
            
            if (result.success) {
                showToast('Produit modifié avec succès', 'success', "Succès");
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(result.message || 'Erreur lors de la modification', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur lors de la communication avec le serveur: ' + error.message, 'error');
        }
    });
    
    // Validation en temps réel des champs
    // Validation du nom
    form.querySelector('[name="nom"]').addEventListener('input', function() {
        if (this.value.length > 30) {
            this.value = this.value.slice(0, 30);
            showToast('Le nom est limité à 30 caractères', 'error');
        }
    });

    // Validation de la description
    form.querySelector('[name="description"]').addEventListener('input', function() {
        if (this.value.length > 150) {
            this.value = this.value.slice(0, 150);
            showToast('La description est limitée à 150 caractères', 'error');
        }
    });

    // Validation des champs numériques
    ['prix', 'poids', 'stock'].forEach(field => {
        form.querySelector(`[name="${field}"]`).addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value < 0) {
                this.value = 0;
                showToast(`La valeur ne peut pas être négative`, 'error');
            }
        });
    });

    // Validation de la couleur
    form.querySelector('[name="couleur"]').addEventListener('input', function() {
        if (this.value.length > 30) {
            this.value = this.value.slice(0, 30);
            showToast('La couleur est limitée à 30 caractères', 'error');
        }
    });

    // Validation de la composition
    form.querySelector('[name="composition"]').addEventListener('input', function() {
        if (this.value.length > 150) {
            this.value = this.value.slice(0, 150);
            showToast('La composition est limitée à 150 caractères', 'error');
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
});

async function loadCategories() {
    try {
        const response = await fetch('getCategories.php');
        const data = await response.json();
        
        const selectPrincipal = document.getElementById('categoriePrincipale');
        selectPrincipal.innerHTML = '<option value="">Sélectionnez une catégorie</option>';
        
        data.main_categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.IDCATEG;
            option.textContent = category.NOMCATEG;
            selectPrincipal.appendChild(option);
        });
    } catch (error) {
        showToast('Erreur lors du chargement des catégories', 'error');
    }
}

async function loadSecondaryCategories(mainCategoryId) {
    try {
        const response = await fetch(`getCategories.php?main_category=${mainCategoryId}`);
        const data = await response.json();
        
        const selectSecondaire = document.getElementById('categoriesSecondaires');
        selectSecondaire.innerHTML = ''; // Réinitialiser les options
        
        data.secondary_categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.IDCATEG;
            option.textContent = category.NOMCATEG;
            selectSecondaire.appendChild(option);
        });
    } catch (error) {
        showToast('Erreur lors du chargement des sous-catégories', 'error');
    }
}

async function fillEditForm(product) {
    const form = document.getElementById('edit-product-form');
    
    // Remplir les champs de base
    form.querySelector('[name="idProd"]').value = product.IDPROD;
    form.querySelector('[name="nom"]').value = product.NOMPROD;
    form.querySelector('[name="description"]').value = product.DESCPROD;
    form.querySelector('[name="prix"]').value = product.PRIXHT;
    form.querySelector('[name="couleur"]').value = product.COULEUR || '';
    form.querySelector('[name="composition"]').value = product.COMPOSITION || '';
    form.querySelector('[name="poids"]').value = product.POIDSPRODUIT;
    form.querySelector('[name="stock"]').value = product.QTESTOCK;
    form.querySelector('[name="disponible"]').value = product.DISPONIBLE;
    form.querySelector('[name="idMarque"]').value = product.IDMARQUE;
    
    // Gérer les catégories
    await loadCategories();
    const selectPrincipal = document.getElementById('categoriePrincipale');
    const publicCibleSpan = document.querySelector('#publicCible span');
    
    if (product.MAIN_CATEGORY && product.MAIN_CATEGORY.IDCATEG) {
        selectPrincipal.value = product.MAIN_CATEGORY.IDCATEG;
        
        try {
            // Récupérer les catégories parentes
            const response = await fetch(`getCategories.php?get_parents=${product.MAIN_CATEGORY.IDCATEG}`);
            const data = await response.json();
            
            if (data.parent_categories && data.parent_categories.length > 0) {
                const publicsCibles = data.parent_categories
                    .filter(cat => ['11', '12', '13', '14'].includes(cat.IDCATEG.toString()))
                    .map(cat => {
                        switch(cat.IDCATEG.toString()) {
                            case '11': return 'Enfants';
                            case '12': return 'Adolescents';
                            case '13': return 'Adultes';
                            case '14': return 'Famille';
                            default: return '';
                        }
                    })
                    .filter(name => name !== '');

                publicCibleSpan.textContent = publicsCibles.length > 0 ? publicsCibles.join(', ') : 'Non défini';
            } else {
                publicCibleSpan.textContent = 'Non défini';
            }
        } catch (error) {
            console.error('Erreur:', error);
            publicCibleSpan.textContent = 'Non défini';
        }
        
        await loadSecondaryCategories(product.MAIN_CATEGORY.IDCATEG);
        
        const selectSecondaire = document.getElementById('categoriesSecondaires');
        if (product.SECONDARY_CATEGORIES && Array.isArray(product.SECONDARY_CATEGORIES)) {
            product.SECONDARY_CATEGORIES.forEach(cat => {
                Array.from(selectSecondaire.options).forEach(option => {
                    if (option.value === cat.IDCATEG.toString()) {
                        option.selected = true;
                    }
                });
            });
        }
    } else {
        publicCibleSpan.textContent = 'Non défini';
    }
}

function validateProductForm() {
    const form = document.getElementById('edit-product-form');
    const nom = form.querySelector('[name="nom"]').value.trim();
    const description = form.querySelector('[name="description"]').value.trim();
    const prix = parseFloat(form.querySelector('[name="prix"]').value);
    const poids = parseFloat(form.querySelector('[name="poids"]').value);
    const stock = parseInt(form.querySelector('[name="stock"]').value);
    
    if (nom.length === 0 || nom.length > 30) {
        showToast('Le nom doit contenir entre 1 et 30 caractères', 'error');
        return false;
    }
    
    if (description.length > 150) {
        showToast('La description ne peut pas dépasser 150 caractères', 'error');
        return false;
    }
    
    if (prix <= 0) {
        showToast('Le prix doit être supérieur à 0', 'error');
        return false;
    }
    
    if (poids <= 0) {
        showToast('Le poids doit être supérieur à 0', 'error');
        return false;
    }
    
    if (stock < 0) {
        showToast('Le stock ne peut pas être négatif', 'error');
        return false;
    }
    
    return true;
}

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

// Fonction pour trier les produits
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
        if (column === 'prix' || column === 'stock' || column === 'id') {
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

// Fonction pour filtrer les produits
function filterProducts() {
    const searchTerm = document.querySelector('.search-bar').value.toLowerCase();
    const marqueFilter = document.getElementById('filter-marque').value.toLowerCase();
    const categorieFilter = document.getElementById('filter-categorie').value.toLowerCase();
    const prixMin = parseFloat(document.getElementById('filter-prix-min').value) || 0;
    const prixMax = parseFloat(document.getElementById('filter-prix-max').value) || Infinity;
    const stockMin = parseInt(document.getElementById('filter-stock-min').value) || 0;
    const stockMax = parseInt(document.getElementById('filter-stock-max').value) || Infinity;
    const disponibleFilter = document.getElementById('filter-disponible').value;

    const rows = document.querySelectorAll('.products-table tbody tr');
    const table = document.querySelector('.products-table');
    const noResults = document.querySelector('.no-results');
    let visibleRows = 0;

    rows.forEach(row => {
        const nom = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const marque = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const prix = parseFloat(row.querySelector('td:nth-child(4)').textContent.replace('€', '').trim());
        const stock = parseInt(row.querySelector('td:nth-child(5)').textContent);
        const disponible = row.querySelector('td:nth-child(6) .disponible-badge').textContent.toLowerCase();
        const categories = row.querySelector('td:nth-child(7)').textContent.toLowerCase();

        const matchesSearch = nom.includes(searchTerm) || marque.includes(searchTerm);
        const matchesMarque = marqueFilter === '' || marque === marqueFilter;
        const matchesCategorie = categorieFilter === '' || categories.includes(categorieFilter);
        const matchesPrix = prix >= prixMin && (prixMax === Infinity || prix <= prixMax);
        const matchesStock = stock >= stockMin && (stockMax === Infinity || stock <= stockMax);
        const matchesDisponible = disponibleFilter === '' || 
            (disponibleFilter === '1' && disponible.includes('oui')) ||
            (disponibleFilter === '0' && disponible.includes('non'));

        const isVisible = matchesSearch && matchesMarque && matchesCategorie && 
            matchesPrix && matchesStock && matchesDisponible;
            
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleRows++;
    });

    // Afficher/masquer le tableau ou le message "Aucun résultat"
    table.style.display = visibleRows > 0 ? '' : 'none';
    noResults.style.display = visibleRows > 0 ? 'none' : 'block';
}

// Écouteurs d'événements pour le tri
document.querySelectorAll('th[data-sort]').forEach(header => {
    header.addEventListener('click', () => {
        sortTable(header.dataset.sort);
    });
});

// Écouteurs d'événements pour les filtres
document.addEventListener('DOMContentLoaded', function() {
    // couteurs pour la recherche et les filtres
    document.querySelector('.search-bar').addEventListener('input', filterProducts);
    document.getElementById('filter-marque').addEventListener('change', filterProducts);
    document.getElementById('filter-categorie').addEventListener('change', filterProducts);
    document.getElementById('filter-prix-min').addEventListener('input', filterProducts);
    document.getElementById('filter-prix-max').addEventListener('input', filterProducts);
    document.getElementById('filter-stock-min').addEventListener('input', filterProducts);
    document.getElementById('filter-stock-max').addEventListener('input', filterProducts);
    document.getElementById('filter-disponible').addEventListener('change', filterProducts);

    // Appliquer les filtres au chargement initial
    filterProducts();
});

document.getElementById('categoriePrincipale').addEventListener('change', function() {
    const publicCibleSpan = document.querySelector('#publicCible span');
    publicCibleSpan.textContent = determinerPublicCible(this.value);
    
    if (this.value) {
        loadSecondaryCategories(this.value);
    } else {
        document.getElementById('categoriesSecondaires').innerHTML = '';
        publicCibleSpan.textContent = 'Non défini';
    }
});

function editImage(productId) {
    const modal = document.getElementById('modal-edit-image');
    const form = document.getElementById('edit-image-form');
    const currentImage = modal.querySelector('.image-container');
    const imagePreview = document.getElementById('image-preview');
    
    // Afficher la modal
    modal.style.display = 'block';
    
    // Réinitialiser le formulaire
    form.reset();
    form.querySelector('[name="idProd"]').value = productId;
    
    // Afficher l'image actuelle
    currentImage.innerHTML = `<img src="images/prod${productId}.png?t=${Date.now()}" alt="Image actuelle">`;
    imagePreview.innerHTML = '';

    // Gérer la prévisualisation de la nouvelle image
    const imageInput = document.getElementById('newImage');
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Vérification du type MIME
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                showToast('Format de fichier non supporté. Utilisez PNG, JPG, JPEG, GIF ou WEBP', 'error');
                this.value = '';
                imagePreview.innerHTML = '';
                return;
            }

            // Vérification de la taille
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                showToast('L\'image ne doit pas dépasser 5MB', 'error');
                this.value = '';
                imagePreview.innerHTML = '';
                return;
            }

            // Prévisualisation
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                imagePreview.innerHTML = '';
                imagePreview.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });

    // Gérer la soumission du formulaire
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const response = await fetch('traitements/updateProductImage.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showToast('Image mise à jour avec succès', 'success', 'Succès');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(result.message || 'Erreur lors de la mise à jour de l\'image', 'error');
            }
        } catch (error) {
            showToast('Erreur lors de la communication avec le serveur', 'error');
        }
    });
}

// Ajouter/modifier la fonction editProduct
async function editProduct(productId) {
    try {
        const response = await fetch(`traitements/getProduit.php?id=${productId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const product = await response.json();

        // Afficher la modal
        const modal = document.getElementById('modal-edit-product');
        modal.style.display = 'block';

        // Remplir le formulaire
        await fillEditForm(product);

        // Gérer la fermeture de la modal
        const closeBtn = modal.querySelector('.close');
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur lors de la récupération des données du produit', 'error');
    }
}

async function deleteProduct(productId) {
    // Créer la modal de confirmation
    const modal = document.createElement('div');
    modal.className = 'confirm-modal';
    modal.innerHTML = `
        <div class="confirm-modal-content">
            <h3>Confirmer la suppression</h3>
            <p>Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.</p>
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
                const response = await fetch('traitements/deleteProduct.php?idProd=${productId}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `idProd=${productId}`
                });

                const result = await response.json();
                modal.remove();

                if (result.success) {
                    showToast(result.message, 'success', 'Succès');
                    setTimeout(() => {
                        const url = new URL(window.location.href);
                        url.search = ''; // Supprimer les paramètres GET
                        window.location.href = url.toString();
                    }, 2000);
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

// Fonction pour déterminer le public cible
function determinerPublicCible(categorieId) {
    const publicCibleMap = {
        '11': 'Enfants',
        '12': 'Adolescents',
        '13': 'Adultes',
        '14': 'Famille'
    };
    return publicCibleMap[categorieId] || 'Non défini';
} 