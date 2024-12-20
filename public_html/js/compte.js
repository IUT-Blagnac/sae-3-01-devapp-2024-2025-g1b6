document.addEventListener('DOMContentLoaded', () => {
    // Gestion des modales
    initModals();
    
    // Gestion des validations de formulaires
    initFormValidations();
    
    // Gestion de la suppression d'adresse
    initAddressDelete();
});

// Fonctions d'initialisation
function initModals() {
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.close');
    const modalModifAdresse = document.getElementById('modal-modif-adresse');
    const modalInfoClient = document.getElementById('modal-infoClient');
    const modalModifMdp = document.getElementById('modal-modifier-mdp');
    const modalAjoutAdresse = document.getElementById('modal-ajouter-adresse');
    const modalAjoutCarte = document.getElementById('modal-ajouter-carte');

    // Boutons d'ouverture des modales
    document.querySelectorAll('.modifier-infoClient').forEach(btn => {
        btn.addEventListener('click', () => modalInfoClient.style.display = 'block');
    });

    document.querySelectorAll('.modifier-mdp').forEach(btn => {
        btn.addEventListener('click', () => modalModifMdp.style.display = 'block');
    });

    document.querySelectorAll('.ajouter-adresse').forEach(btn => {
        btn.addEventListener('click', () => modalAjoutAdresse.style.display = 'block');
    });

    document.querySelectorAll('.ajouter-moyen-paiement').forEach(btn => {
        btn.addEventListener('click', () => modalAjoutCarte.style.display = 'block');
    });

    // Gestion de la modification d'adresse
    document.querySelectorAll('.modifier-adresse').forEach(btn => {
        btn.addEventListener('click', function() {
            const adresseItem = this.closest('.adresse-item');
            fillAddressForm(this.dataset.id, adresseItem);
            modalModifAdresse.style.display = 'block';
        });
    });

    // Fermeture des modales
    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.modal').style.display = 'none';
        });
    });

    window.addEventListener('click', (e) => {
        modals.forEach(modal => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
}

function initFormValidations() {
    // Validation du formulaire d'informations client
    const formInfoClient = document.querySelector('#modal-infoClient form');
    if (formInfoClient) {
        const nomCliInput = document.getElementById('nomCli');
        const prenomCliInput = document.getElementById('prenomCli');
        const emailCliInput = document.getElementById('emailCli');
        const numTelInput = document.getElementById('numTel');
        const submitBtn = document.getElementById('submitBtn-modif-Info-Cli');

        function validateInfoClientForm() {
            let isValid = true;
            let errorMessage = '';

            // Validation du nom (15 caractères max)
            if (nomCliInput.value.length > 15 || nomCliInput.value.length === 0) {
                errorMessage = 'Le nom doit faire entre 1 et 15 caractères';
                isValid = false;
            }
            // Validation du prénom (15 caractères max)
            else if (prenomCliInput.value.length > 15 || prenomCliInput.value.length === 0) {
                errorMessage = 'Le prénom doit faire entre 1 et 15 caractères';
                isValid = false;
            }
            // Validation de l'email (150 caractères max)
            else if (emailCliInput.value.length > 150 || !emailCliInput.value.includes('@')) {
                errorMessage = 'Veuillez entrer une adresse email valide';
                isValid = false;
            }
            // Validation du numéro de téléphone
            else if (numTelInput.value && !/^\d{9,10}$/.test(numTelInput.value)) {
                errorMessage = 'Le numéro de téléphone doit contenir 9 ou 10 chiffres';
                isValid = false;
            }

            if (!isValid && errorMessage) {
                showToast(errorMessage, 'error', 'Format incorrect');
            }

            submitBtn.disabled = !isValid;
            return isValid;
        }

        // Validation en temps réel
        nomCliInput.addEventListener('input', () => {
            const hadNumbers = /\d/.test(nomCliInput.value);
            nomCliInput.value = nomCliInput.value.replace(/\d/g, '');
            limitFieldLength(nomCliInput, 15, 'nom');
            if (hadNumbers && document.activeElement === nomCliInput) {
                showToast('Le nom ne peut pas contenir de chiffres', 'error', 'Format incorrect');
            }
            validateInfoClientForm();
        });

        prenomCliInput.addEventListener('input', () => {
            const hadNumbers = /\d/.test(prenomCliInput.value);
            prenomCliInput.value = prenomCliInput.value.replace(/\d/g, '');
            limitFieldLength(prenomCliInput, 15, 'prénom');
            if (hadNumbers && document.activeElement === prenomCliInput) {
                showToast('Le prénom ne peut pas contenir de chiffres', 'error', 'Format incorrect');
            }
            validateInfoClientForm();
        });

        emailCliInput.addEventListener('input', () => {
            limitFieldLength(emailCliInput, 150, 'email');
            validateInfoClientForm();
        });

        numTelInput.addEventListener('input', () => {
            numTelInput.value = numTelInput.value.replace(/\D/g, '');
            if (numTelInput.value.length > 10) {
                numTelInput.value = numTelInput.value.slice(0, 10);
            }
            validateInfoClientForm();
        });

        // Validation au submit
        formInfoClient.addEventListener('submit', (e) => {
            if (!validateInfoClientForm()) {
                e.preventDefault();
            }
        });
    }

    // Validation du mot de passe
    const formMdp = document.querySelector('#modal-modifier-mdp form');
    if (formMdp) {
        const nouveauMdp = document.getElementById('nouveau-mdp');
        const nouveauMdp2 = document.getElementById('nouveau-mdp2');
        const messageDiv = document.getElementById('password-message');
        const submitBtn = document.getElementById('changer-mdp');

        function validatePasswordForm() {
            const isValid = nouveauMdp.value === nouveauMdp2.value;
            messageDiv.style.display = isValid ? 'none' : 'block';
            messageDiv.textContent = isValid ? '' : 'Les mots de passe ne correspondent pas';
            submitBtn.disabled = !isValid;
            return isValid;
        }

        nouveauMdp.addEventListener('input', validatePasswordForm);
        nouveauMdp2.addEventListener('input', validatePasswordForm);

        formMdp.addEventListener('submit', (e) => {
            if (!validatePasswordForm()) {
                e.preventDefault();
            }
        });
    }

        // Validation du formulaire d'adresse
        const formAdresse = document.querySelector('#modal-ajouter-adresse form, #modal-modif-adresse form');
        if (formAdresse) {
            const numRueInput = formAdresse.querySelector('#numRue');
            const nomRueInput = formAdresse.querySelector('#nomRue');
            const complementInput = formAdresse.querySelector('#complement');
            const villeInput = formAdresse.querySelector('#ville');
            const codePostalInput = formAdresse.querySelector('#codePostal');
            const paysInput = formAdresse.querySelector('#pays');
            const submitBtn = formAdresse.querySelector('button[type="submit"]');
    
            function validateAddressForm() {
                let isValid = true;
                let errorMessage = '';
    
                // Validation du numéro de rue
                if (numRueInput.value <= 0 || numRueInput.value.length > 10) {
                    errorMessage = 'Le numéro de rue doit être positif et ne pas dépasser 10 caractères';
                    isValid = false;
                }
                // Validation du nom de rue
                else if (nomRueInput.value.length > 60 || nomRueInput.value.length === 0) {
                    errorMessage = 'Le nom de rue est requis et ne doit pas dépasser 60 caractères';
                    isValid = false;
                }
                // Validation du complément (optionnel)
                else if (complementInput.value.length > 60) {
                    errorMessage = 'Le complément d\'adresse ne doit pas dépasser 60 caractères';
                    isValid = false;
                }
                // Validation de la ville
                else if (villeInput.value.length > 30 || villeInput.value.length === 0) {
                    errorMessage = 'La ville est requise et ne doit pas dépasser 30 caractères';
                    isValid = false;
                }
                // Validation du code postal
                else if (codePostalInput.value <= 0) {
                    errorMessage = 'Le code postal doit être un nombre positif';
                    isValid = false;
                }
                // Validation du pays
                else if (paysInput.value.length > 30 || paysInput.value.length === 0) {
                    errorMessage = 'Le pays est requis et ne doit pas dépasser 30 caractères';
                    isValid = false;
                }
    
                if (!isValid && errorMessage) {
                    showToast(errorMessage, 'error', 'Format incorrect');
                }
    
                submitBtn.disabled = !isValid;
                return isValid;
            }
    
            // Validation en temps réel
            numRueInput.addEventListener('input', () => {
                limitFieldLength(numRueInput, 10, 'numéro de rue', 'number');
                validateAddressForm();
            });
    
            nomRueInput.addEventListener('input', () => {
                const hadNumbers = /\d/.test(nomRueInput.value);
                nomRueInput.value = nomRueInput.value.replace(/\d/g, '');
                limitFieldLength(nomRueInput, 60, 'nom de rue');
                if (hadNumbers && document.activeElement === nomRueInput) {
                    showToast('Le nom de rue ne peut pas contenir de chiffres', 'error', 'Format incorrect');
                }
                validateAddressForm();
            });
    
            complementInput.addEventListener('input', () => {
                limitFieldLength(complementInput, 60, 'complément d\'adresse');
                validateAddressForm();
            });
    
            villeInput.addEventListener('input', () => {
                const hadNumbers = /\d/.test(villeInput.value);
                villeInput.value = villeInput.value.replace(/\d/g, '');
                limitFieldLength(villeInput, 30, 'ville');
                if (hadNumbers && document.activeElement === villeInput) {
                    showToast('La ville ne peut pas contenir de chiffres', 'error', 'Format incorrect');
                }
                validateAddressForm();
            });
    
            codePostalInput.addEventListener('input', () => {
                limitFieldLength(codePostalInput, 8, 'code postal', 'number');
                validateAddressForm();
            });
    
            paysInput.addEventListener('input', () => {
                const hadNumbers = /\d/.test(paysInput.value);
                paysInput.value = paysInput.value.replace(/\d/g, '');
                limitFieldLength(paysInput, 30, 'pays');
                if (hadNumbers && document.activeElement === paysInput) {
                    showToast('Le pays ne peut pas contenir de chiffres', 'error', 'Format incorrect');
                }
                validateAddressForm();
            });
    
            // Validation au submit
            formAdresse.addEventListener('submit', (e) => {
                if (!validateAddressForm()) {
                    e.preventDefault();
                }
            });
        }
    
}

// Fonctions utilitaires
function limitFieldLength(input, maxLength, fieldName, type = 'text') {
    if (input.value.length > maxLength) {
        input.value = input.value.slice(0, maxLength);
        if (document.activeElement === input) {
            showToast(`Le ${fieldName} ne peut pas dépasser ${maxLength} caractères`, 'error', 'Limite atteinte');
        }
    }
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