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
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/compte.css">
    <title>Compte</title>
</head>
<body>
    
    <!-- En-tête -->
    <?php include("header.php"); 
        // Préparer la requête pour récupérer les informations du client
        $stmt = $pdo->prepare("SELECT * FROM CLIENT WHERE IDCLIENT = :id_client");
        $stmt->execute(['id_client' => $id_client]);
        $user = $stmt->fetch();
        var_dump($user);
    ?>

    <main>
        <div>
                <!--<div class="compte">
                <!--    <h1 class="titreCompte">Mon compte</h1>
                <!--    <div class="infosCompte">
                <!--        <div class="infosPerso">
                <!--            <div class="infos">
                <!--                <p class="info">Nom : <?= isset($user['NOMCLIENT']) ? htmlspecialchars($user['NOMCLIENT']) : 'Non défini' ?></p>
                <!--                <p class="info">Prenom : <?= isset($user['PRENOMCLIENT']) ? htmlspecialchars($user['PRENOMCLIENT']) : 'Non défini' ?></p>
                <!--                <p class="info">email : <?= isset($user['EMAIL']) ? htmlspecialchars($user['EMAIL']) : 'Non défini' ?></p>
                <!--                <?php if (!empty($user['NUMTEL'])):?><p class="info">Téléphone : <?= isset($user['NUMTEL']) ? htmlspecialchars($user['NUMTEL']) : 'Non défini'; endif;?></p>
                <!--            </div>
                <!--        </div>
                <!--    </div>
                <!--    
                <!--     <a href="updateInfosClient.php"><button class="modifier" type="submit" name="updateInfo">Modifier</button></a>  
                <!--</div> !-->
        <div>
            <div class="adresse">

            </div>

            <div class="cartes">

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


</body>
</html>