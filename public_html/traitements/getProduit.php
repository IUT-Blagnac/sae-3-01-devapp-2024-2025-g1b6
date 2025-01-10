<?php
include("./../connect.inc.php");
header('Content-Type: application/json');

if (!isset($_SESSION["admin"])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Récupération des informations du produit
        $query = $pdo->prepare("
            SELECT 
                P.*,
                M.NOMMARQUE,
                GROUP_CONCAT(DISTINCT AC.IDCATEG) as CATEGORIES
            FROM PRODUIT P
            LEFT JOIN MARQUE M ON P.IDMARQUE = M.IDMARQUE
            LEFT JOIN APPARTENIRCATEG AC ON P.IDPROD = AC.IDPROD
            WHERE P.IDPROD = ?
            GROUP BY P.IDPROD
        ");
        $query->execute([$id]);
        $produit = $query->fetch(PDO::FETCH_ASSOC);

        // Récupération de la catégorie principale
        $queryMainCat = $pdo->prepare("
            SELECT DISTINCT C.IDCATEG, C.NOMCATEG
            FROM CATEGORIE C
            JOIN APPARTENIRCATEG AC ON C.IDCATEG = AC.IDCATEG
            LEFT JOIN CATPERE ON C.IDCATEG = CATPERE.IDCATEG
            WHERE AC.IDPROD = ? 
            AND C.IDCATEG NOT IN (11, 12, 13, 14)
            AND NOT EXISTS (
                SELECT 1 
                FROM CATPERE 
                WHERE IDCATEG = C.IDCATEG 
                AND IDCATEG_PERE NOT IN (11, 12, 13, 14)
            )
        ");
        $queryMainCat->execute([$id]);
        $mainCategory = $queryMainCat->fetch(PDO::FETCH_ASSOC);
        $queryMainCat->closeCursor();
        // Récupération des catégories secondaires
        $querySecCat = $pdo->prepare("
            SELECT C.IDCATEG, C.NOMCATEG
            FROM CATEGORIE C
            JOIN APPARTENIRCATEG AC ON C.IDCATEG = AC.IDCATEG
            JOIN CATPERE CP ON C.IDCATEG = CP.IDCATEG
            WHERE AC.IDPROD = ? 
            AND CP.IDCATEG_PERE = ?
        ");
        $querySecCat->execute([$id, $mainCategory['IDCATEG']]);
        $secondaryCategories = $querySecCat->fetchAll(PDO::FETCH_ASSOC);

        // Ajout des informations de catégorie au produit
        $produit['MAIN_CATEGORY'] = $mainCategory;
        $produit['SECONDARY_CATEGORIES'] = $secondaryCategories;

        echo json_encode($produit);
    } else {
        throw new Exception('ID non fourni');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 