<?php
// Fichier: Connect.inc.php


// Informations de connexion
$host = 'localhost';  // Serveur local
$dbname = 'R2024MYSAE3012';  // Nom de la base de données
$user = 'R2024MYSAE3012';  
$pass = '3cg3xV23y6EeYB';  


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);


    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>