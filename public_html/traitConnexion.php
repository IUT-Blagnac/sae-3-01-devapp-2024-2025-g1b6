<?php
session_start();

if (empty($_POST["EMAIL"]) || empty($_POST["PASSWORD"])) {
    echo "Email ou mot de passe manquant.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["EMAIL"]) && isset($_POST["PASSWORD"])) {
    include("connect.inc.php");

    $email = $_POST["EMAIL"];
    $PASSWORD = $_POST["PASSWORD"];
    $rememberMe = isset($_POST["remember_me"]);

    try {
        // Vérifier dans la table administrateur
        $stmt = $pdo->prepare("SELECT * FROM ADMINISTRATEUR WHERE EMAIL = :EMAIL");
        $stmt->execute(["EMAIL" => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($PASSWORD, $user["PASSWORD"])) {
            error_log("Utilisateur trouvé : " . print_r($user, true));
            $_SESSION["user"] = $user;

            // Gestion des cookies "Se souvenir de moi"
            if ($rememberMe) {
                setcookie("EMAIL", $email, time() + (365 * 24 * 60 * 60)); // 1 an
                setcookie("PASSWORD", $PASSWORD, time() + (365 * 24 * 60 * 60));
            } else {
                setcookie("EMAIL", "", time() - 3600);
                setcookie("PASSWORD", "", time() - 3600);
            }

            header("Location: admin.php");
            exit;
        }

        // Vérifier dans la table client
        $stmt = $pdo->prepare("SELECT * FROM CLIENT WHERE EMAIL = :EMAIL");
        $stmt->execute(["EMAIL" => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($PASSWORD, $user["PASSWORD"])) {
            error_log("Utilisateur trouvé : " . print_r($user, true));
            $_SESSION["user"] = $user;
            $id_client = $user["IDCLIENT"];

            // Gestion des cookies "Se souvenir de moi"
            if ($rememberMe) {
                setcookie("EMAIL", $email, time() + (365 * 24 * 60 * 60)); // 1 an
                setcookie("PASSWORD", $PASSWORD, time() + (365 * 24 * 60 * 60));
            } else {
                setcookie("EMAIL", "", time() - 3600);
                setcookie("PASSWORD", "", time() - 3600);
            }

            header("Location: compte.php");
            exit;
        }

        // Si aucune correspondance, redirection avec message d'erreur
        header("Location: connexion.php?error=1");
        exit;

    } catch (PDOException $e) {
        // En cas d'erreur de base de données
        header("Location: connexion.php?error=2");
        exit;
    }


} else {
    // Redirection vers la page de connexion
    header("Location: connexion.php");
    exit;
}   
?>
