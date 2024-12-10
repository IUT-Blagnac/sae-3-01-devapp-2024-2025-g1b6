<?php
session_start();
?>
<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="Css/admin.css">
    <link rel="stylesheet" href="Css/all.css">
</head>
<body>
<?php
include("header.php");
?>
<div class="menuAdmin">
    <ul>
        <a href=""><li>Dashboard</li></a>
        <a href=""><li>Produits</li></a>
        <a href=""><li>Catégories</li></a>
        <a href=""><li>Commandes</li></a>
        <a href=""><li>Clients</li></a>
        <a href=""><li>Paramètres</li></a>
        <a href=""><li>Déconnexion</li></a>
    </ul>
</div>

<main>
    <div class="containerDashboard">
        <div class="listeStat">
            <ul>
                <li><div></div></li>
                <li><div></div></li>
                <li><div></div></li>
                <li><div></div></li>
            </ul>
        </div>

    </div>

</main>



<?php
include("footer.php");
?>
</body>