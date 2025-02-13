<?php
session_start();
ob_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations de Paiement - Ludorama</title>
    <link rel="stylesheet" href="Css/info_paiement.css">
    <link rel="stylesheet" href="Css/all.css">
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
                            $stmt = $pdo->prepare("SELECT DISTINCT C.IDCATEG, C.NOMCATEG
                                                    FROM CATEGORIE C 
                                                    JOIN CATPERE CP ON C.IDCATEG = CP.IDCATEG 
                                                WHERE CP.IDCATEG_PERE BETWEEN 11 AND 14;");
                            $stmt->execute();
                            $categories = $stmt->fetchAll();
                            foreach ($categories as $categorie) {
                                echo "<li><a href='traitCategorie.php?idCategPere=".$categorie['IDCATEG']."'>".$categorie['NOMCATEG']."</a>";
                                echo "<div class=\"containerSouscategories\"> <ul>";
                                $stmt = $pdo->prepare("SELECT DISTINCT C.IDCATEG, C.NOMCATEG
                                                       FROM CATEGORIE C JOIN CATPERE CP ON C.IDCATEG = CP.IDCATEG 
                                                       WHERE CP.IDCATEG_PERE = :IDCATPERE;");
                                $stmt->execute(["IDCATPERE" => $categorie['IDCATEG']]);
                                $sous_categories = $stmt->fetchAll();
                                foreach ($sous_categories as $sous_categorie) {
                                    echo "<a href='traitCategorie.php?idCateg=".$sous_categorie['IDCATEG']."&idCategPere=".$categorie['IDCATEG']."'> <li>".$sous_categorie['NOMCATEG']."</li></a>";

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

<?php 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyer les entrées avec trim()
    $cardNumber = trim($_POST['cardNumber']);
    $cardName = trim($_POST['cardName']);
    $expiryDate = trim($_POST['expiryDate']);
    $cvv = trim($_POST['cvv']);
    $idClient = intval($_SESSION['user']['IDCLIENT']);

    // Regex patterns
    $cardNumberPattern = '/^\d{16}$/';
    $cardNamePattern = '/^[A-Za-z\s]+$/';
    $expiryDatePattern = '/^(0[1-9]|1[0-2])\/?([2-9][5-9])$/';
    $cvvPattern = '/^\d{3}$/';

    // Validate inputs
    if (!preg_match($cardNumberPattern, $cardNumber)) {
        $error = 'Le numéro de carte doit contenir 16 chiffres.';
    } elseif (!preg_match($cardNamePattern, $cardName)) {
        $error = 'Le nom doit contenir uniquement des lettres et des espaces.';
    } elseif (!preg_match($expiryDatePattern, $expiryDate)) {
        $error = 'La date d\'expiration doit être au format MM/AA et respecter les contraintes spécifiées.';
    } elseif (!preg_match($cvvPattern, $cvv)) {
        $error = 'Le CVV doit contenir 3 chiffres.';
    } else {
        // Convert expiry date to YYYY-MM-DD format
        $expiryDateFormatted = convertExpiryDateToFullDate($expiryDate);

        try {
            // Commencer une transaction
            $pdo->beginTransaction();

            // Insérer les informations de paiement dans la table INFORMATIONPAIEMENT
            $stmt = $pdo->prepare("INSERT INTO INFORMATIONPAIEMENT (NUMCB, NOMCOMPLETCB, DATEEXP, CRYPTOGRAMME) VALUES (?, ?, ?, ?)");
            $stmt->execute([$cardNumber, $cardName, $expiryDateFormatted, $cvv]);

            // Insérer la relation entre le client et les informations de paiement dans la table POSSEDERIP
            $stmt = $pdo->prepare("INSERT INTO POSSEDERIP (NUMCB, IDCLIENT) VALUES (?, ?)");
            $stmt->execute([$cardNumber, $idClient]);

            // Valider la transaction
            $pdo->commit();

            // Rediriger vers la page de commande pour effacer les messages d'erreur
            header("Location: commande.php");
            exit();
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollBack();
            $error = "Erreur lors de l'ajout des informations de paiement : " . $e->getMessage();
        }
    }
}

// Fonction pour convertir MM/YY en YYYY-MM-DD
function convertExpiryDateToFullDate($expiryDate) {
    // Séparer MM et YY
    list($month, $year) = explode('/', $expiryDate);

    // Ajouter "20" au début de l'année (puisque l'année commence à partir de 2025 selon la regex)
    $year = '20' . $year;

    // Créer une date au premier jour du mois
    $date = new DateTime("$year-$month-01");

    // Obtenir le dernier jour du mois
    $lastDay = $date->format('t');

    // Retourner la date au format YYYY-MM-DD
    return "$year-$month-$lastDay";
}
?>

<div class="container">
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="info_paiement.php" method="post" onsubmit="return validateForm()">
        <div class="payment-method">
            <label for="cardNumber">Numéro de carte</label>
            <input type="text" id="cardNumber" name="cardNumber" maxlength="16" required>
        </div>
        <div class="payment-method">
            <label for="cardName">Nom sur la carte</label>
            <input type="text" id="cardName" name="cardName" required>
        </div>
        <div class="payment-method">
            <label for="expiryDate">Date d'expiration</label>
            <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/AA" maxlength="5" required>
        </div>
        <div class="payment-method">
            <label for="cvv">CVV</label>
            <input type="text" id="cvv" name="cvv" maxlength="3" required>
        </div>
        <button type="submit" class="btn validate">Ajouter</button>
    </form>
</div>

<script>
function validateForm() {
    const cardNumber = document.getElementById('cardNumber').value;
    const cardName = document.getElementById('cardName').value;
    const expiryDate = document.getElementById('expiryDate').value;
    const cvv = document.getElementById('cvv').value;

    const cardNumberPattern = /^\d{16}$/;
    const cardNamePattern = /^[A-Za-z\s]+$/;
    const expiryDatePattern = /^(0[1-9]|1[0-2])\/?([2-9][5-9])$/;
    const cvvPattern = /^\d{3}$/;

    if (!cardNumberPattern.test(cardNumber)) {
        alert('Le numéro de carte doit contenir 16 chiffres.');
        return false;
    }

    if (!cardNamePattern.test(cardName)) {
        alert('Le nom doit contenir uniquement des lettres et des espaces.');
        return false;
    }

    if (!expiryDatePattern.test(expiryDate)) {
        alert('La date d\'expiration doit être au format MM/AA et respecter les contraintes spécifiées.');
        return false;
    }

    if (!cvvPattern.test(cvv)) {
        alert('Le CVV doit contenir 3 chiffres.');
        return false;
    }

    return true;
}

document.getElementById('expiryDate').addEventListener('input', function(e) {
    let input = e.target.value;
    if (input.length === 2 && !input.includes('/')) {
        e.target.value = input + '/';
    }
});
</script>

</body>
</html>