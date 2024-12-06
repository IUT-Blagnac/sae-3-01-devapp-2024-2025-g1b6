<?php
session_start();

function verifierSession() {
    if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'OK') {
        header("Location: connexion.php?erreur=session");
        exit(); 
    }
}
?>