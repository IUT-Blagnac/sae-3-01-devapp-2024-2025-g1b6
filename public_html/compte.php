<?php
    session_start();
    include("connect.inc.php");

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION["user"]["IDCLIENT"])) {
        header("Location: connexion.php");
        exit();
    }

    header("Cache-Control: no-cache, must-revalidate");

    // Récupérer l'ID client depuis la session
    $id_client = $_SESSION["user"]["IDCLIENT"];
    var_dump($_SESSION["user"]);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/compte.css?v=1.9">
    <title>Compte</title>
</head>
<body>
    
    <!-- En-tête -->
    <?php include("header.php"); 
        // Préparer la requête pour récupérer les informations du client
        $stmt = $pdo->prepare("SELECT * FROM CLIENT WHERE IDCLIENT = :id_client");
        $stmt->execute(['id_client' => $id_client]);
        $user = $stmt->fetch();
    ?>

    <main>
        <div class="toast-container">
            <?php if (isset($_GET['modif'])): ?>
                <?php 
                $type = $_GET['modif'] === 'ok' ? 'success' : 'error';
                $title = $type === 'success' ? 'Succès!' : 'Erreur!';
                $message = '';
                
                if (isset($_GET['typeModif'])) {
                    switch ($_GET['typeModif']) {
                        case '1':
                            $message = $type === 'success' ? 
                                'Vos informations personnelles ont été mises à jour avec succès.' : 
                                'Une erreur est survenue lors de la mise à jour de vos informations.';
                            break;
                        case '2':
                            if (isset($_GET['error']) && $_GET['error'] === 'wrong_password') {
                                $message = 'L\'ancien mot de passe est incorrect.';
                            } else {
                                $message = $type === 'success' ? 
                                    'Votre mot de passe a été modifié avec succès.' : 
                                    'Une erreur est survenue lors de la modification du mot de passe.';
                            }
                            break;
                        case '3':
                            $message = $type === 'success' ? 
                                'Votre nouvelle adresse a été ajoutée avec succès.' : 
                                'Une erreur est survenue lors de l\'ajout de l\'adresse.';
                            break;
                        // Ajoutez d'autres cas selon vos besoins
                    }
                }
                ?>
                <div class="toast <?= $type ?>" onclick="this.remove()">
                    <div class="toast-icon">
                        <?= $type === 'success' ? '✓' : '✕' ?>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title"><?= $title ?></div>
                        <div class="toast-message"><?= $message ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <div class="info-client">
                <h2>Mes informations personnelles</h2>
                <div class="infosCompte">
                    <div class="infosPerso">
                        <div class="infos">
                            <p class="info">Nom : <?= isset($user['NOMCLIENT']) ? htmlspecialchars($user['NOMCLIENT']) : 'Non défini' ?></p>
                            <p class="info">Prenom : <?= isset($user['PRENOMCLIENT']) ? htmlspecialchars($user['PRENOMCLIENT']) : 'Non défini' ?></p>
                            <p class="info">Email : <?= isset($user['EMAIL']) ? htmlspecialchars($user['EMAIL']) : 'Non défini' ?></p>
                            <p class="info">Date de naissance : <?= isset($user['DATEN']) ? htmlspecialchars((new DateTime($user['DATEN']))->format('d/m/Y')) : 'Non défini' ?></p>
                            <?php if (!empty($user['NUMTEL'])): ?>
                                <p class="info">Téléphone : <?= '+' . substr(htmlspecialchars($user['NUMTEL']), 2) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>    
                <div class="action-buttons">
                    <button class="modal-btn modifier-infoClient">Modifier Mes Infos</button>
                    <button class="modal-btn modifier-mdp">Modifier Mot de passe</button>
                </div>
        </div>
            
        <div class="modif-client">
            <?php 
                $query = $pdo->prepare('SELECT A.* FROM ADRESSE A, POSSEDERADR Pa
                                               WHERE A.IDADRESSE = Pa.IDADRESSE
                                               AND Pa.IDCLIENT = :idClient 
                ');
                $query->execute(['idClient'=> $id_client]);
                $adresses = $query->fetchAll();
                $query->closeCursor()
            ?>
            <div class="adresse">
                <h2>Mes Adresses</h2>
                <?php if (count($adresses) > 0): ?>
                    <?php foreach($adresses as $adresse): ?>
                        <div class="adresse-item">
                            <p>
                                <?= htmlspecialchars($adresse['NUMRUE']) ?> 
                                <?= htmlspecialchars($adresse['NOMRUE']) ?>
                                <?= !empty($adresse['COMPLEMENT']) ? ', ' . htmlspecialchars($adresse['COMPLEMENT']) : '' ?>
                                <br>
                                <?= htmlspecialchars($adresse['VILLE']) ?> 
                                <?= htmlspecialchars($adresse['CODEPOSTAL']) ?>
                                <br>
                                <?= htmlspecialchars($adresse['PAYS']) ?>
                            </p>
                            <div class="action-buttons">
                                <button class="modal-btn modifier-adresse" data-id="<?= $adresse['IDADRESSE'] ?>">Modifier</button>
                                <button class="modal-btn supprimer-adresse" data-id="<?= $adresse['IDADRESSE'] ?>">Supprimer</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Vous n'avez encore aucune adresse enregistrée.</p>
                <?php endif; ?>
                <button class="modal-btn ajouter-adresse">Ajouter</button>
            </div>

            <div class="cartes">
                <h2>Mes Moyens de Paiement</h2>
                <?php 
                    $query = $pdo->prepare('SELECT Ip.* FROM INFORMATIONPAIEMENT Ip, POSSEDERIP Pip
                                                   WHERE Ip.NUMCB = Pip.NUMCB
                                                   AND Pip.IDCLIENT = :idClient 
                    ');
                    $query->execute(['idClient'=> $id_client]);
                    $tabCartes = $query->fetchAll();
                    $query->closeCursor();
                    if (count($tabCartes) > 0): ?>
                        <?php foreach ($tabCartes as $carte): ?>
                            <div class="carte-item">
                                <p><?php substr_replace($carte['NUMCB'], str_repeat('*', strlen($carte['NUMCB']) - 5), 0, strlen($carte['NUMCB'])-5); ?></p>
                                <div class="action-buttons">
                                    <button class="modal-btn supprimer-carte">Supprimer</button>
                                </div>
                            </div>
                        <?php endforeach;?>
                    <?php else: ?>
                        <p>Vous n'avez encore aucune carte de paiement enregistrée.</p>
                    <?php endif; ?>
                <button class="modal-btn ajouter-moyen-paiement"> Ajouter </button>
            </div>
        </div>
        <button class="modal-btn disconnect-btn">Se déconecter</button>
    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <div class="footer-column">
            <h3>Qui sommes-nous ?</h3>
            <ul>
                <li><a href="#">Ludorama.com</a></li>
                <li><a href="#">Nos magasins</a></li>
                <li><a href="#">Cartes cadeaux</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>En ce moment</h3>
            <ul>
                <li><a href="#">Ambiance de Noël</a></li>
                <li><a href="#">Nouveautés</a></li>
                <li><a href="#">Rejoignez LudiSphere !</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Marques</h3>
            <ul>
                <li><a href="#">Lego</a></li>
                <li><a href="#">Playmobil</a></li>
                <li><a href="#">Jurassic Park</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Personnages jouets</h3>
            <ul>
                <li><a href="#">Pokemon</a></li>
                <li><a href="#">Tous les personnages</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Nos sites</h3>
            <ul>
                <li><a href="#">France</a></li>
                <li><a href="#">Allemagne</a></li>
                <li><a href="#">Tous nos sites</a></li>
            </ul>
        </div>
    </footer>

    <!-- Fenêtre contextuelle pour modifier les informations client -->
    <div id="modal-infoClient" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier mes informations</h2>
            <form method="post" action="updateInfoCli.php?typeModif=1">
                <label for="nomCli">Nom :</label>
                <input type="text" id="nomCli" name="nomCli" value=<?php echo '"' . htmlspecialchars($user['NOMCLIENT']) . '"'?>required>
                <label for="prenomCli">Prénom :</label>
                <input type="text" id="prenomCli" name="prenomCli" value=<?php echo '"' . htmlspecialchars($user['PRENOMCLIENT']) . '"'?>required>
                <label for="emailCli">Email :</label>
                <input type="text" id="emailCli" name="emailCli" value=<?php echo '"' . htmlspecialchars($user['EMAIL']) . '"'?>required>
                <label for="dateN">Date de naissance :</label>
                <input type="date" id="dateN" name="dateN" value=<?php echo '"' . $user['DATEN'] . '"'?>required>
                <?php if (!empty($user['NUMTEL'])): ?>
                    <label for="telephone-container">Numéro de Téléphone :</label>
                    <div class="telephone-container">
                        <select id="indicatif" name="indicatif">
                            <?php
                            $indicatifs = json_decode(file_get_contents('indicatifs.json'), true);
                            // On extrait l'indicatif du numéro existant (les deux premiers chiffres après le 00)
                            $currentIndicatif = !empty($user['NUMTEL']) ? '+' . substr($user['NUMTEL'], 2, 2) : '+33';
                            
                            foreach ($indicatifs as $indicatif) {
                                $selected = ($currentIndicatif === $indicatif['code']) ? 'selected' : '';
                                echo "<option value=\"{$indicatif['code']}\" {$selected}>{$indicatif['emoji']} {$indicatif['pays']} ({$indicatif['code']})</option>";
                            }
                            ?>
                        </select>
                        <input type="text" id="numTel" name="numTel" value="<?= !empty($user['NUMTEL']) ? substr($user['NUMTEL'], 4) : '' ?>">
                    </div>
                <?php else: ?>
                    <label for="telephone-container">Numéro de Téléphone :</label>
                    <div class="telephone-container">
                        <select id="indicatif" name="indicatif">
                            <?php
                            $indicatifs = json_decode(file_get_contents('indicatifs.json'), true);
                            foreach ($indicatifs as $indicatif) {
                                $selected = ($indicatif['code'] === '+33') ? 'selected' : ''; // Par défaut, on sélectionne la France
                                echo "<option value=\"{$indicatif['code']}\" {$selected}>{$indicatif['emoji']} {$indicatif['pays']} ({$indicatif['code']})</option>";
                            }
                            ?>
                        </select>
                        <input type="text" id="numTel" name="numTel">
                    </div>
                <?php endif; ?>
                <button type="submit" id="submitBtn-modif-Info-Cli">Modifier</button>
            </form>
        </div>
    </div>



    <div id="modal-modifier-mdp" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier mon Mot de Passe</h2>
            <form method="post" action="updateInfoCli.php?typeModif=2">
                <label for="ancien-mdp">Ancien mot de passe:</label>
                <input type="password" id="ancien-mdp" name="ancien-mdp" required>
                <label for="ancien-mdp">Nouveau mot de passe:</label>
                <input type="password" id="nouveau-mdp" name="nouveau-mdp" required>
                <label for="ancien-mdp">Confirmer le nouveau mot de passe:</label>
                <input type="password" id="nouveau-mdp2" name="nouveau-mdp2" required>
                <div id="password-message" style="color: red; display: none;"></div>
                <button type="submit" id="changer-mdp">Modifier</button>
            </form>
        </div>
    </div>

    <div id="modal-modifier-adresse" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier une adresse</h2>
            <form>
                <label for="modif-adr">Adresse:</label>
                <input type="text" id="modif-adr" name="adresse">
                <button type="submit">Modifier</button>
            </form>
        </div>
    </div>

    <!-- Fenêtre contextuelle pour modifier les adresses -->
    <div id="modal-ajouter-adresse" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Ajouter une adresse</h2>
            <form method="post" action="updateInfoCli.php?typeModif=3">
                <label for="numRue">Numéro de rue :</label>
                <input type="number" id="numRue" name="numRue" required min="1">
                
                <label for="nomRue">Nom de la rue :</label>
                <input type="text" id="nomRue" name="nomRue" required>
                
                <label for="complement">Complément d'adresse :</label>
                <input type="text" id="complement" name="complement">
                
                <label for="ville">Ville :</label>
                <input type="text" id="ville" name="ville" required>
                
                <label for="codePostal">Code postal :</label>
                <input type="number" id="codePostal" name="codePostal" required min="1" max="99999999" oninput="javascript: if (this.value.length > 8) this.value = this.value.slice(0, 8)">
                
                <label for="pays">Pays :</label>
                <input type="text" id="pays" name="pays" required>
                
                <button type="submit" id="submitBtn-ajout-adresse">Ajouter</button>
            </form>
        </div>
    </div>
    
    <!-- Fenêtre contextuelle pour modifier les cartes -->
    <div id="modal-ajouter-carte" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Ajouter une carte</h2>
            <form>
                <label for="numero-carte">Numéro de carte:</label>
                <input type="text" id="numero-carte" name="numero-carte">
            </form>
        </div>
    </div>
</body>
</html>


<script>
    // Fonction pour afficher la fenêtre contextuelle
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "block";
        }
    }
    
    // Fonction pour masquer la fenêtre contextuelle
    function closeModal(modal) {
        modal.style.display = "none";
    }
    
    // Initialisation des événements une fois le DOM chargé
    document.addEventListener('DOMContentLoaded', () => {
        //------SELECTION DES BOUTONS--------------
        const modifAdresseBtn = document.querySelectorAll('.modifier-adresse');
        const supprAdresseBtn = document.querySelectorAll('.supprimer-adresse');
        const ajouterAdresseBt = document.querySelectorAll('.ajouter-adresse');
        const modalAdresse = document.getElementById('modal-ajouter-adresse');
        
        const supprCarteBtn = document.querySelectorAll('.supprimer-carte');
        const modalCarte = document.getElementById('modal-ajouter-carte');
        const ajouterCarteBtn = document.querySelectorAll('.ajouter-moyen-paiement');

        const modifCliBtn = document.querySelectorAll('.modifier-infoClient');
        const modifMdpBtn = document.querySelectorAll('.modifier-mdp');
        const modalInfoCli = document.getElementById('modal-ajouter-adresse');
        
        const closeModalBtns = document.querySelectorAll('.close');
        
        //------EVENTS LISTENERS--------------
        // Boutons d'ajout
        ajouterAdresseBt.forEach(btn => btn.addEventListener('click', () => showModal('modal-ajouter-adresse')));
        ajouterCarteBtn.forEach(btn => btn.addEventListener('click', () => showModal('modal-ajouter-carte')));
        
        // Boutons de modification
        modifCliBtn.forEach(btn => btn.addEventListener('click', () => showModal('modal-infoClient')));
        modifMdpBtn.forEach(btn => btn.addEventListener('click', () => showModal('modal-modifier-mdp')));
        
        // Boutons de fermeture
        closeModalBtns.forEach(btn => btn.addEventListener('click', () => closeModal(btn.closest('.modal'))));

        //--------------------VERIFICATION DES MOTS DE PASSE---------------------
        const nouveauMdp = document.getElementById('nouveau-mdp');
        const nouveauMdp2 = document.getElementById('nouveau-mdp2');
        const submitBtn = document.getElementById('changer-mdp');
        const passwordMessage = document.getElementById('password-message');

        function validatePassword(password) {
            const minLength = password.length >= 8;
            const hasUpperCase = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            
            return {
                isValid: minLength && hasUpperCase && hasNumber && hasSpecialChar,
                errors: {
                    minLength,
                    hasUpperCase,
                    hasNumber,
                    hasSpecialChar
                }
            };
        }

        function getErrorMessage(errors) {
            const messages = [];
            if (!errors.minLength) messages.push("8 caractères minimum");
            if (!errors.hasUpperCase) messages.push("une majuscule");
            if (!errors.hasNumber) messages.push("un chiffre");
            if (!errors.hasSpecialChar) messages.push("un caractère spécial");
            
            return "Le mot de passe doit contenir : " + messages.join(", ");
        }

        function checkPasswords() {
            const validation = validatePassword(nouveauMdp.value);
            const passwordsMatch = nouveauMdp.value === nouveauMdp2.value && nouveauMdp.value !== '';
            
            // Vérifier d'abord la validité du mot de passe
            if (!validation.isValid) {
                passwordMessage.style.color = 'red';
                passwordMessage.textContent = getErrorMessage(validation.errors);
                passwordMessage.style.display = 'block';
                submitBtn.disabled = true;
                return;
            }
            
            // Ensuite vérifier si les mots de passe correspondent
            if (!passwordsMatch) {
                passwordMessage.style.color = 'red';
                passwordMessage.textContent = 'Les mots de passe ne correspondent pas.';
                submitBtn.disabled = true;
            } else {
                passwordMessage.style.color = 'green';
                passwordMessage.textContent = 'Le nouveau mot de passe est valide et les mots de passe correspondent.';
                submitBtn.disabled = false;
            }
            
            passwordMessage.style.display = 'block';
        }

        // Écouter les entrées pour vérifier les mots de passe en temps réel
        nouveauMdp.addEventListener('input', checkPasswords);
        nouveauMdp2.addEventListener('input', checkPasswords);

        //----------------------VERIFICATION DU NUMERO DE TELEPHONE----------------------------
        const numTelInput = document.getElementById('numTel');
        numTelInput.addEventListener('input', () => {
            // Nettoyage du numéro
            if (numTelInput.value.startsWith('0')) {
                numTelInput.value = numTelInput.value.substring(1);
            }
            // Supprimer les caractères non numériques
            numTelInput.value = numTelInput.value.replace(/[^\d]/g, '');
            
            // Validation de la longueur
            const isValid = numTelInput.value.length === 9;
            const submitBtn = document.getElementById('submitBtn-modif-Info-Cli');
            
            // Mise à jour de l'interface
            submitBtn.disabled = !isValid;
            
            // Limiter à 9 chiffres et afficher message d'erreur
            if (numTelInput.value.length > 9) {
                numTelInput.value = numTelInput.value.slice(0, 9);
                showToast(
                    'Le numéro de téléphone est limité à 9 chiffres',
                    'error',
                    'Limite atteinte'
                );
            }
            // Afficher message d'erreur si nombre insuffisant de chiffres
            else if (numTelInput.value.length > 0 && numTelInput.value.length < 9) {
                showToast(
                    'Le numéro de téléphone doit contenir exactement 9 chiffres',
                    'error',
                    'Format incorrect'
                );
            }
            // Afficher succès uniquement si exactement 9 chiffres et pas de troncature
            else if (isValid && numTelInput.value.length === 9) {
                showToast(
                    'Format du numéro de téléphone valide',
                    'success',
                    'Validation'
                );
            }
        });


        // Gestion des toasts
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(toast => {
            // Disparition automatique après 5 secondes
            setTimeout(() => {
                if (toast && toast.parentElement) {
                    toast.remove();
                }
            }, 5000);

            // Disparition au clic
            toast.addEventListener('click', () => {
                toast.remove();
            });
        });

        // Validation du nom de rue
        const nomRueInput = document.getElementById('nomRue');
        if (nomRueInput) {
            nomRueInput.addEventListener('input', (e) => {
                // Si l'entrée contient des chiffres
                if (/\d/.test(e.target.value)) {
                    // Supprimer les chiffres
                    e.target.value = e.target.value.replace(/\d/g, '');
                    
                    // Afficher un message d'erreur temporaire
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'toast error';
                    errorDiv.innerHTML = `
                        <div class="toast-icon">ℹ️</div>
                        <div class="toast-content">
                            <div class="toast-title">Attention</div>
                            <div class="toast-message">Veuillez utiliser le champ "Numéro de rue" pour les chiffres</div>
                        </div>
                    `;
                    
                    document.querySelector('.toast-container').appendChild(errorDiv);
                    
                    // Supprimer le message après 3 secondes
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 3000);
                }
            });
        }

        // Validation du code postal
        const codePostalInput = document.getElementById('codePostal');
        if (codePostalInput) {
            codePostalInput.addEventListener('input', (e) => {
                // Limiter à 8 caractères
                if (e.target.value.length > 8) {
                    e.target.value = e.target.value.slice(0, 8);
                }
                
                // Empêcher les nombres négatifs
                if (e.target.value < 0) {
                    e.target.value = Math.abs(e.target.value);
                }
            });
        }

        // Validation du formulaire d'adresse
        const formAdresse = document.querySelector('#modal-ajouter-adresse form');
        const submitBtnAdresse = document.getElementById('submitBtn-ajout-adresse');

        function validateAdresseForm() {
            const numRue = document.getElementById('numRue').value;
            const nomRue = document.getElementById('nomRue').value;
            const complement = document.getElementById('complement').value;
            const ville = document.getElementById('ville').value;
            const codePostal = document.getElementById('codePostal').value;
            const pays = document.getElementById('pays').value;

            let isValid = true;
            let errorMessage = '';

            // Validation spécifique pour chaque champ
            // On ne vérifie que les dépassements de limite et les formats incorrects
            if (numRue && numRue <= 0) {
                errorMessage = 'Le numéro de rue doit être positif';
                isValid = false;
            } else if (nomRue && nomRue.length > 60) {
                errorMessage = 'Le nom de rue est trop long (maximum 60 caractères)';
                isValid = false;
            } else if (complement && complement.length > 60) {
                errorMessage = 'Le complément d\'adresse est trop long (maximum 60 caractères)';
                isValid = false;
            } else if (ville && ville.length > 30) {
                errorMessage = 'Le nom de la ville est trop long (maximum 30 caractères)';
                isValid = false;
            } else if (codePostal && codePostal <= 0) {
                errorMessage = 'Le code postal doit être positif';
                isValid = false;
            } else if (codePostal && codePostal.length > 8) {
                errorMessage = 'Le code postal est trop long (maximum 8 chiffres)';
                isValid = false;
            } else if (pays && pays.length > 30) {
                errorMessage = 'Le nom du pays est trop long (maximum 30 caractères)';
                isValid = false;
            }

            // Afficher le message d'erreur uniquement si on a un message d'erreur de format
            if (!isValid && errorMessage) {
                showToast(errorMessage, 'error', 'Format incorrect');
            }

            // La validation des champs requis se fait toujours via les attributs HTML required
            submitBtnAdresse.disabled = !isValid;
            return isValid;
        }

        // Ajouter les event listeners pour la validation en temps réel
        if (formAdresse) {
            const inputs = formAdresse.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', validateAdresseForm);
            });

            // Validation au submit
            formAdresse.addEventListener('submit', (e) => {
                if (!validateAdresseForm()) {
                    e.preventDefault();
                }
            });
        }

        // Ajouter après la fonction showModal

        // Fonction utilitaire pour créer et afficher un toast
        function showToast(message, type = 'error', title = 'Erreur de saisie') {
            const toastDiv = document.createElement('div');
            toastDiv.className = `toast ${type}`;
            toastDiv.innerHTML = `
                <div class="toast-icon">${type === 'success' ? '✓' : '⚠️'}</div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
            `;
            
            document.querySelector('.toast-container').appendChild(toastDiv);
            setTimeout(() => toastDiv.remove(), 3000);
        }

        // Fonction pour gérer la limitation des champs avec alerte uniquement en focus
        function limitFieldLength(input, maxLength, fieldName, type = 'text') {
            if (type === 'number') {
                if (input.value < 0) {
                    input.value = Math.abs(input.value);
                }
                if (input.value.length > maxLength && document.activeElement === input) {
                    input.value = input.value.slice(0, maxLength);
                    showToast(`Le champ ${fieldName} est limité à ${maxLength} chiffres`, 'error', 'Limite atteinte');
                }
            } else {
                if (input.value.length > maxLength && document.activeElement === input) {
                    input.value = input.value.slice(0, maxLength);
                    showToast(`Le champ ${fieldName} est limité à ${maxLength} caractères`, 'error', 'Limite atteinte');
                }
            }
        }

        // Modifier la sélection du formulaire Info Client
        const formInfoClient = document.querySelector('#modal-infoClient form');
        if (formInfoClient) {
            const nomCliInput = document.getElementById('nomCli');
            const prenomCliInput = document.getElementById('prenomCli');
            const emailCliInput = document.getElementById('emailCli');
            const submitBtnInfoCli = document.getElementById('submitBtn-modif-Info-Cli');

            function validateInfoClientForm() {
                const nomCli = nomCliInput.value;
                const prenomCli = prenomCliInput.value;
                const emailCli = emailCliInput.value;

                let isValid = true;
                let errorMessage = '';

                // Validation spécifique pour chaque champ
                if (nomCli && nomCli.length > 20) {
                    errorMessage = 'Le nom est trop long (maximum 20 caractères)';
                    isValid = false;
                } else if (prenomCli && prenomCli.length > 15) {
                    errorMessage = 'Le prénom est trop long (maximum 15 caractères)';
                    isValid = false;
                } else if (emailCli && emailCli.length > 150) {
                    errorMessage = 'L\'email est trop long (maximum 150 caractères)';
                    isValid = false;
                }

                // Validation du format email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailCli && !emailRegex.test(emailCli)) {
                    errorMessage = 'Format d\'email invalide';
                    isValid = false;
                }

                // Afficher le message d'erreur uniquement si on a un message d'erreur de format
                if (!isValid && errorMessage) {
                    showToast(errorMessage, 'error', 'Format incorrect');
                }

                submitBtnInfoCli.disabled = !isValid;
                return isValid;
            }

            // Nom client (20 caractères max)
            nomCliInput.addEventListener('input', () => {
                const hadNumbers = /\d/.test(nomCliInput.value);
                nomCliInput.value = nomCliInput.value.replace(/\d/g, '');
                limitFieldLength(nomCliInput, 20, 'nom');
                if (hadNumbers && document.activeElement === nomCliInput) {
                    showToast('Le nom ne peut pas contenir de chiffres', 'error', 'Format incorrect');
                }
                validateInfoClientForm();
            });
            
            // Prénom client (15 caractères max)
            prenomCliInput.addEventListener('input', () => {
                const hadNumbers = /\d/.test(prenomCliInput.value);
                prenomCliInput.value = prenomCliInput.value.replace(/\d/g, '');
                limitFieldLength(prenomCliInput, 15, 'prénom');
                if (hadNumbers && document.activeElement === prenomCliInput) {
                    showToast('Le prénom ne peut pas contenir de chiffres', 'error', 'Format incorrect');
                }
                validateInfoClientForm();
            });
            
            // Email (150 caractères max)
            emailCliInput.addEventListener('input', () => {
                limitFieldLength(emailCliInput, 150, 'email');
                validateInfoClientForm();
            });

            // Validation au submit
            formInfoClient.addEventListener('submit', (e) => {
                if (!validateInfoClientForm()) {
                    e.preventDefault();
                }
            });
        }

        // Modal Adresse
        if (formAdresse) {
            const numRueInput = document.getElementById('numRue');
            const nomRueInput = document.getElementById('nomRue');
            const complementInput = document.getElementById('complement');
            const villeInput = document.getElementById('ville');
            const codePostalInput = document.getElementById('codePostal');
            const paysInput = document.getElementById('pays');

            numRueInput.addEventListener('input', () => limitFieldLength(numRueInput, 10, 'numéro de rue', 'number'));
            complementInput.addEventListener('input', () => limitFieldLength(complementInput, 60, 'complément'));
            villeInput.addEventListener('input', () => limitFieldLength(villeInput, 30, 'ville'));
            codePostalInput.addEventListener('input', () => limitFieldLength(codePostalInput, 8, 'code postal', 'number'));
            paysInput.addEventListener('input', () => limitFieldLength(paysInput, 30, 'pays'));

            // Cas spécial pour le nom de rue
            nomRueInput.addEventListener('input', () => {
                const hadNumbers = /\d/.test(nomRueInput.value);
                nomRueInput.value = nomRueInput.value.replace(/\d/g, '');
                limitFieldLength(nomRueInput, 60, 'nom de rue');
                if (hadNumbers && document.activeElement === nomRueInput) {
                    showToast('Veuillez utiliser le champ "Numéro de rue" pour les chiffres', 'error', 'Format incorrect');
                }
            });
        }
    });
</script>