<?php
session_start();

// Chargement des indicatifs
$indicatifs = json_decode(file_get_contents('indicatifs.json'), true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("connect.inc.php");

    // Récupération des données
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    
    // Traitement du numéro de téléphone
    $numTel = null;
    if (!empty($_POST["numTel"])) {
        $indicatif = str_replace("+", "00", $_POST["indicatif"]);
        $numero = $indicatif . $_POST["numTel"];
        $numTel = strlen($numero) > 11 ? $numero : null;
    }
    
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $dtn = $_POST["dtn"];
    $password = $_POST["password"];
    $password2 = $_POST["password2"];

    // Vérification des mots de passe
    if ($password !== $password2) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\W)(?=.*\d)[A-Za-z\d\W]{8,}$/', $password)) {
        $error = "Le mot de passe doit comporter au moins 8 caractères, une majuscule, un chiffre, et un caractère spécial.";
    } else {
        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM CLIENT WHERE EMAIL = :email");
            $stmt->execute(["email" => $email]);
            $emailExists = $stmt->fetchColumn();

            if ($emailExists) {
                $error = "Un compte avec cet email existe déjà.";
            } else {

                $stmt = $pdo->query("SELECT MAX(IDCLIENT) AS maxIdClient FROM CLIENT");
                $maxIdClient = $stmt->fetchColumn();
                $newIdClient = $maxIdClient ? $maxIdClient + 1 : 1;


                // Insertion du nouveau client
                $stmt = $pdo->prepare("INSERT INTO CLIENT (IDCLIENT, NOMCLIENT, PRENOMCLIENT, NUMTEL, EMAIL, PASSWORD, DATEN, DATEINSCRIPTION) 
                                       VALUES (:idClient, :nom, :prenom, :telephone, :email, :password, :dtn, :dateInscription)");
                $stmt->execute([
                    "idClient" => $newIdClient,
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "telephone" => $numTel,
                    "email" => $email,
                    "password" => $hashedPassword,
                    "dtn" => $dtn,
                    "dateInscription" => date('Y-m-d'),
                ]);

                // Récupérer l'utilisateur nouvellement inscrit
                $stmt = $pdo->prepare("SELECT * FROM CLIENT WHERE EMAIL = :email");
                $stmt->execute(["email" => $email]);
                $user = $stmt->fetch();

                // Stocker l'utilisateur dans la session
                $_SESSION["user"] = $user;

                // Redirection vers la page compte
                header("Location: compte.php");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
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
    <style>
    .phone-input {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .phone-input .indicatif {
        width: 100px;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: white;
    }

    .phone-input .tel {
        flex: 1;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    </style>
</head>
<body>
    
    <!-- En-tête -->
    <?php include("header.php"); ?>
    
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
                        <div class="phone-input">
                            <select name="indicatif" id="indicatif" class="indicatif">
                                <?php foreach ($indicatifs as $indicatif): ?>
                                    <option value="<?= $indicatif['code'] ?>" 
                                            <?= (isset($_POST['indicatif']) && $_POST['indicatif'] === $indicatif['code']) || 
                                                (!isset($_POST['indicatif']) && $indicatif['code'] === '+33') ? 'selected' : '' ?>>
                                        <?= $indicatif['code'] ?> <?= $indicatif['pays'] ?> <?= $indicatif['emoji'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" id="numTel" name="numTel" class="tel">
                        </div>
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

    <!-- Déplacez le script à la fin du body -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const telInput = document.getElementById('numTel');
        if (telInput) {
            telInput.addEventListener('input', function(e) {
                // Ne garde que les chiffres
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Limite la longueur à 9 chiffres
                if (this.value.length > 9) {
                    this.value = this.value.slice(0, 9);
                }
            });
        }
    });
    </script>
</body>
</html>