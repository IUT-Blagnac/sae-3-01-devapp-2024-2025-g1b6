document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la prévisualisation de l'image
    const imageInput = document.getElementById('imageProd');
    const imagePreview = document.getElementById('image-preview');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Vérification du type MIME
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                showToast('Format de fichier non supporté. Utilisez PNG, JPG, JPEG, GIF ou WEBP', 'error');
                this.value = ''; // Réinitialiser l'input
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

            // Prévisualisation si tout est OK
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

    // Chargement des catégories principales en fonction des catégories mères sélectionnées
    const catMereCheckboxes = document.querySelectorAll('input[name="categories_meres[]"]');
    const categoriePrincipale = document.getElementById('categoriePrincipale');

    catMereCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', loadMainCategories);
    });

    // Chargement des catégories secondaires
    document.getElementById('categoriePrincipale').addEventListener('change', function() {
        if (this.value) {
            loadSecondaryCategories(this.value);
        } else {
            document.getElementById('categoriesSecondaires').innerHTML = '';
        }
    });

    // Validation du formulaire
    const form = document.getElementById('add-product-form');
    form.addEventListener('submit', validateForm);

    // Validation en temps réel des champs numériques
    const numericInputs = {
        'prix': { min: 0.01, max: 9999.99, step: 0.01 },
        'poids': { min: 0.01, max: 999.99, step: 0.01 },
        'stock': { min: 0, max: 99999, step: 1 }
    };

    Object.entries(numericInputs).forEach(([field, constraints]) => {
        const input = document.querySelector(`[name="${field}"]`);
        let timeoutId;

        // Validation uniquement quand l'utilisateur quitte le champ
        input.addEventListener('blur', function() {
            let value = parseFloat(this.value);
            if (value < constraints.min) {
                this.value = constraints.min;
                showToast(`La valeur minimale pour ${field} est ${constraints.min}`, 'warning');
            } else if (value > constraints.max) {
                this.value = constraints.max;
                showToast(`La valeur maximale pour ${field} est ${constraints.max}`, 'warning');
            }
        });

        // Empêcher les valeurs négatives pendant la saisie
        input.addEventListener('input', function(e) {
            // Annuler le timeout précédent s'il existe
            if (timeoutId) clearTimeout(timeoutId);

            // Si la valeur est négative, la corriger immédiatement
            if (this.value.startsWith('-')) {
                this.value = this.value.replace('-', '');
            }

            // Pour les autres validations, attendre que l'utilisateur ait fini de taper
            timeoutId = setTimeout(() => {
                let value = parseFloat(this.value);
                if (!isNaN(value) && value === 0) {
                    this.value = constraints.min;
                    showToast(`La valeur minimale pour ${field} est ${constraints.min}`, 'warning');
                }
            }, 1500); // Attendre 1.5 secondes après la dernière frappe
        });
    });

    // Validation en temps réel des champs texte
    const textInputs = {
        'nom': 30,
        'couleur': 30,
        'composition': 150,
        'description': 150
    };

    Object.entries(textInputs).forEach(([field, maxLength]) => {
        const input = document.querySelector(`[name="${field}"]`);
        input.addEventListener('input', function() {
            if (this.value.length > maxLength) {
                this.value = this.value.slice(0, maxLength);
                showToast(`Le champ ${field} est limité à ${maxLength} caractères`, 'warning');
            }
        });
    });
});

// Fonction pour charger les catégories principales
async function loadMainCategories() {
    const selectedMeres = Array.from(document.querySelectorAll('input[name="categories_meres[]"]:checked'))
        .map(cb => cb.value);

    try {
        const response = await fetch('getCategories.php?parent_categories=' + selectedMeres.join(','));
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

// Fonction pour charger les catégories secondaires
async function loadSecondaryCategories(mainCategoryId) {
    try {
        const response = await fetch(`getCategories.php?main_category=${mainCategoryId}`);
        const data = await response.json();
        
        const selectSecondaire = document.getElementById('categoriesSecondaires');
        selectSecondaire.innerHTML = '';
        
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

// Fonction de validation du formulaire
function validateForm(e) {
    e.preventDefault();
    
    // Vérifications des champs requis et des formats
    const form = e.target;
    const formData = new FormData(form);

    // Vérification des catégories mères
    const catMeres = document.querySelectorAll('input[name="categories_meres[]"]:checked');
    if (catMeres.length === 0) {
        showToast('Veuillez sélectionner au moins une catégorie mère', 'error');
        return false;
    }

    // Autres validations...
    if (!validateProductData(formData)) {
        return false;
    }

    // Envoi du formulaire
    submitForm(formData);
}

// Fonction pour valider les données du produit
function validateProductData(formData) {
    // Validation du nom
    const nom = formData.get('nom').trim();
    if (nom.length === 0 || nom.length > 30) {
        showToast('Le nom doit contenir entre 1 et 30 caractères', 'error');
        return false;
    }

    // Validation de la description
    const description = formData.get('description').trim();
    if (description.length > 150) {
        showToast('La description ne peut pas dépasser 150 caractères', 'error');
        return false;
    }

    // Validation du prix
    const prix = parseFloat(formData.get('prix'));
    if (isNaN(prix) || prix <= 0 || prix > 9999.99) {
        showToast('Le prix doit être compris entre 0.01€ et 9999.99€', 'error');
        return false;
    }

    // Validation du poids
    const poids = parseFloat(formData.get('poids'));
    if (isNaN(poids) || poids <= 0 || poids > 999.99) {
        showToast('Le poids doit être compris entre 0.01kg et 999.99kg', 'error');
        return false;
    }

    // Validation du stock
    const stock = parseInt(formData.get('stock'));
    if (isNaN(stock) || stock < 0 || stock > 99999) {
        showToast('Le stock doit être compris entre 0 et 99999', 'error');
        return false;
    }

    // Validation de la couleur
    const couleur = formData.get('couleur').trim();
    if (couleur && couleur.length > 30) {
        showToast('La couleur ne peut pas dépasser 30 caractères', 'error');
        return false;
    }

    // Validation de la composition
    const composition = formData.get('composition').trim();
    if (composition && composition.length > 150) {
        showToast('La composition ne peut pas dépasser 150 caractères', 'error');
        return false;
    }

    // Validation de l'image
    const imageFile = formData.get('image');
    if (imageFile && imageFile.name) {
        // Vérification du type MIME
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(imageFile.type)) {
            showToast('Format de fichier non supporté. Utilisez PNG, JPG, JPEG, GIF ou WEBP', 'error');
            return false;
        }

        // Vérification de l'extension
        const validExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
        const extension = imageFile.name.split('.').pop().toLowerCase();
        if (!validExtensions.includes(extension)) {
            showToast('Extension de fichier non supportée. Utilisez PNG, JPG, JPEG, GIF ou WEBP', 'error');
            return false;
        }

        const maxSize = 5 * 1024 * 1024; // 5MB
        if (imageFile.size > maxSize) {
            showToast('L\'image ne doit pas dépasser 5MB', 'error');
            return false;
        }
    }

    return true;
}

// Fonction pour envoyer le formulaire
async function submitForm(formData) {
    try {
        const response = await fetch('saveProduct.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showToast('Produit ajouté avec succès', 'success');
            setTimeout(() => {
                window.location.href = 'listeProduits.php';
            }, 1500);
        } else {
            showToast(result.message || 'Erreur lors de l\'ajout du produit', 'error');
        }
    } catch (error) {
        showToast('Erreur lors de la communication avec le serveur', 'error');
    }
}

// Fonction pour afficher les messages toast
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