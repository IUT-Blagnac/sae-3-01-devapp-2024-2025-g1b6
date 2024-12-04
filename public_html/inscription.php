<?php
// Démarrer la session
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inclure la connexion à la base de données
    include("connect.inc.php");

    // Récupérer les données du formulaire
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $telephone = $_POST["telephone"];
    $email = $_POST["email"];
    $dtn = $_POST["dtn"];
    $password = $_POST["password"];
    $password2 = $_POST["password2"];

    // Vérifier que les mots de passe correspondent
    if ($password != $password2) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier la validité du mot de passe (min 8 caractères, une majuscule, un caractère spécial)
        if (!preg_match('/^(?=.*[A-Z])(?=.*\W)(?=.*\d)[A-Za-z\d\W]{8,}$/', $password)) {
            $error = "Le mot de passe doit comporter au moins 8 caractères, une majuscule, et un caractère spécial.";
        } else {
            // Hachage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                // Vérifier si l'email existe déjà dans la base de données
<<<<<<< HEAD
                $stmt = $pdo->prepare("SELECT * FROM CLIENT WHERE email = :email");
=======
                $stmt = $pdo->prepare("SELECT * FROM client WHERE email = :email");
>>>>>>> 9c63628609383197f7a62b91fbe22b2918e6e1d6
                $stmt->execute(["email" => $email]);
                $user = $stmt->fetch();

                if ($user) {
                    $error = "Un compte avec cet email existe déjà.";
                } else {
                    // Récupérer le idClient maximum dans la base de données
                    $stmt = $pdo->query("SELECT MAX(idClient) AS maxIdClient FROM client");
                    $maxIdClient = $stmt->fetchColumn();
                    $newIdClient = $maxIdClient ? $maxIdClient + 1 : 1; // Si aucun client, commence à 1

                    // Récupérer la date actuelle pour l'insertion
                    $dateInscription = date('Y-m-d');

                    // Préparer la requête d'insertion
                    $stmt = $pdo->prepare("INSERT INTO CLIENT (IDCLIENT, NOMCLIENT, PRENOMCLIENT, NUMTEL, EMAIL, PASSWORD, DATEN, DATEINSCRIPTION) 
                                        VALUES (:idClient, :nom, :prenom, :telephone, :email, :password, :dtn, :dateInscription)");

                    // Exécution de l'insertion
                    $stmt->execute([
                        "idClient" => $newIdClient,
                        "nom" => $nom,
                        "prenom" => $prenom,
                        "telephone" => $telephone,
                        "email" => $email,
                        "password" => $hashedPassword,
                        "dtn" => $dtn,
                        "dateInscription" => $dateInscription
                    ]);

                    // Redirection après succès
                    $_SESSION["user"] = [
                        "nom" => $nom,
                        "prenom" => $prenom,
                        "email" => $email
                    ];
                    header("Location: compte.php");
                    exit;
                }
            } catch (PDOException $e) {
                // Gérer l'exception pour la base de données
                $error = "Une erreur est survenue, veuillez réessayer plus tard.";
                // Optionnel : log de l'erreur dans un fichier de log
                error_log("Erreur PDO: " . $e->getMessage());
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/inscription.css">
    <link rel="stylesheet" href="Css/all.css">
    <title>Inscription</title>
</head>
<body>
    
<header class="header">
        <div class="barreMenu">
            <ul class="menuListe">
                <li> 
                    <label class="burger" for="burgerToggle">
                        <input type="checkbox" id="burgerToggle">
                        <ul class="categories">
                            <?php
                            include ("connect.inc.php");
                            $stmt = $pdo->prepare("SELECT * FROM CATEGORIE WHERE IDCATEG_CATPERE IS NULL");
                            $stmt->execute();
                            $categories = $stmt->fetchAll();
                            foreach ($categories as $categorie) {
                                echo "<li><a href='categorie.php?id=".$categorie['IDCATEG']."'>".$categorie['NOMCATEG']."</a></li>";
                            }
                            ?>
                        </ul>
                        <span></span>
                        <span></span>
                        <span></span>
                    </label> 
                </li>
                <li> <a class="lienAccueil" href="index.php"><h1 class="titreLudorama"> Ludorama </h1>  </a></li>
                <li> <input class="barreRecherche" type="text" placeholder="Barre de recherche ..."> </li>
                <li> <div class="imgLoc"></div> </li>
                <li> <a href="panier.php"><div class="imgPanier"></div></a></li>
                <li> <?php
                        // Vérification de la session utilisateur
                        if (isset($_SESSION["user"])) {
                            $id_client = $_SESSION["user"]["IDCLIENT"];
                            // Si l'utilisateur est connecté, on le redirige vers son compte
                            echo '<a href="compte.php?id_client=' . $id_client . '"><div class="imgCompte"></div></a>';
                        } else {
                            // Sinon, on le redirige vers la page de connexion
                            echo '<a href="connexion.php"><div class="imgCompte"></div></a>';
                        }
                    ?> 
                </li>
            </ul>
        </div>
    </header>
    
    <main>
        <div class="container">
            <div class="inscription">
                <h1 class="titreCompte">Créer un compte</h1>
                <div class="infos">
                    <form class="formInscription" action="inscription.php" method="post">
                        <!-- Affichage de l'erreur si elle existe -->
                        <?php if (isset($error)): ?>
                            <div class="error"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <label for="nom">Nom</label>
                        <input class="nom" type="text" id="nom" name="nom" value="<?= isset($nom) ? htmlspecialchars($nom) : '' ?>" required>

                        <label for="prenom">Prénom</label>
                        <input class="nom" type="text" id="prenom" name="prenom" value="<?= isset($prenom) ? htmlspecialchars($prenom) : '' ?>" required>

                        <label for="telephone">Téléphone</label>
                        <input class="tel" type="text" id="telephone" name="telephone" value="<?= isset($telephone) ? htmlspecialchars($telephone) : '' ?>" required>
                        <br>

                        <label for="email">Email</label>
                        <input class="mail" type="email" id="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                        <br>

                        <label for="dtn">Date de naissance</label>
                        <input class="dtn" type="date" id="dtn" name="dtn" value="<?= isset($dtn) ? htmlspecialchars($dtn) : '' ?>" required>
                        <br>

                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required>
                        <br>

                        <label for="password2">Confirmer le mot de passe</label>
                        <input type="password" id="password2" name="password2" required>

                        <button type="submit">Créer un compte</button>
                    </form>
                </div>

                <div class="seConnecter">
                    <p>Vous avez déjà un compte ?</p>
                    <a href="connexion.php">Se connecter</a>
                </div>
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