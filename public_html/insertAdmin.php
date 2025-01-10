<?php
include("connect.inc.php");

// Remplissez ces champs avec les données de l'administrateur
$email = "marwane.ibrahim@etu.univ-tlse2.fr"; // Remplacez par l'email souhaité
$password = "adminpassword"; // Remplacez par le mot de passe souhaité

try {
    // Hacher le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Préparer la requête d'insertion
    $stmt = $pdo->prepare("INSERT INTO ADMINISTRATEUR (EMAIL, PASSWORD) VALUES (:email, :password)");
    $stmt->execute([
        ":email" => $email,
        ":password" => $hashedPassword,
    ]);

    echo "Administrateur ajouté avec succès.";
} catch (PDOException $e) {
    echo "Erreur lors de l'ajout de l'administrateur : " . $e->getMessage();
}
?>
