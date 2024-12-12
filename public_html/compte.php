<?php
    session_start();
    include("connect.inc.php");

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION["user"]["IDCLIENT"])) {
        header("Location: connexion.php");
        exit();
    }

    // Récupérer l'ID client depuis la session
    $id_client = $_SESSION["user"]["IDCLIENT"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/compte.css?v=1.3">
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
                                <p class="info">Téléphone : <?= htmlspecialchars($user['NUMTEL']) ?></p>
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
                    <div class="adresse-item">
                        <p>123 Rue Exemple, Ville, Pays</p>
                        <div class="action-buttons">
                            <button class="modal-btn modifier-adresse">Modifier</button>
                            <button class="modal-btn supprimer-adresse">Supprimer</button>
                        </div>
                    </div>
                <?php else: ?>
                    <p>Vous n'avez encore aucune adresse enregistrée.</p>
                <?php endif; ?>
                <button class="modal-btn ajouter-adresse"> Ajouter </button>
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
                <input type="text" id="nomCli" name="nomCli" value=<?php echo '"' . htmlspecialchars($user['NOMCLIENT']) . '"'?>>
                <label for="prenomCli">Prénom :</label>
                <input type="text" id="prenomCli" name="prenomCli" value=<?php echo '"' . htmlspecialchars($user['PRENOMCLIENT']) . '"'?>>
                <label for="emailCli">Email :</label>
                <input type="text" id="emailCli" name="emailCli" value=<?php echo '"' . htmlspecialchars($user['EMAIL']) . '"'?>>
                <label for="dateN">Date de naissance :</label>
                <input type="date" id="dateN" name="dateN" value=<?php echo '"' . $user['DATEN'] . '"'?>>
                <?php if (!empty($user['NUMTEL'])): ?>
                    <label for="numTel">Email :</label>
                    <input type="text" id="numTel" name="numTel" value=<?php echo '"' . htmlspecialchars($user['NUMTEL']) . '"'?>>
                <?php else: ?>
                    <label for="numTel">Numéro de Télephone :</label>
                    <input type="text" id="numTel" name="numTel">
                <?php endif; ?>
                <button type="submit">Modifier</button>
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
            <form>
                <label for="adresse">Adresse:</label>
                <input type="text" id="adresse" name="adresse">
                <button type="submit">Modifier</button>
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
    
    // Gestion des événements pour les boutons de modification et de suppression
    document.addEventListener('DOMContentLoaded', () => {
        //Adresse
        const modifAdresseBtn = document.querySelectorAll('.modifier-adresse');
        const supprAdresseBtn = document.querySelectorAll('.supprimer-adresse');
        const ajouterAdresseBt =document.querySelectorAll('.ajouter-adresse')
        const modalAdresse = document.getElementById('modal-ajouter-adresse');
        //Carte
        const supprCarteBtn = document.querySelectorAll('.supprimer-carte');
        const modalCarte = document.getElementById('modal-ajouter-carte');
        const ajouterCarteBtn = document.querySelectorAll('.ajouter-moyen-paiement')

        //Informations Client
        const modifCliBtn = document.querySelectorAll('.modifier-infoClient');
        const modifMdpBtn = document.querySelectorAll('.modifier-mdp');
        const modalInfoCli = document.getElementById('modal-ajouter-adresse');
        //Bouton de fermeture
        const closeModalBtns = document.querySelectorAll('.close');
        
        //Events Listeners
        //------EVENTS AJOUTER--------------
        ajouterAdresseBt.forEach(btn => btn.addEventListener('click', () => showModal('modal-ajouter-adresse')));
        ajouterCarteBtn.forEach(btn => btn.addEventListener('click', () => showModal('modal-ajouter-carte')));
        modifCliBtn.forEach(btn => btn.addEventListener('click', () => showModal('modal-infoClient')));
        //------EVENTS MODIFIER--------------
        modifCliBtn.forEach(btn => btn.addEventListener('click', () => showModal('modal-infoClient')));
        modifMdpBtn.forEach(btn => btn.addEventListener('click', () => showModal('modal-modifier-mdp')));
        //------------BOUTON FERMER----------------------------------------
        closeModalBtns.forEach(btn => btn.addEventListener('click', () => closeModal(btn.closest('.modal'))));


        //--------------------VERIFICATION DES MOTS DE PASSE---------------------
        const nouveauMdp = document.getElementById('nouveau-mdp');
        const nouveauMdp2 = document.getElementById('nouveau-mdp2');
        const submitBtn = document.getElementById('changer-mdp');
        const passwordMessage = document.getElementById('password-message');

        function checkPasswords() {
            if (nouveauMdp.value === nouveauMdp2.value && nouveauMdp.value !== '') {
                // Si les mots de passe correspondent
                nouveauMdp.classList.remove('error');
                nouveauMdp2.classList.remove('error');
                passwordMessage.style.color = 'green';
                passwordMessage.textContent = 'Les deux mots de passe correspondent.';
                passwordMessage.style.display = 'block';  // Afficher le message
                submitBtn.disabled = false; // Activer le bouton
            } else {
                // Si les mots de passe ne correspondent pas
                nouveauMdp.classList.add('error');
                nouveauMdp2.classList.add('error');
                passwordMessage.style.color = 'red';
                passwordMessage.textContent = 'Les mots de passe ne correspondent pas.';
                passwordMessage.style.display = 'block';  // Afficher le message
                submitBtn.disabled = true; // Désactiver le bouton
            }
        }

        // Écouter les entrées pour vérifier les mots de passe en temps réel
        nouveauMdp.addEventListener('input', checkPasswords);
        nouveauMdp2.addEventListener('input', checkPasswords);
    });
</script>