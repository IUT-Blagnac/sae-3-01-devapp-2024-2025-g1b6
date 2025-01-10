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
        // Validation des données reçues
        if (!isset($_POST["nom"]) || empty(trim($_POST["nom"]))) {
            throw new Exception("Le nom du pack est requis");
        }

        $nom = trim($_POST["nom"]);
        $description = isset($_POST["description"]) ? trim($_POST["description"]) : '';
        $produits = isset($_POST["produits"]) ? $_POST["produits"] : [];

        // Validation des données
        if (strlen($nom) > 30) {
            throw new Exception("Le nom du pack doit contenir au maximum 30 caractères");
        }

        if (strlen($description) > 150) {
            throw new Exception("La description ne peut pas dépasser 150 caractères");
        }

        if (count($produits) < 2) {
            throw new Exception("Veuillez sélectionner au moins deux produits pour le pack");
        }

        // Vérification que les produits existent et calcul du prix total
        $placeholders = str_repeat('?,', count($produits) - 1) . '?';
        $query = $pdo->prepare("
            SELECT SUM(PRIXHT) as total_price, COUNT(*) as count 
            FROM PRODUIT 
            WHERE IDPROD IN ($placeholders) AND DISPONIBLE = 1
        ");
        $query->execute($produits);
        $result = $query->fetch();

        if ($result['count'] !== count($produits)) {
            throw new Exception("Certains produits sélectionnés ne sont pas disponibles");
        }

        // Calcul du prix du pack (prix total - 15%)
        $prixPack = round($result['total_price'] * 0.85, 2);

        // Début de la transaction
        $pdo->beginTransaction();

        // Insertion du pack avec le prix
        $query = $pdo->prepare("INSERT INTO PACK (NOMPACK, DESCPACK, PRIXPACK) VALUES (?, ?, ?)");
        $query->execute([$nom, $description, $prixPack]);
        $packId = $pdo->lastInsertId();

        // Insertion des associations produits-pack
        $query = $pdo->prepare("INSERT INTO ASSOPACK (IDPACK, IDPROD) VALUES (?, ?)");
        foreach ($produits as $produitId) {
            $query->execute([$packId, $produitId]);
        }

        $pdo->commit();
        echo json_encode([
            "success" => true, 
            "message" => "Pack ajouté avec succès",
            "packId" => $packId
        ]);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(400);
        echo json_encode([
            "success" => false, 
            "message" => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false, 
        "message" => "Méthode non autorisée"
    ]);
} 