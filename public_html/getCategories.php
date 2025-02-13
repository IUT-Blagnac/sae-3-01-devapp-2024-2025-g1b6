<?php
include("connect.inc.php");
header('Content-Type: application/json');


try {

    // Récupération des catégories principales
    $queryMainCat = $pdo->prepare("
        SELECT DISTINCT C.IDCATEG, C.NOMCATEG
        FROM CATEGORIE C
        LEFT JOIN CATPERE CP ON C.IDCATEG = CP.IDCATEG
        WHERE C.IDCATEG NOT IN (11, 12, 13, 14)
        ORDER BY C.NOMCATEG
    ");
    $queryMainCat->execute();
    $mainCategories = $queryMainCat->fetchAll(PDO::FETCH_ASSOC);

    // Si une catégorie principale est spécifiée, récupérer ses sous-catégories
    if (isset($_GET['main_category'])) {
        $querySecCat = $pdo->prepare("
            SELECT C.IDCATEG, C.NOMCATEG
            FROM CATEGORIE C
            JOIN CATPERE ON C.IDCATEG = CATPERE.IDCATEG
            WHERE CATPERE.IDCATEG_PERE = ?
            ORDER BY C.NOMCATEG
        ");
        $querySecCat->execute([$_GET['main_category']]);
        $secondaryCategories = $querySecCat->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'main_categories' => $mainCategories,
            'secondary_categories' => $secondaryCategories
        ]);
    } else {
        echo json_encode(['main_categories' => $mainCategories]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 