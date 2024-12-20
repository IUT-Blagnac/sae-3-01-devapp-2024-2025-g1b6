document.addEventListener('DOMContentLoaded', async function() {
    // Charger les catégories au chargement de la page
    await loadCategories();
    
    // Écouteur pour le changement de catégorie principale
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

    // Gestion de la prévisualisation de l'image
    const imageInput = document.getElementById('imageProd');
    const previewContainer = document.getElementById('image-preview');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Vérification du type MIME
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                showToast('Format de fichier non supporté. Utilisez PNG, JPG, JPEG, GIF ou WEBP', 'error');
                this.value = '';
                previewContainer.innerHTML = '';
                return;
            }

            // Vérification de la taille
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                showToast('L\'image ne doit pas dépasser 5MB', 'error');
                this.value = '';
                previewContainer.innerHTML = '';
                return;
            }

            // Prévisualisation
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                previewContainer.innerHTML = '';
                previewContainer.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });

    // Gestion de la soumission du formulaire
    document.getElementById('add-product-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validateProductForm()) {
            return;
        }

        try {
            const formData = new FormData(this);
            const response = await fetch('traitements/saveProduct.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                const toast = showToast('Produit ajouté avec succès', 'success', 'Succès');
                // Attendre que le toast soit ajouté au DOM
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        // S'assurer que le toast est visible avant la redirection
                        if (toast && toast.parentNode) {
                            window.location.href = 'listeProduits.php?toast=success&message=Produit ajouté avec succès';
                        }
                    }, 2000);
                });
            } else {
                showToast(result.message || 'Erreur lors de l\'ajout du produit', 'error');
            }
        } catch (error) {
            showToast('Erreur lors de la communication avec le serveur', 'error');
        }
    });

    // Ajouter des écouteurs pour normaliser l'entrée en temps réel
    const decimalInputs = ['prix', 'poids'];
    
    decimalInputs.forEach(inputName => {
        const input = document.querySelector(`[name="${inputName}"]`);
        if (input) {
            // Empêcher le collage de caractères non autorisés
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = (e.originalEvent || e).clipboardData.getData('text/plain');
                const sanitized = text.replace(/[^0-9.,]/g, '');
                document.execCommand('insertText', false, sanitized);
            });

            // Gérer la saisie des caractères
            input.addEventListener('keypress', function(e) {
                // Autoriser les touches de contrôle (backspace, delete, etc.)
                if (e.key.length > 1) return;
                
                // Autoriser les chiffres
                if (/\d/.test(e.key)) return;
                
                // Gérer le point et la virgule
                if (e.key === '.' || e.key === ',') {
                    // Empêcher plus d'un séparateur décimal
                    if (this.value.includes('.') || this.value.includes(',')) {
                        e.preventDefault();
                    }
                    return;
                }
                
                // Bloquer tous les autres caractères
                e.preventDefault();
            });
            
            // Formater à la perte du focus
            input.addEventListener('blur', function() {
                if (this.value) {
                    const value = parseFloat(this.value.replace(',', '.'));
                    if (!isNaN(value)) {
                        this.value = value.toFixed(2).replace('.', ',');
                    }
                }
            });
        }
    });
});

// Fonction pour charger les catégories principales
async function loadCategories() {
    try {
        console.log('Chargement des catégories...');
        const response = await fetch('getCategories.php');
        const data = await response.json();
        console.log('Données reçues:', data);
        
        if (!data.success) {
            throw new Error('Erreur lors du chargement des catégories');
        }
        
        const selectPrincipal = document.getElementById('categoriePrincipale');
        if (!selectPrincipal) {
            throw new Error('Select categoriePrincipale non trouvé');
        }
        
        selectPrincipal.innerHTML = '<option value="">Sélectionnez une catégorie</option>';
        
        if (data.main_categories && Array.isArray(data.main_categories)) {
            data.main_categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.IDCATEG;
                option.textContent = category.NOMCATEG;
                selectPrincipal.appendChild(option);
            });
            console.log('Catégories chargées:', data.main_categories.length);
        } else {
            console.error('Format de données incorrect:', data);
        }
    } catch (error) {
        console.error('Erreur lors du chargement des catégories:', error);
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

// Fonction pour convertir une chaîne en nombre, acceptant virgule ou point
function parseDecimalNumber(value) {
    // Conserver le point ou la virgule
    return parseFloat(value.replace(',', '.'));
}

// Fonction de validation du formulaire
function validateProductForm() {
    const form = document.getElementById('add-product-form');
    const nom = form.querySelector('[name="nom"]').value.trim();
    const description = form.querySelector('[name="description"]').value.trim();
    const prix = parseDecimalNumber(form.querySelector('[name="prix"]').value);
    const poids = parseDecimalNumber(form.querySelector('[name="poids"]').value);
    const stock = parseInt(form.querySelector('[name="stock"]').value);
    const image = form.querySelector('#imageProd').value;
    
    if (nom.length === 0 || nom.length > 30) {
        showToast('Le nom doit contenir entre 1 et 30 caractères', 'error');
        return false;
    }
    
    if (description.length > 150) {
        showToast('La description ne peut pas dépasser 150 caractères', 'error');
        return false;
    }
    
    if (isNaN(prix) || prix <= 0) {
        showToast('Le prix doit être un nombre supérieur à 0', 'error');
        return false;
    }
    
    if (isNaN(poids) || poids <= 0) {
        showToast('Le poids doit être un nombre supérieur à 0', 'error');
        return false;
    }
    
    if (isNaN(stock) || stock < 0) {
        showToast('Le stock doit être un nombre positif ou nul', 'error');
        return false;
    }
    
    if (!image) {
        showToast('Une image est requise', 'error');
        return false;
    }

    // Normaliser les valeurs dans le formulaire avant l'envoi
    form.querySelector('[name="prix"]').value = prix.toString();
    form.querySelector('[name="poids"]').value = poids.toString();
    
    return true;
}

// Fonction pour afficher les toasts
function showToast(message, type = 'error', title = 'Erreur') {
    const toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        console.error('Toast container not found');
        return null;
    }

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

    return toast;
} 