<?php
if (mail("votre-email@test.com", "Test Mail", "Test mail fonctionnel")) {
    echo "Email envoyé avec succès.";
} else {
    echo "Échec de l'envoi de l'email.";
}
?>