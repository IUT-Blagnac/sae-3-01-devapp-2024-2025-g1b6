<?php
    session_start();
    include("connect.inc.php");

    // Vérifier si l'admin est connecté
    if (!isset($_SESSION["admin"])) {
        header("Location: connexion.php");
        exit();
    }

    header("Cache-Control: no-cache, must-revalidate");

    // Récupérer les informations de l'administrateur
    $stmt = $pdo->prepare("SELECT * FROM ADMINISTRATEUR");
    $stmt->execute();
    $admin = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/toasts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <title>Gestion Admin</title>
    <style>
        /* Variables globales */
        :root {
            --dark-purple: #6d00b0;
            --clair-purple: rgba(109, 0, 176, 0.15);
            --medium-purple: #8A2BE2;
            --light-purple: #f8f5fb;
            --white: #FFFFFF;   
            --light-gray: #F0F0F0;
            --text-dark: #2c3e50;
            --shadow: 0 4px 6px rgba(109, 0, 176, 0.1);
        }

        /* Styles spécifiques pour la page admin */
        .dashboard-container {
            display: flex;
            gap: 30px;
            padding: 20px;
            min-height: calc(100vh - 11em);
            width: 100%;
            box-sizing: border-box;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .info-admin {
            background-color: var(--white);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--light-gray);
            margin: 20px 0;
            width: 100%;
        }

        .infosCompte {
            background-color: var(--light-purple);
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            border: 1px solid var(--light-gray);
            width: 95%;
        }

        .info {
            margin: 10px 0;
            padding: 10px 0;
            color: var(--text-dark);
            font-size: 1.1rem;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info i {
            color: var(--dark-purple);
            width: 20px;
            text-align: center;
        }

        h2 {
            color: var(--dark-purple);
            text-align: center;
            margin-bottom: 25px;
            font-size: 1.8rem;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }

        .modal-btn {
            background: linear-gradient(135deg, var(--medium-purple), var(--dark-purple));
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 2px 5px rgba(109, 0, 176, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(109, 0, 176, 0.3);
        }

        /* Styles des modales */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow-y: auto;
        }

        .modal-content {
            background-color: var(--white);
            margin: 15vh auto;
            padding: 25px;
            border-radius: 10px;
            max-width: 500px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 28px;
            cursor: pointer;
            color: #aaa;
            line-height: 20px;
        }

        .close:hover {
            color: var(--text-dark);
        }

        /* Styles des formulaires */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        form label {
            font-weight: 500;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        form label i {
            color: var(--dark-purple);
        }

        form input {
            padding: 12px;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            font-size: 1rem;
        }

        form button {
            background: linear-gradient(135deg, var(--medium-purple), var(--dark-purple));
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        form button:disabled {
            background: var(--light-gray);
            cursor: not-allowed;
        }

        #password-message {
            padding: 10px;
            border-radius: 4px;
            margin-top: 5px;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
                padding: 15px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .modal-content {
                width: 90%;
                margin: 10vh auto;
            }
        }

        @media (max-width: 480px) {
            .dashboard-container {
                padding: 10px;
            }

            .info-admin {
                padding: 15px;
            }
        }

        .body {
            display: flex;
            width: 100%;
        }

        main {
            width: 90%;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <?php include("header.php"); ?>

    <div class="body">
        <main>
            <div class="dashboard-container">
                <?php 
                    $currentPage = 'gestion-admin';
                    include("includes/adminSidebar.php"); 
                ?>

                <div class="main-content">
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
                                            'Votre adresse email a été mise à jour avec succès.' : 
                                            'Une erreur est survenue lors de la mise à jour de votre email.';
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

                    <div class="info-admin">
                        <h2>Mes informations administrateur</h2>
                        <div class="infosCompte">
                            <div class="infosPerso">
                                <div class="infos">
                                    <p class="info"><i class="fas fa-envelope"></i>Email : <?= isset($admin['EMAIL']) ? htmlspecialchars($admin['EMAIL']) : 'Non défini' ?></p>
                                </div>
                            </div>
                        </div>    
                        <div class="action-buttons">
                            <button class="modal-btn modifier-email"><i class="fas fa-envelope"></i>Modifier Email</button>
                            <button class="modal-btn modifier-mdp"><i class="fas fa-key"></i>Modifier Mot de passe</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal pour modifier l'email -->
            <div id="modal-modifier-email" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Modifier mon Email</h2>
                    <form method="post" action="traitements/updateAdmin.php?typeModif=1">
                        <label for="email"><i class="fas fa-envelope"></i>Nouvel email :</label>
                        <input type="email" id="email" name="email" required maxlength="150">
                        <button type="submit" id="submitBtn-email"><i class="fas fa-check"></i>Modifier</button>
                    </form>
                </div>
            </div>

            <!-- Modal pour modifier le mot de passe -->
            <div id="modal-modifier-mdp" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Modifier mon Mot de Passe</h2>
                    <form method="post" action="traitements/updateAdmin.php?typeModif=2">
                        <label for="ancien-mdp"><i class="fas fa-lock"></i>Ancien mot de passe:</label>
                        <input type="password" id="ancien-mdp" name="ancien-mdp" required>
                        <label for="nouveau-mdp"><i class="fas fa-key"></i>Nouveau mot de passe:</label>
                        <input type="password" id="nouveau-mdp" name="nouveau-mdp" required>
                        <label for="nouveau-mdp2"><i class="fas fa-check-double"></i>Confirmer le nouveau mot de passe:</label>
                        <input type="password" id="nouveau-mdp2" name="nouveau-mdp2" required>
                        <div id="password-message" style="color: red; display: none;"></div>
                        <button type="submit" id="changer-mdp"><i class="fas fa-check"></i>Modifier</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <?php include("footer.php"); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des modales
            const modals = {
                email: {
                    modal: document.getElementById('modal-modifier-email'),
                    btn: document.querySelector('.modifier-email')
                },
                mdp: {
                    modal: document.getElementById('modal-modifier-mdp'),
                    btn: document.querySelector('.modifier-mdp')
                }
            };

            // Ouvrir les modales
            Object.values(modals).forEach(({modal, btn}) => {
                btn.addEventListener('click', () => modal.style.display = 'block');
            });

            // Fermer les modales
            document.querySelectorAll('.close').forEach(closeBtn => {
                closeBtn.addEventListener('click', () => {
                    closeBtn.closest('.modal').style.display = 'none';
                });
            });

            // Fermer les modales en cliquant en dehors
            window.addEventListener('click', (e) => {
                Object.values(modals).forEach(({modal}) => {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });

            // Validation du mot de passe
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
                
                if (!validation.isValid) {
                    passwordMessage.style.color = 'red';
                    passwordMessage.textContent = getErrorMessage(validation.errors);
                    passwordMessage.style.display = 'block';
                    submitBtn.disabled = true;
                    return;
                }
                
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

            nouveauMdp.addEventListener('input', checkPasswords);
            nouveauMdp2.addEventListener('input', checkPasswords);
        });
    </script>
</body>
</html> 