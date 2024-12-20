<?php
session_start();
require_once('../connect.inc.php');
header('Content-Type: application/json');

try {
    if (!isset($_POST['idCateg'])) {
        throw new Exception('ID de catégorie non fourni');
    }

    $idCateg = $_POST['idCateg'];

    // Vérifier si la catégorie est utilisée dans des produits
    $queryCheck = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM APPARTENIRCATEG 
        WHERE IDCATEG = ?
    ");
    $queryCheck->execute([$idCateg]);
    $result = $queryCheck->fetch();

    if ($result['count'] > 0) {
        throw new Exception('Cette catégorie est utilisée par des produits et ne peut pas être supprimée');
    }

    // Vérifier si la catégorie a des sous-catégories
    $queryCheckSub = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM CATPERE 
        WHERE IDCATEG_PERE = ?
    ");
    $queryCheckSub->execute([$idCateg]);
    $resultSub = $queryCheckSub->fetch();

    if ($resultSub['count'] > 0) {
        throw new Exception('Cette catégorie a des sous-catégories et ne peut pas être supprimée');
    }

    // Supprimer les relations de catégorie parente
    $queryDeleteParent = $pdo->prepare("DELETE FROM CATPERE WHERE IDCATEG = ?");
    $queryDeleteParent->execute([$idCateg]);

    // Supprimer la catégorie
    $queryDelete = $pdo->prepare("DELETE FROM CATEGORIE WHERE IDCATEG = ?");
    $queryDelete->execute([$idCateg]);

    echo json_encode([
        'success' => true,
        'message' => 'Catégorie supprimée avec succès'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 