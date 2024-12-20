<?php
require_once('connect.inc.php');
header('Content-Type: application/json');

try {
    // Si on demande les catégories parentes
    if (isset($_GET['get_parents'])) {
        $categoryId = $_GET['get_parents'];
        
        // Récupérer toutes les catégories parentes
        $query = $pdo->prepare("
            SELECT DISTINCT C.IDCATEG, C.NOMCATEG
            FROM CATEGORIE C
            JOIN CATPERE CP ON C.IDCATEG = CP.IDCATEG_PERE
            WHERE CP.IDCATEG = ?
        ");
        $query->execute([$categoryId]);
        $parentCategories = $query->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'parent_categories' => $parentCategories
        ]);
        exit;
    }
    
    // Si on demande les sous-catégories
    if (isset($_GET['main_category'])) {
        $mainCategoryId = $_GET['main_category'];
        
        $query = $pdo->prepare("
            SELECT C.IDCATEG, C.NOMCATEG
            FROM CATEGORIE C
            JOIN CATPERE CP ON C.IDCATEG = CP.IDCATEG
            WHERE CP.IDCATEG_PERE = ?
            ORDER BY C.NOMCATEG
        ");
        $query->execute([$mainCategoryId]);
        $categories = $query->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'secondary_categories' => $categories
        ]);
        exit;
    }
    
    // Retourner toutes les catégories principales
    $query = $pdo->query("
        SELECT DISTINCT C.IDCATEG, C.NOMCATEG
        FROM CATEGORIE C
        WHERE C.IDCATEG IN (
            SELECT CP.IDCATEG
            FROM CATPERE CP
            WHERE CP.IDCATEG_PERE IN (11, 12, 13, 14)
        )
        ORDER BY C.NOMCATEG
    ");
    $categories = $query->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'main_categories' => $categories
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 