<?php
session_start();
include("connect.inc.php");

if (isset($_POST['updateInfo'])) {
    $id_client = $_SESSION['user']['IDCLIENT'];
    $nom = $_POST['NOMCLIENT'];
    $prenom = $_POST['PRENOMCLIENT'];
    $email = $_POST['EMAIL'];
    $numtel = $_POST['NUMTEL'];

    try {
        $stmt = $pdo->prepare("UPDATE CLIENT SET NOMCLIENT = :nom, PRENOMCLIENT = :prenom, EMAIL = :email, NUMTEL = :numtel WHERE IDCLIENT = :id");
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':numtel' => $numtel,
            ':id' => $id_client
        ]);

        $_SESSION['user']['NOMCLIENT'] = $nom;
        $_SESSION['user']['PRENOMCLIENT'] = $prenom;
        $_SESSION['user']['EMAIL'] = $email;
        $_SESSION['user']['NUMTEL'] = $numtel;

        header("Location: compte.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
