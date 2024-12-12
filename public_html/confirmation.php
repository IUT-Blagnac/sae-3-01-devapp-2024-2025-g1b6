<?php
session_start();
include("connect.inc.php");

if (!isset($_GET['commande'])) {
    echo "Numéro de commande manquant.";
    exit();
}

$numCommande = intval($_GET['commande']);

// Récupérer les informations de la commande
$stmt = $pdo->prepare("
    SELECT c.NUMCOMMANDE, c.DATECOMMANDE, c.STATUTLIVRAISON, c.CODESUIVI, c.TOTALCOMMANDE,
           a.NUMRUE, a.NOMRUE, a.COMPLEMENTADR, a.NOMVILLE, a.CODEPOSTAL, a.PAYS,
           t.TYPEEXP
    FROM COMMANDE c
    JOIN ADRESSE a ON c.IDADRESSE = a.IDADRESSE
    JOIN TRANSPORTEUR t ON c.IDTRANSPORTEUR = t.IDTRANSPORTEUR
    WHERE c.NUMCOMMANDE = ?
");
$stmt->execute([$numCommande]);
$commande = $stmt->fetch();

if (!$commande) {
    echo "Commande non trouvée.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - Ludorama</title>
    <link rel="stylesheet" href="Css/confirmation.css">
    <link rel="stylesheet" href="Css/all.css">
</head>
<body>

<header class="header">
    <div class="barreMenu">
        <ul class="menuListe">
            <li style="flex: 1;"></li> <!-- Espace vide pour centrer le titre -->
            <li>
                <a class="lienAccueil" href="index.php">
                    <h1 class="titreLudorama">Ludorama</h1>
                </a>
            </li>
            <li style="flex: 1; display: flex; justify-content: flex-end;">
                <?php
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

<h1>Merci pour votre commande !</h1>
<p>Votre paiement a été traité avec succès. Vous recevrez bientôt un email de confirmation.</p>

<h2>Détails de la commande</h2>
<p><strong>Prix de la commande :</strong> <?php echo htmlspecialchars($commande['TOTALCOMMANDE']); ?></p>
<p><strong>Numéro de commande :</strong> <?php echo htmlspecialchars($commande['NUMCOMMANDE']); ?></p>
<p><strong>Date de commande :</strong> <?php echo htmlspecialchars($commande['DATECOMMANDE']); ?></p>
<p><strong>Statut de livraison :</strong> <?php echo htmlspecialchars($commande['STATUTLIVRAISON']); ?></p>
<p><strong>Code de suivi :</strong> <?php echo htmlspecialchars($commande['CODESUIVI']); ?></p>

<h2>Adresse de livraison</h2>
<p><?php echo htmlspecialchars($commande['NUMRUE']) . ' ' . htmlspecialchars($commande['NOMRUE']); ?></p>
<p><?php echo htmlspecialchars($commande['COMPLEMENTADR']); ?></p>
<p><?php echo htmlspecialchars($commande['NOMVILLE']) . ', ' . htmlspecialchars($commande['CODEPOSTAL']) . ', ' . htmlspecialchars($commande['PAYS']); ?></p>

<h2>Transporteur</h2>
<p><?php echo htmlspecialchars($commande['TYPEEXP']); ?></p>

<?php include("footer.php"); ?>
</body>
</html>