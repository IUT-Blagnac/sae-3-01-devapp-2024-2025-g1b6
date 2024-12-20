<?php
session_start();
require_once('../connect.inc.php');

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID de catégorie manquant');
    }

    $id = intval($_GET['id']);

    // Récupérer les informations de la catégorie
    $stmt = $pdo->prepare("
        SELECT 
            C.IDCATEG,
            C.NOMCATEG,
            C.DESCCATEG,
            CASE 
                WHEN C.IDCATEG IN (11, 12, 13, 14) THEN 'Mère'
                WHEN EXISTS (
                    SELECT 1 
                    FROM CATPERE CP 
                    WHERE CP.IDCATEG = C.IDCATEG 
                    AND CP.IDCATEG_PERE IN (11, 12, 13, 14)
                ) THEN 'Principale'
                ELSE 'Secondaire'
            END as NIVEAU
        FROM CATEGORIE C
        WHERE C.IDCATEG = ?
    ");
    $stmt->execute([$id]);
    $categorie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$categorie) {
        throw new Exception('Catégorie non trouvée');
    }

    // Récupérer les parents de la catégorie
    $stmt = $pdo->prepare("
        SELECT IDCATEG_PERE 
        FROM CATPERE 
        WHERE IDCATEG = ?
    ");
    $stmt->execute([$id]);
    $categorie['parents'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Récupérer toutes les catégories mères
    $stmtMeres = $pdo->query("
        SELECT IDCATEG, NOMCATEG 
        FROM CATEGORIE 
        WHERE IDCATEG IN (11, 12, 13, 14)
        ORDER BY NOMCATEG
    ");
    $categoriesMeres = $stmtMeres->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer toutes les catégories principales
    $stmtPrincipales = $pdo->query("
        SELECT DISTINCT C.IDCATEG, C.NOMCATEG
        FROM CATEGORIE C
        JOIN CATPERE CP ON C.IDCATEG = CP.IDCATEG
        WHERE CP.IDCATEG_PERE IN (11, 12, 13, 14)
        ORDER BY C.NOMCATEG
    ");
    $categoriesPrincipales = $stmtPrincipales->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'categorie' => $categorie,
        'categoriesMeres' => $categoriesMeres,
        'categoriesPrincipales' => $categoriesPrincipales
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 