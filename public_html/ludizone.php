<?php
session_start();
include("connect.inc.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/ludizone.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>LudiZone</title>
</head>

<body>

<?php
include("header.php");
?>

<div class="principale">

    <div class="filtres">
        <h1>Filtres</h1>
        <ul class="listFiltres">
            <li class="liFiltres">Prix</li>
            <li class="liFiltres">Marques</li>
            <li class="liFiltres">Catégorie</li>
            <li class="liFiltres">Avis</li>
        </ul>
    </div>

    <div class="ludizone">
        <h1 class="titreLudizone">Bienvenue dans la LudiZone !</h1>
        
        <div class="coupDeCoeur">
            <h1 class="titreCDC">Coup de coeur</h1>
            
            <ul class="listeCDC">
                <?php
                // Sélectionner les produits de la catégorie enfant (ID = 20)
                try {
                    $stmt = $pdo->prepare("
                        SELECT p.*, 
                               m.NOMMARQUE,
                               GROUP_CONCAT(c.NOMCATEG SEPARATOR ', ') AS CATEGORIES
                        FROM PRODUIT p
                        LEFT JOIN MARQUE m ON p.IDMARQUE = m.IDMARQUE
                        LEFT JOIN APPARTENIRCATEG ac ON p.IDPROD = ac.IDPROD
                        LEFT JOIN CATEGORIE c ON ac.IDCATEG = c.IDCATEG
                        WHERE ac.IDCATEG = ?
                        GROUP BY p.IDPROD
                    ");
                    $stmt->execute([20]); // Catégorie Enfant (ID 20)
                    $produits = $stmt->fetchAll();
                } catch (PDOException $e) {
                    echo "Erreur lors de la récupération des produits : " . $e->getMessage();
                    exit();
                }

                // Affichage des produits
                foreach ($produits as $produit): ?>
                    <li class="produit-item">
                        <div class="produit-card">
                            <!-- Image -->
                            <div class="produit-image">
                                <img src="./images/<?= htmlspecialchars($produit['IDPROD']) ?>.jpg" alt="<?= htmlspecialchars($produit['NOMPROD']) ?>">
                            </div>
                            <!-- Infos produit -->
                            <div class="produit-info">
                                <h2 class="produit-nom"><?= htmlspecialchars($produit['NOMPROD']) ?></h2>
                                <p class="produit-prix"><?= number_format($produit['PRIXHT'], 2, ',', ' ') ?> €</p>
                                <p class="produit-categories">Catégories : <?= htmlspecialchars($produit['CATEGORIES']) ?></p>
                                <a href="descProduit.php?idProd=<?= htmlspecialchars($produit['IDPROD']) ?>" class="produit-lien">Voir le produit</a>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="ludiGames">
            <h1 class="titreLudiGame">Ludi'Games</h1>
            <div class="imgLudiGame"></div>
        </div>

        <div class="ludiEvent">
            <h1 class="titreLudiEvent">Ludi'Events</h1>
            <ul class="listeEvent">
                <li><div class="Event"><div class="imgEvent1"></div><button class="btnParticipe">J'y participe</button></div></li>
                <li><div class="Event"><div class="imgEvent2"></div><button class="btnParticipe">J'y participe</button></div></li>
                <li><div class="Event"><div class="imgEvent3"></div><button class="btnParticipe">J'y participe</button></div></li>
                <li><div class="Event"><div class="imgEvent4"></div><button class="btnParticipe">J'y participe</button></div></li>
                <li><div class="Event"><div class="imgEvent5"></div><button class="btnParticipe">J'y participe</button></div></li>
            </ul>
        </div>

        <div class="vide"></div>

    </div>
</div>  

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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const burgerToggle = document.getElementById('burgerToggle');
        const categoriesMenu = document.querySelector('.categories');

        // Fermer le menu déroulant si on clique ailleurs
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.burger')) {
                burgerToggle.checked = false; // Décocher la checkbox pour fermer le menu
            }
        });

        // Empêcher le clic sur le menu burger de se propager
        burgerToggle.addEventListener('click', (event) => {
            event.stopPropagation();
        });
    });
</script>

</body>
</html>
