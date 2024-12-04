<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["password"])) {
    include("connect.inc.php");

    $email = $_POST["email"];
    $password = $_POST["password"];
    $rememberMe = isset($_POST["remember_me"]);

    // Vérifier dans la table administrateur
    $stmt = $pdo->prepare("SELECT * FROM administrateur WHERE email = :email");
    $stmt->execute(["email" => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = $user;

        // Gestion des cookies "Se souvenir de moi"
        if ($rememberMe) {
            setcookie("email", $email, time() + (365 * 24 * 60 * 60)); // 1 an
            setcookie("password", $password, time() + (365 * 24 * 60 * 60));
        } else {
            setcookie("email", "", time() - 3600);
            setcookie("password", "", time() - 3600);
        }

        header("Location: admin.php");
        exit;
    }

    // Vérifier dans la table client
    $stmt = $pdo->prepare("SELECT * FROM client WHERE email = :email");
    $stmt->execute(["email" => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = $user;
        $id_client = $user["id_client"];

        // Gestion des cookies "Se souvenir de moi"
        if ($rememberMe) {
            setcookie("email", $email, time() + (365 * 24 * 60 * 60)); // 1 an
            setcookie("password", $password, time() + (365 * 24 * 60 * 60));
        } else {
            setcookie("email", "", time() - 3600);
            setcookie("password", "", time() - 3600);
        }

        header("Location: compte.php?id_client=$id_client");
        exit;
    }

    // Si aucune correspondance
    header("Location: connexion.php?error=1");
    exit;
}
?>
