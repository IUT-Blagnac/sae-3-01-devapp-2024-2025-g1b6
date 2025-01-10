<?php
session_start();
require_once('../connect.inc.php');

header('Content-Type: application/json');

if (!isset($_SESSION["admin"])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    // Vérification des données reçues
    if (!isset($_POST['id']) || !isset($_POST['nom']) || empty($_POST['nom'])) {
        throw new Exception('Données manquantes');
    }

    $id = intval($_POST['id']);
    $nom = trim($_POST['nom']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;

    // Vérifier si le nom existe déjà pour une autre catégorie
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM CATEGORIE 
        WHERE NOMCATEG = ? AND IDCATEG != ?
    ");
    $stmt->execute([$nom, $id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Une catégorie avec ce nom existe déjà');
    }

    // Début de la transaction
    $pdo->beginTransaction();

    // Mise à jour de la catégorie
    $stmt = $pdo->prepare("
        UPDATE CATEGORIE 
        SET NOMCATEG = ?, DESCCATEG = ? 
        WHERE IDCATEG = ?
    ");
    $stmt->execute([$nom, $description, $id]);

    // Récupérer le niveau de la catégorie
    $stmt = $pdo->prepare("
        SELECT 
            CASE 
                WHEN IDCATEG IN (11, 12, 13, 14) THEN 'Mère'
                WHEN EXISTS (
                    SELECT 1 
                    FROM CATPERE CP 
                    WHERE CP.IDCATEG = CATEGORIE.IDCATEG 
                    AND CP.IDCATEG_PERE IN (11, 12, 13, 14)
                ) THEN 'Principale'
                ELSE 'Secondaire'
            END as NIVEAU
        FROM CATEGORIE
        WHERE IDCATEG = ?
    ");
    $stmt->execute([$id]);
    $niveau = $stmt->fetchColumn();

    // Supprimer les anciennes relations
    $stmt = $pdo->prepare("DELETE FROM CATPERE WHERE IDCATEG = ?");
    $stmt->execute([$id]);

    // Ajouter les nouvelles relations
    if ($niveau === 'Principale' && isset($_POST['categories_meres'])) {
        $stmt = $pdo->prepare("
            INSERT INTO CATPERE (IDCATEG, IDCATEG_PERE) 
            VALUES (?, ?)
        ");
        foreach ($_POST['categories_meres'] as $idMere) {
            $stmt->execute([$id, $idMere]);
        }
    } 
    elseif ($niveau === 'Secondaire' && isset($_POST['categories_principales'])) {
        $stmt = $pdo->prepare("
            INSERT INTO CATPERE (IDCATEG, IDCATEG_PERE) 
            VALUES (?, ?)
        ");
        foreach ($_POST['categories_principales'] as $idPrincipale) {
            $stmt->execute([$id, $idPrincipale]);
        }
    }

    // Validation de la transaction
    $pdo->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 