<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["password"])) {
    include ("connect.inc.php");
    $email = $_POST["email"];
    $password = $_POST["password"];


    $stmt = $pdo->prepare("SELECT * FROM administrateur WHERE email = :email AND password = :password");
    $stmt->execute(["email" => $email, "password" => $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION["user"] = $user;
        header("Location: admin.php");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM client WHERE email = :email AND password = :password");
        $stmt->execute(["email" => $email, "password" => $password]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION["user"] = $user;
            $id_client = $user["id_client"];
            header("Location: compte.php?id_client=$id_client");
        } else {
            header("Location: connexion.php?error=1");
        }
    }



}



?>