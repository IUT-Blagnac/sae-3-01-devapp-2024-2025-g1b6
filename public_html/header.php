<header class="header">
        <div class="barreMenu">
            <ul class="menuListe">
                <li> 
                    <label class="burger" for="burgerToggle">
                        <input type="checkbox" id="burgerToggle">
                        <ul class="categories">
                        <?php
                            include ("connect.inc.php");
                            $stmt = $pdo->prepare("SELECT DISTINCT C.IDCATEG, C.NOMCATEG
                                                    FROM CATEGORIE C 
                                                    JOIN CATPERE CP ON C.IDCATEG = CP.IDCATEG 
                                                WHERE CP.IDCATEG_PERE BETWEEN 11 AND 14;");
                            $stmt->execute();
                            $categories = $stmt->fetchAll();
                            foreach ($categories as $categorie) {
                                echo "<li><a href='traitCategorie.php?id=".$categorie['IDCATEG']."'>".$categorie['NOMCATEG']."</a>";
                                echo "<div class=\"containerSouscategories\"> <ul>";
                                $stmt = $pdo->prepare("SELECT DISTINCT C.IDCATEG, C.NOMCATEG
                                                       FROM CATEGORIE C JOIN CATPERE CP ON C.IDCATEG = CP.IDCATEG 
                                                       WHERE CP.IDCATEG_PERE = :IDCATPERE;");
                                $stmt->execute(["IDCATPERE" => $categorie['IDCATEG']]);
                                $sous_categories = $stmt->fetchAll();
                                foreach ($sous_categories as $sous_categorie) {
                                    echo "<li><a href='traitCategorie.php?idCateg=".$sous_categorie['IDCATEG']."&idCategPere=".$categorie['IDCATEG']."'>".$sous_categorie['NOMCATEG']."</a></li>";

                                }
                                echo "</ul> </div>";
                                echo "</li>";
                                
                            }
                            ?>
                        </ul>
                        <span></span>
                        <span></span>
                        <span></span>
                    </label> 
                </li>
                <li> <a class="lienAccueil" href="index.php"><h1 class="titreLudorama"> Ludorama </h1>  </a></li>
                <li>
                    <form method="GET" action="recherche.php">
                        <input type="text" class="barreRecherche" name="mot_cle" placeholder="Rechercher...">
                    </form>
                </li>
                <li> <div class="imgLoc"></div> </li>
                <li> <a href="panier.php"><div class="imgPanier"></div></a></li>
                <li> <?php
                        // Vérification de la session utilisateur
                        if (isset($_SESSION["user"])) {
                            $id_client = $_SESSION["user"]["IDCLIENT"];
                            // Si l'utilisateur est connecté, on le redirige vers son compte
                            echo '<a href="compte.php"><div class="imgCompte"></div></a>';
                        } else {
                            // Sinon, on le redirige vers la page de connexion
                            echo '<a href="connexion.php"><div class="imgCompte"></div></a>';
                        }
                    ?> 
                </li>
            </ul>
        </div>
    </header>