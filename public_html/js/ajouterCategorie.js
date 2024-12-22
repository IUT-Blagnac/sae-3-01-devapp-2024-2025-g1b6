document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('add-category-form');
    const niveauSelect = document.getElementById('niveau');
    const sectionMeres = document.getElementById('section-categories-meres');
    const sectionPrincipales = document.getElementById('section-categories-principales');

    // Gérer l'affichage des sections en fonction du niveau sélectionné
    niveauSelect.addEventListener('change', function() {
        sectionMeres.style.display = 'none';
        sectionPrincipales.style.display = 'none';

        if (this.value === 'Principale') {
            sectionMeres.style.display = 'block';
        } else if (this.value === 'Secondaire') {
            sectionPrincipales.style.display = 'block';
        }
    });

    // Fonction pour afficher les toasts
    function showToast(message, type = 'success') {
        const toastContainer = document.querySelector('.toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        // Ajouter l'icône appropriée
        const icon = document.createElement('i');
        icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        toast.appendChild(icon);

        // Ajouter le message
        const messageSpan = document.createElement('span');
        messageSpan.textContent = message;
        toast.appendChild(messageSpan);

        toastContainer.appendChild(toast);

        // Animation d'entrée
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        // Supprimer le toast après 3 secondes
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // Gérer la soumission du formulaire
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Validation
        const niveau = niveauSelect.value;
        let isValid = true;
        let errorMessage = '';

        if (niveau === 'Principale') {
            const selectedMeres = document.querySelectorAll('input[name="categories_meres[]"]:checked');
            if (selectedMeres.length === 0) {
                isValid = false;
                errorMessage = 'Veuillez sélectionner au moins une catégorie mère';
            }
        } else if (niveau === 'Secondaire') {
            const selectedPrincipales = document.querySelectorAll('input[name="categories_principales[]"]:checked');
            if (selectedPrincipales.length === 0) {
                isValid = false;
                errorMessage = 'Veuillez sélectionner au moins une catégorie principale';
            }
        }

        if (!isValid) {
            showToast(errorMessage, 'error');
            return;
        }

        // Préparation des données
        const formData = new FormData(form);

        try {
            const response = await fetch('traitements/ajouterCategorie.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast('La catégorie a été ajoutée avec succès', 'success');
                form.reset();
                sectionMeres.style.display = 'none';
                sectionPrincipales.style.display = 'none';
                setTimeout(() => {
                    window.location.href = 'listeCategories.php';
                }, 2000);
            } else {
                showToast(data.message || 'Erreur lors de l\'ajout de la catégorie', 'error');
            }
        } catch (error) {
            showToast('Erreur lors de la communication avec le serveur', 'error');
            console.error('Erreur:', error);
        }
    });
}); 