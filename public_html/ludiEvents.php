<?php
// Simuler une session utilisateur connectée
session_start();
$_SESSION['isLoggedIn'] = true; // Simulez la connexion pour tester
$_SESSION['email'] = 'user@example.com'; // Simulez une adresse e-mail

// Gérer les cinq événements
$events = [
    1 => 'Evènement Halloween',
    2 => 'Evènement Noël',
    3 => 'Evènement Paques',
    4 => 'Evènement LudoTheque',
    5 => 'evènement La Cotière'
];

// Récupérer l'ID de l'événement
$eventId = isset($_GET['idEvent']) ? (int)$_GET['idEvent'] : null;

// Vérifier si l'événement est valide
if (!$eventId || !isset($events[$eventId])) {
    echo "<p>Événement non trouvé.</p>";
    exit;
}

$eventName = $events[$eventId];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participation à un événement</title>
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/ludiEvents.css">
    <style>
        
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <div class="containerEvent">
        <h1>Participation à : <?= htmlspecialchars($eventName) ?></h1>

        <?php if (isset($_SESSION["isLoggedIn"])): ?>
            <form id="participationForm">
                <label for="participants">Nombre de participants :</label>
                <select name="participants" id="participants" required>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <button type="button" id="participerButton">Participer</button>
            </form>
        <?php else: ?>
            <p>Veuillez vous connecter pour participer à cet événement.</p>
        <?php endif; ?>
    </div>
</main>

<!-- Modale -->
<div id="qrModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Votre QR Code pour <?= htmlspecialchars($eventName) ?></h2>
        <img id="qrCodeImage" src="" alt="QR Code">
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("qrModal");
        const qrCodeImage = document.getElementById("qrCodeImage");
        const closeBtn = document.querySelector(".close");
        const participerButton = document.getElementById("participerButton");

        // Récupérer l'ID de l'événement
        const eventId = <?= json_encode($eventId) ?>;

        // Afficher la modale au clic sur "Participer"
        participerButton.addEventListener("click", function () {
            qrCodeImage.src = "./images/QREvent" + eventId + ".png";    
            modal.style.display = "block";
        });

        // Fermer la modale au clic sur "x"
        closeBtn.addEventListener("click", function () {
            modal.style.display = "none";
        });

        // Fermer la modale si on clique en dehors
        window.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    });
</script>

</body>
</html>
