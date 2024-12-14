<?php
session_start();
include("connect.inc.php");

// Récupérer l'ID client depuis la session
$id_client = $_SESSION['user']['IDCLIENT'];

// Traitement de la soumission du formulaire pour mettre à jour les infos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateInfo'])) {
    $nom = trim($_POST['NOMCLIENT']);
    $prenom = trim($_POST['PRENOMCLIENT']);
    $email = trim($_POST['EMAIL']);
    $numtel = trim($_POST['NUMTEL']);

    if ($nom && $prenom && $email && $numtel) {
        try {
            $updateStmt = $pdo->prepare("
                UPDATE CLIENT 
                SET NOMCLIENT = :nom, PRENOMCLIENT = :prenom, EMAIL = :email, NUMTEL = :numtel 
                WHERE IDCLIENT = :id_client
            ");
            $updateStmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'numtel' => $numtel,
                'id_client' => $id_client
            ]);

            header('Location: compte.php');
            exit();

        } catch (PDOException $e) {
            echo "Une erreur est survenue : " . $e->getMessage();
            exit();
        }
    } else {
        echo "Tous les champs doivent être remplis correctement.";
        exit();
    }
}

// Récupérer les informations du client pour remplir le formulaire
$stmt = $pdo->prepare("SELECT * FROM CLIENT WHERE IDCLIENT = :id_client");
$stmt->execute(['id_client' => $id_client]);
$user = $stmt->fetch();

if (!$user) {
    echo "Erreur : utilisateur introuvable.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/updateInfosClient.css">
    <link rel="stylesheet" href="Css/all.css">
    <title>Modifier les Informations</title>
</head>
<body>

    <!-- En-tête -->
    <?php include("header.php"); ?>


    <div class="container-update">
    <h2>Modifier vos informations</h2>

    <form action="updateInfosClient.php" method="POST">
        <label for="NOMCLIENT">Nom</label>
        <input id="NOMCLIENT" type="text" name="NOMCLIENT" value="<?= htmlspecialchars($user['NOMCLIENT']) ?>" required>

        <label for="PRENOMCLIENT">Prénom</label>
        <input id="PRENOMCLIENT" type="text" name="PRENOMCLIENT" value="<?= htmlspecialchars($user['PRENOMCLIENT']) ?>" required>

        <label for="EMAIL">Email</label>
        <input id="EMAIL" type="email" name="EMAIL" value="<?= htmlspecialchars($user['EMAIL']) ?>" required>

        <label for="NUMTEL">Téléphone</label>
        <input id="NUMTEL" type="tel" name="NUMTEL" value="<?= htmlspecialchars($user['NUMTEL']) ?>" required>

        <button type="submit" name="updateInfo">Modifier</button>
        <button type="button" onclick="window.location.href='compte.php'">Annuler</button>
    </form>
    </div>

</body>
</html>
