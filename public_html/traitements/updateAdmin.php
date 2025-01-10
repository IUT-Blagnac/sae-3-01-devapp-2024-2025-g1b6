<?php
session_start();
include("../connect.inc.php");

// Vérifier si l'admin est connecté
if (!isset($_SESSION["admin"])) {
    header("Location: ../connexion.php");
    exit();
}

$typeModif = isset($_GET['typeModif']) ? $_GET['typeModif'] : null;

switch($typeModif) {
    // Modification de l'email
    case '1':
        if (isset($_POST['email'])) {
            $newEmail = trim($_POST['email']);
            
            // Mettre à jour l'email
            $stmt = $pdo->prepare("UPDATE ADMINISTRATEUR SET EMAIL = :email");
            if ($stmt->execute(['email' => $newEmail])) {
                $_SESSION["admin"]["EMAIL"] = $newEmail;
                header("Location: ../gestionAdmin.php?modif=ok&typeModif=1");
            } else {
                header("Location: ../gestionAdmin.php?modif=error&typeModif=1");
            }
        }
        break;

    // Modification du mot de passe
    case '2':
        if (isset($_POST['ancien-mdp']) && isset($_POST['nouveau-mdp']) && isset($_POST['nouveau-mdp2'])) {
            $ancienMdp = $_POST['ancien-mdp'];
            $nouveauMdp = $_POST['nouveau-mdp'];
            $nouveauMdp2 = $_POST['nouveau-mdp2'];

            // Vérifier si les nouveaux mots de passe correspondent
            if ($nouveauMdp !== $nouveauMdp2) {
                header("Location: ../gestionAdmin.php?modif=error&typeModif=2");
                exit();
            }

            // Vérifier l'ancien mot de passe
            $stmt = $pdo->prepare("SELECT PASSWORD FROM ADMINISTRATEUR");
            $stmt->execute();
            $result = $stmt->fetch();

            if (!password_verify($ancienMdp, $result['PASSWORD'])) {
                header("Location: ../gestionAdmin.php?modif=error&typeModif=2&error=wrong_password");
                exit();
            }

            // Vérifier la complexité du nouveau mot de passe
            if (strlen($nouveauMdp) < 8 || 
                !preg_match("/[A-Z]/", $nouveauMdp) || 
                !preg_match("/[0-9]/", $nouveauMdp) || 
                !preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $nouveauMdp)) {
                header("Location: ../gestionAdmin.php?modif=error&typeModif=2");
                exit();
            }

            // Hasher et mettre à jour le nouveau mot de passe
            $hashedPassword = password_hash($nouveauMdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE ADMINISTRATEUR SET PASSWORD = :password");
            
            if ($stmt->execute(['password' => $hashedPassword])) {
                header("Location: ../gestionAdmin.php?modif=ok&typeModif=2");
            } else {
                header("Location: ../gestionAdmin.php?modif=error&typeModif=2");
            }
        }
        break;

    default:
        header("Location: ../gestionAdmin.php");
        break;
}
?> 