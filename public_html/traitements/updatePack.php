<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["admin"])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

include("../connect.inc.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $idPack = $_POST["idPack"];
        $nom = $_POST["nom"];
        $description = $_POST["description"];
        $produits = isset($_POST["produits"]) ? $_POST["produits"] : [];

        // Début de la transaction
        $pdo->beginTransaction();

        // Mise à jour des informations du pack
        $query = $pdo->prepare("UPDATE PACK SET NOMPACK = ?, DESCPACK = ? WHERE IDPACK = ?");
        $query->execute([$nom, $description, $idPack]);

        // Suppression des anciennes associations
        $query = $pdo->prepare("DELETE FROM ASSOPACK WHERE IDPACK = ?");
        $query->execute([$idPack]);

        // Ajout des nouvelles associations
        if (!empty($produits)) {
            $query = $pdo->prepare("INSERT INTO ASSOPACK (IDPACK, IDPROD) VALUES (?, ?)");
            foreach ($produits as $idProd) {
                $query->execute([$idPack, $idProd]);
            }
        }

        $pdo->commit();
        echo json_encode(["success" => true, "message" => "Pack modifié avec succès"]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["success" => false, "message" => "Erreur lors de la modification : " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
} 