<?php
    session_start();
    include("connect.inc.php");

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION["user"]["IDCLIENT"])) {
        header("Location: connexion.php");
        exit();
    }

    if (isset($_GET['disconnect']) && $_GET['disconnect'] === 'true') {
        session_destroy();
        header("Location: connexion.php");
        exit();
    }

    header("Cache-Control: no-cache, must-revalidate");

    // Récupérer l'ID client depuis la session
    $id_client = $_SESSION["user"]["IDCLIENT"];

?>  


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/compte.css?v=1.97">
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
                        case '4':
                            $message = $type === 'success' ? 
                                'L\'adresse a été modifiée avec succès !' : 
                                'Une erreur est survenue lors de la modification de l\'adresse.';
                            break;
                        case '5':
                            $message = $type === 'success' ? 
                                'L\'adresse a été supprimée avec succès !' : 
                                'Une erreur est survenue lors de la suppression de l\'adresse.';
                            break;
                        case '6':
                            if (isset($_GET['error']) && $_GET['error'] === 'wrong_holder') {
                                $message = 'Le nom du détenteur de la carte ne correspond pas à celui enregistré.';
                            } else {
                                $message = $type === 'success' ? 
                                    'Votre carte bancaire a été ajoutée avec succès !' : 
                                    'Une erreur est survenue lors de l\'ajout de la carte bancaire.';
                            }
                            break;
                        case '7':
                            $message = $type === 'success' ? 
                                'La carte bancaire a été supprimée avec succès !' : 
                                'Une erreur est survenue lors de la suppression de la carte bancaire.';
                            break;
                        default:
                            $message = $type === 'success' ? 'Opération réussie !' : 'Une erreur est survenue.';
                            break;
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

        <div class="adresse">
            <h2>Mes Adresses</h2>
            <div class="adresse-list">
                <?php 
                    $query = $pdo->prepare('SELECT A.* FROM ADRESSE A, POSSEDERADR Pa
                                               WHERE A.IDADRESSE = Pa.IDADRESSE
                                               AND Pa.IDCLIENT = :idClient 
                    ');
                    $query->execute(['idClient'=> $id_client]);
                    $adresses = $query->fetchAll();
                    $query->closeCursor()
                ?>
                <?php if (count($adresses) > 0): ?>
                    <?php foreach($adresses as $adresse): ?>
                        <div class="adresse-item">
                            <p>
                                <span class="num-rue"><?= htmlspecialchars($adresse['NUMRUE']) ?></span>
                                <span class="nom-rue"><?= htmlspecialchars($adresse['NOMRUE']) ?></span>
                                <?php if (!empty($adresse['COMPLEMENTADR'])): ?>
                                    <span class="complement">, <?= htmlspecialchars($adresse['COMPLEMENTADR']) ?></span>
                                <?php endif; ?>
                                <br>
                                <span class="ville"><?= htmlspecialchars($adresse['NOMVILLE']) ?></span>
                                <span class="code-postal"><?= htmlspecialchars($adresse['CODEPOSTAL']) ?></span>
                                <br>
                                <span class="pays"><?= htmlspecialchars($adresse['PAYS']) ?></span>
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
            </div>
            <button class="modal-btn ajouter-adresse">Ajouter</button>
        </div>

        <div class="cartes">
            <h2>Mes Moyens de Paiement</h2>
            <div class="carte-list">  <!-- Conteneur pour la scrollbar -->
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
                            <p>Carte n° <?php 
                                $lastFourDigits = substr($carte['NUMCB'], -4);
                                echo str_repeat('*', 12) . $lastFourDigits;
                            ?></p>
                            <div class="action-buttons">
                                <button class="modal-btn supprimer-carte" data-id="<?= $carte['NUMCB'] ?>">Supprimer</button>
                            </div>
                        </div>
                    <?php endforeach;?>
                <?php else: ?>
                    <p>Vous n'avez encore aucune carte de paiement enregistrée.</p>
                <?php endif; ?>
            </div>
            <button class="modal-btn ajouter-moyen-paiement">Ajouter</button>
        </div>

        <div class="commandes">
            <h2>Mes Commandes</h2>
            <div class="commande-list">
                <?php 
                    $query = $pdo->prepare("
                        SELECT 
                            C.*,
                            T.TYPEEXP, T.FRAISEXP, T.FRAISKG, T.DELAILIVRAISON,
                            P.QUANTITEPROD,
                            PR.IDPROD, PR.NOMPROD, PR.PRIXHT, PR.COMPOSITION, PR.COULEUR
                        FROM COMMANDE C, TRANSPORTEUR T, PANIER P, PRODUIT PR
                        WHERE C.IDTRANSPORTEUR = T.IDTRANSPORTEUR
                        AND P.IDCOMMANDE = C.NUMCOMMANDE 
                        AND P.IDCLIENT = C.IDCLIENT
                        AND P.IDPROD = PR.IDPROD
                        AND P.IDCOMMANDE != 0
                        AND C.IDCLIENT = :idClient
                        ORDER BY C.DATECOMMANDE DESC
                    ");
                    $query->execute(['idClient' => $id_client]);
                    $commandes = [];

                    // Organiser les données
                    while($row = $query->fetch()) {
                        if (!isset($commandes[$row['NUMCOMMANDE']])) {
                            $commandes[$row['NUMCOMMANDE']] = [
                                'date' => new DateTime($row['DATECOMMANDE']),
                                'statut' => $row['STATUTLIVRAISON'],
                                'suivi' => $row['CODESUIVI'],
                                'reglement' => $row['TYPEREGLEMENT'],
                                'transport' => [
                                    'type' => $row['TYPEEXP'],
                                    'frais' => $row['FRAISEXP'],
                                    'delai' => $row['DELAILIVRAISON']
                                ],
                                'produits' => []
                            ];
                        }
                        $commandes[$row['NUMCOMMANDE']]['produits'][] = [
                            'idprod' => $row['IDPROD'],  
                            'nom' => $row['NOMPROD'],
                            'quantite' => $row['QUANTITEPROD'],
                            'prix' => $row['PRIXHT']
                        ];
                    }
                
                    if (count($commandes) > 0): 
                        foreach($commandes as $numCommande => $commande): ?>
                            <div class="commande-item">
                                <div class="commande-header">
                                    <div class="commande-info">
                                        <h3>Commande n°<?= $numCommande ?></h3>
                                        <p class="date">Du <?= $commande['date']->format('d/m/Y') ?></p>
                                    </div>
                                    <div class="commande-status <?= strtolower($commande['statut']) ?>">
                                        <?= $commande['statut'] ?>
                                    </div>
                                </div>

                                <div class="commande-details">
                                    <div class="produits-list">
                                        <?php foreach($commande['produits'] as $produit): ?>
                                            <div class="produit-item">
                                                <a href="descProduit.php?idProd=<?= $produit['idprod'] ?>" class="produit-nom" target="_blank">
                                                    <?= $produit['nom'] ?>
                                                </a>
                                                <span class="produit-quantite">x<?= $produit['quantite'] ?></span>
                                                <span class="produit-prix"><?= number_format($produit['prix'] * $produit['quantite'], 2) ?> €</span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                        
                                    <div class="livraison-info">
                                        <p>Mode de livraison : <?= $commande['transport']['type'] ?></p>
                                        <p>Délai estimé : <?= $commande['transport']['delai'] ?> jours</p>
                                        <?php if ($commande['suivi']): ?>
                                            <p>Code de suivi : <?= $commande['suivi'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;?>
                        <div class="pagination">
                            <div class="pagination-numbers"></div>
                        </div>
                    <?php else: ?>
                        <p class="no-commandes">Vous n'avez pas encore passé de commande.</p>
                <?php endif; ?>
            </div>
        </div>

        <button class="disconnect-btn">Se déconnecter</button>
        <script>
            document.querySelector('.disconnect-btn').addEventListener('click', () => {
                window.location.href = 'compte.php?disconnect=true';
            });
        </script>
    </main>

    <?php include("footer.php"); ?>

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

    <div id="modal-modif-adresse" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier l'adresse</h2>
            <form action="updateInfoCli.php?typeModif=4" method="POST">
                <input type="hidden" id="idAddress" name="idAddress">
                
                <label for="numRue">Numéro de rue :</label>
                <input type="number" id="numRue" name="numRue" required>
                
                <label for="nomRue">Nom de rue :</label>
                <input type="text" id="nomRue" name="nomRue" required>
                
                <label for="complement">Complément d'adresse :</label>
                <input type="text" id="complement" name="complement">
                
                <label for="ville">Ville :</label>
                <input type="text" id="ville" name="ville" required>
                
                <label for="codePostal">Code postal :</label>
                <input type="number" id="codePostal" name="codePostal" required>
                
                <label for="pays">Pays :</label>
                <input type="text" id="pays" name="pays" required>
                
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
    
    <!-- Fenêtre contextuelle pour ajouter une carte -->
    <div id="modal-ajouter-carte" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Ajouter une carte bancaire</h2>
            <form method="post" action="updateInfoCli.php?typeModif=6">
                <label for="numCB">Numéro de carte :</label>
                <input type="text" id="numCB" name="numCB" required maxlength="19" placeholder="XXXX XXXX XXXX XXXX">
                            
                <label for="nomCompletCB">Nom sur la carte :</label>
                <input type="text" id="nomCompletCB" name="nomCompletCB" required maxlength="50">
                            
                <label for="dateExp">Date d'expiration :</label>
                <input type="month" id="dateExp" name="dateExp" required>
                            
                <label for="cryptogramme">Cryptogramme :</label>
                <input type="password" id="cryptogramme" name="cryptogramme" required maxlength="3" pattern="[0-9]{3}">
                            
                <button type="submit" id="submitBtn-ajout-carte">Ajouter</button>
            </form>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div id="modal-confirm-delete" class="modal">
        <div class="modal-content">
            <div class="confirm-delete-content">
                <h2>Confirmation de suppression</h2>
                <p>Êtes-vous sûr de vouloir supprimer cette adresse ? Cette action est irréversible.</p>
                <div class="confirm-buttons">
                    <button class="modal-btn confirm-yes">Supprimer</button>
                    <button class="modal-btn confirm-no">Annuler</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-confirm-delete-card" class="modal">
    <div class="modal-content">
        <div class="confirm-delete-content">
            <h2>Confirmation de suppression</h2>
            <p>Êtes-vous sûr de vouloir supprimer cette carte ? Cette action est irréversible.</p>
            <div class="confirm-buttons">
                <button class="modal-btn confirm-yes">Supprimer</button>
                <button class="modal-btn confirm-no">Annuler</button>
            </div>
        </div>
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

        // Récupération des éléments
        const modalModifAdresse = document.getElementById('modal-modif-adresse');
        const closeModifAdresse = modalModifAdresse.querySelector('.close');
        
        // Gestion de la modification d'adresse
        document.querySelectorAll('.modifier-adresse').forEach(btn => {
            btn.addEventListener('click', function() {
                const adresseItem = this.closest('.adresse-item');
                
                // Remplissage du formulaire
                document.getElementById('idAddress').value = this.dataset.id;
                document.getElementById('numRue').value = adresseItem.querySelector('.num-rue').textContent.trim();
                document.getElementById('nomRue').value = adresseItem.querySelector('.nom-rue').textContent.trim();
                
                const complement = adresseItem.querySelector('.complement');
                document.getElementById('complement').value = complement ? complement.textContent.replace(',', '').trim() : '';
                
                document.getElementById('ville').value = adresseItem.querySelector('.ville').textContent.trim();
                document.getElementById('codePostal').value = adresseItem.querySelector('.code-postal').textContent.trim();
                document.getElementById('pays').value = adresseItem.querySelector('.pays').textContent.trim();
                
                // Affichage de la modale
                modalModifAdresse.style.display = 'block';
            });
        });

        // Fermeture de la modale
        closeModifAdresse.addEventListener('click', () => {
            modalModifAdresse.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === modalModifAdresse) {
                modalModifAdresse.style.display = 'none';
            }
        });


           // Gestion de la suppression d'adresse
        const modalConfirmDelete = document.getElementById('modal-confirm-delete');
        let addressToDelete = null;

        document.querySelectorAll('.supprimer-adresse').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                addressToDelete = this.dataset.id;
                modalConfirmDelete.style.display = 'block';
            });
        });

        // Gestion des boutons de confirmation
        const confirmYes = modalConfirmDelete.querySelector('.confirm-yes');
        const confirmNo = modalConfirmDelete.querySelector('.confirm-no');

        confirmYes.addEventListener('click', function() {
            if (addressToDelete) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'updateInfoCli.php?typeModif=5';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'idAddress';
                input.value = addressToDelete;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
            modalConfirmDelete.style.display = 'none';
        });

        confirmNo.addEventListener('click', function() {
            modalConfirmDelete.style.display = 'none';
            addressToDelete = null;
        });

        // Fermeture de la modal en cliquant en dehors
        window.addEventListener('click', function(e) {
            if (e.target === modalConfirmDelete) {
                modalConfirmDelete.style.display = 'none';
                addressToDelete = null;
            }
        });

        const formCarte = document.querySelector('#modal-ajouter-carte form');
            if (formCarte) {
            const numCBInput = document.getElementById('numCB');
            const nomCompletCBInput = document.getElementById('nomCompletCB');
            const dateExpInput = document.getElementById('dateExp');
            const cryptogrammeInput = document.getElementById('cryptogramme');
            const submitBtn = document.getElementById('submitBtn-ajout-carte');


            // Formatage automatique du numéro de carte
            numCBInput.addEventListener('input', (e) => {
                // Supprime tout ce qui n'est pas un chiffre
                let value = e.target.value.replace(/[^\d]/g, '');

                // Ajoute un espace tous les 4 chiffres
                let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');

                // Limite à 16 chiffres (19 caractères avec les espaces)
                if (value.length > 16) {
                    value = value.slice(0, 16);
                    formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
                }

                e.target.value = formattedValue;
                validateCardForm();
            });

            // Validation du nom
            nomCompletCBInput.addEventListener('input', () => {
                const hadNumbers = /\d/.test(nomCompletCBInput.value);
                nomCompletCBInput.value = nomCompletCBInput.value.replace(/[^A-Za-z\s]/g, '').toUpperCase();
                if (hadNumbers) {
                    showToast('Le nom ne peut pas contenir de chiffres', 'error', 'Format incorrect');
                }
                validateCardForm();
            });

            // Validation de la date d'expiration
            dateExpInput.addEventListener('change', () => {
                const selectedDate = new Date(dateExpInput.value);
                const today = new Date();
                if (selectedDate < today) {
                    showToast('La date d\'expiration doit être dans le futur', 'error', 'Date invalide');
                    dateExpInput.value = '';
                }
                validateCardForm();
            });

            // Validation du cryptogramme
            cryptogrammeInput.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 3);
                validateCardForm();
            });

            function validateCardForm() {
                const numCB = numCBInput.value.replace(/\s/g, '');
                const isValid = 
                    numCB.length === 16 && 
                    nomCompletCBInput.value.length >= 3 &&
                    dateExpInput.value && 
                    cryptogrammeInput.value.length === 3;

                submitBtn.disabled = !isValid;
                return isValid;
            }

            formCarte.addEventListener('submit', (e) => {
                if (!validateCardForm()) {
                    e.preventDefault();
                    showToast('Veuillez remplir correctement tous les champs', 'error', 'Formulaire invalide');
                }
            });
        }

        // Gestion de la suppression de carte
        const modalConfirmDeleteCard = document.getElementById('modal-confirm-delete-card');
        let cardToDelete = null;

        document.querySelectorAll('.supprimer-carte').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                cardToDelete = this.dataset.id;
                modalConfirmDeleteCard.style.display = 'block';
            });
        });

        // Gestion des boutons de confirmation
        const confirmCardYes = modalConfirmDeleteCard.querySelector('.confirm-yes');
        const confirmCardNo = modalConfirmDeleteCard.querySelector('.confirm-no');

        confirmCardYes.addEventListener('click', function() {
            if (cardToDelete) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'updateInfoCli.php?typeModif=7';
            
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'numCB';
                input.value = cardToDelete;
            
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
            modalConfirmDeleteCard.style.display = 'none';
        });

        confirmCardNo.addEventListener('click', function() {
            modalConfirmDeleteCard.style.display = 'none';
            cardToDelete = null;
        });

        // Fermeture de la modal en cliquant en dehors
        window.addEventListener('click', function(e) {
            if (e.target === modalConfirmDeleteCard) {
                modalConfirmDeleteCard.style.display = 'none';
                cardToDelete = null;
            }
        });

        const commandesParPage = 2; // Changé de 3 à 2
        const commandeItems = document.querySelectorAll('.commande-item');
        const paginationContainer = document.querySelector('.pagination-numbers');
        let pageCourante = 1;

        function afficherCommandes(page) {
            const debut = (page - 1) * commandesParPage;
            const fin = debut + commandesParPage;
        
            commandeItems.forEach((item, index) => {
                if (index >= debut && index < fin) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            // Mise à jour des boutons de pagination
            document.querySelectorAll('.page-number').forEach(btn => {
                btn.classList.toggle('active', parseInt(btn.textContent) === page);
            });
        }

        function initialiserPagination() {
            const nombrePages = Math.ceil(commandeItems.length / commandesParPage);
            paginationContainer.innerHTML = '';

            for (let i = 1; i <= nombrePages; i++) {
                const button = document.createElement('button');
                button.className = 'page-number';
                button.textContent = i;
                button.addEventListener('click', () => {
                    pageCourante = i;
                    afficherCommandes(i);
                });
                paginationContainer.appendChild(button);
            }

            // Afficher la première page
            afficherCommandes(1);
        }

        if (commandeItems.length > 0) {
            initialiserPagination();
        }

    });
    
</script>