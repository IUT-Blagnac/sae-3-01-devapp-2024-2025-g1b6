<?php
session_start();
include("./../connect.inc.php");

if (!isset($_SESSION["admin"])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

header('Content-Type: application/json');

if (!isset($_SESSION["admin"])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID du pack non fourni');
    }

    $idPack = $_GET['id'];

    // Récupérer les informations du pack
    $query = $pdo->prepare("
        SELECT 
            P.*,
            GROUP_CONCAT(AP.IDPROD) as PRODUITS
        FROM PACK P
        LEFT JOIN ASSOPACK AP ON P.IDPACK = AP.IDPACK
        WHERE P.IDPACK = ?
        GROUP BY P.IDPACK
    ");
    $query->execute([$idPack]);
    $pack = $query->fetch(PDO::FETCH_ASSOC);

    if (!$pack) {
        throw new Exception('Pack non trouvé');
    }

    // Récupérer la liste de tous les produits disponibles
    $queryProduits = $pdo->prepare("
        SELECT IDPROD, NOMPROD, PRIXHT 
        FROM PRODUIT 
        WHERE DISPONIBLE = 1 
        ORDER BY NOMPROD
    ");
    $queryProduits->execute();
    $produits = $queryProduits->fetchAll(PDO::FETCH_ASSOC);

    // Préparer la réponse
    $response = [
        'pack' => $pack,
        'produits_disponibles' => $produits
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
} 